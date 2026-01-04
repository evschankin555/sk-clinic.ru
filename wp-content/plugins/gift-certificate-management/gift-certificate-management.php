<?php
/*
 * Plugin Name: Управление Подарочными Сертификатами
 * Plugin URI: https://kwork.ru/user/fastproweb
 * Description: Это пользовательский плагин, разработанный для сайта SK-Clinic. Он предназначен для управления рабочим процессом подарочных сертификатов, включая их создание, оплату и генерацию индивидуализированных страниц.
 * Version: 1.0
 * Author: Евгений Щанькин
 * Author URI: https://kwork.ru/user/fastproweb
 * License: GPL2
 */

require_once __DIR__ . '/vendor/autoload.php';
use YooKassa\Client;

global $gift_certificate_db_version;
$gift_certificate_db_version = '1.0';
// Устанавливаем страницу с ID 5009 как главную страницу
//update_option( 'page_on_front', 5009 );
//update_option( 'show_on_front', 'page' );

function gift_certificate_install() {
    global $wpdb;
    global $gift_certificate_db_version;

    $table_name = $wpdb->prefix . 'gift_certificates';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    certificate_id varchar(20) NOT NULL,
    certificate_amount float NOT NULL,
    recipient_name text NOT NULL,
    recipient_message text,
    sender_name text NOT NULL,
    sender_email text NOT NULL,
    sender_phone text NOT NULL,
    payment_id varchar(50), 
    status varchar(20) NOT NULL DEFAULT 'created',
    creation_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
    expiration_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
    user_ip varchar(50) NOT NULL,
    PRIMARY KEY  (id),
    UNIQUE (certificate_id)
) $charset_collate;";


    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

    add_option( 'gift_certificate_db_version', $gift_certificate_db_version );
}
register_activation_hook( __FILE__, 'gift_certificate_install' );

//ini_set('display_errors', 1);
// Регистрация AJAX-обработчика на стороне сервера
add_action('wp_ajax_save_gift_certificate', 'save_gift_certificate');
add_action('wp_ajax_nopriv_save_gift_certificate', 'save_gift_certificate');

function save_gift_certificate($data, $payment_id = null) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'gift_certificates';

    $existing_certificate = $wpdb->get_row("SELECT * FROM $table_name WHERE payment_id = '$payment_id' ORDER BY `id` ASC");

    if($existing_certificate) {
        $certificate_url = home_url("/gift-share/{$existing_certificate->certificate_id}");
        return array('id' => $existing_certificate->certificate_id, 'url' => $certificate_url);
    }

    $certificate_amount = $data['certificate_amount'];
    $recipient_name = $data['recipient_name'];
    $recipient_message = $data['recipient_message'];
    $sender_name = $data['sender_name'];
    $sender_email = $data['sender_email'];
    $sender_phone = $data['sender_phone'];

    $certificate_id = strtoupper(uniqid());

    $expiration_date = date('Y-m-d H:i:s', strtotime('+1 year'));
    $user_ip = $_SERVER['REMOTE_ADDR'];

    $result = $wpdb->insert(
        $table_name,
        array(
            'certificate_id' => $certificate_id,
            'certificate_amount' => $certificate_amount,
            'recipient_name' => $recipient_name,
            'recipient_message' => $recipient_message,
            'sender_name' => $sender_name,
            'sender_email' => $sender_email,
            'sender_phone' => $sender_phone,
            'payment_id' => $payment_id,
            'status' => 'created',
            'creation_time' => current_time('mysql'),
            'expiration_date' => $expiration_date,
            'user_ip' => $user_ip
        )
    );

    if ($result === false) {
        return array('error' => $wpdb->last_error);
    } else {
        $certificate_url = home_url("/gift-share/{$certificate_id}");

        setcookie('certificate_id', $certificate_id, time()+3600*24);  // expires in 1 day
        setcookie('payment_status', 'paid', time()+3600*24);  // expires in 1 day

        return array('id' => $certificate_id, 'url' => $certificate_url);
    }
}


function handle_payment_notification(WP_REST_Request $request) {
    // получаем тело запроса
    $body = $request->get_json_params();

    // проверяем, есть ли нужные данные в запросе
    if (isset($body['type']) && $body['type'] == 'notification' && isset($body['event']) && ($body['event'] == 'payment.waiting_for_capture' || $body['event'] == 'payment.succeeded')) {
        save_gift_certificate();

        return new WP_REST_Response('Payment notification handled successfully', 200);
    }

    // Если нужные данные отсутствуют, отправляем ответ с кодом ошибки
    return new WP_REST_Response('Invalid request', 400);
}

add_action('rest_api_init', function () {
    register_rest_route('gift-certificate-management/v1', '/payment/', array(
        'methods' => 'POST',
        'callback' => 'handle_payment_notification',
    ));
});

function create_payment(WP_REST_Request $request) {
    $data = $request->get_json_params();

    if (!isset($data['sum'])) {
        return new WP_REST_Response('Invalid request', 400);
    }

    // Create gift certificate before creating the payment
    $certificate_data = array(
        'certificate_amount' => $data['certificate_amount'],
        'recipient_name' => $data['recipient_name'],
        'recipient_message' => $data['recipient_message'],
        'sender_name' => $data['sender_name'],
        'sender_email' => $data['sender_email'],
        'sender_phone' => $data['sender_phone'],
    );
    $gift_certificate = save_gift_certificate($certificate_data, null);
    if (isset($gift_certificate['error'])) {
        return new WP_REST_Response('Error saving gift certificate: ' . $gift_certificate['error'], 500);
    }

    $client = new Client();
    $client->setAuth('324277', 'live_3zAOaN0sUy0tcINqwat_kV2LXGX25A_3EIwesJaZ0Yg');

    try {
        $return_url = 'https://sk-clinic.ru/gift-share/' . $gift_certificate['id'];

        $payment = $client->createPayment(
            array(
                'amount' => array(
                    'value' => $data['sum'],
                    'currency' => 'RUB',
                ),
                'confirmation' => array(
                    'type' => 'redirect',
                    'return_url' => $return_url,
                ),
                'capture' => true,
                'description' => 'Оплата подарочного сертификата',
            ),
            $gift_certificate['id']
        );

        global $wpdb;
        $table_name = $wpdb->prefix . 'gift_certificates';
        $wpdb->update(
            $table_name,
            array(
                'payment_id' => $payment->getId(),
            ),
            array( 'certificate_id' => $gift_certificate['id'] )
        );
    } catch (Exception $e) {
        return new WP_REST_Response('Error creating payment: ' . $e->getMessage(), 500);
    }

    return new WP_REST_Response(
        array(
            'payment_id' => $payment->getId(),
            'payment_url' => $payment->getConfirmation()->getConfirmationUrl(),
            'certificate_id' => $gift_certificate['id'],
        ),
        200
    );
}

add_action('rest_api_init', function () {
    register_rest_route('gift-certificate-management/v1', '/create_payment/', array(
        'methods' => 'POST',
        'callback' => 'create_payment',
    ));
});
function check_payment_status(WP_REST_Request $request) {
    $data = $request->get_json_params();
    $payment_id = null;

    if (isset($data['payment_id'])) {
        $payment_id = $data['payment_id'];
    } else if (isset($data['certificate_id'])) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'gift_certificates';

        $payment_id = $wpdb->get_var($wpdb->prepare(
            "SELECT payment_id FROM $table_name WHERE certificate_id = %s",
            $data['certificate_id']
        ));

        if (!$payment_id) {
            return new WP_REST_Response('Invalid request: payment_id not found', 400);
        }
    } else {
        return new WP_REST_Response('Invalid request: neither payment_id nor certificate_id provided', 400);
    }


    $client = new Client();
    $client->setAuth('324277', 'live_3zAOaN0sUy0tcINqwat_kV2LXGX25A_3EIwesJaZ0Yg');

    try {
        // Получаем информацию о платеже
        $payment_info = $client->getPaymentInfo($payment_id);
    } catch (Exception $e) {
        return new WP_REST_Response('Error retrieving payment info: ' . $e->getMessage(), 500);
    }

    if ($payment_info->getStatus() == 'succeeded') {
        // Платеж успешно проведен
        //$gift_certificate = save_gift_certificate($data['payment_id']);
        update_certificate_status_to_paid($data['payment_id']);

        if (isset($gift_certificate['error'])) {
            return new WP_REST_Response('Error saving gift certificate: ' . $gift_certificate['error'], 500);
        }


        return new WP_REST_Response(
            array(
                'status' => 'succeeded',
                'certificate_id' => $data['certificate_id'],
            ),
            200
        );
    }  elseif ($payment_info->getStatus() == 'canceled') {
        // Платеж отменен
        set_certificate_status_by_payment_id($data['payment_id'], 'canceled');
    } else {
        // Платеж еще не был проведен
        return new WP_REST_Response(
            array(
                'status' => $payment_info->getStatus(),
            ),
            200
        );
    }
};

add_action('rest_api_init', function () {
    register_rest_route('gift-certificate-management/v1', '/check_payment/', array(
        'methods' => 'POST',
        'callback' => 'check_payment_status',
    ));
});

function payment_status_shortcode() {
    ob_start();
    ?>
    <div id="payment_status" style="text-align: center;padding: 30px;">
        <!-- Текст статуса будет устанавливаться динамически с помощью JavaScript -->
    </div>
    <?php
    return ob_get_clean();
}

add_shortcode('gift_certificate_management_payment_status', 'payment_status_shortcode');

$gift_certificate_script_version = '1.45';

function gift_certificate_enqueue_scripts() {
    global $gift_certificate_script_version;

    // Старый скрипт для /gift/ страницы
    wp_enqueue_script(
        'gift-certificate-script',
        plugins_url('gift-certificate.js', __FILE__),
        array('jquery'),
        $gift_certificate_script_version,
        true
    );

    // Определяем ajaxurl для фронтенда
    wp_localize_script(
        'gift-certificate-script',
        'ajax_object',
        array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) )
    );

    // Примечание: JS для новой формы [gift_you_form] встроен в шаблон templates/gift-you-form.php
}
add_action('wp_enqueue_scripts', 'gift_certificate_enqueue_scripts');
function update_certificate_status(WP_REST_Request $request) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'gift_certificates';

    // Получаем id и статус из тела запроса
    $body = $request->get_json_params();
    $id = $body['id'];
    $status = $body['status'];

    // Обновляем статус сертификата в базе данных
    $result = $wpdb->update(
        $table_name,
        array('status' => $status),
        array('certificate_id' => $id)
    );

    if ($result === false) {
        // Возвращаем ошибку
        return new WP_REST_Response('Error updating status', 500);
    } else {
        // Возвращаем успешный ответ
        return new WP_REST_Response('Status updated successfully', 200);
    }
}

add_action('rest_api_init', function () {
    register_rest_route('gift-certificate-management/v1', '/status/', array(
        'methods' => 'POST',
        'callback' => 'update_certificate_status',
    ));
});

function show_gift_certificate($atts) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'gift_certificates';

    // Получение атрибутов шорткода
    $atts = shortcode_atts(
        array(
            'field' => '',
        ), $atts, 'gift_certificate'
    );

    // Получение идентификатора сертификата из URL
    $url_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $url_segments = explode('/', trim($url_path, '/'));
    $certificate_id = $url_segments[array_search('gift-share', $url_segments) + 1];

    // Проверка наличия необходимого поля
    if (!in_array($atts['field'], array('expiration_date', 'certificate_id', 'certificate_amount', 'recipient_name', 'recipient_message', 'sender_name', 'sender_email', 'sender_phone'))) {
        return "Invalid field name.";
    }

    // Получение данных сертификата из базы данных
    $certificate = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE certificate_id = %s", $certificate_id));
    if ($certificate) {
        // Форматирование даты
        if ($atts['field'] === 'expiration_date') {
            $expiration_date = date('d.m.Y', strtotime($certificate->{$atts['field']}));
            return $expiration_date;
        }

        // Возвращение запрошенного поля
        return $certificate->{$atts['field']};
    } else {
        return "No certificate found.";
    }
}

add_shortcode('gift_certificate', 'show_gift_certificate');
function custom_rewrite_rule() {
    add_rewrite_rule('^gift-share/([A-Z0-9]+)/?$', 'index.php?pagename=gift-share', 'top');
}
add_action('init', 'custom_rewrite_rule', 10, 0);

function set_certificate_id() {
    global $wp;
    $s_id = isset($wp->request) && preg_match('/^gift-share\/([A-Z0-9]+)/', $wp->request, $matches) ? $matches[1] : null;
    if(!empty($s_id)){
        $wp->query_vars['certificate_id'] = $s_id;
    }
}
add_action('parse_request', 'set_certificate_id');

function my_pre_get_posts( $query ) {
    if (!is_admin() && $query->is_main_query()) {
        $current_url = home_url(add_query_arg(array()));
        if(preg_match('/^\/gift-share\/([A-Z0-9]+)/', parse_url($current_url, PHP_URL_PATH))){
            $query->set('post_type', 'page');
            $query->set('pagename', 'gift-share');
            $query->set('name', 'gift-share');
            $query->is_single = false;
            $query->is_page = true;
            $query->is_singular = true;
            $query->is_archive = false;
            $query->is_home = false;
            $query->is_404 = false;
        }
    }
}
add_action( 'pre_get_posts', 'my_pre_get_posts');

function delete_gift_certificate_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'gift_certificates';

    // Проверка наличия таблицы перед удалением
    if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
        // SQL запрос для удаления таблицы
        $wpdb->query("DROP TABLE IF EXISTS $table_name");
    }
}

// Вызов функции
//delete_gift_certificate_table();
function update_certificate_status_to_paid($payment_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'gift_certificates';

    // Находим сертификат по payment_id
    $certificate = $wpdb->get_row("SELECT * FROM $table_name WHERE payment_id = '$payment_id'");

    // Если сертификат найден, обновляем его статус на 'paid'
    if($certificate) {
        $result = $wpdb->update(
            $table_name,
            array('status' => 'paid'),
            array('certificate_id' => $certificate->certificate_id)
        );

        if ($result === false) {
            // Возвращаем ошибку
            return new WP_REST_Response('Error updating status', 500);
        } else {
            // Возвращаем успешный ответ
            return new WP_REST_Response('Status updated successfully', 200);
        }
    } else {
        // Если сертификат не найден, возвращаем ошибку
        return new WP_REST_Response('Certificate not found', 404);
    }
}
function update_certificate_expiration_date(WP_REST_Request $request) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'gift_certificates';

    // Получаем id и дату окончания из тела запроса
    $body = $request->get_json_params();
    $id = $body['id'];
    $expiration_date = $body['expiration_date'];

    // Обновляем дату окончания сертификата в базе данных
    $result = $wpdb->update(
        $table_name,
        array('expiration_date' => $expiration_date),
        array('certificate_id' => $id)
    );

    if ($result === false) {
        // Возвращаем ошибку
        return new WP_REST_Response('Error updating expiration date', 500);
    } else {
        // Возвращаем успешный ответ
        return new WP_REST_Response('Expiration date updated successfully', 200);
    }
}

add_action('rest_api_init', function () {
    register_rest_route('gift-certificate-management/v1', '/expiration_date/', array(
        'methods' => 'POST',
        'callback' => 'update_certificate_expiration_date',
    ));
});
function create_certificate(WP_REST_Request $request) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'gift_certificates';

    $body = $request->get_json_params();

    // Используем те же данные, что и в вашем коде, только из POST-запроса, а не из cookie.
    $payment_id = $body['payment_id'];
    $certificate_amount = $body['certificate_amount'];
    $recipient_name = $body['recipient_name'];
    $recipient_message = $body['recipient_message'];
    $sender_name = $body['sender_name'];
    $sender_email = $body['sender_email'];
    $sender_phone = $body['sender_phone'];

    // Эти значения такие же, как в вашем коде.
    $certificate_id = strtoupper(uniqid());
    $expiration_date = date('Y-m-d H:i:s', strtotime('+1 year'));

    // Используем код из функции save_gift_certificate для вставки данных в базу данных.
    $result = $wpdb->insert(
        $table_name,
        array(
            'certificate_id' => $certificate_id,
            'certificate_amount' => $certificate_amount,
            'recipient_name' => $recipient_name,
            'recipient_message' => $recipient_message,
            'sender_name' => $sender_name,
            'sender_email' => $sender_email,
            'sender_phone' => $sender_phone,
            'payment_id' => $payment_id,
            'status' => 'paid', // статус устанавливается автоматически
            'creation_time' => current_time('mysql'),
            'expiration_date' => $expiration_date,
        )
    );

    if ($result === false) {
        return new WP_REST_Response($wpdb->last_error, 500);
    } else {
        $certificate_url = home_url("/gift-share/{$certificate_id}");
        return new WP_REST_Response(array('id' => $certificate_id, 'url' => $certificate_url), 200);
    }
}

add_action('rest_api_init', function () {
    register_rest_route('gift-certificate-management/v1', '/create/', array(
        'methods' => 'POST',
        'callback' => 'create_certificate',
    ));
});


class trueOptionsPage
{
    public $page_slug;
    function __construct()
    {
        $this->page_slug = 'gift_certificate';
        add_action('admin_menu', array($this, 'add'), 25);
    }
    function add()
    {
        add_menu_page('Подарочные Сертификаты', 'Подарочные Сертификаты', 'manage_options', $this->page_slug . '_connection', array($this, 'display_connection'), 'dashicons-admin-generic', 1);
    }
    function echoStyle()
    {
        include_once 'css/style.php';
        getStyle();
    }
    function echoStyleTable(){
        include_once 'css/styleTable.php';
        getStyleTable();
    }
    function echoStyleTableTwo(){
        include_once 'css/styleTableTwo.php';
        getStyleTableTwo();
    }
    function echoTitle()
    {
        echo ' <h1>Подарочные Сертификаты<span></span></h1>';
    }
    function getAllCertificatesData()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'gift_certificates';

        $query = "SELECT * FROM {$table_name} WHERE status != 'deleted' ORDER BY creation_time DESC";

        return $wpdb->get_results($query);
    }


    function translate_status($status) {
        switch ($status) {
            case 'created':
                return 'Создан';
            case 'paid':
                return 'Оплачен';
            case 'expired':
                return 'Просрочен';
            case 'used':
                return 'Использован';
            case 'deleted':
                return 'Удален';
            case 'canceled':
                return 'Отменён';
            default:
                return $status;
        }
    }

    function translate_sms_status($status) {
        switch ($status) {
            case 'none':
                return '-';
            case 'pending':
                return 'Ожидает';
            case 'sent':
                return 'Отправлено';
            case 'delivered':
                return 'Доставлено';
            case 'failed':
                return 'Ошибка';
            default:
                return $status;
        }
    }

    function get_sms_status_color($status) {
        switch ($status) {
            case 'delivered':
                return '#4CAF50';
            case 'sent':
                return '#2196F3';
            case 'pending':
                return '#FF9800';
            case 'failed':
                return '#f44336';
            default:
                return '#999';
        }
    }

    function echoGiftData()
    {
        $results = $this->getAllCertificatesData();

        ?><div>
        <style>
            .orders-table td{
                line-height: 1;
            }
            .type-badge {
                display: inline-block;
                padding: 2px 6px;
                border-radius: 3px;
                font-size: 11px;
                font-weight: bold;
            }
            .type-old {
                background: #e0e0e0;
                color: #666;
            }
            .type-new {
                background: #90A384;
                color: #fff;
            }
            .sms-status {
                display: inline-block;
                padding: 2px 6px;
                border-radius: 3px;
                font-size: 11px;
            }
            .btn-send-sms {
                background: #90A384;
                color: #fff;
                border: none;
                padding: 3px 8px;
                border-radius: 3px;
                cursor: pointer;
                font-size: 11px;
            }
            .btn-send-sms:hover {
                background: #7a8c70;
            }
            .btn-send-sms:disabled {
                background: #ccc;
                cursor: not-allowed;
            }
            .filter-buttons {
                margin: 15px 0;
                display: flex;
                gap: 8px;
            }
            .filter-btn {
                background: #f0f0f1;
                border: 1px solid #c3c4c7;
                border-radius: 4px;
                padding: 6px 14px;
                font-size: 13px;
                cursor: pointer;
                color: #50575e;
                transition: all 0.15s ease;
            }
            .filter-btn:hover {
                background: #e0e0e0;
                border-color: #999;
            }
            .filter-btn.active {
                background: #90A384;
                color: #fff;
                border-color: #7a8c70;
            }
        </style>
        <button id="create-certificate">Создать сертификат</button>

        <div class="filter-buttons">
            <button class="filter-btn active" data-filter="all">Все</button>
            <button class="filter-btn" data-filter="old">Старые</button>
            <button class="filter-btn" data-filter="new">Новые</button>
        </div>

        <input type="text" style="width: calc(100% - 20px);margin-bottom: 10px; " id="searchInput" onkeyup="searchFunction()" placeholder="Для поиска введите часть или целиком имя получателя или отправителя, емейл или номер сертификата">

        <table class="heavyTable orders-table" id="header-table">
            <thead>
            <tr>
                <th style="width: 4%">Тип</th>
                <th style="width: 7%">ID / Код</th>
                <th style="width: 5%">Сумма</th>
                <th style="width: 7%">Получатель</th>
                <th style="width: 7%">Тел. получателя</th>
                <th style="width: 12%">Сообщение</th>
                <th style="width: 7%">Отправитель</th>
                <th style="width: 8%">Email</th>
                <th style="width: 5%">Статус</th>
                <th style="width: 6%">SMS</th>
                <th style="width: 8%">ID Платежа</th>
                <th style="width: 8%">Создан</th>
                <th style="width: 8%">Истекает</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($results as $item):
                $is_new = (isset($item->certificate_type) && $item->certificate_type === 'new');
                $cert_url = $is_new
                    ? 'https://sk-clinic.ru/gift-you/' . ($item->short_code ?? $item->certificate_id) . '/'
                    : 'https://sk-clinic.ru/gift-share/' . $item->certificate_id . '/';
                $short_url = $is_new && !empty($item->short_code)
                    ? 'https://sk-clinic.ru/g/' . $item->short_code . '/'
                    : '';
            ?>
                <tr data-type="<?= $is_new ? 'new' : 'old' ?>">
                    <td>
                        <span class="type-badge <?= $is_new ? 'type-new' : 'type-old' ?>">
                            <?= $is_new ? 'NEW' : 'OLD' ?>
                        </span>
                    </td>
                    <td>
                        <a href="<?= $cert_url ?>" target="_blank" title="Открыть сертификат">
                            <?= $is_new && !empty($item->short_code) ? strtoupper($item->short_code) : substr($item->certificate_id, 0, 8) ?>
                        </a>
                    </td>
                    <td><?= number_format($item->certificate_amount, 0, '', ' ') ?></td>
                    <td><?= esc_html($item->recipient_name) ?></td>
                    <td><?= isset($item->recipient_phone) ? esc_html($item->recipient_phone) : '-' ?></td>
                    <td title="<?= esc_attr($item->recipient_message) ?>"><?= mb_substr(esc_html($item->recipient_message), 0, 30) ?>...</td>
                    <td><?= esc_html($item->sender_name) ?></td>
                    <td><?= esc_html($item->sender_email) ?></td>
                    <td>
                        <a href="#" class="change-status-link" data-status="<?= $item->status ?>" data-id="<?= $item->certificate_id ?>">
                            <?= $this->translate_status($item->status) ?>
                        </a>
                    </td>
                    <td>
                        <?php if ($is_new): ?>
                            <?php
                            $sms_status = isset($item->sms_status) ? $item->sms_status : 'none';
                            $is_scheduled = !empty($item->scheduled_at) && $sms_status === 'pending';

                            // Для запланированных показываем специальный статус
                            if ($is_scheduled) {
                                $sms_color = '#9C27B0'; // Фиолетовый для запланированных
                                $sms_text = '⏰ ' . date('d.m H:i', strtotime($item->scheduled_at));
                            } else {
                                $sms_color = $this->get_sms_status_color($sms_status);
                                $sms_text = $this->translate_sms_status($sms_status);
                            }
                            ?>
                            <span class="sms-status" style="background: <?= $sms_color ?>; color: #fff;">
                                <?= $sms_text ?>
                            </span>
                            <?php if ($sms_status === 'pending' && $item->status === 'paid'): ?>
                            <br>
                            <button class="btn-send-sms" data-id="<?= $item->certificate_id ?>" title="Отправить SMS сейчас">
                                <?= $is_scheduled ? 'Отправить сейчас' : 'Отправить' ?>
                            </button>
                            <?php endif; ?>
                        <?php else: ?>
                            <span style="color:#999;">-</span>
                        <?php endif; ?>
                    </td>
                    <td><a href="https://yookassa.ru/my/payments?search=<?= $item->payment_id ?>/" target="_blank"><?= substr($item->payment_id, 0, 10) ?>...</a></td>
                    <td><?= date('d.m.Y H:i', strtotime($item->creation_time)) ?></td>
                    <td>
                        <a href="#" class="change-expiration-date-link" data-id="<?= $item->certificate_id ?>" data-expiration_date="<?= $item->expiration_date ?>">
                            <?= date('d.m.Y', strtotime($item->expiration_date)) ?>
                        </a>
                    </td>

                    <td hidden><?= $item->recipient_name . " " . $item->sender_name . " " . $item->sender_email . " " . $item->certificate_id . " " . ($item->short_code ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <!-- HTML для модального окна -->
        <div id="modal-overlay" style="display:none;">
            <div id="modal-status" style="display:none;">
                <h2>Изменение статуса</h2>
                <select id="status-select">
                    <option value="paid">Оплачен</option>
                    <option value="expired">Просрочен</option>
                    <option value="used">Использован</option>
                    <!--<option value="deleted">Удален</option>-->
                </select>
                <div id="modal-buttons">
                    <button id="cancel-status">Отмена</button>
                    <button id="save-status">Сохранить</button>
                </div>
            </div>
            <div id="modal-expiration-date" style="display:none;">
                <h2>Изменение даты окончания</h2>
                <input type="datetime-local" id="expiration-date-input">
                <div id="modal-buttons-expiration-date">
                    <button id="cancel-expiration-date">Отмена</button>
                    <button id="save-expiration-date">Сохранить</button>
                </div>
            </div>
            <div id="modal-create" style="display:none;">
                <h2>Создание сертификата</h2>
                <form id="certificate-form">
                    <label for="payment-id-input">ID платежа</label>
                    <input type="text" id="payment-id-input" required>

                    <label for="certificate-amount-input">Сумма сертификата</label>
                    <input type="number" id="certificate-amount-input" required>

                    <label for="recipient-name-input">Имя получателя</label>
                    <input type="text" id="recipient-name-input" required>

                    <label for="recipient-message-input">Сообщение получателю</label>
                    <input type="text" id="recipient-message-input" required>

                    <label for="sender-name-input">Имя отправителя</label>
                    <input type="text" id="sender-name-input" required>

                    <label for="sender-email-input">Email отправителя</label>
                    <input type="email" id="sender-email-input" required>

                    <label for="sender-phone-input">Телефон отправителя</label>
                    <input type="tel" id="sender-phone-input" required>

                    <div id="modal-create-buttons">
                        <button type="reset" id="cancel-create">Отмена</button>
                        <button type="submit" id="save-create">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>




        <!-- jQuery и JavaScript для обработки клика по статусу и сохранения нового статуса -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script>
            jQuery(document).ready(function() {
                var currentId = null;

                // Показываем модальное окно и оверлей
                jQuery('.change-status-link').click(function(e) {
                    e.preventDefault();
                    currentId = jQuery(this).data('id');
                    var currentStatus = jQuery(this).data('status');

                    jQuery('#status-select').val(currentStatus);

                    jQuery('#modal-overlay').show();
                    jQuery('#modal-status').show();
                });

                // Закрытие модального окна и оверлея при нажатии кнопки "Отмена"
                jQuery('#cancel-status').click(function() {
                    jQuery('#modal-overlay').hide();
                    jQuery('#modal-status').hide();
                });

                jQuery('#save-status').click(function() {
                    var newStatus = jQuery('#status-select').val();
                    jQuery('#modal-overlay').hide();
                    jQuery('#modal-status').hide();
                    // Здесь вызывается ajax-запрос для обновления статуса в базе данных
                    jQuery.ajax({
                        url: '/wp-json/gift-certificate-management/v1/status/',
                        method: 'POST',
                        data: JSON.stringify({
                            id: currentId,
                            status: newStatus
                        }),
                        contentType: 'application/json; charset=utf-8',
                        dataType: 'json',
                        success: function(response) {
                            // Получаем выбранный текст статуса (русский)
                            var newStatusText = jQuery('#status-select option:selected').text();

                            // При успешном обновлении статуса обновляем его на странице и закрываем модальное окно
                            jQuery('a[data-id="' + currentId + '"].change-status-link').text(newStatusText);
                            jQuery('#modal-status').hide();
                        },

                        error: function() {
                            alert('Error updating status');
                        }
                    });
                });
                var currentId = null;

// Показываем модальное окно и оверлей
                jQuery('.change-expiration-date-link').click(function(e) {
                    e.preventDefault();
                    currentId = jQuery(this).data('id');
                    var currentExpirationDate = jQuery(this).data('expiration_date'); // Получаем текущую дату
                    jQuery('#expiration-date-input').val(currentExpirationDate); // Устанавливаем текущую дату в поле ввода
                    jQuery('#modal-overlay').show();
                    jQuery('#modal-expiration-date').show();
                });



                // Закрытие модального окна и оверлея при нажатии кнопки "Отмена"
                jQuery('#cancel-expiration-date').click(function() {
                    jQuery('#modal-overlay').hide();
                    jQuery('#modal-expiration-date').hide();
                });




// Преобразование даты в формат MySQL
                function toMySQLFormat(dateString) {
                    var date = new Date(dateString);
                    var yyyy = date.getFullYear();
                    var mm = date.getMonth() < 9 ? "0" + (date.getMonth() + 1) : (date.getMonth() + 1); // getMonth() is zero-based
                    var dd  = date.getDate() < 10 ? "0" + date.getDate() : date.getDate();
                    var hh = date.getHours() < 10 ? "0" + date.getHours() : date.getHours();
                    var min = date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes();
                    return "".concat(yyyy).concat("-").concat(mm).concat("-").concat(dd).concat(" ").concat(hh).concat(":").concat(min);
                }

                jQuery('#save-expiration-date').click(function() {
                    var newExpirationDate = jQuery('#expiration-date-input').val();
                    jQuery('#modal-overlay').hide();
                    jQuery('#modal-expiration-date').hide();

                    // Здесь вызывается ajax-запрос для обновления даты окончания в базе данных
                    jQuery.ajax({
                        url: '/wp-json/gift-certificate-management/v1/expiration_date/',
                        method: 'POST',
                        data: JSON.stringify({
                            id: currentId,
                            expiration_date: newExpirationDate
                        }),
                        contentType: 'application/json; charset=utf-8',
                        dataType: 'json',
                        success: function(response) {
                            // При успешном обновлении даты окончания обновляем её на странице
                            var mysqlFormattedDate = toMySQLFormat(newExpirationDate);
                            jQuery('a[data-id="' + currentId + '"].change-expiration-date-link').text(mysqlFormattedDate);
                            jQuery('#modal-expiration-date').hide();
                        },
                        error: function() {
                            alert('Error updating expiration date');
                        }
                    });
                });

                jQuery('#create-certificate').click(function(e) {
                    e.preventDefault();
                    jQuery('#modal-overlay').show();
                    jQuery('#modal-create').show();
                });

                jQuery('#cancel-create').click(function() {
                    jQuery('#modal-overlay').hide();
                    jQuery('#modal-create').hide();
                });

                jQuery('#certificate-form').submit(function(e) {
                    e.preventDefault();
                    jQuery('#modal-overlay').hide();
                    jQuery('#modal-create').hide();

                    var data = {
                        payment_id: jQuery('#payment-id-input').val(),
                        certificate_amount: jQuery('#certificate-amount-input').val(),
                        recipient_name: jQuery('#recipient-name-input').val(),
                        recipient_message: jQuery('#recipient-message-input').val(),
                        sender_name: jQuery('#sender-name-input').val(),
                        sender_email: jQuery('#sender-email-input').val(),
                        sender_phone: jQuery('#sender-phone-input').val()
                    };

                    jQuery.ajax({
                        url: '/wp-json/gift-certificate-management/v1/create/',
                        method: 'POST',
                        data: JSON.stringify(data),
                        contentType: 'application/json; charset=utf-8',
                        dataType: 'json',
                        success: function(response) {
                            document.location.href = document.location.href;
                        },
                        error: function() {
                            alert('Error creating certificate');
                        }
                    });
                });

                // Фильтрация по типу сертификата
                jQuery('.filter-btn').click(function() {
                    jQuery('.filter-btn').removeClass('active');
                    jQuery(this).addClass('active');

                    var filter = jQuery(this).data('filter');
                    var rows = jQuery('#header-table tbody tr');

                    if (filter === 'all') {
                        rows.show();
                    } else {
                        rows.each(function() {
                            var rowType = jQuery(this).data('type');
                            if (rowType === filter) {
                                jQuery(this).show();
                            } else {
                                jQuery(this).hide();
                            }
                        });
                    }
                });

                // Кнопка отправки SMS
                jQuery('.btn-send-sms').click(function() {
                    var btn = jQuery(this);
                    var certificateId = btn.data('id');

                    if (!confirm('Отправить SMS сейчас?')) {
                        return;
                    }

                    btn.prop('disabled', true).text('...');

                    jQuery.ajax({
                        url: '/wp-json/gift-you/v1/send_sms/',
                        method: 'POST',
                        data: JSON.stringify({ certificate_id: certificateId }),
                        contentType: 'application/json; charset=utf-8',
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'sent') {
                                btn.closest('td').html('<span class="sms-status" style="background: #2196F3; color: #fff;">Отправлено</span>');
                            } else {
                                btn.prop('disabled', false).text('Отправить');
                                alert('Ошибка отправки SMS');
                            }
                        },
                        error: function() {
                            btn.prop('disabled', false).text('Отправить');
                            alert('Ошибка отправки SMS');
                        }
                    });
                });

            });

        </script>

        <?php
    }


    function echoGift()
    {
        echo '<div id="tab-content5" 
             class="tab-content" 
             role="tabpanel" 
             aria-labelledby="description" 
             aria-hidden="true">';
        $this->echoGiftData();
        echo '</div>';
    }
    function echoScriptSave(){
        include_once 'js/ScriptSave.php';
        getScriptSave();
    }
    function display_connection()
    {
        $this->echoStyle();
        $this->echoStyleTable();
        $this->echoStyleTableTwo();
        $this->echoScriptSave();
        $this->echoTitle();
        echo '<script type="module" src="https://unpkg.com/@deckdeckgo/highlight-code@latest/dist/deckdeckgo-highlight-code/deckdeckgo-highlight-code.esm.js"></script>';
        $this->echoGift();
    }
}
$page000 = new trueOptionsPage();


function gift_certificate_update_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'gift_certificates';

    $wpdb->query("ALTER TABLE $table_name ADD user_ip varchar(50) NOT NULL AFTER expiration_date");
}
register_activation_hook( __FILE__, 'gift_certificate_update_table' );

add_action('rest_api_init', function () {
    register_rest_route('gift-certificate-management/v1', '/all-certificates/', array(
        'methods' => 'GET',
        'callback' => 'get_all_certificates',
    ));
});

function get_all_certificates() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'gift_certificates';

    $results = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id DESC", ARRAY_A);

    if (empty($results)) {
        return new WP_REST_Response('No data found', 404);
    }

    return new WP_REST_Response($results, 200);
}

function get_certificate_status(WP_REST_Request $request) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'gift_certificates';

    $certificate_id = $request->get_param('certificate_id');

    $certificate = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE certificate_id = %s", $certificate_id));

    if (!$certificate) {
        return new WP_REST_Response('Certificate not found', 404);
    }

    return new WP_REST_Response(
        array(
            'status' => $certificate->status,
            'payment_id' => $certificate->payment_id
        ),
        200
    );
}


add_action('rest_api_init', function () {
    register_rest_route('gift-certificate-management/v1', '/certificate_status/', array(
        'methods' => 'GET',
        'callback' => 'get_certificate_status',
    ));
});
function set_certificate_status_by_payment_id($payment_id, $status) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'gift_certificates';

    // Находим сертификат по payment_id
    $certificate = $wpdb->get_row("SELECT * FROM $table_name WHERE payment_id = '$payment_id'");

    // Если сертификат найден, обновляем его статус на переданный $status
    if($certificate) {
        $result = $wpdb->update(
            $table_name,
            array('status' => $status),
            array('certificate_id' => $certificate->certificate_id)
        );

        if ($result === false) {
            // Возвращаем ошибку
            return new WP_REST_Response('Error updating status', 500);
        } else {
            // Возвращаем успешный ответ
            return new WP_REST_Response('Status updated successfully', 200);
        }
    } else {
        // Если сертификат не найден, возвращаем ошибку
        return new WP_REST_Response('Certificate not found', 404);
    }
}

// ============================================================================
// GIFT-YOU: Новая система сертификатов с SMS-оповещением
// ============================================================================

/**
 * Миграция БД - добавление новых полей для gift-you
 */
function gift_you_migrate_database() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'gift_certificates';

    // Проверяем, существует ли уже колонка short_code
    $column_exists = $wpdb->get_results("SHOW COLUMNS FROM $table_name LIKE 'short_code'");

    if (empty($column_exists)) {
        $wpdb->query("ALTER TABLE $table_name
            ADD COLUMN short_code VARCHAR(10) NULL AFTER user_ip,
            ADD COLUMN recipient_phone VARCHAR(20) NULL AFTER short_code,
            ADD COLUMN scheduled_at DATETIME NULL AFTER recipient_phone,
            ADD COLUMN sms_sent_at DATETIME NULL AFTER scheduled_at,
            ADD COLUMN sms_status VARCHAR(20) DEFAULT 'none' AFTER sms_sent_at,
            ADD COLUMN sms_id VARCHAR(50) NULL AFTER sms_status,
            ADD COLUMN sender_notified TINYINT(1) DEFAULT 0 AFTER sms_id,
            ADD COLUMN certificate_type VARCHAR(10) DEFAULT 'old' AFTER sender_notified
        ");

        // Добавляем индекс для short_code
        $wpdb->query("ALTER TABLE $table_name ADD UNIQUE INDEX idx_short_code (short_code)");
    }
}
add_action('admin_init', 'gift_you_migrate_database');

/**
 * Генерация уникального короткого кода
 *
 * 5 символов = 36^5 = 60,466,176 комбинаций
 * При 200 сертификатах в год - хватит на 300,000+ лет
 */
function generate_gift_short_code($length = 5) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'gift_certificates';

    // Только строчные буквы и цифры (без путаницы 0/O, 1/l)
    $characters = 'abcdefghjkmnpqrstuvwxyz23456789';
    $max_attempts = 10;

    for ($attempt = 0; $attempt < $max_attempts; $attempt++) {
        $short_code = '';
        for ($i = 0; $i < $length; $i++) {
            $short_code .= $characters[random_int(0, strlen($characters) - 1)];
        }

        // Проверяем уникальность
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE short_code = %s",
            $short_code
        ));

        if ($exists == 0) {
            return $short_code;
        }
    }

    // Если не удалось - добавляем длину
    return generate_gift_short_code($length + 1);
}

/**
 * Rewrite rules для gift-you
 */
function gift_you_rewrite_rules() {
    // Полный URL: /gift-you/{short_code}/
    add_rewrite_rule('^gift-you/([a-zA-Z0-9]+)/?$', 'index.php?gift_you_code=$1', 'top');
    // Короткий URL для SMS: /g/{short_code}/
    add_rewrite_rule('^g/([a-zA-Z0-9]+)/?$', 'index.php?gift_you_code=$1', 'top');
}
add_action('init', 'gift_you_rewrite_rules');

/**
 * Регистрация query var
 */
function gift_you_query_vars($vars) {
    $vars[] = 'gift_you_code';
    return $vars;
}
add_filter('query_vars', 'gift_you_query_vars');

/**
 * Подключение шаблона для gift-you
 */
function gift_you_template_redirect() {
    $short_code = get_query_var('gift_you_code');

    if (!empty($short_code)) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'gift_certificates';

        // Ищем сертификат по short_code
        $certificate = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE short_code = %s",
            $short_code
        ));

        if ($certificate) {
            // Подключаем шаблон
            include plugin_dir_path(__FILE__) . 'templates/gift-you-template.php';
            exit;
        } else {
            // Сертификат не найден - 404
            global $wp_query;
            $wp_query->set_404();
            status_header(404);
        }
    }
}
add_action('template_redirect', 'gift_you_template_redirect');

/**
 * Подключение класса SMS
 */
require_once plugin_dir_path(__FILE__) . 'includes/class-sms-sender.php';

/**
 * REST API: Создание сертификата нового типа с оплатой
 *
 * ТЕСТОВЫЙ РЕЖИМ: передайте test_mode: true в данных для пропуска оплаты
 * Или откройте страницу /gift-new/?test=1
 */
function gift_you_create_payment(WP_REST_Request $request) {
    $data = $request->get_json_params();

    if (!isset($data['sum']) || !isset($data['recipient_phone'])) {
        return new WP_REST_Response('Invalid request: sum and recipient_phone required', 400);
    }

    // Проверяем тестовый режим
    $is_test_mode = !empty($data['test_mode']);

    global $wpdb;
    $table_name = $wpdb->prefix . 'gift_certificates';

    // Генерируем короткий код
    $short_code = generate_gift_short_code();
    $certificate_id = strtoupper(uniqid());
    $expiration_date = date('Y-m-d H:i:s', strtotime('+1 year'));

    // Определяем время отправки SMS
    $scheduled_at = null;
    $sms_status = 'pending';
    if (!empty($data['scheduled_at'])) {
        $scheduled_at = date('Y-m-d H:i:s', strtotime($data['scheduled_at']));
    }

    // В тестовом режиме сразу ставим статус paid
    $initial_status = $is_test_mode ? 'paid' : 'created';

    // Сохраняем сертификат
    $result = $wpdb->insert(
        $table_name,
        array(
            'certificate_id' => $certificate_id,
            'certificate_amount' => $data['certificate_amount'],
            'recipient_name' => $data['recipient_name'],
            'recipient_message' => $data['recipient_message'],
            'sender_name' => $data['sender_name'],
            'sender_email' => $data['sender_email'],
            'sender_phone' => $data['sender_phone'],
            'status' => $initial_status,
            'creation_time' => current_time('mysql'),
            'expiration_date' => $expiration_date,
            'user_ip' => $_SERVER['REMOTE_ADDR'],
            'short_code' => $short_code,
            'recipient_phone' => $data['recipient_phone'],
            'scheduled_at' => $scheduled_at,
            'sms_status' => $sms_status,
            'certificate_type' => 'new',
            'payment_id' => $is_test_mode ? 'TEST-' . $certificate_id : null
        )
    );

    if ($result === false) {
        return new WP_REST_Response('Error saving certificate: ' . $wpdb->last_error, 500);
    }

    // ТЕСТОВЫЙ РЕЖИМ: пропускаем оплату, сразу отправляем SMS (если не запланировано)
    if ($is_test_mode) {
        // Если нет запланированной даты - отправляем SMS сразу
        if (empty($scheduled_at)) {
            gift_you_send_sms_now($certificate_id);
        }

        return new WP_REST_Response(
            array(
                'test_mode' => true,
                'payment_id' => 'TEST-' . $certificate_id,
                'payment_url' => home_url('/gift-you/' . $short_code . '/'), // Сразу на страницу сертификата
                'certificate_id' => $certificate_id,
                'short_code' => $short_code,
                'certificate_url' => home_url('/gift-you/' . $short_code . '/'),
                'short_url' => home_url('/g/' . $short_code . '/'),
            ),
            200
        );
    }

    // БОЕВОЙ РЕЖИМ: создаём платёж в YooKassa
    $client = new \YooKassa\Client();
    $client->setAuth('324277', 'live_3zAOaN0sUy0tcINqwat_kV2LXGX25A_3EIwesJaZ0Yg');

    try {
        $return_url = home_url('/gift-you/' . $short_code . '/?sender=1');

        $payment = $client->createPayment(
            array(
                'amount' => array(
                    'value' => $data['sum'],
                    'currency' => 'RUB',
                ),
                'confirmation' => array(
                    'type' => 'redirect',
                    'return_url' => $return_url,
                ),
                'capture' => true,
                'description' => 'Оплата подарочного сертификата (новый формат)',
            ),
            $certificate_id
        );

        // Обновляем payment_id
        $wpdb->update(
            $table_name,
            array('payment_id' => $payment->getId()),
            array('certificate_id' => $certificate_id)
        );

    } catch (Exception $e) {
        return new WP_REST_Response('Error creating payment: ' . $e->getMessage(), 500);
    }

    return new WP_REST_Response(
        array(
            'payment_id' => $payment->getId(),
            'payment_url' => $payment->getConfirmation()->getConfirmationUrl(),
            'certificate_id' => $certificate_id,
            'short_code' => $short_code,
            'certificate_url' => home_url('/gift-you/' . $short_code . '/'),
            'short_url' => home_url('/g/' . $short_code . '/'),
        ),
        200
    );
}

add_action('rest_api_init', function () {
    register_rest_route('gift-you/v1', '/create_payment/', array(
        'methods' => 'POST',
        'callback' => 'gift_you_create_payment',
        'permission_callback' => '__return_true'
    ));
});

/**
 * REST API: Проверка статуса платежа и отправка SMS
 */
function gift_you_check_payment(WP_REST_Request $request) {
    $data = $request->get_json_params();

    if (!isset($data['short_code']) && !isset($data['certificate_id'])) {
        return new WP_REST_Response('Invalid request', 400);
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'gift_certificates';

    // Находим сертификат
    if (isset($data['short_code'])) {
        $certificate = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE short_code = %s",
            $data['short_code']
        ));
    } else {
        $certificate = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE certificate_id = %s",
            $data['certificate_id']
        ));
    }

    if (!$certificate) {
        return new WP_REST_Response('Certificate not found', 404);
    }

    // Проверяем статус платежа в YooKassa
    $client = new \YooKassa\Client();
    $client->setAuth('324277', 'live_3zAOaN0sUy0tcINqwat_kV2LXGX25A_3EIwesJaZ0Yg');

    try {
        $payment_info = $client->getPaymentInfo($certificate->payment_id);

        if ($payment_info->getStatus() == 'succeeded' && $certificate->status != 'paid') {
            // Обновляем статус на paid
            $wpdb->update(
                $table_name,
                array('status' => 'paid'),
                array('certificate_id' => $certificate->certificate_id)
            );

            // Если нет запланированной даты - отправляем SMS сразу
            if (empty($certificate->scheduled_at) && $certificate->sms_status == 'pending') {
                gift_you_send_sms_now($certificate->certificate_id);
            }

            return new WP_REST_Response(
                array(
                    'status' => 'succeeded',
                    'certificate_id' => $certificate->certificate_id,
                    'short_code' => $certificate->short_code,
                ),
                200
            );
        }

        return new WP_REST_Response(
            array('status' => $payment_info->getStatus()),
            200
        );

    } catch (Exception $e) {
        return new WP_REST_Response('Error: ' . $e->getMessage(), 500);
    }
}

add_action('rest_api_init', function () {
    register_rest_route('gift-you/v1', '/check_payment/', array(
        'methods' => 'POST',
        'callback' => 'gift_you_check_payment',
        'permission_callback' => '__return_true'
    ));
});

/**
 * Отправка SMS немедленно
 */
function gift_you_send_sms_now($certificate_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'gift_certificates';

    $certificate = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE certificate_id = %s",
        $certificate_id
    ));

    if (!$certificate || empty($certificate->recipient_phone)) {
        return false;
    }

    $sms_sender = new Gift_SMS_Sender();
    $short_url = 'sk-clinic.ru/g/' . $certificate->short_code;

    // Полный текст SMS (~115 символов = 2 SMS кириллицей)
    $message = "Вам отправили подарок! Подарочный сертификат от клиники «Секреты красоты». Доступен по ссылке: {$short_url}";

    $result = $sms_sender->send($certificate->recipient_phone, $message);

    if ($result['success']) {
        $wpdb->update(
            $table_name,
            array(
                'sms_status' => 'sent',
                'sms_sent_at' => current_time('mysql'),
                'sms_id' => $result['sms_id']
            ),
            array('certificate_id' => $certificate_id)
        );
        return true;
    } else {
        $wpdb->update(
            $table_name,
            array('sms_status' => 'failed'),
            array('certificate_id' => $certificate_id)
        );
        return false;
    }
}

/**
 * REST API: Отправить SMS сейчас (вручную из админки)
 */
function gift_you_send_sms_now_api(WP_REST_Request $request) {
    $data = $request->get_json_params();

    if (!isset($data['certificate_id'])) {
        return new WP_REST_Response('certificate_id required', 400);
    }

    $result = gift_you_send_sms_now($data['certificate_id']);

    if ($result) {
        return new WP_REST_Response(array('status' => 'sent'), 200);
    } else {
        return new WP_REST_Response(array('status' => 'failed'), 500);
    }
}

add_action('rest_api_init', function () {
    register_rest_route('gift-you/v1', '/send_sms/', array(
        'methods' => 'POST',
        'callback' => 'gift_you_send_sms_now_api',
        'permission_callback' => function() {
            return current_user_can('manage_options');
        }
    ));
});

/**
 * WP-Cron: Обработка запланированных SMS
 */
function gift_you_process_scheduled_sms() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'gift_certificates';

    // Логируем запуск
    error_log('Gift SMS Cron: Starting at ' . current_time('mysql'));

    // Находим сертификаты для отправки
    $certificates = $wpdb->get_results(
        "SELECT * FROM $table_name
         WHERE certificate_type = 'new'
         AND status = 'paid'
         AND sms_status = 'pending'
         AND scheduled_at IS NOT NULL
         AND scheduled_at <= NOW()"
    );

    error_log('Gift SMS Cron: Found ' . count($certificates) . ' certificates to send');

    foreach ($certificates as $cert) {
        error_log('Gift SMS Cron: Sending SMS for certificate ' . $cert->certificate_id);
        $result = gift_you_send_sms_now($cert->certificate_id);
        error_log('Gift SMS Cron: Result - ' . ($result ? 'success' : 'failed'));
    }
}

// Регистрация cron-события
function gift_you_schedule_cron() {
    if (!wp_next_scheduled('gift_you_cron_send_sms')) {
        wp_schedule_event(time(), 'every_minute', 'gift_you_cron_send_sms');
    }
}
add_action('wp', 'gift_you_schedule_cron');

// Добавляем интервал "каждую минуту"
function gift_you_cron_intervals($schedules) {
    $schedules['every_minute'] = array(
        'interval' => 60,
        'display' => 'Every Minute'
    );
    return $schedules;
}
add_filter('cron_schedules', 'gift_you_cron_intervals');

// Привязка функции к cron-событию
add_action('gift_you_cron_send_sms', 'gift_you_process_scheduled_sms');

/**
 * Уведомление отправителя о доставке (SMS + Email)
 */
function gift_you_notify_sender($certificate_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'gift_certificates';

    $certificate = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE certificate_id = %s",
        $certificate_id
    ));

    if (!$certificate || $certificate->sender_notified) {
        return false;
    }

    $amount = number_format($certificate->certificate_amount, 0, '', ' ');
    $notified = false;

    // 1. Отправляем SMS отправителю
    if (!empty($certificate->sender_phone)) {
        $sms_sender = new Gift_SMS_Sender();
        $sms_message = "Ваш подарок доставлен! {$certificate->recipient_name} получил сертификат на {$amount} руб. Клиника «Секреты красоты»";
        $sms_result = $sms_sender->send($certificate->sender_phone, $sms_message);

        if ($sms_result['success']) {
            $notified = true;
            error_log("Gift SMS: Sender notified via SMS - {$certificate->sender_phone}");
        }
    }

    // 2. Отправляем Email отправителю
    $subject = 'Ваш подарочный сертификат доставлен!';
    $email_message = "Здравствуйте, {$certificate->sender_name}!\n\n";
    $email_message .= "Ваш подарочный сертификат на сумму {$amount} руб. ";
    $email_message .= "был успешно доставлен получателю ({$certificate->recipient_name}).\n\n";
    $email_message .= "Спасибо, что выбрали клинику «Секреты красоты»!\n\n";
    $email_message .= "С уважением,\nКлиника «Секреты красоты»";

    $email_sent = wp_mail($certificate->sender_email, $subject, $email_message);

    if ($email_sent) {
        $notified = true;
    }

    // Отмечаем что уведомление отправлено
    if ($notified) {
        $wpdb->update(
            $table_name,
            array('sender_notified' => 1),
            array('certificate_id' => $certificate_id)
        );
    }

    return $notified;
}

/**
 * Проверка статуса доставки SMS и уведомление отправителя
 */
function gift_you_check_sms_delivery() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'gift_certificates';

    // Находим отправленные SMS без уведомления
    $certificates = $wpdb->get_results(
        "SELECT * FROM $table_name
         WHERE certificate_type = 'new'
         AND sms_status = 'sent'
         AND sender_notified = 0
         AND sms_id IS NOT NULL"
    );

    $sms_sender = new Gift_SMS_Sender();

    foreach ($certificates as $cert) {
        $status = $sms_sender->check_status($cert->sms_id);

        if ($status == 'delivered') {
            $wpdb->update(
                $table_name,
                array('sms_status' => 'delivered'),
                array('certificate_id' => $cert->certificate_id)
            );

            // Уведомляем отправителя
            gift_you_notify_sender($cert->certificate_id);
        }
    }
}
add_action('gift_you_cron_send_sms', 'gift_you_check_sms_delivery');

/**
 * ============================================================
 * ШОРТКОД [gift_you_form] - Форма оформления подарочного сертификата
 * ============================================================
 *
 * Использование в Elementor: просто добавьте шорткод [gift_you_form]
 * Для тестового режима: добавьте ?test=1 к URL страницы
 */
function gift_you_form_shortcode($atts) {
    // Атрибуты шорткода (можно расширить при необходимости)
    $atts = shortcode_atts(array(
        'cert_image' => 'https://sk-clinic.ru/wp-content/uploads/2025/12/sert.png',
        'payments_image' => 'https://sk-clinic.ru/wp-content/uploads/2025/12/oplata.png',
    ), $atts, 'gift_you_form');

    // Начинаем буферизацию вывода
    ob_start();

    // Подключаем шаблон формы
    include plugin_dir_path(__FILE__) . 'templates/gift-you-form.php';

    // Возвращаем содержимое буфера
    return ob_get_clean();
}
add_shortcode('gift_you_form', 'gift_you_form_shortcode');


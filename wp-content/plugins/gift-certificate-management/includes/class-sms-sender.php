<?php
/**
 * Класс для отправки SMS через SMS.RU
 * Version: 1.0.1
 * Документация API: https://sms.ru/api
 */

class Gift_SMS_Sender {

    private $api_id;
    private $api_url = 'https://sms.ru/sms/send';
    private $status_url = 'https://sms.ru/sms/status';

    public function __construct() {
        // API ключ SMS.RU - нужно получить в личном кабинете sms.ru
        // TODO: Вынести в настройки плагина
        $this->api_id = get_option('gift_sms_api_key', '');
    }

    /**
     * Отправка SMS
     *
     * @param string $phone Номер телефона получателя
     * @param string $message Текст сообщения
     * @return array ['success' => bool, 'sms_id' => string|null, 'error' => string|null]
     */
    public function send($phone, $message) {
        // Нормализуем номер телефона
        $phone = $this->normalize_phone($phone);

        if (empty($this->api_id)) {
            return array(
                'success' => false,
                'sms_id' => null,
                'error' => 'API key not configured'
            );
        }

        $params = array(
            'api_id' => $this->api_id,
            'to' => $phone,
            'msg' => $message,
            'json' => 1
        );

        $response = wp_remote_post($this->api_url, array(
            'body' => $params,
            'timeout' => 30
        ));

        if (is_wp_error($response)) {
            return array(
                'success' => false,
                'sms_id' => null,
                'error' => $response->get_error_message()
            );
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (isset($body['status']) && $body['status'] == 'OK') {
            // Получаем ID SMS из ответа
            $sms_id = null;
            if (isset($body['sms']) && is_array($body['sms'])) {
                $sms_data = reset($body['sms']);
                if (isset($sms_data['sms_id'])) {
                    $sms_id = $sms_data['sms_id'];
                }
            }

            return array(
                'success' => true,
                'sms_id' => $sms_id,
                'error' => null
            );
        } else {
            $error = isset($body['status_text']) ? $body['status_text'] : 'Unknown error';
            return array(
                'success' => false,
                'sms_id' => null,
                'error' => $error
            );
        }
    }

    /**
     * Проверка статуса доставки SMS
     *
     * @param string $sms_id ID SMS сообщения
     * @return string Статус: pending, delivered, failed, unknown
     */
    public function check_status($sms_id) {
        if (empty($this->api_id) || empty($sms_id)) {
            return 'unknown';
        }

        $params = array(
            'api_id' => $this->api_id,
            'sms_id' => $sms_id,
            'json' => 1
        );

        $url = $this->status_url . '?' . http_build_query($params);

        $response = wp_remote_get($url, array(
            'timeout' => 30
        ));

        if (is_wp_error($response)) {
            return 'unknown';
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (isset($body['status']) && $body['status'] == 'OK') {
            if (isset($body['sms']) && is_array($body['sms'])) {
                $sms_data = reset($body['sms']);
                $status_code = isset($sms_data['status_code']) ? $sms_data['status_code'] : -1;

                // Статусы SMS.RU:
                // -1 - Не найдено
                // 100 - Сообщение находится в очереди
                // 101 - Сообщение передается оператору
                // 102 - Сообщение отправлено (в пути)
                // 103 - Сообщение доставлено
                // 104 - Не может быть доставлено: время жизни истекло
                // 105 - Не может быть доставлено: удалено оператором
                // 106 - Не может быть доставлено: сбой в телефоне
                // 107 - Не может быть доставлено: неизвестная причина
                // 108 - Не может быть доставлено: отклонено

                if ($status_code == 103) {
                    return 'delivered';
                } elseif (in_array($status_code, array(104, 105, 106, 107, 108))) {
                    return 'failed';
                } elseif (in_array($status_code, array(100, 101, 102))) {
                    return 'pending';
                }
            }
        }

        return 'unknown';
    }

    /**
     * Нормализация номера телефона
     *
     * @param string $phone Номер телефона
     * @return string Нормализованный номер (только цифры, начинается с 7)
     */
    private function normalize_phone($phone) {
        // Убираем всё кроме цифр
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Если начинается с 8, заменяем на 7
        if (strlen($phone) == 11 && $phone[0] == '8') {
            $phone = '7' . substr($phone, 1);
        }

        // Если 10 цифр (без кода страны), добавляем 7
        if (strlen($phone) == 10) {
            $phone = '7' . $phone;
        }

        return $phone;
    }

    /**
     * Получение баланса аккаунта SMS.RU
     *
     * @return float|false Баланс или false при ошибке
     */
    public function get_balance() {
        if (empty($this->api_id)) {
            return false;
        }

        $url = 'https://sms.ru/my/balance?api_id=' . $this->api_id . '&json=1';

        $response = wp_remote_get($url, array(
            'timeout' => 30
        ));

        if (is_wp_error($response)) {
            return false;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (isset($body['status']) && $body['status'] == 'OK' && isset($body['balance'])) {
            return floatval($body['balance']);
        }

        return false;
    }
}

<?php
/**
 * Скрипт для системного cron (ISPmanager)
 * Отправляет запланированные SMS
 *
 * Команда для cron (каждую минуту):
 * wget -q -O - "https://sk-clinic.ru/wp-content/plugins/gift-certificate-management/cron-sms.php?key=gift_sms_cron_2024_sk" > /dev/null 2>&1
 */

// Секретный ключ для защиты
define('CRON_SECRET_KEY', 'gift_sms_cron_2024_sk');

// Файл лога (в папке плагина)
define('CRON_LOG_FILE', __DIR__ . '/cron-log.txt');

// Функция логирования
function cron_log($message) {
    $time = date('Y-m-d H:i:s');
    $log = "[{$time}] {$message}\n";
    file_put_contents(CRON_LOG_FILE, $log, FILE_APPEND);
    echo $log;
}

// Проверка ключа при вызове через HTTP
if (php_sapi_name() !== 'cli') {
    if (!isset($_GET['key']) || $_GET['key'] !== CRON_SECRET_KEY) {
        http_response_code(403);
        die('Access denied');
    }
}

cron_log('=== CRON START ===');
cron_log('Server time: ' . date('Y-m-d H:i:s'));
cron_log('Timezone: ' . date_default_timezone_get());

// Подключаем WordPress
require_once dirname(dirname(dirname(dirname(__DIR__)))) . '/wp-load.php';

cron_log('WordPress time: ' . current_time('mysql'));
cron_log('WordPress timezone: ' . wp_timezone_string());

// Проверяем есть ли сертификаты для отправки
global $wpdb;
$table_name = $wpdb->prefix . 'gift_certificates';

$pending = $wpdb->get_results(
    "SELECT certificate_id, short_code, scheduled_at, sms_status, status
     FROM $table_name
     WHERE certificate_type = 'new'
     AND sms_status = 'pending'
     ORDER BY creation_time DESC
     LIMIT 10"
);

cron_log('Pending SMS certificates: ' . count($pending));

foreach ($pending as $cert) {
    $scheduled = $cert->scheduled_at ? $cert->scheduled_at : 'NULL';
    $now = current_time('mysql');
    $should_send = ($cert->scheduled_at && $cert->scheduled_at <= $now) ? 'YES' : 'NO';
    cron_log("  - {$cert->short_code}: scheduled={$scheduled}, status={$cert->status}, should_send={$should_send}");
}

// Запускаем обработку
if (function_exists('gift_you_process_scheduled_sms')) {
    cron_log('Running gift_you_process_scheduled_sms()...');
    gift_you_process_scheduled_sms();
    cron_log('Done.');
} else {
    cron_log('ERROR: gift_you_process_scheduled_sms function not found!');
}

// Проверяем статусы доставки
if (function_exists('gift_you_check_sms_delivery')) {
    cron_log('Running gift_you_check_sms_delivery()...');
    gift_you_check_sms_delivery();
    cron_log('Done.');
}

cron_log('=== CRON END ===');
cron_log('');

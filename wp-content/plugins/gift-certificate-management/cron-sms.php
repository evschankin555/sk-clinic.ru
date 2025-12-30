<?php
/**
 * Скрипт для системного cron (ISPmanager)
 * Отправляет запланированные SMS
 *
 * Команда для cron (каждую минуту):
 * /usr/bin/php /путь/к/сайту/wp-content/plugins/gift-certificate-management/cron-sms.php
 *
 * Или через wget:
 * wget -q -O - "https://sk-clinic.ru/wp-content/plugins/gift-certificate-management/cron-sms.php?key=SECRET_KEY" > /dev/null 2>&1
 */

// Секретный ключ для защиты (замени на свой)
define('CRON_SECRET_KEY', 'gift_sms_cron_2024_sk');

// Проверка ключа при вызове через HTTP
if (php_sapi_name() !== 'cli') {
    if (!isset($_GET['key']) || $_GET['key'] !== CRON_SECRET_KEY) {
        http_response_code(403);
        die('Access denied');
    }
}

// Подключаем WordPress
require_once dirname(dirname(dirname(dirname(__DIR__)))) . '/wp-load.php';

// Запускаем обработку запланированных SMS
if (function_exists('gift_you_process_scheduled_sms')) {
    echo "Starting scheduled SMS processing...\n";
    gift_you_process_scheduled_sms();
    echo "Done.\n";
} else {
    echo "Error: gift_you_process_scheduled_sms function not found\n";
}

// Также проверяем статусы доставки
if (function_exists('gift_you_check_sms_delivery')) {
    echo "Checking SMS delivery status...\n";
    gift_you_check_sms_delivery();
    echo "Done.\n";
}

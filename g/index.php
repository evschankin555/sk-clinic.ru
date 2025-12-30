<?php
/**
 * Короткие ссылки для SMS: /g/{short_code}/
 * Редирект на /gift-you/{short_code}/
 */

// Получаем short_code из URL
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
$path = trim($path, '/');
$parts = explode('/', $path);

// URL: /g/abc123 -> parts = ['g', 'abc123']
$short_code = isset($parts[1]) ? $parts[1] : '';

if (empty($short_code)) {
    header('Location: https://sk-clinic.ru/', true, 302);
    exit;
}

// Редирект на полную страницу сертификата
header('Location: https://sk-clinic.ru/gift-you/' . $short_code . '/', true, 301);
exit;

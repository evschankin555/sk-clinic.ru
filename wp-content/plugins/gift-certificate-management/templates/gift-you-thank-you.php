<?php
/**
 * Шаблон страницы благодарности для покупателя сертификата
 * Показывается после успешной оплаты
 */

if (!defined('ABSPATH')) {
    exit;
}

// Форматируем данные
$amount = number_format($certificate->certificate_amount, 0, '', ' ');
$recipient_name = esc_html($certificate->recipient_name);
$sender_name = esc_html($certificate->sender_name);
$short_code = $certificate->short_code;
$certificate_url = home_url('/gift-you/' . $short_code . '/');

// Определяем время отправки
$is_scheduled = !empty($certificate->scheduled_at) && strtotime($certificate->scheduled_at) > time();
if ($is_scheduled) {
    $scheduled_date = date('d.m.Y', strtotime($certificate->scheduled_at));
    $scheduled_time = date('H:i', strtotime($certificate->scheduled_at));
    $delivery_text = "Сертификат будет отправлен";
    $delivery_time = "{$scheduled_date} в {$scheduled_time}";
} else {
    $delivery_text = "Сертификат уже отправлен";
    $delivery_time = "";
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Спасибо за покупку! — Клиника «Секреты красоты»</title>

    <meta name="robots" content="noindex, nofollow">

    <style>
        @font-face {
            font-family: 'Tenor Sans';
            src: url('https://sk-clinic.ru/wp-content/uploads/2025/08/Tenor-Sans.ttf') format('truetype');
            font-weight: 400;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body, html {
            width: 100%;
            min-height: 100vh;
            font-family: 'Tenor Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #1a1a1a 0%, #0d0d0d 100%);
            color: #fff;
            overflow-x: hidden;
        }

        .page-wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            position: relative;
        }

        /* Декоративный фон */
        .bg-decoration {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            overflow: hidden;
            z-index: 0;
        }

        .bg-circle {
            position: absolute;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(144, 163, 132, 0.15) 0%, transparent 70%);
        }

        .bg-circle-1 {
            width: 600px;
            height: 600px;
            top: -200px;
            right: -200px;
            animation: float 20s ease-in-out infinite;
        }

        .bg-circle-2 {
            width: 400px;
            height: 400px;
            bottom: -100px;
            left: -100px;
            animation: float 15s ease-in-out infinite reverse;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(30px, 30px); }
        }

        /* Основной контент */
        .content {
            position: relative;
            z-index: 1;
            max-width: 600px;
            width: 100%;
            text-align: center;
        }

        /* Логотип */
        .logo {
            width: 120px;
            height: 120px;
            margin: 0 auto 40px;
            opacity: 0;
            animation: fadeInUp 0.8s ease-out 0.2s forwards;
        }

        .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        /* Иконка успеха */
        .success-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 30px;
            background: linear-gradient(135deg, #90A384 0%, #7a9070 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transform: scale(0.5);
            animation: popIn 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) 0.4s forwards;
        }

        .success-icon svg {
            width: 40px;
            height: 40px;
            stroke: #fff;
            stroke-width: 3;
            fill: none;
        }

        .success-icon .checkmark {
            stroke-dasharray: 50;
            stroke-dashoffset: 50;
            animation: drawCheck 0.5s ease-out 0.9s forwards;
        }

        @keyframes popIn {
            to { opacity: 1; transform: scale(1); }
        }

        @keyframes drawCheck {
            to { stroke-dashoffset: 0; }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Заголовок */
        .title {
            font-size: clamp(28px, 5vw, 42px);
            font-weight: 400;
            margin-bottom: 15px;
            color: #90A384;
            opacity: 0;
            animation: fadeInUp 0.8s ease-out 0.6s forwards;
        }

        .subtitle {
            font-size: clamp(16px, 3vw, 20px);
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 50px;
            opacity: 0;
            animation: fadeInUp 0.8s ease-out 0.8s forwards;
        }

        /* Карточка информации */
        .info-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(144, 163, 132, 0.2);
            border-radius: 24px;
            padding: 40px 30px;
            margin-bottom: 40px;
            backdrop-filter: blur(10px);
            opacity: 0;
            animation: fadeInUp 0.8s ease-out 1s forwards;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.5);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .info-value {
            font-size: 18px;
            color: #fff;
            text-align: right;
        }

        .info-value.amount {
            font-size: 28px;
            color: #90A384;
            font-style: italic;
        }

        .info-value.recipient {
            color: #90A384;
        }

        /* Блок доставки */
        .delivery-block {
            background: linear-gradient(135deg, rgba(144, 163, 132, 0.15) 0%, rgba(144, 163, 132, 0.05) 100%);
            border-radius: 16px;
            padding: 25px;
            margin-top: 25px;
            text-align: center;
        }

        .delivery-icon {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .delivery-text {
            font-size: 16px;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 5px;
        }

        .delivery-time {
            font-size: 20px;
            color: #90A384;
            font-weight: 400;
        }

        /* Уведомление */
        .notification-info {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-top: 20px;
            padding: 15px;
            background: rgba(144, 163, 132, 0.1);
            border-radius: 12px;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.7);
        }

        .notification-info svg {
            width: 20px;
            height: 20px;
            stroke: #90A384;
            flex-shrink: 0;
        }

        /* Кнопки */
        .buttons {
            display: flex;
            flex-direction: column;
            gap: 15px;
            opacity: 0;
            animation: fadeInUp 0.8s ease-out 1.2s forwards;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 18px 35px;
            border-radius: 50px;
            font-family: inherit;
            font-size: 16px;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #90A384 0%, #7a9070 100%);
            color: #fff;
            box-shadow: 0 10px 30px rgba(144, 163, 132, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(144, 163, 132, 0.4);
        }

        .btn-secondary {
            background: transparent;
            color: rgba(255, 255, 255, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.05);
            color: #fff;
            border-color: rgba(255, 255, 255, 0.3);
        }

        .btn svg {
            width: 20px;
            height: 20px;
        }

        /* Ссылка на сертификат */
        .certificate-link {
            margin-top: 40px;
            opacity: 0;
            animation: fadeInUp 0.8s ease-out 1.4s forwards;
        }

        .certificate-link a {
            color: rgba(255, 255, 255, 0.5);
            font-size: 14px;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .certificate-link a:hover {
            color: #90A384;
        }

        /* Футер */
        .footer {
            margin-top: 60px;
            text-align: center;
            opacity: 0;
            animation: fadeInUp 0.8s ease-out 1.6s forwards;
        }

        .footer-logo {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.3);
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        /* Мобильная адаптация */
        @media (max-width: 480px) {
            .page-wrapper {
                padding: 30px 15px;
            }

            .info-card {
                padding: 25px 20px;
            }

            .info-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }

            .info-value {
                text-align: left;
            }

            .buttons {
                width: 100%;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <!-- Декоративный фон -->
        <div class="bg-decoration">
            <div class="bg-circle bg-circle-1"></div>
            <div class="bg-circle bg-circle-2"></div>
        </div>

        <div class="content">
            <!-- Логотип -->
            <div class="logo">
                <img src="https://sk-clinic.ru/wp-content/uploads/logo_sert.svg" alt="Секреты красоты">
            </div>

            <!-- Иконка успеха -->
            <div class="success-icon">
                <svg viewBox="0 0 24 24">
                    <polyline class="checkmark" points="4 12 9 17 20 6"></polyline>
                </svg>
            </div>

            <!-- Заголовок -->
            <h1 class="title">Спасибо за покупку!</h1>
            <p class="subtitle">Подарочный сертификат успешно создан</p>

            <!-- Информационная карточка -->
            <div class="info-card">
                <div class="info-row">
                    <span class="info-label">Сумма сертификата</span>
                    <span class="info-value amount"><?php echo $amount; ?> ₽</span>
                </div>

                <div class="info-row">
                    <span class="info-label">Получатель</span>
                    <span class="info-value recipient"><?php echo $recipient_name; ?></span>
                </div>

                <div class="info-row">
                    <span class="info-label">От кого</span>
                    <span class="info-value"><?php echo $sender_name; ?></span>
                </div>

                <!-- Блок доставки -->
                <div class="delivery-block">
                    <div class="delivery-icon">📨</div>
                    <div class="delivery-text"><?php echo $delivery_text; ?></div>
                    <?php if ($delivery_time): ?>
                        <div class="delivery-time"><?php echo $delivery_time; ?></div>
                    <?php endif; ?>
                </div>

                <!-- Уведомление -->
                <div class="notification-info">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                    <span>Вы получите SMS-уведомление, когда сертификат будет доставлен</span>
                </div>
            </div>

            <!-- Кнопки -->
            <div class="buttons">
                <a href="https://sk-clinic.ru/" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                    На сайт клиники
                </a>

                <a href="https://sk-clinic.ru/gift-new/" class="btn btn-secondary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 12v10H4V12"></path>
                        <path d="M2 7h20v5H2z"></path>
                        <path d="M12 22V7"></path>
                        <path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"></path>
                        <path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"></path>
                    </svg>
                    Создать ещё один сертификат
                </a>
            </div>

            <!-- Ссылка на сертификат -->
            <div class="certificate-link">
                <a href="<?php echo esc_url($certificate_url); ?>">Посмотреть сертификат →</a>
            </div>
        </div>

        <!-- Футер -->
        <div class="footer">
            <div class="footer-logo">Клиника «Секреты красоты»</div>
        </div>
    </div>
</body>
</html>

<?php
/**
 * Страница просмотра подарочного сертификата
 * URL: /gift-you/{short_code}/
 *
 * Автономная страница без WordPress шаблона
 * Загружает данные из БД и показывает красивую анимацию
 */

// Подключаем WordPress для доступа к БД
require_once dirname(__DIR__) . '/wp-load.php';

// Получаем short_code из URL
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
$path = trim($path, '/');
$parts = explode('/', $path);

// URL: /gift-you/abc123/ -> parts = ['gift-you', 'abc123']
$short_code = isset($parts[1]) ? sanitize_text_field($parts[1]) : '';

if (empty($short_code)) {
    wp_redirect(home_url('/'));
    exit;
}

// Загружаем сертификат из БД
global $wpdb;
$table_name = $wpdb->prefix . 'gift_certificates';

$certificate = $wpdb->get_row($wpdb->prepare(
    "SELECT * FROM $table_name WHERE short_code = %s",
    $short_code
));

// Если не найден по short_code, пробуем по certificate_id (для обратной совместимости)
if (!$certificate) {
    $certificate = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE certificate_id = %s",
        $short_code
    ));
}

if (!$certificate) {
    // Сертификат не найден
    http_response_code(404);
    ?>
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Сертификат не найден</title>
        <style>
            body {
                font-family: 'Tenor Sans', sans-serif;
                background: #90A384;
                color: #fff;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                margin: 0;
                text-align: center;
            }
            .error-box {
                background: rgba(0,0,0,0.2);
                padding: 40px 60px;
                border-radius: 20px;
            }
            h1 { font-size: 28px; margin-bottom: 10px; }
            p { font-size: 16px; opacity: 0.8; }
            a { color: #fff; }
        </style>
    </head>
    <body>
        <div class="error-box">
            <h1>Сертификат не найден</h1>
            <p>Возможно, ссылка устарела или содержит ошибку.</p>
            <p><a href="https://sk-clinic.ru/">Перейти на сайт клиники</a></p>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Форматируем данные - используем short_code как номер
$cert_number = 'GIFT ' . strtoupper($certificate->short_code ?: $short_code);
$amount = number_format($certificate->certificate_amount, 0, '', ' ');
$recipient_name = esc_html($certificate->recipient_name ?: 'Дорогой друг');
$message = esc_html($certificate->recipient_message ?: '');
$sender_name = esc_html($certificate->sender_name ?: '');
$expiration_date = date('d.m.Y', strtotime($certificate->expiration_date));

// Проверяем статус оплаты
$is_paid = ($certificate->status === 'paid');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Подарочный сертификат в клинику «Секреты красоты»</title>

    <meta name="description" content="Вам отправили подарок! Подарочный сертификат на услуги клиники «Секреты красоты»">
    <meta property="og:title" content="Подарочный сертификат в клинику «Секреты красоты»">
    <meta property="og:description" content="Вам отправили подарок! Подарочный сертификат на <?php echo $amount; ?> рублей">
    <meta property="og:image" content="https://sk-clinic.ru/wp-content/uploads/2025/12/fon_sert_gift.png">
    <meta property="og:site_name" content="Клиника «Секреты красоты»">

    <style>
        @font-face {
            font-family: 'Tenor Sans';
            src: url('https://sk-clinic.ru/wp-content/uploads/2025/08/Tenor-Sans.ttf') format('truetype');
            font-weight: 400;
        }

        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow-x: hidden;
            overflow-y: auto;
            font-family: 'Tenor Sans', sans-serif;
            background-color: #000;
            color: #90A384;
        }

        #preloader {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100vh;
            background-color: #90A384;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            transition: opacity 1.5s ease-in-out, visibility 1.5s;
        }

        .preloader-logo {
            width: 250px;
            height: 250px;
            background: url('https://sk-clinic.ru/wp-content/uploads/logo_sert.svg') no-repeat center/contain;
            opacity: 0;
            transform: scale(0.5);
            animation: logoIntro 2s forwards ease-out;
        }

        @keyframes logoIntro {
            to { opacity: 1; transform: scale(1); }
        }

        #main-screen {
            position: relative;
            width: 100%;
            min-height: 100vh;
            opacity: 0;
            transition: opacity 2s ease-in-out;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .bg-image {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: url('https://sk-clinic.ru/wp-content/uploads/2025/12/fon-sert.png') no-repeat center/cover;
            filter: blur(5px);
            transform: scale(1.05);
            z-index: 1;
        }

        .center-anchor {
            position: absolute;
            top: 48%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 10;
            opacity: 0;
            transition: opacity 1.5s ease-out;
        }

        .cert-part {
            position: absolute;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            left: 50%;
            transform: translate(-50%, -50%);
            transition: transform 3s cubic-bezier(0.4, 0, 0.2, 1);
            will-change: transform;
        }

        .sert-1 {
            width: 65vmin;
            height: calc(65vmin * (410 / 600));
            background-image: url('https://sk-clinic.ru/wp-content/uploads/2025/12/sert_1.png');
            z-index: 5;
        }

        .sert-2 {
            width: calc(65vmin * (550 / 600));
            height: calc(65vmin * (370 / 600));
            background-image: url('https://sk-clinic.ru/wp-content/uploads/2025/12/sert_2.png');
            z-index: 4;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 0 5%;
        }

        .footer-links {
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-top: 25px;
            z-index: 20;
            opacity: 0;
            transition: opacity 1.5s ease-in-out;
        }

        .footer-links a {
            color: #fff;
            text-decoration: none;
            font-size: 12px;
            border-bottom: 1px solid;
            white-space: nowrap;
        }

        .sert-no {
            position: absolute;
            top: 9%; left: 5%;
            font-size: clamp(13px, 1.7vmin, 20px);
            letter-spacing: 2px;
        }

        .cert-content-wrapper {
            width: 95%;
            opacity: 0;
            transition: opacity 1.5s ease-in-out 1s;
        }

        .amount-val {
            display: block;
            font-size: clamp(24px, 7vmin, 80px);
            line-height: 0.85;
            font-style: italic;
        }

        .amount-cur {
            display: block;
            font-size: clamp(13px, 1.8vmin, 22px);
        }

        .recipient-name {
            font-size: clamp(18px, 3.5vmin, 48px);
            margin: 1.5vh 0 0.8vh 0;
            display: block;
        }

        .congrats-text {
            font-size: clamp(13px, 1.8vmin, 22px);
            line-height: 1.2;
            color: #647659;
            margin-top: 0px;
        }

        .sender-name {
            position: absolute;
            bottom: 8%; left: 8%;
            font-size: clamp(14px, 2.2vmin, 28px);
            opacity: 0;
            transition: opacity 1.5s ease-in-out 1.5s;
        }

        /* Анимации */
        .show-content { opacity: 1; }
        .sert-1.split-up { transform: translate(-50%, -100%); }
        .sert-2.split-down { transform: translate(-50%, 0%); }
        .reveal-text { opacity: 1; }
        .show-links { opacity: 1; }

        /* Статус не оплачен */
        .payment-status {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 100;
            background: rgba(255, 100, 100, 0.9);
            color: #fff;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            display: none;
        }

        .payment-status.show {
            display: block;
        }

        /* Скролл при малой высоте */
        @media (max-height: 550px) {
            #main-screen { padding: 30px 0; display: block; }

            .center-anchor {
                position: relative; top: auto; left: auto; transform: none;
                margin: 0 auto; width: 500px; height: auto;
                display: flex; flex-direction: column; align-items: center;
            }

            .cert-part {
                position: relative; left: auto; top: auto; transform: none !important;
                width: 500px;
            }

            .sert-1 { height: 341px; }
            .sert-2 { height: 316px; }
            .footer-links { padding-bottom: 20px; }
            .sender-name { left: 10%; }
        }

        @media (max-width: 768px) and (min-height: 551px) {
            .sert-1 { width: 90vw; height: calc(90vw * (410 / 600)); }
            .sert-2 { width: calc(90vw * (550 / 600)); height: calc(90vw * (370 / 600)); }
        }
    </style>
</head>
<body>

    <?php if (!$is_paid): ?>
    <div class="payment-status show" id="paymentStatus">
        Ожидание подтверждения оплаты...
    </div>
    <?php endif; ?>

    <div id="preloader">
        <div class="preloader-logo"></div>
    </div>

    <div id="main-screen">
        <div class="bg-image"></div>

        <div class="center-anchor" id="certAnchor">
            <div class="cert-part sert-1" id="sert1">
                <div class="sert-no"><?php echo $cert_number; ?></div>
            </div>

            <div class="cert-part sert-2" id="sert2">
                <div class="cert-content-wrapper" id="textMain">
                    <div class="amount-block">
                        <span class="amount-val"><?php echo $amount; ?></span>
                        <span class="amount-cur">рублей</span>
                    </div>
                    <span class="recipient-name"><?php echo $recipient_name; ?></span>
                    <?php if ($message): ?>
                    <p class="congrats-text"><?php echo $message; ?></p>
                    <?php endif; ?>
                </div>
                <?php if ($sender_name): ?>
                <div class="sender-name" id="textSender"><?php echo $sender_name; ?></div>
                <?php endif; ?>

                <div class="footer-links" id="footerLinks">
                    <a href="https://sk-clinic.ru/" target="_blank">На сайт клиники</a>
                    <a href="https://sk-clinic.ru/gift-about" target="_blank">Условия использования</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        var preloader = document.getElementById('preloader');
        var mainScreen = document.getElementById('main-screen');
        var certAnchor = document.getElementById('certAnchor');
        var sert1 = document.getElementById('sert1');
        var sert2 = document.getElementById('sert2');
        var textMain = document.getElementById('textMain');
        var textSender = document.getElementById('textSender');
        var footerLinks = document.getElementById('footerLinks');

        window.addEventListener('load', function() {
            setTimeout(function() {
                preloader.style.opacity = '0';
                setTimeout(function() {
                    preloader.style.visibility = 'hidden';
                }, 1500);
                mainScreen.style.opacity = '1';

                setTimeout(function() {
                    certAnchor.classList.add('show-content');

                    if (window.innerHeight > 550) {
                        setTimeout(function() {
                            sert1.classList.add('split-up');
                            sert2.classList.add('split-down');
                            textMain.classList.add('reveal-text');
                            if (textSender) textSender.classList.add('reveal-text');

                            setTimeout(function() {
                                footerLinks.classList.add('show-links');
                            }, 1000);
                        }, 2000);
                    } else {
                        textMain.classList.add('reveal-text');
                        if (textSender) textSender.classList.add('reveal-text');
                        footerLinks.style.opacity = "1";
                    }
                }, 2000);
            }, 2000);
        });

        <?php if (!$is_paid): ?>
        // Проверка статуса оплаты
        function checkPaymentStatus() {
            fetch('/wp-json/gift-you/v1/check_payment/', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    short_code: '<?php echo esc_js($short_code); ?>'
                })
            })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (data.status === 'succeeded' || data.status === 'paid') {
                    document.getElementById('paymentStatus').style.display = 'none';
                    location.reload();
                } else {
                    setTimeout(checkPaymentStatus, 3000);
                }
            })
            .catch(function() {
                setTimeout(checkPaymentStatus, 5000);
            });
        }

        setTimeout(checkPaymentStatus, 2000);
        <?php endif; ?>
    </script>
</body>
</html>

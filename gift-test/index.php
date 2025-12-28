<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Подарочный сертификат в клинику «Секреты красоты»</title>
    
    <meta name="description" content="Вам отправили подарок! Подарочный сертификат на услуги клиники «Секреты красоты»">
    <meta property="og:title" content="Подарочный сертификат в клинику «Секреты красоты»">
    <meta property="og:description" content="Вам отправили подарок! Подарочный сертификат на услуги клиники «Секреты красоты»">
    <meta property="og:image" content="https://sk-clinic.ru/wp-content/uploads/2025/12/fon_sert_gift.png">
  <meta property="og:site_name" content="Клиника «Секреты красоты»" />

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

        /* --- ССЫЛКИ ПОД СЕРТИФИКАТОМ --- */
        .footer-links {
            position: absolute;
            /* Помещаем сразу под нижней границей сертификата */
            top: 100%; 
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-top: 25px; /* Отступ от края сертификата */
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

        /* ТЕКСТ ВНУТРИ */
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
            font-family: 'Notosans (акценты)' !important;
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

        /* АНИМАЦИИ */
        .show-content { opacity: 1; }
        .sert-1.split-up { transform: translate(-50%, -100%); }
        .sert-2.split-down { transform: translate(-50%, 0%); }
        
        .reveal-text { opacity: 1; }
        .show-links { opacity: 1; }

       /* СКРОЛЛ ПРИ МАЛОЙ ВЫСОТЕ */

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
            .footer-links {
                padding-bottom: 20px;
            }
              .sender-name {
            left: 10%;
              }
        } 

        @media (max-width: 768px) and (min-height: 551px) {
            .sert-1 { width: 90vw; height: calc(90vw * (410 / 600)); }
            .sert-2 { width: calc(90vw * (550 / 600)); height: calc(90vw * (370 / 600)); }
           
        }
       
                
    </style>
</head>
<body>

    <div id="preloader">
        <div class="preloader-logo"></div>
    </div>

    <div id="main-screen">
        <div class="bg-image"></div>
        
        <div class="center-anchor" id="certAnchor">
            <div class="cert-part sert-1" id="sert1">
                <div class="sert-no">GIFT №00000000</div>
            </div>
            
            <div class="cert-part sert-2" id="sert2">
                <div class="cert-content-wrapper" id="textMain">
                    <div class="amount-block">
                        <span class="amount-val">10 000</span>
                        <span class="amount-cur">рублей</span>
                    </div>
                    <span class="recipient-name">Екатерина Иванова</span>
                    <p class="congrats-text">Поздравляю с праздником! Желаю здоровья, радости и уверенности в себе. Пусть рядом будут надёжные люди, а каждый день приносит удачу и хорошие эмоции.</p>
                </div>
                <div class="sender-name" id="textSender">Александр</div>

                <div class="footer-links" id="footerLinks">
                    <a href="https://sk-clinic.ru/" target="_blank">На сайт клиники</a>
                    <a href="https://sk-clinic.ru/gift-about" target="_blank">Условия использования</a>
                </div>
            </div>
        </div>
    </div>

 <script>

        const preloader = document.getElementById('preloader');

        const mainScreen = document.getElementById('main-screen');

        const certAnchor = document.getElementById('certAnchor');

        const sert1 = document.getElementById('sert1');

        const sert2 = document.getElementById('sert2');

        const textMain = document.getElementById('textMain');

        const textSender = document.getElementById('textSender');

        const footerLinks = document.getElementById('footerLinks');


        window.addEventListener('load', () => {

            setTimeout(() => {

                preloader.style.opacity = '0';

                setTimeout(() => preloader.style.visibility = 'hidden', 1500);

                mainScreen.style.opacity = '1';


                setTimeout(() => {

                    certAnchor.classList.add('show-content');

                    

                    if (window.innerHeight > 550) {

                        setTimeout(() => {

                            sert1.classList.add('split-up');

                            sert2.classList.add('split-down');

                            textMain.classList.add('reveal-text');

                            textSender.classList.add('reveal-text');

                            

                            // Ссылки появляются через время

                            setTimeout(() => {

                                footerLinks.classList.add('show-links');

                            }, 1000); 


                        }, 2000);

                    } else {

                        textMain.classList.add('reveal-text');

                        textSender.classList.add('reveal-text');

                        footerLinks.style.opacity = "1";

                    }

                }, 2000);

            }, 2000);

        });

    </script> 
</body>
</html>
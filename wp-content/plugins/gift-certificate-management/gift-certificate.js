jQuery(document).ready(function(jQuery) {
    if (window.location.pathname === '/gift/') {
        // При изменении исходного поля ввода
        jQuery('.form_gift .form_gift_input').on('input', function () {
            // Получаем новое значение
            var newValue = jQuery(this).val();

            // Устанавливаем это значение в поле оплаты
            jQuery('#popmake-5974 .yoomoney-payment-form input[name="sum"]').val(newValue)
            // Проверка на минимальное значение
            if(parseInt(newValue) < 10) {
                this.setCustomValidity("Минимальная сумма Подарочного сертификата 2000р");
            } else {
                this.setCustomValidity("");
            }
        });
        // Сразу же устанавливаем начальное значение при загрузке страницы
        var initialValue = jQuery('.form_gift .form_gift_input').val();
        jQuery('#popmake-5974 .yoomoney-payment-form input[name="sum"]').val(initialValue);
        jQuery('.form_gift .form_gift_input').get(0).setCustomValidity(initialValue < 10 ? "Минимальная сумма Подарочного сертификата 2000р" : "");

        jQuery('.form_gift_submit').click(function(e) {
            var formFilled = true;
            var giftInput = jQuery('.form_gift .form_gift_input').get(0);

            jQuery('form[name="gift"] input[type="text"], form[name="gift"] input[type="email"], form[name="gift"] input[type="phone"], form[name="gift"] textarea').each(function() {
                if (!jQuery(this).val()) {
                    formFilled = false;
                }
            });

            if (!formFilled || !jQuery('form[name="gift"] input[name="PERSONAL"]').prop('checked') || !giftInput.checkValidity()) {
                e.preventDefault();
                giftInput.reportValidity();
            } else {
                var data = {
                    'certificate_amount': jQuery('.gift_summa').val(),
                    'recipient_name': jQuery('.gift_name').val(),
                    'recipient_message': jQuery('.gift_messege').val(),
                    'sender_name': jQuery('.gift_myname').val(),
                    'sender_email': jQuery('.gift_sender_email').val(),
                    'sender_phone': jQuery('.gift_sender_phone').val()
                };

                // Отправляем данные на сервер для создания сертификата и платежа
                var certificateData = {
                    'sum': data.certificate_amount,
                    'certificate_amount': data.certificate_amount,
                    'recipient_name': data.recipient_name,
                    'recipient_message': data.recipient_message,
                    'sender_name': data.sender_name,
                    'sender_email': data.sender_email,
                    'sender_phone': data.sender_phone
                };

                jQuery.ajax({
                    url: '/wp-json/gift-certificate-management/v1/create_payment/',
                    type: 'POST',
                    data: JSON.stringify(certificateData),
                    contentType: 'application/json; charset=utf-8',
                    dataType: 'json',
                    success: function (result) {
                        if (result.error) {
                            // Обработка ошибки
                        } else {
                            console.log(result);
                            // Переход на страницу оплаты
                            window.location.href = result.payment_url;
                        }
                    },
                    error: function (xhr, status, error) {
                        // Обработка ошибки
                    }
                });
                e.preventDefault();  // Предотвратить отправку формы
            }
        });
    }

    function getCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for(var i=0;i < ca.length;i++) {
            var c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1,c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
        }
        return null;
    }
    function checkPaymentStatus(payment_id) {
        jQuery.ajax({
            url: '/wp-json/gift-certificate-management/v1/check_payment/',
            type: 'POST',
            data: JSON.stringify({payment_id: payment_id}),
            contentType: 'application/json; charset=utf-8',
            dataType: 'json',
            success: function (result) {
                console.log(result);
                if (result.error) {
                    // Обработка ошибки
                } else if (result.payment_status === 'paid') {
                    var certificate_id = result.certificate_id;

                    // Удаляем cookies
                    document.cookie = 'payment_id=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
                    document.cookie = 'certificate_id=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';

                    // Перенаправляем на страницу сертификата
                    window.location.href = '/gift-share/' + certificate_id;
                } else {
                    // Ждем 3 секунды и снова проверяем статус платежа
                    setTimeout(function() {
                        checkPaymentStatus(payment_id);
                    }, 3000);
                }
            },
            error: function (xhr, status, error) {
                // Обработка ошибки
            }
        });
    }
    function checkPaymentStatusComplite(payment_id) {
        jQuery.ajax({
            url: '/wp-json/gift-certificate-management/v1/check_payment/',
            type: 'POST',
            data: JSON.stringify({payment_id: payment_id}),
            contentType: 'application/json; charset=utf-8',
            dataType: 'json',
            success: function (result) {
                var statusText;
                if (result.status === 'succeeded') {
                    console.log(result);
                    statusText = '<h3>Подарочный сертификат успешно оплачен, вы будете перенаправлены на страницу сертификата через <div id="payment_countdown"></div> секунды</h3><br />';
                    statusText += ' <a href="/gift-share/' + result.certificate_id + '">Подарочный сертификат</a>';

                    window.location.href = '/gift-share/' + result.certificate_id;
                } else {
                    statusText = '<h3>Ваш платеж еще обрабатывается, текущий статус: ' + result.status + '. <br />Обновление через <div id="payment_countdown"></div> секунды</h3>';
                }
                jQuery('#payment_status').html(statusText);

                startCountdown();
            },
            error: function (xhr, status, error) {
                // Обработка ошибки
            }
        });
    }

    function startCountdown() {
        var counter = 3;
        var countdown = setInterval(function() {
            counter--;
            jQuery('#payment_countdown').text(counter);
            if (counter === 0) {
                clearInterval(countdown);
                location.reload();
            }
        }, 1000);
    }

    if (window.location.pathname.includes('/payment_complite/') ) {
        var payment_id = getCookie('payment_id');
        if (payment_id) {
            checkPaymentStatusComplite(payment_id);
        }
    }
    function updatePaymentStatusText(status) {
        // Находим родительский элемент и затем его дочерний div
        let targetElement = jQuery('.elementor-element.elementor-element-76cf8ae.elementor-widget.elementor-widget-text-editor').find('.elementor-widget-container');

        // Основываясь на статусе, изменяем текст в div
        if (status === 'canceled') {
            targetElement.text('Платёж отменён');
        } else {
            targetElement.text('Не оплачен');
        }
    }

    if (/\/gift-share\/.+\/$/.test(window.location.pathname)) {
        let certificateId = window.location.pathname.match(/\/gift-share\/(.+)\/$/)[1];

        // Запрашиваем статус сертификата
        jQuery.ajax({
            url: '/wp-json/gift-certificate-management/v1/certificate_status/',
            type: 'GET',
            data: {certificate_id: certificateId},
            success: function (result) {
                if (result.status === 'canceled' || result.status !== 'paid') {
                    if (result.status === 'canceled') {
                        updatePaymentStatusText('canceled');
                    } else {
                        const paymentId = result.payment_id;

                        // Если статус сертификата не 'paid', проверяем статус платежа
                        jQuery.ajax({
                            url: '/wp-json/gift-certificate-management/v1/check_payment/',
                            type: 'POST',
                            data: JSON.stringify({payment_id: paymentId, certificate_id: certificateId}),
                            contentType: 'application/json; charset=utf-8',
                            dataType: 'json',
                            success: function (paymentResult) {
                                if (paymentResult.status !== 'succeeded') {
                                    updatePaymentStatusText(paymentResult.status);
                                }
                            },
                            error: function (xhr, status, error) {
                                // Обработка ошибки
                            }
                        });
                    }
                }
            },
            error: function (xhr, status, error) {
                // Обработка ошибки
            }
        });
    }


});

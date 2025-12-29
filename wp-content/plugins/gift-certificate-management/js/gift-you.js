/**
 * JavaScript для формы оформления подарочного сертификата (gift-new)
 * Version: 1.0.1
 * Новый формат с SMS-оповещением
 */

jQuery(document).ready(function($) {

    // Проверяем, что мы на странице gift-new
    if (window.location.pathname !== '/gift-new/' && window.location.pathname !== '/gift-new') {
        return;
    }

    console.log('Gift-You JS loaded');

    // Инициализация поля даты/времени отправки
    initScheduledDateTime();

    // Синхронизация суммы с полем оплаты (если есть)
    $('.form_gift .form_gift_input, .gift_summa').on('input', function() {
        var newValue = $(this).val();

        // Валидация минимальной суммы
        if (parseInt(newValue) < 2000) {
            this.setCustomValidity("Минимальная сумма Подарочного сертификата 2000 рублей");
        } else {
            this.setCustomValidity("");
        }
    });

    // Обработка отправки формы
    $('.form_gift_submit, .gift-you-submit').click(function(e) {
        e.preventDefault();

        var formValid = validateForm();
        if (!formValid) {
            return;
        }

        // Собираем данные формы
        var data = collectFormData();

        // Показываем индикатор загрузки
        showLoading();

        // Отправляем на сервер
        $.ajax({
            url: '/wp-json/gift-you/v1/create_payment/',
            type: 'POST',
            data: JSON.stringify(data),
            contentType: 'application/json; charset=utf-8',
            dataType: 'json',
            success: function(result) {
                hideLoading();

                if (result.error) {
                    showError('Произошла ошибка: ' + result.error);
                } else {
                    console.log('Payment created:', result);
                    // Переход на страницу оплаты YooKassa
                    window.location.href = result.payment_url;
                }
            },
            error: function(xhr, status, error) {
                hideLoading();
                console.error('Error:', error);
                showError('Произошла ошибка при создании платежа. Пожалуйста, попробуйте еще раз.');
            }
        });
    });

    /**
     * Инициализация поля выбора даты/времени
     */
    function initScheduledDateTime() {
        // Если есть чекбокс "Отправить позже"
        var $scheduledCheckbox = $('#send_later, .send-later-checkbox');
        var $scheduledDateTime = $('#scheduled_datetime, .scheduled-datetime');

        if ($scheduledCheckbox.length) {
            $scheduledDateTime.hide();

            $scheduledCheckbox.on('change', function() {
                if ($(this).is(':checked')) {
                    $scheduledDateTime.show();
                    // Устанавливаем минимальную дату (сейчас + 5 минут)
                    var minDate = new Date();
                    minDate.setMinutes(minDate.getMinutes() + 5);
                    var minDateStr = minDate.toISOString().slice(0, 16);
                    $scheduledDateTime.find('input[type="datetime-local"]').attr('min', minDateStr);
                } else {
                    $scheduledDateTime.hide();
                }
            });
        }
    }

    /**
     * Валидация формы
     */
    function validateForm() {
        var isValid = true;
        var firstInvalid = null;

        // Обязательные поля
        var requiredFields = [
            { selector: '.gift_summa, input[name="certificate_amount"]', name: 'Сумма сертификата' },
            { selector: '.gift_name, input[name="recipient_name"]', name: 'Имя получателя' },
            { selector: '.gift_recipient_phone, input[name="recipient_phone"]', name: 'Телефон получателя' },
            { selector: '.gift_myname, input[name="sender_name"]', name: 'Ваше имя' },
            { selector: '.gift_sender_email, input[name="sender_email"]', name: 'Ваш Email' },
            { selector: '.gift_sender_phone, input[name="sender_phone"]', name: 'Ваш телефон' }
        ];

        requiredFields.forEach(function(field) {
            var $input = $(field.selector);
            if ($input.length && !$input.val().trim()) {
                isValid = false;
                $input.addClass('error');
                if (!firstInvalid) firstInvalid = $input;
            } else {
                $input.removeClass('error');
            }
        });

        // Валидация суммы
        var $amount = $('.gift_summa, input[name="certificate_amount"]');
        if ($amount.length) {
            var amount = parseInt($amount.val());
            if (isNaN(amount) || amount < 2000) {
                isValid = false;
                $amount.addClass('error');
                showError('Минимальная сумма сертификата 2000 рублей');
                if (!firstInvalid) firstInvalid = $amount;
            }
        }

        // Валидация телефона получателя
        var $recipientPhone = $('.gift_recipient_phone, input[name="recipient_phone"]');
        if ($recipientPhone.length) {
            var phone = $recipientPhone.val().replace(/[^0-9]/g, '');
            if (phone.length < 10 || phone.length > 11) {
                isValid = false;
                $recipientPhone.addClass('error');
                showError('Введите корректный номер телефона получателя');
                if (!firstInvalid) firstInvalid = $recipientPhone;
            }
        }

        // Валидация email
        var $email = $('.gift_sender_email, input[name="sender_email"]');
        if ($email.length) {
            var email = $email.val();
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                isValid = false;
                $email.addClass('error');
                showError('Введите корректный Email');
                if (!firstInvalid) firstInvalid = $email;
            }
        }

        // Проверка чекбокса согласия
        var $consent = $('input[name="PERSONAL"], .consent-checkbox');
        if ($consent.length && !$consent.is(':checked')) {
            isValid = false;
            showError('Необходимо согласие на обработку персональных данных');
        }

        // Валидация запланированной даты
        var $scheduledCheckbox = $('#send_later, .send-later-checkbox');
        if ($scheduledCheckbox.is(':checked')) {
            var $scheduledInput = $('#scheduled_datetime input, .scheduled-datetime input');
            if ($scheduledInput.length && !$scheduledInput.val()) {
                isValid = false;
                $scheduledInput.addClass('error');
                showError('Укажите дату и время отправки');
                if (!firstInvalid) firstInvalid = $scheduledInput;
            } else if ($scheduledInput.val()) {
                var scheduledDate = new Date($scheduledInput.val());
                var now = new Date();
                if (scheduledDate <= now) {
                    isValid = false;
                    $scheduledInput.addClass('error');
                    showError('Дата отправки должна быть в будущем');
                    if (!firstInvalid) firstInvalid = $scheduledInput;
                }
            }
        }

        if (firstInvalid) {
            firstInvalid.focus();
        }

        return isValid;
    }

    /**
     * Сбор данных формы
     */
    function collectFormData() {
        var data = {
            sum: $('.gift_summa, input[name="certificate_amount"]').val(),
            certificate_amount: $('.gift_summa, input[name="certificate_amount"]').val(),
            recipient_name: $('.gift_name, input[name="recipient_name"]').val(),
            recipient_phone: $('.gift_recipient_phone, input[name="recipient_phone"]').val(),
            recipient_message: $('.gift_messege, textarea[name="recipient_message"]').val() || '',
            sender_name: $('.gift_myname, input[name="sender_name"]').val(),
            sender_email: $('.gift_sender_email, input[name="sender_email"]').val(),
            sender_phone: $('.gift_sender_phone, input[name="sender_phone"]').val()
        };

        // Запланированная отправка
        var $scheduledCheckbox = $('#send_later, .send-later-checkbox');
        if ($scheduledCheckbox.is(':checked')) {
            var $scheduledInput = $('#scheduled_datetime input, .scheduled-datetime input');
            if ($scheduledInput.val()) {
                data.scheduled_at = $scheduledInput.val();
            }
        }

        return data;
    }

    /**
     * Показать индикатор загрузки
     */
    function showLoading() {
        // Создаём оверлей если его нет
        if ($('#gift-loading-overlay').length === 0) {
            $('body').append(
                '<div id="gift-loading-overlay" style="' +
                'position: fixed; top: 0; left: 0; width: 100%; height: 100%;' +
                'background: rgba(0,0,0,0.5); z-index: 9999;' +
                'display: flex; justify-content: center; align-items: center;">' +
                '<div style="background: #fff; padding: 30px 50px; border-radius: 10px; text-align: center;">' +
                '<div style="margin-bottom: 15px;">Создание платежа...</div>' +
                '<div class="spinner" style="border: 3px solid #f3f3f3; border-top: 3px solid #90A384;' +
                'border-radius: 50%; width: 30px; height: 30px; animation: spin 1s linear infinite; margin: 0 auto;"></div>' +
                '</div></div>' +
                '<style>@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }</style>'
            );
        }
        $('#gift-loading-overlay').show();
    }

    /**
     * Скрыть индикатор загрузки
     */
    function hideLoading() {
        $('#gift-loading-overlay').hide();
    }

    /**
     * Показать ошибку
     */
    function showError(message) {
        // Удаляем предыдущее сообщение
        $('.gift-error-message').remove();

        // Добавляем новое
        var $error = $(
            '<div class="gift-error-message" style="' +
            'background: #ffebee; color: #c62828; padding: 15px; margin: 10px 0;' +
            'border-radius: 5px; border-left: 4px solid #c62828;">' +
            message + '</div>'
        );

        // Вставляем перед кнопкой отправки
        $('.form_gift_submit, .gift-you-submit').before($error);

        // Автоскрытие через 5 секунд
        setTimeout(function() {
            $error.fadeOut(function() {
                $(this).remove();
            });
        }, 5000);
    }

    // Маска для телефона (простая)
    $('.gift_recipient_phone, .gift_sender_phone, input[name="recipient_phone"], input[name="sender_phone"]').on('input', function() {
        var value = $(this).val().replace(/[^0-9+]/g, '');
        $(this).val(value);
    });

});

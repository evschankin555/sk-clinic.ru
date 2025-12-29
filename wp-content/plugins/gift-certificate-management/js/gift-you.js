/**
 * JavaScript для формы оформления подарочного сертификата (gift-new)
 * Version: 2.1.0
 * Интеграция с существующей вёрсткой верстальщика
 * Поддержка тестового режима через ?test=1
 */

(function() {
    'use strict';

    // Проверяем, что мы на странице gift-new
    if (window.location.pathname.indexOf('/gift-new') === -1) {
        return;
    }

    // Проверяем тестовый режим (?test=1 в URL)
    var isTestMode = (new URLSearchParams(window.location.search)).get('test') === '1';

    // Ждём загрузки DOM
    document.addEventListener('DOMContentLoaded', function() {
        initGiftYouForm();
    });

    function initGiftYouForm() {
        const form = document.getElementById('giftForm');
        if (!form) {
            console.log('Gift-You: форма не найдена');
            return;
        }

        console.log('Gift-You: форма инициализирована');

        // Показываем индикатор тестового режима
        if (isTestMode) {
            var testBadge = document.createElement('div');
            testBadge.style.cssText = 'position: fixed; top: 10px; right: 10px; background: #ff6b6b; color: #fff; padding: 8px 16px; border-radius: 8px; font-size: 14px; z-index: 9999; font-family: sans-serif;';
            testBadge.textContent = 'ТЕСТОВЫЙ РЕЖИМ';
            document.body.appendChild(testBadge);
        }

        // Перехватываем отправку формы
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            handleFormSubmit();
        });

        // Также перехватываем клик по кнопке оплаты
        const payBtn = form.querySelector('.pay-btn');
        if (payBtn) {
            payBtn.addEventListener('click', function(e) {
                e.preventDefault();
                handleFormSubmit();
            });
        }
    }

    /**
     * Обработка отправки формы
     */
    function handleFormSubmit() {
        const form = document.getElementById('giftForm');
        const errorBox = document.getElementById('formError');

        // Сбрасываем ошибки
        resetErrors();

        // Валидация
        if (!validateForm()) {
            return;
        }

        // Собираем данные
        const data = collectFormData();

        console.log('Gift-You: отправка данных', data);

        // Показываем индикатор загрузки
        showLoading();

        // Отправляем на сервер
        fetch('/wp-json/gift-you/v1/create_payment/', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(result) {
            hideLoading();

            if (result.payment_url) {
                console.log('Gift-You: платёж создан', result);
                // Переход на страницу оплаты YooKassa
                window.location.href = result.payment_url;
            } else {
                showError('Ошибка: ' + (result.message || 'Не удалось создать платёж'));
            }
        })
        .catch(function(error) {
            hideLoading();
            console.error('Gift-You: ошибка', error);
            showError('Произошла ошибка при создании платежа. Попробуйте ещё раз.');
        });
    }

    /**
     * Валидация формы
     */
    function validateForm() {
        let valid = true;
        const customAmount = document.getElementById('customAmount');
        const amountField = customAmount ? customAmount.closest('.field') : null;

        // Проверка суммы
        const amount = getSelectedAmount();
        if (!amount || amount < 2000) {
            if (amountField) {
                amountField.classList.add('error');
            }
            showError('Минимальная сумма сертификата 2 000 ₽');
            valid = false;
        }

        // Обязательные поля (с классом .req)
        document.querySelectorAll('.req').forEach(function(block) {
            const input = block.querySelector('input:not([type="checkbox"]), textarea');
            const checkbox = block.querySelector('input[type="checkbox"]');

            if (input && !input.value.trim()) {
                block.classList.add('error');
                valid = false;
            }

            if (checkbox && !checkbox.checked) {
                valid = false;
            }
        });

        // Проверка чекбоксов отдельно
        const offerCheckbox = document.getElementById('offer');
        const privacyCheckbox = document.getElementById('privacy');

        if (offerCheckbox && !offerCheckbox.checked) {
            showError('Необходимо принять оферту');
            valid = false;
        }

        if (privacyCheckbox && !privacyCheckbox.checked) {
            showError('Необходимо принять политику конфиденциальности');
            valid = false;
        }

        // Валидация телефона получателя
        const recipientPhone = getRecipientPhone();
        if (!recipientPhone || recipientPhone.length < 11) {
            showError('Введите корректный номер телефона получателя');
            valid = false;
        }

        // Валидация email
        const senderEmail = getSenderEmail();
        if (senderEmail && !isValidEmail(senderEmail)) {
            showError('Введите корректный Email');
            valid = false;
        }

        if (!valid) {
            showError('Заполните обязательные поля');
        }

        return valid;
    }

    /**
     * Сбор данных формы
     */
    function collectFormData() {
        const inputs = document.querySelectorAll('#giftForm input, #giftForm textarea');
        const fields = Array.from(inputs);

        // Получаем значения по порядку полей в форме
        // Левая колонка: имя получателя, сообщение, телефон получателя
        // Правая колонка: имя отправителя, email, телефон отправителя

        const leftColumn = document.querySelector('.form-grid > div:first-child');
        const rightColumn = document.querySelector('.form-grid > div:last-child');

        const recipientName = leftColumn.querySelector('.field:nth-child(1) input').value.trim();
        const recipientMessage = leftColumn.querySelector('.field:nth-child(2) textarea').value.trim();
        const recipientPhone = normalizePhone(leftColumn.querySelector('.field:nth-child(3) input').value);

        const senderName = rightColumn.querySelector('.field:nth-child(1) input').value.trim();
        const senderEmail = rightColumn.querySelector('.field:nth-child(2) input').value.trim();
        const senderPhone = normalizePhone(rightColumn.querySelector('.field:nth-child(3) input').value);

        const amount = getSelectedAmount();

        // Запланированная отправка
        let scheduledAt = null;
        const planBtn = document.querySelector('.send-btn[data-mode="plan"]');
        if (planBtn && planBtn.classList.contains('active')) {
            const sendDate = document.getElementById('sendDate').value;
            const sendTime = document.getElementById('sendTime').value;
            if (sendDate && sendTime) {
                scheduledAt = sendDate + 'T' + sendTime;
            }
        }

        var formData = {
            sum: amount,
            certificate_amount: amount,
            recipient_name: recipientName,
            recipient_phone: recipientPhone,
            recipient_message: recipientMessage,
            sender_name: senderName,
            sender_email: senderEmail,
            sender_phone: senderPhone,
            scheduled_at: scheduledAt
        };

        // Добавляем флаг тестового режима если ?test=1
        if (isTestMode) {
            formData.test_mode = true;
            console.log('Gift-You: ТЕСТОВЫЙ РЕЖИМ - оплата будет пропущена');
        }

        return formData;
    }

    /**
     * Получить выбранную сумму
     */
    function getSelectedAmount() {
        const customAmount = document.getElementById('customAmount');
        if (customAmount && customAmount.value) {
            return Number(customAmount.value);
        }

        const activeBtn = document.querySelector('.amount-btn.active[data-val]');
        if (activeBtn) {
            return Number(activeBtn.dataset.val);
        }

        return 0;
    }

    /**
     * Получить телефон получателя
     */
    function getRecipientPhone() {
        const leftColumn = document.querySelector('.form-grid > div:first-child');
        const input = leftColumn.querySelector('.field:nth-child(3) input');
        return input ? normalizePhone(input.value) : '';
    }

    /**
     * Получить email отправителя
     */
    function getSenderEmail() {
        const rightColumn = document.querySelector('.form-grid > div:last-child');
        const input = rightColumn.querySelector('.field:nth-child(2) input');
        return input ? input.value.trim() : '';
    }

    /**
     * Нормализация телефона
     */
    function normalizePhone(phone) {
        if (!phone) return '';
        let digits = phone.replace(/\D/g, '');
        if (digits.length === 11 && digits[0] === '8') {
            digits = '7' + digits.slice(1);
        }
        return digits;
    }

    /**
     * Валидация email
     */
    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    /**
     * Показать ошибку
     */
    function showError(text) {
        const errorBox = document.getElementById('formError');
        if (errorBox) {
            errorBox.textContent = text;
            errorBox.style.display = 'block';
        }
    }

    /**
     * Скрыть ошибку
     */
    function hideError() {
        const errorBox = document.getElementById('formError');
        if (errorBox) {
            errorBox.style.display = 'none';
            errorBox.textContent = '';
        }
    }

    /**
     * Сброс ошибок
     */
    function resetErrors() {
        document.querySelectorAll('.field.error').forEach(function(f) {
            f.classList.remove('error');
        });
        hideError();
    }

    /**
     * Показать индикатор загрузки
     */
    function showLoading() {
        // Создаём оверлей если его нет
        var overlay = document.getElementById('gift-loading-overlay');
        var loadingText = isTestMode ? 'Создание тестового сертификата...' : 'Создание платежа...';
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.id = 'gift-loading-overlay';
            overlay.innerHTML = '\
                <div style="background: #fff; padding: 30px 50px; border-radius: 16px; text-align: center; font-family: \'Tenor Sans\', sans-serif;">\
                    <div style="margin-bottom: 15px; font-size: 18px;">' + loadingText + '</div>\
                    <div style="border: 3px solid #f3f3f3; border-top: 3px solid #DFEBCE; border-radius: 50%; width: 40px; height: 40px; animation: gift-spin 1s linear infinite; margin: 0 auto;"></div>\
                </div>\
            ';
            overlay.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; display: flex; justify-content: center; align-items: center;';

            // Добавляем стиль анимации
            const style = document.createElement('style');
            style.textContent = '@keyframes gift-spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }';
            document.head.appendChild(style);

            document.body.appendChild(overlay);
        }
        overlay.style.display = 'flex';
    }

    /**
     * Скрыть индикатор загрузки
     */
    function hideLoading() {
        const overlay = document.getElementById('gift-loading-overlay');
        if (overlay) {
            overlay.style.display = 'none';
        }
    }

})();

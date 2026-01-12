<?php
/**
 * Шаблон формы оформления подарочного сертификата (gift-you / gift-new)
 * Version: 1.0.0
 * Используется шорткодом [gift_you_form]
 */

if (!defined('ABSPATH')) {
    exit;
}

// Проверяем тестовый режим
$is_test_mode = isset($_GET['test']) && $_GET['test'] === '1';
?>

<div class="gift-wrap">

    <?php if ($is_test_mode): ?>
    <div class="gift-test-badge">ТЕСТОВЫЙ РЕЖИМ</div>
    <?php endif; ?>

    <!-- СУММА -->
    <section class="amounts">
        <h2>Укажите сумму подарочного сертификата</h2>
        <div class="amount-grid">
            <button type="button" class="amount-btn active" data-val="2000">2000</button>
            <button type="button" class="amount-btn" data-val="5000">5000</button>
            <button type="button" class="amount-btn" data-val="10000">10000</button>
            <button type="button" class="amount-btn" data-val="15000">15000</button>
            <button type="button" class="amount-btn" data-val="20000">20000</button>
            <div class="field" id="certSum">
                <input type="number" id="customAmount" placeholder="Ваша сумма">
            </div>
        </div>
    </section>

    <!-- СЕРТИФИКАТ -->
    <section class="cert">
        <img src="https://sk-clinic.ru/wp-content/uploads/2025/12/sert.png" alt="Подарочный сертификат">
        <div class="cert-amount">
            <div class="value" id="certValue">2 000</div>
            <div class="cur">рублей</div>
        </div>
    </section>

    <!-- ФОРМА -->
    <form id="giftForm" novalidate>
        <div class="form-grid">

            <!-- ЛЕВАЯ КОЛОНКА -->
            <div>
                <div class="field">
                    <label>Имя получателя</label>
                    <input type="text" name="recipient_name" maxlength="30">
                </div>

                <div class="field">
                    <label>Сообщение получателю</label>
                    <small>не более 150 символов</small>
                    <textarea name="recipient_message" maxlength="150"></textarea>
                </div>

                <div class="field req">
                    <label>Номер телефона получателя *</label>
                    <input type="tel" name="recipient_phone" class="phone">
                    <small>на этот номер отправим подарочный сертификат</small>
                </div>

                <div class="field">
                    <label>Когда отправим</label>
                    <div class="send-time">
                        <div class="send-btns">
                            <button type="button" class="send-btn active" data-mode="now">Сейчас</button>
                            <button type="button" class="send-btn" data-mode="plan">Запланировать</button>
                        </div>
                        <div class="send-info">сертификат придет сразу после оплаты</div>
                        <div class="send-plan">
                            <input type="date" id="sendDate">
                            <input type="time" id="sendTime">
                            <small style="display: block; margin-top: 5px; color: var(--gift-gray); font-size: 12px;">Время указывается во времени Екатеринбурга (ЕКБ, UTC+5)</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ПРАВАЯ КОЛОНКА -->
            <div>
                <div class="field">
                    <label>Имя отправителя</label>
                    <input type="text" name="sender_name" maxlength="30">
                </div>

                <div class="field req">
                    <label>Email отправителя *</label>
                    <input type="email" name="sender_email">
                    <small>отправим на него только чек</small>
                </div>

                <div class="field req">
                    <label>Телефон отправителя *</label>
                    <input type="tel" name="sender_phone" class="phone">
                    <small>для связи, в случае проблем</small>
                </div>

                <div class="checks req">
                    <div class="check-item">
                        <input type="checkbox" id="offer" name="offer">
                        <label for="offer">Я принимаю <a href="/oferta/" target="_blank">оферту</a></label>
                    </div>

                    <div class="check-item">
                        <input type="checkbox" id="privacy" name="privacy">
                        <label for="privacy">Я принимаю <a href="/privacy-policy/" target="_blank">политику конфиденциальности</a></label>
                    </div>
                </div>

                <button type="submit" class="pay-btn">Перейти к оплате</button>
                <img class="payments" src="https://sk-clinic.ru/wp-content/uploads/2025/12/oplata.png" alt="Способы оплаты">

                <div class="error-text" id="formError"></div>
            </div>

        </div>
    </form>

    <!-- ЗАГРУЗКА -->
    <div class="gift-loading" id="giftLoading">
        <div class="gift-loading-content">
            <div class="gift-loading-text">Создание платежа...</div>
            <div class="gift-loading-spinner"></div>
        </div>
    </div>
</div>

<style>
@font-face {
    font-family: 'Tenor Sans';
    src: url('https://sk-clinic.ru/wp-content/uploads/2025/08/Tenor-Sans.ttf') format('truetype');
    font-weight: 400;
}

:root {
    --gift-beige: #F3EFE6;
    --gift-green: #DFEBCE;
    --gift-gray: #A9ABAE;
    --gift-error: #f6dede;
}

.gift-wrap {
    max-width: 1200px;
    margin: 0 auto;
    padding: 40px 50px;
    font-family: 'Tenor Sans', sans-serif;
    position: relative;
}

.gift-test-badge {
    position: fixed;
    top: 10px;
    right: 10px;
    background: #ff6b6b;
    color: #fff;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 14px;
    z-index: 9999;
}

/* ===== СУММА ===== */
.gift-wrap .amounts h2 {
    font-size: 22px;
    margin-bottom: 16px;
}

.gift-wrap .amount-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(130px, 160px));
    gap: 12px;
}

.gift-wrap .amount-btn {
    font-family: "Tenor Sans", sans-serif;
    height: 56px;
    border-radius: 12px;
    border: 0;
    background: var(--gift-beige);
    font-size: 22px;
    cursor: pointer;
    color: #666;
    transition: background 0.2s;
}

.gift-wrap .amount-btn:hover {
    background: #e8e4db;
}

.gift-wrap .amount-btn.active {
    background: var(--gift-green);
}

.gift-wrap #customAmount {
    font-family: "Tenor Sans", sans-serif;
    width: 220px;
    text-align: center;
}

.gift-wrap .inline-error {
    text-align: center;
    padding-top: 10px;
    color: #c33;
    font-size: 14px;
}

/* ===== СЕРТИФИКАТ ===== */
.gift-wrap .cert {
    position: relative;
    margin-bottom: 40px;
}

.gift-wrap .cert img {
    width: 100%;
    display: block;
    border-radius: 16px;
}

.gift-wrap .cert-amount {
    position: absolute;
    right: 5%;
    bottom: 20%;
    text-align: right;
    color: #fff;
}

.gift-wrap .cert-amount .value {
    font-size: 64px;
    line-height: 1;
}

.gift-wrap .cert-amount .cur {
    font-size: 20px;
    letter-spacing: 1px;
}

/* ===== ФОРМА ===== */
.gift-wrap .form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
}

.gift-wrap .field {
    margin-bottom: 18px;
}

.gift-wrap .field label {
    display: block;
    font-size: 18px;
    margin-bottom: 6px;
}

.gift-wrap .field small {
    display: block;
    color: var(--gift-gray);
    margin: 5px 5px 6px;
}

.gift-wrap .field input,
.gift-wrap .field textarea {
    width: 100%;
    padding: 14px 16px;
    border-radius: 12px;
    border: 0;
    background: var(--gift-beige);
    font-family: inherit;
    font-size: 22px;
}

.gift-wrap .field textarea {
    resize: none;
    height: 96px;
}

.gift-wrap .field.error input,
.gift-wrap .field.error textarea {
    background: var(--gift-error);
}

/* ===== КОГДА ОТПРАВИМ ===== */
.gift-wrap .send-btns {
    display: flex;
    gap: 10px;
    margin-bottom: 10px;
}

.gift-wrap .send-btn {
    flex: 1;
    height: 44px;
    border-radius: 12px;
    border: 0;
    background: var(--gift-beige);
    font-family: inherit;
    cursor: pointer;
    color: #666;
    transition: background 0.2s;
}

.gift-wrap .send-btn:hover {
    background: #e8e4db;
}

.gift-wrap .send-btn.active {
    background: var(--gift-green);
}

.gift-wrap .send-info {
    font-size: 14px;
    color: var(--gift-gray);
}

.gift-wrap .send-plan {
    display: none;
    gap: 10px;
}

.gift-wrap .send-plan input {
    flex: 1;
}

/* ===== ПРАВАЯ КОЛОНКА ===== */
.gift-wrap .checks {
    margin-bottom: 20px;
}

.gift-wrap .check-item {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 10px;
}

.gift-wrap .check-item input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.gift-wrap .check-item label {
    font-size: 14px;
    cursor: pointer;
}

.gift-wrap .check-item a {
    color: inherit;
    text-decoration: underline;
}

.gift-wrap .pay-btn {
    font-family: "Tenor Sans", sans-serif;
    width: 100%;
    height: 66px;
    border-radius: 16px;
    border: 0;
    background: var(--gift-green);
    font-size: 20px;
    cursor: pointer;
    color: #666;
    transition: background 0.2s;
}

.gift-wrap .pay-btn:hover {
    background: #d4e3be;
}

.gift-wrap .payments {
    max-width: 100%;
    margin-top: 20px;
}

.gift-wrap .error-text {
    color: #c33;
    font-size: 14px;
    display: none;
    margin-top: 10px;
}

/* ===== ЗАГРУЗКА ===== */
.gift-wrap .gift-loading {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 9999;
    display: none;
    justify-content: center;
    align-items: center;
}

.gift-wrap .gift-loading-content {
    background: #fff;
    padding: 30px 50px;
    border-radius: 16px;
    text-align: center;
}

.gift-wrap .gift-loading-text {
    margin-bottom: 15px;
    font-size: 18px;
}

.gift-wrap .gift-loading-spinner {
    border: 3px solid #f3f3f3;
    border-top: 3px solid var(--gift-green);
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: gift-spin 1s linear infinite;
    margin: 0 auto;
}

@keyframes gift-spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* ===== АДАПТАЦИЯ ===== */
@media (max-width: 900px) {
    .gift-wrap {
        padding: 30px 20px;
    }
    .gift-wrap .form-grid {
        grid-template-columns: 1fr;
    }
    .gift-wrap .cert-amount .value {
        font-size: 44px;
    }
}

@media (max-width: 480px) {
    .gift-wrap {
        padding: 30px 10px;
    }
    .gift-wrap .cert-amount .cur {
        font-size: 10px;
    }
    .gift-wrap .cert-amount .value {
        font-size: 18px;
    }
    .gift-wrap .cert {
        margin-bottom: 20px;
        margin-top: 20px;
    }
    .gift-wrap .amount-btn {
        font-size: 18px;
        height: inherit;
    }
    .gift-wrap .field input,
    .gift-wrap .field textarea {
        font-size: 18px;
    }
    .gift-wrap #customAmount {
        width: 100%;
    }
    .gift-wrap .field {
        margin-bottom: 10px;
    }
    .gift-wrap #certSum {
        margin-bottom: 0;
    }
    .gift-wrap .amounts h2 {
        font-size: 20px;
    }
}
</style>

<script>
(function() {
    'use strict';

    // ======================================================
    // НАСТРОЙКИ
    // ======================================================

    var isTestMode = <?php echo $is_test_mode ? 'true' : 'false'; ?>;
    var apiUrl = '<?php echo esc_url(rest_url('gift-you/v1/create_payment/')); ?>';

    var form = document.getElementById('giftForm');
    var errorBox = document.getElementById('formError');
    var certValue = document.getElementById('certValue');
    var amountBtns = document.querySelectorAll('.gift-wrap .amount-btn[data-val]');
    var customAmount = document.getElementById('customAmount');
    var loadingOverlay = document.getElementById('giftLoading');

    var format = function(v) {
        return v.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
    };

    var setAmount = function(v) {
        certValue.textContent = format(v);
    };

    // ======================================================
    // ВЫБОР СУММЫ СЕРТИФИКАТА (КНОПКИ)
    // ======================================================

    amountBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            amountBtns.forEach(function(b) {
                b.classList.remove('active');
            });
            btn.classList.add('active');
            customAmount.value = '';
            clearAmountError();
            setAmount(btn.dataset.val);
            hideError();
        });
    });

    // ======================================================
    // ДРУГАЯ СУММА — ВАЛИДАЦИЯ ПРИ ВВОДЕ
    // ======================================================

    var amountField = customAmount.closest('.field');
    var amountError = document.createElement('div');
    amountError.className = 'inline-error';
    amountError.style.display = 'none';
    amountField.appendChild(amountError);

    customAmount.addEventListener('input', function() {
        amountBtns.forEach(function(b) {
            b.classList.remove('active');
        });

        var value = Number(customAmount.value);

        if (!customAmount.value) {
            clearAmountError();
            return;
        }

        // ВРЕМЕННО: минимум 10₽ для тестирования (вернуть 2000)
        if (value < 10) {
            amountField.classList.add('error');
            amountError.textContent = 'не меньше 10 ₽';
            amountError.style.display = 'block';
            return;
        }

        clearAmountError();
        setAmount(value);
    });

    function clearAmountError() {
        amountField.classList.remove('error');
        amountError.style.display = 'none';
        amountError.textContent = '';
    }

    // ======================================================
    // МАСКА РОССИЙСКОГО ТЕЛЕФОНА
    // ======================================================

    document.querySelectorAll('.gift-wrap .phone').forEach(function(input) {
        input.addEventListener('input', function() {
            var v = input.value.replace(/\D/g, '').slice(0, 11);
            if (v[0] === '8') v = '7' + v.slice(1);

            var r = '+7';
            if (v.length > 1) r += ' (' + v.slice(1, 4);
            if (v.length >= 4) r += ') ' + v.slice(4, 7);
            if (v.length >= 7) r += '-' + v.slice(7, 9);
            if (v.length >= 9) r += '-' + v.slice(9, 11);

            input.value = r;

            var field = input.closest('.field');
            if (field) field.classList.remove('error');

            hideError();
        });
    });

    // ======================================================
    // БЛОК "КОГДА ОТПРАВИМ"
    // ======================================================

    var sendBtns = document.querySelectorAll('.gift-wrap .send-btn');
    var sendInfo = document.querySelector('.gift-wrap .send-info');
    var sendPlan = document.querySelector('.gift-wrap .send-plan');

    sendBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            sendBtns.forEach(function(b) {
                b.classList.remove('active');
            });
            btn.classList.add('active');

            if (btn.dataset.mode === 'plan') {
                sendInfo.style.display = 'none';
                sendPlan.style.display = 'flex';
            } else {
                sendInfo.style.display = 'block';
                sendPlan.style.display = 'none';
            }
        });
    });

    // Устанавливаем дату/время по умолчанию (в ЕКБ)
    var sendDate = document.getElementById('sendDate');
    var sendTime = document.getElementById('sendTime');
    
    // Переменная для хранения текущего времени сервера в Екатеринбурге
    var serverTimeYek = null;
    
    // Получаем текущее время сервера в Екатеринбурге
    function getServerTime() {
        return fetch('/wp-json/gift-you/v1/get_server_time/')
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                if (data.iso) {
                    // Парсим ISO 8601 время с часовым поясом
                    serverTimeYek = new Date(data.iso);
                    
                    // Устанавливаем минимальную дату на сегодня (в ЕКБ)
                    var yekDateStr = data.date;
                    sendDate.min = yekDateStr;
                    sendDate.value = yekDateStr;
                    
                    // Устанавливаем текущее время
                    sendTime.value = data.time_hours;
                    
                    return serverTimeYek;
                } else {
                    console.error('Ошибка получения времени сервера:', data);
                    // Fallback: используем текущее время браузера (не идеально, но лучше чем ничего)
                    var now = new Date();
                    serverTimeYek = new Date(now.getTime() + (5 * 3600000)); // UTC+5
                    var yekDateStr = serverTimeYek.toISOString().slice(0, 10);
                    sendDate.min = yekDateStr;
                    sendDate.value = yekDateStr;
                    var hours = String(serverTimeYek.getHours()).padStart(2, '0');
                    var minutes = String(serverTimeYek.getMinutes()).padStart(2, '0');
                    sendTime.value = hours + ':' + minutes;
                    return serverTimeYek;
                }
            })
            .catch(function(error) {
                console.error('Ошибка запроса времени сервера:', error);
                // Fallback: используем текущее время браузера
                var now = new Date();
                serverTimeYek = new Date(now.getTime() + (5 * 3600000)); // UTC+5
                var yekDateStr = serverTimeYek.toISOString().slice(0, 10);
                sendDate.min = yekDateStr;
                sendDate.value = yekDateStr;
                var hours = String(serverTimeYek.getHours()).padStart(2, '0');
                var minutes = String(serverTimeYek.getMinutes()).padStart(2, '0');
                sendTime.value = hours + ':' + minutes;
                return serverTimeYek;
            });
    }
    
    // Загружаем время сервера при инициализации
    getServerTime();
    
    // Функция валидации даты/времени (проверяем в ЕКБ)
    function validateDateTime() {
        if (sendDate.value && sendTime.value && serverTimeYek) {
            // Создаем дату из введенных значений (интерпретируем как ЕКБ)
            var selectedYekStr = sendDate.value + 'T' + sendTime.value + ':00+05:00';
            var selectedYek = new Date(selectedYekStr);
            
            // Если выбранное время в прошлом (в ЕКБ), показываем ошибку
            if (selectedYek < serverTimeYek) {
                showError('Нельзя выбрать прошедшее время');
                var sendPlanField = sendDate.closest('.field');
                if (sendPlanField) {
                    sendPlanField.classList.add('error');
                }
                return false;
            } else {
                // Убираем ошибку если время корректно
                var sendPlanField = sendDate.closest('.field');
                if (sendPlanField) {
                    sendPlanField.classList.remove('error');
                }
                hideError();
            }
        }
        return true;
    }
    
    // Добавляем обработчики для валидации
    sendDate.addEventListener('change', validateDateTime);
    sendTime.addEventListener('change', validateDateTime);

    // ======================================================
    // СНЯТИЕ ОШИБОК ПРИ ИСПРАВЛЕНИИ ПОЛЕЙ
    // ======================================================

    form.querySelectorAll('input, textarea').forEach(function(el) {
        el.addEventListener('input', function() {
            var field = el.closest('.field');
            if (field) field.classList.remove('error');
            hideError();
        });
    });

    form.querySelectorAll('input[type="checkbox"]').forEach(function(el) {
        el.addEventListener('change', hideError);
    });

    // ======================================================
    // ОТПРАВКА ФОРМЫ
    // ======================================================

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        handleFormSubmit();
    });

    function handleFormSubmit() {
        resetErrors();

        if (!validateForm()) {
            return;
        }

        var data = collectFormData();
        console.log('Gift-You: отправка данных', data);

        showLoading();

        fetch(apiUrl, {
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

    // ======================================================
    // ВАЛИДАЦИЯ ФОРМЫ
    // ======================================================

    function validateForm() {
        var valid = true;

        // Проверка суммы
        var amount = getSelectedAmount();
        // ВРЕМЕННО: минимум 10₽ для тестирования (вернуть 2000)
        if (!amount || amount < 10) {
            amountField.classList.add('error');
            amountError.textContent = 'Не меньше 10₽';
            amountError.style.display = 'block';
            valid = false;
        }

        // Валидация даты/времени планирования
        var planBtn = document.querySelector('.gift-wrap .send-btn[data-mode="plan"]');
        if (planBtn && planBtn.classList.contains('active')) {
            if (!validateDateTime()) {
                valid = false;
            }
        }

        // Обязательные поля с классом .req
        document.querySelectorAll('.gift-wrap .req').forEach(function(block) {
            var input = block.querySelector('input:not([type="checkbox"]), textarea');

            if (input && !input.value.trim()) {
                var field = input.closest('.field');
                if (field) field.classList.add('error');
                valid = false;
            }
        });

        // Проверка чекбоксов
        var offerCheckbox = document.getElementById('offer');
        var privacyCheckbox = document.getElementById('privacy');

        if (!offerCheckbox.checked || !privacyCheckbox.checked) {
            valid = false;
        }

        // Валидация телефона получателя
        var recipientPhone = normalizePhone(form.querySelector('[name="recipient_phone"]').value);
        if (recipientPhone.length < 11) {
            var phoneField = form.querySelector('[name="recipient_phone"]').closest('.field');
            if (phoneField) phoneField.classList.add('error');
            valid = false;
        }

        // Валидация email
        var senderEmail = form.querySelector('[name="sender_email"]').value.trim();
        if (senderEmail && !isValidEmail(senderEmail)) {
            var emailField = form.querySelector('[name="sender_email"]').closest('.field');
            if (emailField) emailField.classList.add('error');
            valid = false;
        }

        if (!valid) {
            showError('Заполните обязательные поля и поставьте галочки');
        }

        return valid;
    }

    // ======================================================
    // СБОР ДАННЫХ ФОРМЫ
    // ======================================================

    function collectFormData() {
        var amount = getSelectedAmount();

        var scheduledAt = null;
        var planBtn = document.querySelector('.gift-wrap .send-btn[data-mode="plan"]');
        if (planBtn && planBtn.classList.contains('active')) {
            var sendDate = document.getElementById('sendDate').value;
            var sendTime = document.getElementById('sendTime').value;
            if (sendDate && sendTime) {
                // Введенные дата и время интерпретируются как ЕКБ
                // Отправляем время в формате Екатеринбурга (UTC+5): "2026-01-11T01:07:00+05:00"
                scheduledAt = sendDate + 'T' + sendTime + ':00+05:00';
            }
        }

        var formData = {
            sum: amount,
            certificate_amount: amount,
            recipient_name: form.querySelector('[name="recipient_name"]').value.trim(),
            recipient_phone: normalizePhone(form.querySelector('[name="recipient_phone"]').value),
            recipient_message: form.querySelector('[name="recipient_message"]').value.trim(),
            sender_name: form.querySelector('[name="sender_name"]').value.trim(),
            sender_email: form.querySelector('[name="sender_email"]').value.trim(),
            sender_phone: normalizePhone(form.querySelector('[name="sender_phone"]').value),
            scheduled_at: scheduledAt
        };

        if (isTestMode) {
            formData.test_mode = true;
            console.log('Gift-You: ТЕСТОВЫЙ РЕЖИМ - оплата будет пропущена');
        }

        return formData;
    }

    // ======================================================
    // ВСПОМОГАТЕЛЬНЫЕ ФУНКЦИИ
    // ======================================================

    function getSelectedAmount() {
        if (customAmount.value) {
            return Number(customAmount.value);
        }
        var activeBtn = document.querySelector('.gift-wrap .amount-btn.active[data-val]');
        if (activeBtn) {
            return Number(activeBtn.dataset.val);
        }
        return 0;
    }

    function normalizePhone(phone) {
        if (!phone) return '';
        var digits = phone.replace(/\D/g, '');
        if (digits.length === 11 && digits[0] === '8') {
            digits = '7' + digits.slice(1);
        }
        return digits;
    }

    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    function showError(text) {
        errorBox.textContent = text;
        errorBox.style.display = 'block';
    }

    function hideError() {
        errorBox.style.display = 'none';
        errorBox.textContent = '';
    }

    function resetErrors() {
        document.querySelectorAll('.gift-wrap .field.error').forEach(function(f) {
            f.classList.remove('error');
        });
        hideError();
        clearAmountError();
    }

    function showLoading() {
        var text = isTestMode ? 'Создание тестового сертификата...' : 'Создание платежа...';
        loadingOverlay.querySelector('.gift-loading-text').textContent = text;
        loadingOverlay.style.display = 'flex';
    }

    function hideLoading() {
        loadingOverlay.style.display = 'none';
    }

})();
</script>

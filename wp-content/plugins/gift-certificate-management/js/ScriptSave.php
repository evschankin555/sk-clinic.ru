<?php

function getScriptSave() {
    ?>
    <script type="text/javascript">
        function searchFunction() {
            var input, filter, table, tbody, tr, td, i, j, txtValue, cellMatch;
            input = document.getElementById("searchInput");
            filter = input.value.toUpperCase();
            table = document.getElementById("header-table");
            tbody = table.getElementsByTagName("tbody")[0]; // обходим только строки в теле таблицы
            tr = tbody.getElementsByTagName("tr");

            for (i = 0; i < tr.length; i++) {
                // Сбрасываем флаг совпадения для этой строки
                cellMatch = false;
                td = tr[i].getElementsByTagName("td");
                for (j = 0; j < td.length; j++) {
                    if (td[j]) {
                        txtValue = td[j].textContent || td[j].innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            cellMatch = true;  // Найдено совпадение в этой ячейке
                        }
                    }
                }
                // Если было найдено совпадение в любой из ячеек этой строки, отобразить строку
                if (cellMatch) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }


        // Функция для проверки наличия элементов с определенным селектором
        function checkElementsExist(selector) {
            return document.querySelector(selector) !== null;
        }

        // Функция для эмуляции клика на вкладке с указанным номером
        function clickOnTab(tabNumber) {
            // Задаем селектор в зависимости от номера вкладки
            var selector = '.tabs li:nth-child(' + tabNumber + ') input[type="radio"]';

            // Запускаем интервал проверки наличия элемента
            var interval = setInterval(function() {
                // Проверяем наличие элемента с заданным селектором
                if (checkElementsExist(selector)) {
                    // Если элемент существует, то находим его и эмулируем клик
                    var tab = document.querySelector(selector);
                    tab.click();
                    // Удаляем интервал, так как элемент найден
                    clearInterval(interval);
                }
            }, 1000); // Проверяем каждую секунду
        }

        // Используем функцию для клика на вкладке с номером из URL-адреса
        var tabNumber = parseInt(window.location.search.replace(/^\D+/g, ''));
        if (!isNaN(tabNumber)) {
            clickOnTab(tabNumber);
        }else{

        }
        window.onload = function() {
            (function (jQuery) {
          /*------------------------------------------
                    String html characters
                ------------------------------------------*/
                function escapeHtml(unsafe) {
                    return unsafe
                        .replace(/&/g, "&amp;")
                        .replace(/</g, "&lt;")
                        .replace(/>/g, "&gt;")
                        .replace(/"/g, "&quot;")
                        .replace(/'/g, "&#039;");
                }
                function escapeHtmlBack(unsafe) {
                    return unsafe
                        .replace(/\"/g, '"').replace(/\"/g, '"');
                }



                jQuery('#header_ru').on('click', function(e) {
                    document.getElementById('myFrame').contentWindow.location.href = 'https://basinn.ru/prevs/prev_header_block.php';
                });
                jQuery('#header_en').on('click', function(e) {
                    document.getElementById('myFrame').contentWindow.location.href = 'https://basinn.ru/prevs/prev_header_block.php?lang=en';
                });
                jQuery('#menu').on('click', function(e) {
                    document.location.href = 'https://basinn.ru/wp-admin/nav-menus.php';
                });
                jQuery('#menu_ru').on('click', function(e) {
                    document.getElementById('myFrameMenu').contentWindow.location.href = 'https://basinn.ru/prevs/prev_menu_block.php';
                });
                jQuery('#menu_en').on('click', function(e) {
                    document.getElementById('myFrameMenu').contentWindow.location.href = 'https://basinn.ru/prevs/prev_menu_block.php?lang=en';
                });
                jQuery('#footer_ru').on('click', function(e) {
                    document.getElementById('myFrameFooter').contentWindow.location.href = 'https://basinn.ru/prevs/prev_footer_block.php';
                });
                jQuery('#footer_en').on('click', function(e) {
                    document.getElementById('myFrameFooter').contentWindow.location.href = 'https://basinn.ru/prevs/prev_footer_block.php?lang=en';
                });
                jQuery('.val_text_ru,.val_text_en').on('keyup', function(e) {
                    var data = JSON.parse(jQuery(this).attr('data-data'));
                    data.value = jQuery(this).text();

                    jQuery.post('/wp-content/plugins/basinn_plugin/save_data.php',
                        {
                            action: 'update',
                            basinn: 'yes',
                            data: data
                        },
                        function(html){
                            document.getElementById('myFrame').contentWindow.location.reload();
                            document.getElementById('myFrameMenu').contentWindow.location.reload();
                            document.getElementById('myFrameFooter').contentWindow.location.reload();
                        });
                });
                jQuery('.code').on('keyup', function(e) {
                    var data = JSON.parse(jQuery(this).attr('data-data'));
                    data.value = escapeHtmlBack(jQuery(this).text());
                    console.log(data.value);

                    jQuery.post('/wp-content/plugins/basinn_plugin/save_data.php',
                        {
                            action: 'update',
                            basinn: 'yes',
                            data: data
                        },
                        function(html){
                            document.getElementById('myFrame').contentWindow.location.reload();
                            document.getElementById('myFrameMenu').contentWindow.location.reload();
                            document.getElementById('myFrameFooter').contentWindow.location.reload();
                        });
                });
                jQuery('.code').on('paste', function(e) {
                    var data = JSON.parse(jQuery(this).attr('data-data'));
                    data.value = escapeHtmlBack(jQuery(this).text());
                    console.log(data.value);

                    jQuery.post('/wp-content/plugins/basinn_plugin/save_data.php',
                        {
                            action: 'update',
                            basinn: 'yes',
                            data: data
                        },
                        function(html){
                            document.getElementById('myFrame').contentWindow.location.reload();
                        });
                });

                jQuery('.editable').on('keydown', function(e) {
                    if (e.keyCode === 9) { // если нажата клавиша Tab
                        e.preventDefault();
                        jQuery(this).next('.editable').focus();
                    }
                });

                jQuery('.open').each(function () {
                    jQuery(this).click(function () {
                        jQuery.post('/wp-content/plugins/basinn_plugin/editor.php',
                            {
                                'id': jQuery(this).attr('data-id')
                            },
                            function(html){
                                var back = jQuery('<div>');
                                back.attr('style', 'display: flex;width: 100%;height: 100%;position: fixed;left: 0;top: 0;z-index: 1000;justify-content: center;align-items: center;');
                                back.append(html)
                                jQuery('body').append(back);
                                jQuery('#close_basinn_plugin_shortcode').click(function (){
                                    back.remove();
                                });
                                jQuery('.field_basinn_plugin_shortcode').each(function () {
                                    jQuery(this).keyup(function () {
                                        var name = jQuery(this).attr('data-name');
                                        var value = jQuery(this).val();
                                        var id = jQuery(this).attr('data-id');
                                        sendDataToServer(name, value, id);
                                        if(name == 'desc'){
                                            jQuery('#editor-basinn-'+id+' td').last().text(value);
                                        }
                                    });
                                });
                                jQuery('#code-iframe textarea').keyup(function () {
                                    var value = jQuery(this).val();
                                    var encodedValue = base64_encode(value);
                                    var id = jQuery(this).attr('data-id');
                                    sendDataToServer('code', encodedValue, id, function(html){
                                        document.getElementById('myFrameEditor').contentWindow.location.reload();
                                    });
                                });



                                applyHeight();
                            }
                        );
                        var block = jQuery(this).parent().find('.main');
                        block.show();
                    });
                });
                function base64_encode(value) {
                    return btoa(unescape(encodeURIComponent(value)));
                }
                function applyHeight() {
                    var parent = jQuery('#code-iframe');
                    var height = parent.height() - 10;
                    var code = parent.find('code');
                    var iframe = parent.find('iframe');
                    code.css('height', height*0.5);
                    iframe.css('height', height*0.5);
                    iframe[0].contentWindow.scrollTo(0, 0);
                }


                jQuery(window).on('resize', function() {
                    applyHeight();
                });


                function fnRemove(){
                    jQuery.post('/wp-content/plugins/game-lenta/chest_things.php',
                        {
                            'chest_things_id': jQuery(this).data('chest_things_id'),
                            'action': 'delete',
                        }, function (item) {
                            jQuery('#rowmy-'+item.chest_things_id).remove();

                        });
                };
                function fnRemoveInventory(){
                    jQuery.post('/wp-content/plugins/game-lenta/chest_things.php',
                        {
                            'inventory_id': jQuery(this).data('inventory_id'),
                            'action': 'delete',
                        }, function (item) {
                            document.location.href = document.location.href + '&tab=5';
                        });
                };

                jQuery(document).on('click', '.remove-button', fnRemove);
                jQuery(document).on('click', '.delete-inventory', fnRemoveInventory);
                function fnChangeChance(){
                    jQuery.post('/wp-content/plugins/game-lenta/chest_things.php',
                        {
                            'chest_things_id': jQuery(this).data('chest_things_id'),
                            'action': 'change_chance',
                            'chance': jQuery(this).text(),
                        });
                };
                jQuery(document).on('keyup', '.edit-chance', fnChangeChance);
                // Проверяем, есть ли параметр "tab" в URL

                jQuery('table').on('click', '.delete', function() {
                    var itemId = jQuery(this).data('item-id');
                    var table = jQuery(this).data('table');
                    jQuery.ajax({
                        url: '/wp-content/plugins/game-lenta/game-lenta.php',
                        type: 'POST',
                        data: {
                            action: 'delete_data',
                            table: table,
                            item_id: itemId
                        },
                        success: function(response) {
                            document.location.href = document.location.href;
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.log(textStatus, errorThrown);
                        }
                    });
                });
                function generateUniqueId() {
                    return Math.random().toString(36).substring(2) + Date.now().toString(36);
                };
                function saveEditable(th, name, image, chance, price){
                    th.keyup(function () {
                        if(!name){
                            name = jQuery(this).data('id');
                        }
                        if(!chance){
                            chance = jQuery(this).data('id');
                        }
                        if(!price){
                            price = jQuery(this).data('id');
                        }
                        jQuery(this).text();
                        sendInsertToServer(itemId, name, image, chance, price);
                    });
                };
                function addRow(){
                    const table = jQuery('#things-table');
                    const tbody = table.find('tbody');

                    const newRow = jQuery('<tr>');
                    const idCell = jQuery('<td data-id="new">');
                    const nameCell = jQuery('<td  contenteditable="true" data-id="new">');
                    const chanceCell = jQuery('<td  contenteditable="true" data-id="new">');
                    const priceCell = jQuery('<td  contenteditable="true" data-id="new">');
                    const imageCell = jQuery('<td data-id="new">');
                    idCell.text('new');
                    const image = jQuery('<img>', {
                        id: 'image_prev_new',
                        src: '#',
                        style: 'max-width: 75px;max-height: 75px;'
                    });

                    const inpu0 = jQuery('<input>', {
                        id: 'image_new',
                        type: 'file',
                        'data-item-id': 'new'
                    });

                    const preview = jQuery('<div>', {
                        id: 'image_preview_new'
                    });
                    const inqId = 'input_'+generateUniqueId();
                    const previewId = 'preview_'+generateUniqueId();
                    const script = jQuery('<script>', {
                        html:
                            'const '+inqId+' = document.getElementById("image_new");' +
                            'const '+previewId+' = document.getElementById("image_prev_new");' +
                            ''+inqId+'.addEventListener("change", function() {' +
                            '  const file = this.files[0];' +
                            '  const formData = new FormData();' +
                            '  formData.append("image", file);' +
                            '  formData.append("item_id", this.dataset.itemId);' +
                            '  const xhr = new XMLHttpRequest();' +
                            '  xhr.open("POST", "/wp-content/plugins/game-lenta/game-lenta.php");' +
                            '  xhr.send(formData);' +
                            '  xhr.onload = function() {' +
                            '    const response = JSON.parse(xhr.responseText);' +
                            '    if (response.success) {' +
                            '      '+previewId+'.src = response.image_url + "?" + Math.floor(Math.random() * 1000000);' +
                            '    } else {' +
                            '      alert("Ошибка загрузки картинки");' +
                            '    }' +
                            '  };' +
                            '});'
                    });

                    imageCell.append(image).append(inpu0).append(preview).append(script);

                    saveEditable(nameCell, true, false, false,false);
                    saveEditable(chanceCell, false, false, true, false);
                    saveEditable(priceCell, false, false, false, true);
                    newRow.append(idCell);
                    newRow.append(nameCell);
                    newRow.append(imageCell);
                    newRow.append(chanceCell);
                    newRow.append(priceCell);

                    tbody.prepend(newRow);
                };
                jQuery.noConflict();

                jQuery('#basinn_shortcode_add').click(function () {
                    showPopup();
                });
                function showPopup() {
                    var popup = jQuery('<div class="popup">' +
                        '<div class="popup-overlay"></div>' +
                        '<div class="popup-content">' +
                        '<h2>Название</h2>' +
                        '<input class="new_name" type="text" style="min-width: 300px;padding: 5px 10px;" >' +
                        '<div class="popup-buttons">' +
                        '<button class="cancel">Отмена</button>' +
                        '<button class="save">Сохранить</button>' +
                        '</div>' +
                        '</div>' +
                        '</div>');

                    popup.find('.popup-buttons .cancel').css({
                        'background-color': '#e74c3c'
                    });

                    popup.find('.popup-buttons .save').css({
                        'background-color': '#76b851'
                    });
                    popup.find('.save').on('click', function() {
                        jQuery.ajax({
                            url: '/wp-content/plugins/basinn_plugin/editor.php',
                            type: 'POST',
                            data: {
                                action: 'add',
                                name: jQuery('.new_name').val()
                            },
                            success: function(response) {
                                if(response.success) {
                                    document.location.href = document.location.href
                                        .replace('&tab=2', '')
                                        .replace('&tab=3', '')
                                        .replace('&tab=4', '')
                                        .replace('&tab=5', '')
                                        .replace('&tab=6', '')+'&tab=4';
                                }else{
                                    alert(response.error);
                                }
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                console.log(textStatus, errorThrown);
                            }
                        });
                    });

                    popup.find('.cancel').on('click', function() {
                        popup.remove();
                    });

                    popup.appendTo('body');

                    centerPopup(popup);
                    jQuery(window).on('resize', function() {
                        centerPopup(popup);
                    });
                    setTimeout(function() {
                        popup.find('.new_name').focus().val(function(i, val) {
                            return val + 'basinn_shortcode_'; // Добавляем пустую строку, чтобы установить курсор в конец строки
                        });

                    }, 100);

                    function centerPopup(popup) {
                        var windowHeight = jQuery(window).height();
                        var windowWidth = jQuery(window).width();
                        var popupHeight = popup.outerHeight();
                        var popupWidth = popup.outerWidth();
                        popup.css({
                            'top': (windowHeight - popupHeight) / 2,
                            'left': (windowWidth - popupWidth) / 2
                        });
                    };
                };
                function sendDataToServer(name, value, id, fn) {
                    jQuery.ajax({
                        url: '/wp-content/plugins/basinn_plugin/editor.php',
                        type: 'POST',
                        data: {
                            action: 'save',
                            name: name,
                            value: value,
                            id: id
                        },
                        success: function(response) {
                            console.log(response);
                            if (fn) {
                                fn(name, value, id);
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.log(textStatus, errorThrown);
                        }
                    });
                };
                function sendInsertToServer(itemId, name, image, chance, price) {
                    jQuery.ajax({
                        url: '/wp-content/plugins/game-lenta/game-lenta.php',
                        type: 'POST',
                        data: {
                            action: 'insert_data',
                            table: 'things',
                            name: name,
                            image: image,
                            chance: chance,
                            price: price,
                            item_id: itemId
                        },
                        success: function(response) {
                            console.log(response);
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.log(textStatus, errorThrown);
                        }
                    });
                };
                jQuery('.editable').each(function () {
                    jQuery(this).keyup(function () {
                        var column = jQuery(this).data('column');
                        var value = jQuery(this).text();
                        var itemId = jQuery(this).data('id');
                        var table = jQuery(this).data('table');
                        sendDataToServer(column, value, itemId, table);
                    });
                });
                jQuery('.active-checkbox').each(function () {
                    jQuery(this).change(function () {
                        var column = jQuery(this).data('column');
                        var itemId = jQuery(this).data('id');
                        var table = jQuery(this).data('table');
                        var value = jQuery(this).prop('checked') ? 1 : 0;

                        sendDataToServer(column, value, itemId, table);
                    });
                });
                jQuery('.processed-checkbox').each(function () {
                    jQuery(this).change(function () {
                        var column = jQuery(this).data('column');
                        var itemId = jQuery(this).data('id');
                        var table = jQuery(this).data('table');
                        var value = jQuery(this).prop('checked') ? 1 : 0;

                        sendDataToServer(column, value, itemId, table);
                    });
                });
                jQuery('.notice.notice-error').hide();
            })(jQuery);
        };
    </script>
    <?php
}
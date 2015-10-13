/* 
 * Copyright 2015 TRIAL.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
/*
 * Created on : 09/10/2015, 13:13:05
 * Author     : Matheus Leonardo dos Santos Martins
 */

var TRANSFORM_CONTEXT_MENU = 1;

function confirmationDialog(dialog, background, data, clickPositiveButton) {
    $(document).ready(function () {
        dialog.fadeIn();
        background.fadeIn();
        background.click(function () {
            dismissDialog(dialog, background);
        });
        dialog.children('#positive-button').click(function () {
            clickPositiveButton(data);
        });
        dialog.children('#negative-button').click(function () {
            dismissDialog(dialog, background);
        });
    });
}

function alertDialog(dialog, background) {
    $(document).ready(function () {
        dialog.fadeIn();
        background.fadeIn();
        background.click(function () {
            dismissDialog(dialog, background);
        });
        dialog.children('#neutral-button').click(function () {
            dismissDialog(dialog, background);
        });
    });
}

function dismissDialog(dialog, background) {
    $(document).ready(function () {
        dialog.fadeOut();
        background.fadeOut();
    });
}

function showPopup(target, background) {
    $(document).ready(function () {
        $('html, body').css('overflow-y', 'hidden');
        target.fadeIn(200).animate({top: 0}, 200);
        background.fadeIn(200);
        background.click(function () {
            closePopup(target, background);
        });
    });
}

function closePopup(target, background) {
    $(document).ready(function () {
        $('html, body').css('overflow-y', 'auto');
        target.animate({top: '100%'}, 200).fadeOut(200);
        background.fadeOut();
    });
}

function transformFloatingActionButton(fab, transform) {
    $(document).ready(function () {
        if (transform === TRANSFORM_CONTEXT_MENU) {
            fab.getElementsByTagName('i')[0].style.display = 'none';
            fab.style.padding = '0';
            fab.animate({borderRadius: '1px', right: '-30px', width: '370px'}, 400);
            fab.style.backgroundColor = '#eee';
            fab.children('section').fadeIn(400);
            fab.getElementsByTagName('section')[0].getElementsByTagName('ul')[0].querySelector('li:first-child').onclick = function () {
                restoreFloatingActionButton(fab, transform);
            };
        }
    });
}

function restoreFloatingActionButton(fab, restore) {
    $(document).ready(function () {
        if (restore === TRANSFORM_CONTEXT_MENU) {
            fab.children('section').fadeOut(0);
            fab.css('padding', '15px 21px');
            fab.animate({borderRadius: '60px', right: '0', width: '64px'}, 300);
            fab.css({'background': '#d50000'});
            fab.children('i').fadeIn(0);
        }
    });
}
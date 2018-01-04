/* 
 * (c) 2017 TRIAL.
 * Created on 15/06/2017, 22:40:20.
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

"use strict";

(function (window) {
    window.T = window.T || {};
    window.T.elements = window.T.elements || {};
    window.T.elements.TRIAL = window.T.elements.TRIAL || {};
    
    window.T.elements.TRL_BUTTON = 'trl-button';
    window.T.elements.TRL_CIRCLE_PERCENTAGE = 'trl-circle-percentage';
    window.T.elements.TRL_EDITABLE = 'trl-editable';
    window.T.elements.TRL_LOADING = 'trl-loading';
    window.T.elements.TRL_LOGIN_BUTTON = 'trl-login-button';
    window.T.elements.TRL_LOGO = 'trl-logo';
    window.T.elements.TRL_POPUP = 'trl-popup';
    window.T.elements.TRL_RATING = 'trl-rating';
    window.T.elements.TRL_SELECTABLE_ITEMS = 'trl-selectable-items';
    window.T.elements.TRL_SLIDESHOW = 'trl-slideshow';
    window.T.elements.TRL_SUGGESTIONS = 'trl-suggestions';
    window.T.elements.TRL_TABS = 'trl-tabs';
    
    var Polyfills = {
        WebComponents: 'https://cdnjs.cloudflare.com/ajax/libs/webcomponentsjs/0.7.24/webcomponents.min.js',
        classList: 'https://cdnjs.cloudflare.com/ajax/libs/classlist/1.1.20150312/classList.min.js'
    };
        
    if (!document.registerElement)
        window.T.load(Polyfills.WebComponents);
    
    window.T.elements.register = function (elements, callback) {
        if (elements === undefined)
            throw new TypeError("(T.register) Param isn't defined");
        var arr, is_e_str = typeof elements === 'string';
        if (!is_e_str && !(elements.constructor === Array))
            throw new TypeError("(T.register) Param must be a string or array");
        if (is_e_str && elements === "all")
            arr = [window.T.elements.TRL_BUTTON, window.T.elements.TRL_CIRCLE_PERCENTAGE, window.T.elements.TRL_EDITABLE, window.T.elements.TRL_LOADING, window.T.elements.TRL_LOGIN_BUTTON, window.T.elements.TRL_LOGO, window.T.elements.TRL_POPUP, window.T.elements.TRL_RATING, window.T.elements.TRL_SELECTABLE_ITEMS, window.T.elements.TRL_SLIDESHOW, window.T.elements.TRL_SUGGESTIONS, window.T.elements.TRL_TABS];
        else
            arr = is_e_str ? [elements] : elements;
        
        if (document.registerElement)
            reg();
        else
            window.T.load(Polyfills.WebComponents, function () {
                reg();
            });
        
        function reg() {
            for (var i = 0, t = arr.length; i < t; i++)
                arr[i] = window.T.ROOT + "T/elements/" + arr[i];
            window.T.load(arr, callback);
        }
    };
})(window);
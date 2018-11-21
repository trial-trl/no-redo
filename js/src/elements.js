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

'use strict';

window.T = (function (T) {
  
  var polyfill = {
    has     : false,
    loading : false,
    loaded  : false
  };

  T.elements = {};
  T.elements.TRIAL = T.elements.TRIAL || {};
  
  T.elements.TRL_BUTTON = 'trl-button';
  T.elements.TRL_CIRCLE_PERCENTAGE = 'trl-circle-percentage';
  T.elements.TRL_EDITABLE = 'trl-editable';
  T.elements.TRL_LOADING = 'trl-loading';
  T.elements.TRL_LOGIN_BUTTON = 'trl-login-button';
  T.elements.TRL_LOGO = 'trl-logo';
  T.elements.TRL_PAGINATION = 'trl-pagination';
  T.elements.TRL_POPUP = 'trl-popup';
  T.elements.TRL_RATING = 'trl-rating';
  T.elements.TRL_SELECTABLE_ITEMS = 'trl-selectable-items';
  T.elements.TRL_SLIDESHOW = 'trl-slideshow';
  T.elements.TRL_SUGGESTIONS = 'trl-suggestions';
  T.elements.TRL_TABS = 'trl-tabs';
  T.elements.ALL = [T.elements.TRL_BUTTON, T.elements.TRL_CIRCLE_PERCENTAGE, T.elements.TRL_EDITABLE, T.elements.TRL_LOADING, T.elements.TRL_LOGIN_BUTTON, T.elements.TRL_LOGO, T.elements.TRL_PAGINATION, T.elements.TRL_POPUP, T.elements.TRL_RATING, T.elements.TRL_SELECTABLE_ITEMS, T.elements.TRL_SLIDESHOW, T.elements.TRL_SUGGESTIONS, T.elements.TRL_TABS];
  
  T.elements.polyfill = function () {
    
    if ('registerElement' in document)
      return false;
    
    T.load(T.BOWER + '/document-register-element/build/document-register-element.js', function () {
      polyfill.has = polyfill.loaded = true;
      window.dispatchEvent(new CustomEvent('CustomElementsReady'));
    });
    return true;
    
  };
  
  T.elements.custom = function (tag, options, obj) {
    
    var need_polyfill = T.elements.polyfill();

    if (need_polyfill === false || polyfill.loaded === true)
      register();
    else
      window.addEventListener('CustomElementsReady', register);

    function register() {
      
      try {
        
        T.elements[obj] = document.registerElement(tag, options);
        
      } catch (e) {}
      
    }
    
  };

  T.elements.register = function (elements, callback) {
    
    if (elements === undefined)
      throw new TypeError('(T.register) Param isn\'t defined');
    
    var is_e_str = typeof elements === 'string';

    if (!is_e_str && elements.constructor !== Array)
      throw new TypeError('(T.register) Param must be a string or array');
    
    var arr = elements === 'all' 
              ? T.elements.ALL
              : (is_e_str ? [elements] : elements);

    for (var i = 0, t = arr.length; i < t; i++) {
      
      if (arr[i] === T.elements.TRL_LOADING || arr[i] === T.elements.TRL_CIRCLE_PERCENTAGE)
        arr.push(T.CSS + '/elements/' + arr[i] + '.css');
      
      arr[i] = T.API + '/elements/' + arr[i] + '.js';
      
    }
    
    T.load(arr, callback);
    
  };
    
  return T;
  
})(window.T || {});
    
window.dispatchEvent(new CustomEvent('T.elements.loaded'));
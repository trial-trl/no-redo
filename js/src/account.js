/* 
 * (c) 2017 TRIAL.
 * Created on 16/06/2017, 23:01:24.
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
  
  var _auth;
    
  function _(c, cookies) {
    var m = [];
    if (!T.Utils)
      m.push('utils');
    if (cookies === true && !T.Cookies)
      m.push('cookies');
    if (m.length > 0)
      T.load(m, c);
    else
      c();
  }

  T.Account = {
    URL: 'https://conta.trialent.com',
    LOGIN: 'https://conta.trialent.com/api/login',
    LOGOUT: 'https://conta.trialent.com/api/logout',
    open: function (options) {
      var url = this.URL + '/entrar/' + btoa(JSON.stringify(options));
      if (typeof options.popup === 'boolean' && !options.popup) {
        location.href = url;
      } else if (typeof options.popup === 'string') {
        switch (options.popup) {
          case 'in-page':
            var account_page = document.createElement('iframe'), exist = document.getElementById('trl-account-page');
            if (exist) {
              document.body.removeChild(exist);
            }
            account_page.id = 'trl-account-page';
            account_page.src = url;
            document.body.appendChild(account_page);
            T(account_page).Standards.open({fit_screen: true});
            break;
          case 'window':
            window.open(url + (options.hasOwnProperty('keep') && options.keep ? '#' + options.keep : ''), 'trl-account-page', 'resizable,scrollbars,width=413,height=554');
            break;
        }
      }
    },
    setAuthorization: function (accountId) {
      if (typeof accountId !== 'number') {
        throw new TypeError('accountId must be a number');
      }
      _auth = accountId;
    },
    getProfile: function (fields, onsuccess) {
      if (!_auth) {
        throw new SyntaxError('Must set authorization before. Call T.Account.setAuthorization(accountId).');
      }
      var url = this.URL + '/api/profile?fields=' + fields.join(',');
      _(function () {
        T.Utils.ajax({
          url: url,
          headers: [
            'Authorization: Client-ID ' + _auth
          ],
          response: 'json',
          onloadend: function (e) {
            onsuccess(e.target.response);
          }
        });
      });
    },
    login: function (options) {
      this.open(options);
    },
    logout: function (after) {
      _(function () {
        T.Utils.ajax({
          url: T.Account.LOGOUT,
          onloadend: function () {
            var refresh = true;
            if (after)
              refresh = after();
            if (refresh)
              location.reload();
          }
        });
      }, true);
    },
    edit: function (options) {
      var url = this.URL + '/perfil/?mode=edition';
      options.popup = options.popup || 'window';
      if (typeof options.popup === 'boolean' && !options.popup) {
        location.href = url;
      } else if (typeof options.popup === 'string') {
        switch (options.popup) {
          case 'in-page':
            var account_page = document.createElement('iframe'), exist = document.getElementById('trl-account-profile-page');
            if (exist) {
              document.body.removeChild(exist);
            }
            account_page.id = 'trl-account-profile-page';
            account_page.src = url;
            document.body.appendChild(account_page);
            T(account_page).Standards.open({fit_screen: true});
            break;
          case 'window':
            window.open(url, 'trl-account-profile-page', 'resizable,scrollbars,width=413,height=554');
            if (options.onfinishedition) {
              window.addEventListener('message', onprofileupdated);
              
              function onprofileupdated(e) {
                if (e.data.eventType === 'T.Account.Event.profileUpdated') {
                  options.onfinishedition();
                  window.removeEventListener('message', onprofileupdated);
                }
              }
            }
            break;
        }
      }
    }
  };

  return T;
    
})(window.T || {});
    
window.dispatchEvent(new CustomEvent('T.Account.loaded'));
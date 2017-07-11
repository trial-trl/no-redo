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

(function (window) {
    function missing(c, cookies) {
        var m = [];
        if (!window.T.Utils)
            m.push("utils");
        if (cookies === true && !window.T.Cookies)
            m.push("cookies");
        if (m.length > 0)
            window.T.load(m, c);
        else
            c();
    }
    
    function service(code, options) {
        missing(function () {
            var data = {r: code + "auth", id: options.id};
            if (options.type)
                data.type = options.type;
            window.T.Utils.ajax({
                data: data,
                url: T.Account.url + "/action",
                response: "json",
                onloadend: options.onloadend
            });
        });
    }
    
    window.T = window.T || {};
    window.T.Account = {
        url: "http://account.trialent.com",
        open: function (options) {
            var url = T.Account.url + "/entrar/" + btoa(JSON.stringify(options));
            if (typeof options.popup === "boolean" && !options.popup) {
                location.href = url;
            } else if (typeof options.popup === "string") {
                switch (options.popup) {
                    case "in-page":
                        var account_page = document.createElement("iframe"), exist = document.getElementById("trl-account-page");
                        if (exist) {
                            document.body.removeChild(exist);
                        }
                        account_page.id = "trl-account-page";
                        account_page.src = url;
                        document.body.appendChild(account_page);
                        T(account_page).Standards.open({fit_screen: true});
                        break;
                    case "window":
                        window.open(T.Account.url + "/entrar/" + btoa(JSON.stringify(options)) + (options.hasOwnProperty("keep") ? "#" + options.keep : ""), "trl-account-page", "resizable,scrollbars,width=413,height=554");
                        break;
                }
            }
        },
        logout: function (after) {
            missing(function () {
                window.T.Utils.ajax({
                    data: {r: 'lgout'},
                    url: T.Account.url + "/action",
                    onloadend: function () {
                        window.T.Cookies('trl_').deleteAll();
                        if (after)
                            after();
                        else
                            location.reload();
                    }
                });
            }, true);
        },
        Clicker: {
            authenticate: function (options) {
                service(window.T.App.CLICKER, options);
            }
        },
        NoRedo: {
            authenticate: function (options) {
                service(window.T.App.NO_REDO, options);
            }
        },
        StreetRace: {
            authenticate: function (options) {
                service(window.T.App.STREET_RACE, options);
            }
        },
        OpportunityNow: {
            authenticate: function (options) {
                service(window.T.App.OPPORTUNITY_NOW, options);
            }
        },
        FixIt: {
            authenticate: function (options) {
                service(window.T.App.FIX_IT, options);
            }
        }
    };
})(window);
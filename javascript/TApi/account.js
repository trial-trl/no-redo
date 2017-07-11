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
                url: "/Library/account/action",
                response: "json",
                onloadend: options.onloadend
            });
        });
    }
    
    window.T = window.T || {};
    // 17/10/2016, 19:28:03
    window.T.Account = {
        open: function (options) {
            // 17/10/2016, 15:51:30 - 15:57:01: added multiple popup types: a lot of ways to use TRIAL Account login, in-page & window
            var url = "/Library/account/entrar/" + btoa(JSON.stringify(options));
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
                        // added 'options.keep' on 28/04/2017, 15:24:54 
                        window.open("/Library/account/entrar/" + btoa(JSON.stringify(options)) + (options.hasOwnProperty("keep") ? "#" + options.keep : ""), "trl-account-page", "resizable,scrollbars,width=413,height=554");
                        break;
                }
            }
        },
        logout: function (after) {
            missing(function () {
                // 18/10/2015, 00:42:33 => added after callback and updated the usage of deprecated T library codes
                window.T.Utils.ajax({
                    data: {r: 'lgout'},
                    url: "/Library/account/action",
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
        // added on 21/10/2016, 18:09:32
        OpportunityNow: {
            authenticate: function (options) {
                service(window.T.App.OPPORTUNITY_NOW, options);
            }
        },
        // added on 22/11/2016, 18:58:30
        FixIt: {
            authenticate: function (options) {
                service(window.T.App.FIX_IT, options);
            }
        }
    };
})(window);
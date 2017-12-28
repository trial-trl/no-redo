/* 
 * (c) 2017 TRIAL.
 * Created on 18/06/2017, 21:03:21.
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
    window.T.elements.TRIAL.LoginButton = document.registerElement(window.T.elements.TRL_LOGIN_BUTTON, {
        prototype: Object.create(HTMLButtonElement.prototype, {
            createdCallback: {
                value: function () {
                    this.setup();
                }
            },
            attachedCallback: {
                value: function () {
                    this.setup();
                }
            },
            setup: {
                value: function () {
                    var background = document.createElement("div"),
                            logo = document.createElement("trl-logo"),
                            text = document.createElement("div");
                    background.style.position = "absolute";
                    background.style.top = 0;
                    background.style.cssFloat = "left";
                    background.style.padding = "5px";
                    background.style.width = "100%";
                    background.style.height = "100%";
                    background.style.background = "linear-gradient(60deg, #fff 35px, transparent 36px)";
                    logo.compact = true;
                    logo.scale = ".2";
                    logo.style.position = "relative";
                    logo.style.cssFloat = "left";
                    logo.style.margin = "2.5px";
                    text.style.cssFloat = "left";
                    text.style.margin = "0 5px 0 10px";
                    text.style.lineHeight = "30px";
                    text.innerHTML = "Entrar com a Conta TRIAL";
                    this.innerHTML = null;
                    this.style.position = "relative";
                    this.style.cssFloat = "left";
                    this.style.cursor = "pointer";
                    this.style.margin = "4px";
                    this.style.backgroundColor = "#24c742";
                    this.style.color = "#fff";
                    this.style.font = "200 16px \"Segoe UI\", arial, sans-serif";
                    this.style.boxShadow = "0 2px 2px rgba(0, 0, 0, .13)";
                    this.onclick = function () {
                        window.T.Account.open({
                            popup: "window"
                        });
                    };
                    this.appendChild(background);
                    this.appendChild(logo);
                    this.appendChild(text);
                }
            }
        })
    });
})(window);
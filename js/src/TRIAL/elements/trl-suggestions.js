/* 
 * (c) 2017 TRIAL.
 * Created on 18/06/2017, 21:05:24.
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
    window.T.elements.custom(window.T.elements.TRL_SUGGESTIONS, {
        prototype: Object.create(HTMLDivElement.prototype, {
            createdCallback: {
                value: function () {
                    this.setup();
                }
            },
            attributeChangedCallback: {
                value: function (attr_name, old_value, new_value) {
                    if (attr_name === "trl-store-accepted-suggestions") {
                        if (new_value === "true") {
                            this.open();
                        } else {
                            this.close();
                        }
                    }
                }
            },
            attachedCallback: {
                value: function () {
                    this.setup();
                }
            },
            accepted: {
                value: []
            },
            opened: {
                set: function (opened) {
                    this.setAttribute("trl-opened", opened ? "true" : "false");
                },
                get: function () {
                    return this.getAttribute("trl-opened") === "true";
                }
            },
            close: {
                value: function () {
                    if (this.opened)
                        this.opened = false;
                }
            },
            open: {
                value: function () {
                    if (!this.opened)
                        this.opened = true;
                }
            },
            storeAcceptedSuggestions: {
                set: function (store) {
                    this.setAttribute("trl-store-accepted-suggestions", store.toString());
                },
                get: function () {
                    return this.getAttribute("trl-store-accepted-suggestions") === "true";
                }
            },
            onacceptsuggestion: {
                set: function (callback) {
                    this.addEventListener("acceptsuggestion", callback);
                }
            },
            setup: {
                value: function () {
                    var that = this,
                            observer = new MutationObserver(function () {
                                x();
                            });
                    observer.observe(this, {
                        childList: true,
                        attributes: true
                    });
                    function x() {
                        for (var i = 0, total = that.children.length; i < total; i++) {
                            var suggestion = that.children[i];
                            suggestion.onclick = function (e) {
                                if (that.storeAcceptedSuggestions) {
                                    that.accepted.push(e.target);
                                    that.removeChild(e.target);
                                }
                                that.dispatchEvent(new CustomEvent("acceptsuggestion", {
                                    detail: {
                                        suggestion: e.target
                                    }
                                }));
                                that.close();
                            };
                        }
                    }
                }
            }
        })
    }, window.T.elements.Suggestions);
})(window);
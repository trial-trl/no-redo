/* 
 * (c) 2017 TRIAL.
 * Created on 18/06/2017, 20:52:01.
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

// moved all from T.php to trl-tabs.js on 18/06/2017, 20:53:37
(function (window) {
    window.T.elements.TabsContents = document.registerElement("trl-tabs-contents", {
        prototype: Object.create(HTMLDivElement.prototype, {
            createdCallback: {
                value: function () {
                    //this.style.cssFloat = "left";
                    //this.style.width = "100%";
                }
            }
        })
    });

    window.T.elements.TabContent = document.registerElement("trl-tab-content", {
        prototype: Object.create(HTMLDivElement.prototype, {
            createdCallback: {
                value: function () {
                    if (!this.getAttribute("index") || this.getAttribute("index") != 1) { // 19:34:20 - 19:35:10
                        this.style.display = "none";
                    }
                    this.style.width = "100%";
                }
            }
        })
    });

    /*
     * 22/10/2016
     *      14:31:22 => tab.onclick changed to addEventListener("click", function)
     *      14:41:30 - 15:38:52 => added trl-default: it specifies which tab is default, it means, tab that'll will be opened in setup. Values are: "none" => no default tab, "integer" => default tab index.
     */
    window.T.elements.Tabs = document.registerElement(window.T.elements.TRL_TABS, {
        prototype: Object.create(HTMLDivElement.prototype, {
            createdCallback: {
                value: function () {
                    var tabs = this;
                    // Last edition: 17/08/2016, 23:44:35
                    var observer = new MutationObserver(function () {
                        tabs.setup();
                    });
                    observer.observe(tabs, {
                        attributes: true,
                        childList: true
                    });
                    window.addEventListener("load", function () {
                        if (tabs.children.length > 0)
                            tabs.setup();
                    }, false);
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
                    var tabs_for = document.getElementById(this.getAttribute("trl-for")),
                            default_tab = this.getAttribute("trl-default") || 0;
                    for (var i = 0, total = this.children.length; i < total; i++) {
                        var tab = this.children[i];
                        tab.onclick = (function (index, tabs) {
                            return function () {
                                var tab = tabs.children[index];
                                if (!tab.classList.contains("current")) {
                                    var current_displayed = tabs_for.querySelector(".current"),
                                            display = tabs_for.children[index];
                                    // Reinserted current_displayed condition on 25/08/2016 ~16:47:15;
                                    if (current_displayed) {
                                        animHide(current_displayed);
                                    }
                                    try { // 21:08:57
                                        animShow(display);
                                    } catch (e) {}
                                    try {
                                        tabs.querySelector(".current").classList.remove("current");
                                    } catch (e) {}
                                    tabs.dispatchEvent(new CustomEvent("selecttab", {
                                        detail: {
                                            index: index,
                                            tab: tab,
                                            display: display
                                        }
                                    }));
                                    tab.classList.add("current");
                                }
                            };
                        })(i, this);
                    }
                    if (default_tab === 0 || default_tab !== "none")
                        this.children[parseInt(default_tab)].click();

                    function animShow(el) {
                        if (!T.Animation)
                            T.load("design", anim);
                        else
                            anim();
                        function anim() {
                            var original_width = +(window.getComputedStyle(el, null).getPropertyValue("width").replace("px", ""));
                            el.style.width = original_width + "px";
                            el.style.position = "absolute";
                            el.style.display = "";
                            el.style.opacity = 0;
                            window.T.Animation.start({
                                duration : 100,
                                delta : function (p) {
                                    return p;
                                },
                                step : function (delta) {
                                    el.style.opacity = delta;
                                }
                            });
                            setTimeout(function () {
                                el.style.position = "";
                            }, 101);
                            el.classList.add("current");
                        }
                    }

                    function animHide(el) {
                        if (!T.Animation)
                            T.load("design", anim);
                        else
                            anim();
                        function anim() {
                            el.style.position = "absolute";
                            window.T.Animation.start({
                                duration : 100,
                                delta : function (p) {
                                    return p;
                                },
                                step : function (delta) {
                                    el.style.opacity = 1 - delta;
                                }
                            });
                            setTimeout(function () {
                                el.style.position = "";
                                el.style.display = "none";
                            }, 101);
                            el.classList.remove("current");
                        }
                    }
                }
            }
        }),
        extends: "nav"
    });
        
    window.T.elements.Tab = document.registerElement("trl-tab", {
        prototype: Object.create(HTMLButtonElement.prototype, {
            createdCallback: {
                value: function () {
                    this.style.webkitUserSelect = "none";
                    this.style.mozUserSelect = "none";
                    this.style.webkitUserSelect = "none";
                    this.style.userSelect = "none";
                }
            }
        })
    });
})(window);
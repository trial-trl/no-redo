/* 
 * (c) 2017 TRIAL.
 * Created on 18/06/2017, 20:56:33.
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
    window.T.elements.SelectableItems = document.registerElement(window.T.elements.TRL_SELECTABLE_ITEMS, {
        prototype: Object.create(HTMLDivElement.prototype, {
            createdCallback: {
                value: function () {
                    var that = this;
                    this.active = false;
                    this.selected = 0;

                    this.setup();

                    var observer = new MutationObserver(observerCallback);
                    observer.observe(this, {
                        childList: true
                    });

                    function observerCallback(records, instance) {
                        that.setup();
                    }
                }
            },
            attachedCallback: {
                value: function () {
                    this.setup();
                }
            },
            attributeChangedCallback: {
                value: function (attr_name, old_value, new_value) {
                    if (attr_name === "trl-state") {
                        if (new_value === "active") {
                            this.activeSelection();
                        } else {
                            this.desactiveSelection();
                        }
                    }
                }
            },
            setup: {
                value: function () {
                    var that = this;
                    for (var i = 0, total = this.children.length; i < total; i++) {
                        this.children[i].onclick = function (e) {
                            that.toggleItem(e);
                        };
                    }
                    if (this.state === "active") {
                        this.activeSelection();
                    } else {
                        this.desactiveSelection();
                    }
                }
            },
            length: {
                get: function () {
                    return this.children.length;
                }
            },
            state: {
                get: function () {
                    return this.getAttribute("trl-state");
                },
                set: function (state) {
                    this.setAttribute("trl-state", state);
                }
            },
            isActive: {
                value: function () {
                    return this.active;
                }
            },
            activeSelection: {
                value: function () {
                    this.active = true;
                    this.setAttribute("trl-state", "active");

                }
            },
            desactiveSelection: {
                value: function () {
                    this.active = false;
                    this.setAttribute("trl-state", "desactive");
                }
            },
            selectAll: {
                value: function () {
                    for (var i = 0, total = this.children.length; i < total; i++) {
                        this.selectItem({target: this.children[i]});
                    }
                }
            },
            deselectAll: {
                value: function () {
                    for (var i = 0, total = this.children.length; i < total; i++) {
                        this.deselectItem({target: this.children[i]});
                    }
                }
            },
            toggleItem: {
                value: function (e) {
                    var that = this,
                            item = e.target;
                    if (that.isActive()) {
                        that.selected += item.classList.contains("selected") ? -1 : 1;
                        item.classList.toggle("selected");
                        that.dispatchEvent(new CustomEvent("selectitem", {
                            detail: {
                                selected: item.classList.contains("selected"),
                                length: that.selected,
                                item: item
                            }
                        }));
                    }
                }
            },
            selectItem: {
                value: function (e) {
                    var that = this,
                            item = e.target;
                    if (that.isActive()) {
                        if (!item.classList.contains("selected")) {
                            that.selected++;
                            item.classList.add("selected");
                            that.dispatchEvent(new CustomEvent("selectitem", {
                                detail: {
                                    selected: true,
                                    length: that.selected,
                                    item: item
                                }
                            }));
                        }
                    }
                }
            },
            deselectItem: {
                value: function (e) {
                    var that = this,
                            item = e.target;
                    if (that.isActive()) {
                        if (item.classList.contains("selected")) {
                            that.selected--;
                            item.classList.remove("selected");
                            that.dispatchEvent(new CustomEvent("selectitem", {
                                detail: {
                                    selected: false,
                                    length: that.selected,
                                    item: item
                                }
                            }));
                        }
                    }
                }
            },
            selectedItems: {
                get: function () {
                    return this.querySelectorAll(".selected");
                }
            },
            removeSelectedItems: {
                value: function () {
                    var selectedItems = this.selectedItems;
                    for (var i = 0, total = selectedItems.length; i < total; i++) {
                        this.removeChild(selectedItems[i]);
                    }
                }
            }
        })
    });
})(window);
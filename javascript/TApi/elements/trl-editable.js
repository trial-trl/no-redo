/* 
 * (c) 2017 TRIAL.
 * Created on 18/06/2017, 20:54:48.
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
    /* 
     * 21/05/2017
     *      21:17:16 - 21:22:38 => Due to memory leaks caused by MutationObserver, it gaves place to update(), which is the old observerCallback() in a trl-editable method
     *      23:05:26 => Changes in code, calling some function with call() method to maintain context
     */
    window.T.elements.Editable = document.registerElement(window.T.elements.TRL_EDITABLE, {
        prototype: Object.create(HTMLDivElement.prototype, {
            createdCallback: {
                value: function () {
                    this.init();
                }
            },
            attachedCallback: {
                value: function () {
                    this.init();
                }
            },
            attributeChangedCallback: {
                value: function (attr_name, old_value, new_value) {
                    if (attr_name === "trl-editing") {
                        this.isEditing = new_value.toLowerCase() === "true";
                        this.activeEditMode = new_value.toLowerCase() === "true";
                    }
                    if (attr_name === "trl-saving") {
                        if (old_value !== new_value && new_value === "false")
                            this.activeEditMode = false;
                    }
                    if (attr_name === "trl-placeholder") {
                        if (old_value !== new_value)
                            this.placeholder = new_value;
                    }
                }
            },
            update: {
                value: function () {
                    this.undefined_innerHTML = this.innerHTML === undefined || this.innerHTML === null || this.innerHTML === "" || this.innerHTML === "Definir valor" || this.innerHTML === "Definir " + this.title;
                    if (this.undefined_innerHTML)
                        this.innerHTML = this.placeholder || "Definir " + (this.title || "valor");
                }
            },
            saving: {
                get: function () {
                    return this.isSaving;
                },
                set: function (value) {
                    this.isSaving = value;
                    this.setAttribute("trl-saving", value.toString());
                }
            },
            placeholder: {
                get: function () {
                    return this.placeholder_text;
                },
                set: function (value) {
                    this.placeholder_text = value;
                    this.setAttribute("trl-placeholder", value);
                }
            },
            init: {
                value: function () {
                    if (this.hasAttribute("trl-placeholder"))
                        this.placeholder = this.getAttribute("trl-placeholder");
                    this.content = this.getAttribute("trl-content");
                    this.clickable = this.hasAttribute("trl-clickable") ? (this.getAttribute("trl-clickable").toLowerCase() === "true") : true;
                    this.keyControl = this.hasAttribute("trl-key-control") ? (this.getAttribute("trl-key-control").toLowerCase() === "true") : true;
                    if (this.content) {
                        var oneditname = this.getAttribute("trl-onedit");
                        if (oneditname) {
                            var onedit = window[oneditname];
                            if (typeof onedit === "function")
                                this.addEventListener("edit", function (e) {
                                    onedit(e);
                                });
                        }
                        if (this.clickable) {
                            try {
                                this.onclick = function () {
                                    this.activeEditMode = true;
                                };
                            } catch (e) {}
                        }
                    }
                    this.update();
                }
            },
            activeEditMode: {
                set: function (active) {
                    var that = this;

                    if (active)
                        activeEditMode.call(this);
                    else
                        finishEdition.call(this);

                    function activeEditMode() {
                        var s = document.createElement(this.content === "select" || this.content === "textarea" ? this.content : "input");
                        copyAttrs.call(this, s);
                        this.update();
                        this.original_value = this.undefined_innerHTML || this.innerHTML === this.placeholder ? "" : this.innerHTML;
                        switch (this.content) {
                            case 'select':
                                var select_data = JSON.parse(atob(this.getAttribute("data-select")));
                                for (var i = 0, total = select_data.length; i < total; i++) {
                                    var data = select_data[i],
                                            option = document.createElement("option");

                                    option.value = data.value;
                                    option.innerHTML = data.innerHTML;
                                    if (data.innerHTML === this.original_value) {
                                        option.selected = true;
                                    }

                                    s.appendChild(option);
                                }
                                s.style.width = "auto";
                                break;
                            case 'textarea':
                                s.placeholder = this.placeholder;
                                s.innerHTML = this.original_value;
                                s.onclick = function (e) {
                                    e.stopPropagation();
                                };
                                s.style.width = "auto";
                                break;
                            default:
                                s.type = this.content;
                                s.placeholder = this.title || "";
                                s.value = this.original_value;
                                s.onclick = function (e) {
                                    e.stopPropagation();
                                };
                                s.style.width = this.offsetWidth + "px";
                        }
                        if (this.keyControl) {
                            s.onkeydown = function (e) {
                                switch (e.keyCode) {
                                    case 13:
                                        save.call(that, e);
                                        break;
                                }
                            };
                        }
                        s.onblur = function (e) {
                            save.call(that, e);
                        };
                        s.style.minWidth = this.offsetWidth + "px";
                        this.onclick = null;
                        this.innerHTML = "";
                        this.appendChild(s);
                        s.focus();
                    };

                    function save(e) {
                        if (e.target.nodeName === "SELECT") {
                            this.editing_value = e.target.options[e.target.selectedIndex].innerHTML;
                        } else {
                            this.editing_value = e.target.value;
                        }
                        if (this.editing_value !== this.original_value) {
                            this.saving = true;
                        }
                        this.setAttribute("trl-editing", "false");
                    };

                    function finishEdition() {
                        var element = this.children[0];
                        if (this.saving && this.editing_value !== this.original_value) {
                            try {
                                this.dispatchEvent(new CustomEvent("edit", {
                                    detail: {
                                        value: element.value
                                    }
                                }));
                            } catch (e) {}
                        } else {
                            this.saving = false;
                            desactiveEditMode.call(this);
                        }
                    }

                    function desactiveEditMode() {
                        if (!this.saving) {
                            this.original_value = this.editing_value;
                            if (this.clickable) {
                                this.onclick = function () {
                                    this.setAttribute("trl-editing", "true");
                                };
                            }
                            this.innerHTML = this.original_value;
                            this.update();
                        }
                    };

                    function copyAttrs(to) {
                        if (this.hasAttributes()) {
                            for (var attr, i = 0, attrs = this.attributes, total = attrs.length; i < total; i++) {
                                attr = attrs[i];
                                if (attr.name !== "id" && attr.name !== "class" && attr.name !== "name" && attr.name.indexOf("trl-") === -1 && attr.name.indexOf("data-") === -1) {
                                    to.setAttribute(attr.name, attr.value);
                                }
                            }
                        }
                    }
                }
            }
        })
    });
})(window);
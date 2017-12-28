/* 
 * (c) 2017 TRIAL.
 * Created on 18/06/2017, 20:58:17.
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
    window.T.elements.Popup = document.registerElement(window.T.elements.TRL_POPUP, {
        prototype: Object.create(HTMLDivElement.prototype, {
            createdCallback: {
                value: function () {
                    window.T.load(["design", "utils"]);
                }
            },
            attributeChangedCallback: {
                value: function (attr_name, old_value, new_value) {
                    if (new_value !== old_value) {
                        switch (attr_name) {
                            case "trl-show":
                                if (new_value)
                                    this.open();
                                else
                                    this.close();
                                break;
                        }
                    }
                }
            },
            CONTROLLER_POSITIVE: {
                configurable: false,
                enumerable: false,
                writable: false,
                value: "positive"
            },
            CONTROLLER_NEGATIVE: {
                configurable: false,
                enumerable: false,
                writable: false,
                value: "negative"
            },
            ALIGNMENT_CENTER_HORIZONTAL: {
                configurable: false,
                enumerable: false,
                writable: false,
                value: 1
            },
            ALIGNMENT_CENTER_VERTICAL: {
                configurable: false,
                enumerable: false,
                writable: false,
                value: 2
            },
            ALIGNMENT_CENTER: {
                configurable: false,
                enumerable: false,
                writable: false,
                value: 1 | 2
            },
            ALIGNMENT_ABSOLUTE: {
                configurable: false,
                enumerable: false,
                writable: false,
                value: 4
            },
            ALIGNMENT_FIT_SCREEN: {
                configurable: false,
                enumerable: false,
                writable: false,
                value: 8
            },
            getBackground: {
                value: function () {
                    if (!this.__bg)
                        this.appendChild(this.background);
                    else
                        this.appendChild(this.__bg);
                    return this.__bg;
                }
            },
            background: {
                get: function () {
                    if (!this.__bg)
                        this.__bg = this.querySelector("[background]");
                    if (!(!!this.__bg)) {
                        this.__bg = document.createElement("div");
                        this.__bg.setAttribute("background", "");
                    }
                    return this.__bg;
                }
            },
            getContent: {
                value: function () {
                    if (!this.__content)
                        this.appendChild(this.content);
                    else
                        this.appendChild(this.__content);
                    return this.__content;
                }
            },
            content: {
                get: function () {
                    if (!this.__content)
                        this.__content = this.querySelector("[content]");
                    if (!(!!this.__content)) {
                        this.__content = document.createElement("div");
                        this.__content.setAttribute("content", "");
                        for (var i = this.children.length - 1, t = 0; i >= t; i--)
                            this.__content.insertBefore(this.children[i], this.__content.firstChild);
                    }
                    return this.__content;
                }
            },
            outside_click: {
                set: function (b) {
                    if (typeof b !== "boolean")
                        throw new TypeError("trl-popup outside click argument must be a boolean value");
                    this.__outside_click = b;
                },
                get: function () {
                    return this.__outside_click || true;
                }
            },
            autodismiss: {
                set: function (b) {
                    if (typeof b !== "boolean")
                        throw new TypeError("trl-popup autodismiss argument must be a boolean value");
                    this.__autodismiss = b;
                },
                get: function () {
                    return this.__autodismiss || true;
                }
            },
            setAlignment: {
                value: function (a) {
                    if (a !== this.ALIGNMENT_CENTER_HORIZONTAL && a !== this.ALIGNMENT_CENTER_VERTICAL && a !== this.ALIGNMENT_CENTER && a !== this.ALIGNMENT_ABSOLUTE && a !== this.ALIGNMENT_FIT_SCREEN)
                        throw new TypeError("trl-popup alignment must be one of the following values: self::ALIGNMENT_CENTER_HORIZONTAL | self.ALIGNMENT_CENTER_VERTICAL | self.ALIGNMENT_CENTER | self.ALIGNMENT_ABSOLUTE | self.ALIGNMENT_FIT_SCREEN");
                    this.__alignment |= a;
                }
            },
            getAlignment: {
                value: function () {
                    if (!this.__alignment) 
                        this.setAlignment(this.ALIGNMENT_CENTER);
                    return this.__alignment;
                }
            },
            freeze: {
                set: function (b) {
                    if (typeof b !== "boolean")
                        throw new TypeError("trl-popup freeze page argument must be a boolean value");
                    this.setAttribute("trl-freeze", b);
                },
                get: function () {
                    return this.hasAttribute("trl-freeze") ? this.getAttribute("trl-freeze") === "true" : true;
                }
            },
            setController: {
                value: function (which, element) {
                    if (which !== this.CONTROLLER_POSITIVE && which !== this.CONTROLLER_NEGATIVE)
                        throw new TypeError("trl-popup setController() argument must be one of the following values: self::CONTROLLER_POSITIVE | self::CONTROLLER_NEGATIVE");
                    if (!this.__controller)
                        this.__controller = [];
                    this.__controller[which] = element;
                }
            },
            getController: {
                value: function (which) {
                    if (which !== this.CONTROLLER_POSITIVE && which !== this.CONTROLLER_NEGATIVE)
                        throw new TypeError("trl-popup getController() argument must be one of the following values: self::CONTROLLER_POSITIVE | self::CONTROLLER_NEGATIVE");
                    if (!this.__controller)
                        this.__controller = [];
                    return this.__controller[which] || this.querySelector("[" + which + "], ." + which);
                }
            },
            width: {
                set: function (n) {
                    if (typeof n !== "string")
                        throw new TypeError("trl-popup width argument must be a css number value");
                    this.setAttribute("trl-width", n);
                },
                get: function () {
                    return this.getAttribute("trl-width") || "auto";
                }
            },
            height: {
                set: function (n) {
                    if (typeof n !== "string")
                        throw new TypeError("trl-popup width argument must be a css number value");
                    this.setAttribute("trl-height", n);
                },
                get: function () {
                    return this.getAttribute("trl-height") || "auto";
                }
            },
            keep: {
                set: function (b) {
                    if (typeof b !== "boolean")
                        throw new TypeError("trl-popup keep argument must be a boolean value");
                    this.setAttribute("trl-keep", b ? "true" : "false");
                },
                get: function () {
                    return this.hasAttribute("trl-keep") ? this.getAttribute("trl-keep") === "true" : true;
                }
            },
            open: {
                value: function (e) {
                    var that = this, top, left, center_horizontal_padding, center_vertical_padding, 
                            center_container, container; // MUST be removed

                    calculateEventArea.call(this, e);

                    if (!center_container)
                        center_container = false;

                    var popup_content = this.getContent(),
                            popup_bg = this.getBackground();
                    popup_content.style.top = top + "px";
                    popup_content.style.left = left + "px";
                    popup_content.style.width = this.width;
                    popup_content.style.height = this.height;
                    popup_content.style.opacity = 0;

                    if (popup_bg) {
                        popup_bg.style.top = top + "px";
                        popup_bg.style.left = left + "px";
                        popup_bg.style.display = "block";
                        popup_bg.style.opacity = 0;
                        if (this.outside_click)
                            popup_bg.onclick = popup_bg.oncontextmenu = function (e) {
                                that.close(e);
                            };
                    }
                    if (container) {
                        container.style.top = top + "px";
                        container.style.left = left + "px";
                        container.style.display = "block";
                        container.style.opacity = 0;
                        if (this.outside_click)
                            container.onclick = container.oncontextmenu = function (e) {
                                that.close(e);
                            };
                    }

                    configureControllers.call(this);

                    if (this.freeze)
                        document.documentElement.style.overflow = "hidden";
                    
                    window.T.load("design", function () {
                        document.body.appendChild(that);
                        that.style.display = "block";
                        calculateElements.call(that);
                        that.dispatchEvent(new CustomEvent("openstart"));
                        window.T.Animation.start({
                            duration: 200,
                            delta: function (p) {
                                return window.T.Animation.Easing.easeInOutQuart(p);
                            },
                            step: function (delta, t) {
                                if (that.getAlignment() & that.ALIGNMENT_FIT_SCREEN) {
                                    popup_content.style.top = top - (top * delta) + "px";
                                    popup_content.style.left = left - (left * delta) + "px";
                                }
                                if (that.getAlignment() & that.ALIGNMENT_CENTER_HORIZONTAL || center_container) {
                                    if (center_container === true) {
                                        var size = (that.sizes.width.size) + center_horizontal_padding,
                                                real_container_left = container.left.unit !== "%" ? window.T.prototype.Utils.toPercent(container.left.size, window.innerWidth) : (50 - container.left.size) + container.left.size + (container.left.size / 2.75);
                                        popup_content.style.left = "calc(" + ((window.T.prototype.Utils.toPercent(that.sizes.width.size + container.width.size, window.innerWidth) / 2) * delta) + "% + " + (window.T.prototype.Utils.toPercent(left, window.innerWidth) - (window.T.prototype.Utils.toPercent(left, window.innerWidth) * delta)) + "%)";
                                        popup_content.style.marginLeft = "calc(-" + (size * delta) + "px + " + real_container_left + "%)";
                                    } else {
                                        var size = (that.sizes.width.size / 2) + center_horizontal_padding;
                                        popup_content.style.left = "calc(" + (50 * delta) + "% + " + (left - (left * delta)) + "px)";
                                        popup_content.style.marginLeft = "-" + (size * delta) + "px";
                                    }
                                }
                                if (that.getAlignment() & that.ALIGNMENT_CENTER_VERTICAL || center_container === true) {
                                    var size = (that.sizes.height.size / 2) + center_vertical_padding;
                                    popup_content.style.top = "calc(" + (50 * delta) + "% + " + (top - (top * delta)) + "px)";
                                    popup_content.style.marginTop = "-" + (size * delta) + "px";
                                }
                                if (popup_bg) {
                                    popup_bg.style.top = top - (top * delta) + "px";
                                    popup_bg.style.left = left - (left * delta) + "px";
                                    popup_bg.style.width = 100 * delta + "%";
                                    popup_bg.style.height = 100 * delta + "%";
                                    popup_bg.style.opacity = delta;
                                }
                                if (container) {
                                    container.style.top = top - (top * delta) + "px";
                                    container.style.left = "calc(" + (container.left.size * delta) + "" + container.left.unit + " - " + (left - (left * delta)) + "px)";
                                    container.style.width = container.width.size * delta + container.width.unit;
                                    container.style.height = container.height.size * delta + container.height.unit;
                                    container.style.opacity = delta;
                                }
                                if (!(that.getAlignment() & that.ALIGNMENT_FIT_SCREEN)) {
                                    popup_content.style.width = (that.sizes.width.size * delta) + that.sizes.width.unit;
                                    popup_content.style.height = (that.sizes.height.size * delta) + that.sizes.height.unit;
                                } else {
                                    popup_content.style.width = (100 * delta) + "%";
                                    popup_content.style.height = (100 * delta) + "%";
                                }
                                popup_content.style.opacity = delta;
                                that.dispatchEvent(new CustomEvent("opening", {
                                    detail: {
                                        delta: delta,
                                        time: t
                                    }
                                }));
                            }
                        });
                        setTimeout(function () {
                            that.dispatchEvent(new CustomEvent("openend"));
                            that.opened = true;
                        }, 200);
                    });

                    function calculateEventArea(e) {
                        if (e) {
                            if (e instanceof Event)
                                e.preventDefault();
                            top = e.clientY ? e.clientY : window.innerHeight / 2;
                            left = e.clientX ? e.clientX : window.innerWidth / 2;
                        } else {
                            top = window.innerHeight / 2;
                            left = window.innerWidth / 2;
                        }
                    }

                    function calculateElements() {
                        if (!(this.getAlignment() & this.ALIGNMENT_FIT_SCREEN)) {
                            this.sizes = {
                                width: calculateStyle(popup_content, "width"),
                                height: calculateStyle(popup_content, "height")
                            };
                        }
                        if (center_container === true) {
                            container = popup_content.parentNode;
                            container.width = calculateStyle(container, "width");
                            container.height = calculateStyle(container, "height");
                            container.left = calculateStyle(container, "left");
                        }
                        if ((this.getAlignment() & this.ALIGNMENT_CENTER) || (this.getAlignment() & this.ALIGNMENT_CENTER_HORIZONTAL) || (this.getAlignment() & this.ALIGNMENT_CENTER_VERTICAL) || center_container) {
                            popup_content.padding = window.T(popup_content).Utils.getTotalPadding();
                            if ((this.getAlignment() & this.ALIGNMENT_CENTER_VERTICAL) || center_container === true) {
                                center_vertical_padding = ((popup_content.padding.padding_top.size + popup_content.padding.padding_bottom.size) / 2);
                            }
                            if ((this.getAlignment() & this.ALIGNMENT_CENTER_HORIZONTAL) || center_container === true) {
                                center_horizontal_padding = ((popup_content.padding.padding_left.size + popup_content.padding.padding_right.size) / 2);
                            }
                        }
                    }

                    function configureControllers() {
                        var that = this,
                                negative = this.getController(this.CONTROLLER_NEGATIVE),
                                positive = this.getController(this.CONTROLLER_POSITIVE);
                        if (negative)
                            negative.onclick = c("negative");
                        if (positive)
                            positive.onclick = c("positive");

                        function c(controller) {
                            return function (e) {
                                that.close(e);
                                that.dispatchEvent(new CustomEvent(controller));
                            };
                        }
                    }

                    function calculateStyle(element, property) {
                        var size, literal_size = window.getComputedStyle(element, null).getPropertyValue(property);
                        if (literal_size !== "auto" && literal_size !== 0 && literal_size !== "0px" && literal_size !== "0%") {
                            var is_px = literal_size.indexOf("px") !== -1;
                            size = {
                                size: is_px ? +(literal_size.replace("px", "")) : +(literal_size.replace("%", "")),
                                unit: is_px ? "px" : "%"
                            };
                        } else {
                            size = {
                                size: 100,
                                unit: "%"
                            };
                        }
                        return size;
                    }
                }
            },
            close: {
                value: function (e) {
                    var that = this,
                            popup_content = this.getContent(),
                            popup_bg = this.getBackground(),
                            container;
                    if (this.freeze)
                        document.documentElement.style.overflow = "auto";
                    var top = e ? e.clientY : 0, left = e ? e.clientX : 0;
                    popup_content.style.opacity = 1;
                    window.T.load("design", function () {
                        window.T.Animation.start({
                            duration: 200,
                            delta: function (p) {
                                return window.T.Animation.Easing.easeInOutQuart(p);
                            },
                            step: function (delta) {
                                var old_top = +(window.getComputedStyle(popup_content, null).getPropertyValue("top").replace("px", ""));
                                var old_left = +(window.getComputedStyle(popup_content, null).getPropertyValue("left").replace("px", ""));
                                if (popup_bg) {
                                    popup_bg.style.top = (top * delta) + "px";
                                    popup_bg.style.left = (left * delta) + "px";
                                    popup_bg.style.width = 100 - (100 * delta) + "%";
                                    popup_bg.style.height = 100 - (100 * delta) + "%";
                                    popup_bg.style.opacity = 1 - delta;
                                }
                                if (container) {
                                    container.style.top = (top * delta) + "px";
                                    container.style.left = (left * delta) + "px";
                                    container.style.width = container.width.size - (container.width.size * delta) + container.width.unit;
                                    container.style.height = container.height.size - (container.height.size * delta) + container.height.unit;
                                    container.style.opacity = 1 - delta;
                                }
                                popup_content.style.top = old_top - ((old_top - top) * delta) + "px";
                                popup_content.style.left = old_left - ((old_left - left) * delta) + "px";
                                popup_content.style.width = (that.sizes.width.size - (that.sizes.width.size * delta)) + that.sizes.width.unit;
                                popup_content.style.height = (that.sizes.height.size - (that.sizes.height.size * delta)) + that.sizes.height.unit;
                                popup_content.style.opacity = 1 - delta;
                            }
                        });
                        setTimeout(function () {
                            that.dispatchEvent(new CustomEvent("closeend"));
                            that.opened = false;
                            if (!that.keep)
                                document.body.removeChild(that);
                            else
                                that.style.display = "";
                        }, 201);
                    });
                }
            },
            isOpen: {
                value: function () {
                    return this.opened;
                }
            },
            transition: {
                value: function (options) {
                this.el_init_bottom = +(window.getComputedStyle(options.element_triggered, null).getPropertyValue("bottom").replace("px", ""));
                this.el_init_right = +(window.getComputedStyle(options.element_triggered, null).getPropertyValue("right").replace("px", ""));
                this.el_init_width = +(window.getComputedStyle(options.element_triggered, null).getPropertyValue("width").replace("px", ""));
                this.el_init_height = +(window.getComputedStyle(options.element_triggered, null).getPropertyValue("height").replace("px", ""));
                this.el_init_border_radius = +(window.getComputedStyle(options.element_triggered, null).getPropertyValue("border-radius").replace("px", ""));
                var self = this;
                options.transition_to.style.top = "100%";
                options.transition_to.style.left = "100%";
                options.transition_to.style.display = "block";
                options.element_triggered.style.transition = "none";
                if (options.previousStart) {
                    options.previousStart(options);
                }
                window.T.Animation.start({
                    duration: options.duration | 700,
                    delta: function (p) {
                        return window.T.Animation.Easing.easeInOutQuart(p);
                    },
                    step: function (delta, t) {
                        options.transition_to.style.top = 100 - (100 * delta) + "%";
                        options.current_page.style.left = "-" + (100 * delta) + "%";
                        options.transition_to.style.left = 100 - (100 * delta) + "%";
                        if (options.additionalStep) {
                            options.additionalStep(delta, t, options, self);
                        }
                    }
                });
                setTimeout(function () {
                    if (options.transitionFinished) {
                        options.transitionFinished(options);
                    }
                }, options.duration | 1000);
                }
            },
            restore: {
                value: function (options) {
                this.el_init_bottom = 30;
                this.el_init_right = 30;
                this.el_init_width = 64;
                this.el_init_height = 64;
                this.el_init_border_radius = 60;
                var self = this;
                options.current_page.style.display = "block";
                if (options.previousStart)
                    options.previousStart(options);
                    
                if (!window.T.Animation)
                    window.T.load("design", animate);
                else
                    animate();

                function animate() {
                    window.T.Animation.start({
                        duration: options.duration | 1000,
                        delta: function (p) {
                            return window.T.Animation.Easing.easeInOutQuart(p);
                        },
                        step: function (delta, t) {
                            options.current_page.style.top = 100 * delta + "%";
                            options.current_page.style.left = 100 * delta + "%";
                            options.transition_to.style.left = "-" + (100 - (100 * delta)) + "%";
                            if (options.additionalStep) {
                                options.additionalStep(delta, t, options, self);
                            }
                        }
                    });
                    setTimeout(function () {
                        options.current_page.style.display = "none";
                        options.element_triggered.style.transition = "0.1s all ease-in-out";
                        if (options.transitionFinished) {
                            options.transitionFinished(options);
                        }
                    }, options.duration | 1000);
                }
                }
            }
        })
    });
})(window);
/* 
 * (c) 2017 TRIAL.
 * Created on 18/06/2017, 21:02:16.
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
    window.T.elements.Slideshow = document.registerElement(window.T.elements.TRL_SLIDESHOW, {
        prototype: Object.create(HTMLDivElement.prototype, {
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
            auto: {
                set: function (auto) {
                    this.setAttribute("trl-auto", auto ? "true" : "false");
                },
                get: function () {
                    return this.getAttribute("trl-auto") === "true";
                }
            },
            unidirectional: {
                set: function (unidirectional) {
                    this.setAttribute("trl-unidirectional", unidirectional ? "true" : "false");
                },
                get: function () {
                    return this.getAttribute("trl-unidirectional") === "true";
                }
            },
            repeat: {
                set: function (repeat) {
                    this.setAttribute("trl-repeat", repeat ? "true" : "false");
                },
                get: function () {
                    return this.getAttribute("trl-repeat") === "true";
                }
            },
            lastslide_conclude: {
                set: function (lastslide_conclude) {
                    this.setAttribute("trl-lastslide-conclude", lastslide_conclude ? "true" : "false");
                },
                get: function () {
                    return this.hasAttribute("trl-lastslide-conclude") ? this.getAttribute("trl-lastslide-conclude") === "true" : true;
                }
            },
            position: {
                set: function (position) {
                    this.setAttribute("trl-position", position);
                },
                get: function () {
                    return parseInt(this.getAttribute("trl-position")) || 0;
                }
            },
            recalcSlides: {
                value: function () {
                    var slide_items = this.slideSelector !== null ? this.querySelectorAll(this.slideSelector) : this.children;
                    this.slideshow_items = [];
                    for (var i = 0, total = slide_items.length; i < total; i++) {
                        var slide = slide_items[i];
                        if (!slide.classList.contains("controller") && slide.nodeName !== "HEADER") {
                            slide.classList.add("slide");
                            this.slideshow_items.push(slide);
                        }
                    }
                }
            },
            // created 04/12/2017, 17:43:16
            slideSelector: {
                set: function (selector) {
                    this.setAttribute("trl-slide-selector", selector);
                },
                get: function () {
                    return this.hasAttribute("trl-slide-selector") ? this.getAttribute("trl-slide-selector") : null;
                }
            },
            slides: {
                get: function () {
                    if (this.slideshow_items === null || this.slideshow_items === undefined || this.slideshow_items.length <= 0) {
                        this.recalcSlides();
                    }
                    return this.slideshow_items;
                }
            },
            callbacks: {
                set: function (callbacks) {
                    this.slideshow_callbacks = callbacks;
                },
                get: function () {
                    return this.slideshow_callbacks || {};
                }
            },
            next: {
                value: function (callback) {
                    var that = this, slides = this.slides, current, next, height_header = 0;
                    if (this.auto)
                        this.defineInterval();
                    current = next = this.position;
                    if (!this.unidirectional) {
                        if (this.controller.previous)
                            this.controller.previous.style.display = "";
                    }
                    if (this.controller.conclude)
                        this.controller.conclude.style.display = "none";
                    if (this.controller.next)
                        this.controller.next.style.display = "";
                    if (this.position < slides.length + 1) {
                        next++;
                        if (next >= slides.length && this.repeat) {
                            next = 0;
                        } else if (next >= slides.length - 1 && !this.repeat) {
                            if (this.lastslide_conclude) {
                                if (this.controller.conclude)
                                    this.controller.conclude.style.display = "";
                            }
                            if (this.controller.next)
                                this.controller.next.style.display = "none";
                        }
                    }
                    this.position = next;
                    if (callback !== undefined)
                        callback(this.slideshow_info);
                    slides[next].classList.add("current");
                    window.T.load("navigation", function () {
                        window.T.Navigation.next({
                            top: height_header,
                            current: slides[current], 
                            next: slides[next],
                            onfinish: function () {
                                slides[current].classList.remove("current");
                                if (that.callbacks.onchangeslide)
                                    that.callbacks.onchangeslide(that.slideshow_info);
                            }
                        });
                    });
                }
            },
            previous: {
                value: function (callback) {
                    var that = this, slides = this.slides, current, before, height_header = 0;
                    if (this.auto) {
                        this.defineInterval();
                    }
                    current = before = this.position;
                    if (!this.unidirectional) {
                        this.controller.previous.style.display = "";
                    }
                    if (this.controller.next)
                        this.controller.next.style.display = "";
                    if (this.controller.conclude)
                        this.controller.conclude.style.display = "none";
                    if (this.position > -1) {
                        before--;
                        if (before <= -1 && this.repeat) {
                            before = slides.length - 1;
                        } else if (before <= 0 && !this.repeat && this.controller.previous)
                            this.controller.previous.style.display = "none";
                    } else {
                        if (this.controller.previous)
                            this.controller.previous.style.display = "none";
                    }
                    this.position = before;
                    if (callback !== undefined)
                        callback(this.slideshow_info);
                    slides[before].classList.add("current");
                    window.T.load("navigation", function () {
                        window.T.Navigation.previous({
                            top: height_header,
                            current: slides[current], 
                            previous: slides[before],
                            onfinish: function () {
                                slides[current].classList.remove("current");
                                if (that.callbacks.onchangeslide) {
                                    that.callbacks.onchangeslide(that.slideshow_info);
                                }
                            }
                        });
                    });
                }
            },
            interval: {
                set: function (interval) {
                    this.setAttribute("trl-changeslide-ms", interval);
                },
                get: function () {
                    return this.getAttribute("trl-changeslide-ms") || 4000;
                }
            },
            defineInterval: {
                value: function () {
                    clearInterval(this.interval_controller);
                    this.interval_controller = setInterval((function (self) {
                        return function () {
                            self.next();
                        };
                    })(this), this.interval);
                }
            },
            slideshow_info: {
                get: function () {
                    var that = this;
                    return {
                        position: that.position,
                        controller: that.controller,
                        slide: that.slides[that.position]
                    };
                }
            },
            setup: {
                value: function () {
                    var observer = new MutationObserver(function (m) {
                        this.recalcSlides();
                        this.dispatchEvent(new CustomEvent("append"), {
                            detail: m
                        });
                    }.bind(this));
                    observer.observe(this, {childList: true});
                    
                    var slides = this.slides, that = this;
                    this.controller = {};
                    try {
                        this.controller.cancel = that.getElementsByClassName("cancel")[0];
                    } catch (e) {}
                    try {
                        this.controller.previous = that.getElementsByClassName("previous")[0];
                    } catch (e) {}
                    try {
                        this.controller.next = that.getElementsByClassName("next")[0];
                    } catch (e) {}
                    try {
                        this.controller.conclude = that.getElementsByClassName("conclude")[0];
                    } catch (e) {}
                    var header = this.getElementsByTagName("header")[0],
                            cont = this.getElementsByClassName("controller")[0];
                    if (slides !== null && slides !== undefined && slides.length > 0) {
                        var slide = slides[this.position];
                        slide.classList.add("current");
                        var height_header = 0,
                                height_cont = 0;
                        if (cont || header) {
                            var stylesheet;
                            if (cont) {
                                height_cont = +(window.getComputedStyle(cont, null).getPropertyValue("height").replace("px", ""));
                            }
                            if (header) {
                                height_header = +(window.getComputedStyle(header, null).getPropertyValue("height").replace("px", ""));
                            }
                            for (var i in document.styleSheets) {
                                if (document.styleSheets[i].href && document.styleSheets[i].href.indexOf("standards")) {
                                    stylesheet = document.styleSheets[i];
                                    break;
                                }
                            }
                            if (!window.T.prototype.style)
                                window.T.load("utils", applyStyles);
                            else
                                applyStyles();
                            function applyStyles() {
                                T(stylesheet).style({
                                    Styles: {
                                        "[container-slideshow-items] [slideshow-item]": {
                                            height: "calc(100% - " + (height_cont + height_header) + "px)"
                                        }
                                    }
                                });
                            }
                        }
                        if (this.controller.previous) {
                            this.controller.previous.style.display = "none";
                        }
                        if (this.controller.conclude) {
                            this.controller.conclude.style.display = "none";
                        }
                        slide.style.display = "";

                        if (this.callbacks.onstart) {
                            this.callbacks.onstart(this.slideshow_info);
                        }

                        if (this.auto) {
                            this.defineInterval();
                        }

                        if (!this.unidirectional && this.controller.previous) {
                            this.controller.previous.onclick = function (e) {
                                if (that.callbacks.onpreviousslide) {
                                    var slideshow_info = that.slideshow_info;
                                    slideshow_info.event = e;
                                    if (that.callbacks.onpreviousslide(slideshow_info)) {
                                        that.previous();
                                    }
                                } else {
                                    that.previous();
                                }
                            };
                        }
                        if (this.controller.next) {
                            this.controller.next.onclick = function (e) {
                                if (that.callbacks.onnextslide) {
                                    var slideshow_info = that.slideshow_info;
                                    slideshow_info.event = e;
                                    if (that.callbacks.onnextslide(slideshow_info)) {
                                        that.next();
                                    }
                                } else {
                                    that.next();
                                }
                            };
                        }
                        if (this.controller.conclude) {
                            this.controller.conclude.onclick = function (e){
                                if (that.callbacks.onconclude) {
                                    var slideshow_info = that.slideshow_info;
                                    slideshow_info.event = e;
                                    that.callbacks.onconclude(slideshow_info);
                                }
                            };
                        }
                        if (this.controller.cancel) {
                            this.controller.cancel.onclick = function (e){
                                if (that.callbacks.oncancel) {
                                    var slideshow_info = that.slideshow_info;
                                    slideshow_info.event = e;
                                    if (that.callbacks.oncancel(slideshow_info)) {
                                        that.position = 0;
                                    }
                                } else {
                                    that.position = 0;
                                }
                            };
                        }
                    }
                }
            }
        })
    });
})(window);
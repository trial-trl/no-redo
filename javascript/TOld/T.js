/*
 * T.js Library₢
 * Version: 1.12
 * 
 * 2016 ₢ TRIAL. All rights reserved.
 * 
 * Created on   : 10/05/2016, 18:59:09
 * Author       : Matheus Leonardo dos Santos Martins
 */

/* 
 * Editions:
 * 
 * 21/10/2016, 19:50:32 => addded "use strict" mode
 * 
 * 24/10/2016
 *      19:50:58 => all deprecated T.Utils.animate() codes changed to T.Animation.start()
 *      19:53:21 => all deprecated easing codes changed to T.Animation.Easing
 *      
 * 30/10/2016
 *      18:25:51 => slideshow(), slideshow#nextSlide(), slideshow#previousSlide(), next(), previous() => replaced all "block" and "default_positioning" to "" (default value).
 *      18:35:56 => slideshow(): now the class "current" is added to the first slide.
 *      18:39:11 => next(), previous(): now, instead of setting "none" to CSS display property in setTimeout, the class "current" is removed from the options.current slide.
 *      
 * 17/12/2016, 14:48:23 => UI.Compact.Logo.draw(), UI.Logo.draw() => added options.setup property
 */

/*
 * Easing Functions - inspired from http://gizma.com/easing/
 * only considering the t value for the range [0, 1] => [0, 1]
 * 
 * by: gre / easing.js, GitHub
 * 
 */

"use strict";

var easing = {
  // no easing, no acceleration
  linear: function (t) { return t; },
  // accelerating from zero velocity
  easeInQuad: function (t) { return t*t; },
  // decelerating to zero velocity
  easeOutQuad: function (t) { return t*(2-t); },
  // acceleration until halfway, then deceleration
  easeInOutQuad: function (t) { return t<.5 ? 2*t*t : -1+(4-2*t)*t; },
  // accelerating from zero velocity 
  easeInCubic: function (t) { return t*t*t; },
  // decelerating to zero velocity 
  easeOutCubic: function (t) { return (--t)*t*t+1; },
  // acceleration until halfway, then deceleration 
  easeInOutCubic: function (t) { return t<.5 ? 4*t*t*t : (t-1)*(2*t-2)*(2*t-2)+1; },
  // accelerating from zero velocity 
  easeInQuart: function (t) { return t*t*t*t; },
  // decelerating to zero velocity 
  easeOutQuart: function (t) { return 1-(--t)*t*t*t; },
  // acceleration until halfway, then deceleration
  easeInOutQuart: function (t) { return t<.5 ? 8*t*t*t*t : 1-8*(--t)*t*t*t; },
  // accelerating from zero velocity
  easeInQuint: function (t) { return t*t*t*t*t; },
  // decelerating to zero velocity
  easeOutQuint: function (t) { return 1+(--t)*t*t*t*t; },
  // acceleration until halfway, then deceleration 
  easeInOutQuint: function (t) { return t<.5 ? 16*t*t*t*t*t : 1+16*(--t)*t*t*t*t; }
};

// created on 12/03/2017, 01:14:17
window.Modules = {
    Polyfills: [
        'https://cdnjs.cloudflare.com/ajax/libs/webcomponentsjs/0.7.24/webcomponents.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/classlist/1.1.20150312/classList.min.js'
    ],
    Templates: {
        Rating: "http://localhost/Library/account/views/rating/template.html"
    }
};

var on;

window.T = (function () {
    var T = function (element) {
        if (!(this instanceof T)) {
            return new T(element);
        }
        if (element !== undefined) {
            if (typeof element !== "string") {
                on = element;
            } else {
                on = document.querySelector(element);
            }
        }
    };
    T.fn = T.prototype = {
        Constants: {
            Message: {
                EXIST: "E",
                NOT_EXIST: "NE",
                NOT_ACTIVATED: "NA",
                SAVED_WITH_SUCCESS: "SWS",
                ERROR_PASSWORD_INCORRECT: "ERR_PI",
                MEMBER_WITHOUT_INSTITUTION: "MWI"
            },
            Slideshow: {
                CONTAINER_BACKGROUND: "Cn_bCk_Ssw-Ll"
            }
        },
        Utils: {
            Pattern: {
                maskCNPJ: function () {
                    on.onkeydown = function (e) {
                        if (e.keyCode !== 8) {
                            this.value = this.value.replace(/\D/g, "");
                            this.value = this.value.replace(/^(\d{2})/g, "$1.");
                            this.value = this.value.replace(/(\d{4})(\d{2})$/g, "$1-$2");
                            this.value = this.value.replace(/(\d{3})(\d{3})/g, "$1.$2/");
                        }
                    };
                },
                maskCEP: function () {
                    on.onkeydown = function (e) {
                        if (e.keyCode !== 8) {
                            this.value = this.value.replace(/\D/g, "");
                            this.value = this.value.replace(/(\d{3})$/g, "$1");
                            this.value = this.value.replace(/^(\d{5})/g, "$1-");
                        }
                    };
                },
                maskBirthday: function () {
                    on.onkeydown = function (e) {
                        if (e.keyCode !== 8) {
                            this.value = this.value.replace(/\D/g, "");
                            this.value = this.value.replace(/^(\d{2})/g, "$1/");
                            this.value = this.value.replace(/(\d{2})/g, "$1");
                            this.value = this.value.replace(/(\d{4})$/g, "/$1");
                        }
                    };
                }
            },
            Message: {
                EXIST: "E",
                NOT_EXIST: "NE",
                NOT_ACTIVATED: "NA",
                SAVED_WITH_SUCCESS: "SWS",
                ERROR_PASSWORD_INCORRECT: "ERR_PI"
            },
            ajax: function (options) {
                function eventHandler(e, custom, main) {
                    if (custom) {
                        if (custom(e)) {
                            main();
                        }
                    }
                    main();
                }
                var e = event;
                if (e) {
                    e.preventDefault();
                }

                var xhr = new XMLHttpRequest(), url, data = null, method;

                xhr.timeout = options.timeout ? options.timeout : 30000;

                if (options.data) {
                    method = "POST";
                    if (this.isElement(options.data) || this.isNode(options.data)) {
                        method = options.data.getAttribute("method");
                        url = options.data.getAttribute("action");
                        data = new FormData(options.data);
                        if (options.additional_data) {
                            var names = Object.getOwnPropertyNames(options.additional_data);
                            for (var i = 0, total = names.length; i < total; i++) {
                                data.append(names[i], options.additional_data[names[i]]);
                            }
                        }
                    } else {
                        url = options.url;
                        data = JSON.stringify(options.data);
                    }
                } else {
                    method = "GET";
                    if (options.url) {
                        url = options.url;
                    }
                }
                if (options.method) {
                    method = options.method;
                }

                xhr.onloadstart = (function (e) {
                    eventHandler(e, options.onloadstart, function () {
                        console.log("loadtstart");
                    });
                });
                xhr.onprogress = (function (e) {
                    eventHandler(e, options.onprogress, function () {
                        console.log("progress");
                    });
                });
                xhr.onabort = (function (e) {
                    eventHandler(e, options.onabort, function () {
                        console.log("abort");
                    });
                });
                xhr.onerror = (function (e) {
                    eventHandler(e, options.onerror, function () {
                        console.log("error");
                    });
                });
                xhr.onload = (function (e) {
                    eventHandler(e, options.onload, function () {
                        console.log("load = " + e.target.status);
                    });
                });
                xhr.ontimeout = (function (e) {
                    eventHandler(e, options.ontimeout, function () {
                        console.log("timeout");
                    });
                });
                xhr.onloadend = (function (e) {
                    eventHandler(e, options.onloadend, function () {
                        console.log("loadend");
                    });
                });

                xhr.open(method, url);
                xhr.setRequestHeader("X-REQUESTED-WITH", "xmlhttprequest");
                if (data) {
                    xhr.send(data);
                } else {
                    xhr.send();
                }
            },
            isNode: function (element) {
                return (typeof Node === "object" ? element instanceof Node : element && typeof element === "object" && element !== null && element.nodeType === "number" & typeof element.nodeName === "string");
            },
            isElement: function (element) {
                return (typeof HTMLElement === "object" ? element instanceof HTMLElement : element && typeof element === "object" && element !== null && element.nodeType === 1 & typeof element.nodeName === "string");
            },
            animate: function (options) {
                var start = new Date,
                animation = setInterval(function () {
                    var timePassed = new Date - start;
                    var progress = timePassed / options.duration;
                    if (progress > 1) {
                        progress = 1;
                    }
                    var delta = options.delta(progress);
                    options.step(delta, progress);
                    if (progress === 1) {
                        clearInterval(animation);
                    }
                }, options.delay || 10);
            },
            getTotalPadding: function () {
                var padding_left = window.getComputedStyle(on, null).getPropertyValue("padding-left");
                var padding_left_number = padding_left.indexOf("px") !== -1 ? +(padding_left.replace("px", "")) : +(padding_left.replace("%", ""));
                var padding_left_unit = padding_left.indexOf("px") !== -1 ? "px" : "%";

                var padding_right = window.getComputedStyle(on, null).getPropertyValue("padding-right");
                var padding_right_number = padding_right.indexOf("px") !== -1 ? +(padding_right.replace("px", "")) : +(padding_right.replace("%", ""));
                var padding_right_unit = padding_right.indexOf("px") !== -1 ? "px" : "%";

                var padding_top = window.getComputedStyle(on, null).getPropertyValue("padding-top");
                var padding_top_number = padding_top.indexOf("px") !== -1 ? +(padding_top.replace("px", "")) : +(padding_top.replace("%", ""));
                var padding_top_unit = padding_top.indexOf("px") !== -1 ? "px" : "%";   

                var padding_bottom = window.getComputedStyle(on, null).getPropertyValue("padding-bottom");
                var padding_bottom_number = padding_bottom.indexOf("px") !== -1 ? +(padding_bottom.replace("px", "")) : +(padding_bottom.replace("%", ""));
                var padding_bottom_unit = padding_bottom.indexOf("px") !== -1 ? "px" : "%";   

                var padding =  {
                    padding_left: {
                        size: padding_left_number,
                        unit: padding_left_unit
                    },
                    padding_right: {
                        size: padding_right_number,
                        unit: padding_right_unit
                    },
                    padding_top: {
                        size: padding_top_number,
                        unit: padding_top_unit
                    },
                    padding_bottom: {
                        size: padding_bottom_number,
                        unit: padding_bottom_unit
                    }
                };

                return padding;
            },
            controlVisibility: function (options) {
                options.when_hide = options.when_hide || 200;
                options.interval = options.interval || 100;
                var exhibiting, scrollY = window.pageYOffset;
                window.onscroll = function (e) {
                    if ((exhibiting && window.pageYOffset >= scrollY) || (!exhibiting && window.pageYOffset <= scrollY)) {
                        options.current += options.interval;
                    }

                    scrollY = window.pageYOffset;

                    if (options.current >= options.when_hide) {
                        showHideElement(exhibiting);
                        options.current = 0;
                        scrollY = window.pageYOffset;
                    }
                    if (window.pageYOffset === 0) {
                        showHideElement(false);
                        options.current = 0;
                        scrollY = window.pageYOffset;
                    }
                };
            },
            slideshow: function (options) {
                var interval, slides, default_image, container_background = undefined, that;
                if (options.container) {
                    if (typeof options.container === "string") {
                        options.container = document.querySelector(options.container);
                    }
                } else {
                    options.container = on;
                }
                options.controller = {
                    cancel: options.container.querySelector(".cancel"),
                    previous: options.container.querySelector(".previous"),
                    next: options.container.querySelector(".next"),
                    conclude: options.container.querySelector(".conclude")
                };
                var header = options.container.querySelector("header"),
                        cont = options.container.querySelector(".controller");
                if (!options.hasOwnProperty("auto")) {
                    options.auto = true;
                }
                if (!options.hasOwnProperty("unidirectional")) {
                    options.unidirectional = false;
                }
                if (!options.hasOwnProperty("repeat")) {
                    options.repeat = true;
                }
                if (!options.hasOwnProperty("show_conclude_button_last_slide")) {
                    options.show_conclude_button_last_slide = false;
                }
                options.pos = 0;
                slides = options.container.querySelectorAll(options.search_for ? options.search_for : " [slideshow-item]");
                if (slides !== null && slides !== undefined && slides.length > 0) {
                    options.slide = slides[options.pos];
                    options.slide.classList.add("current");
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
                        T(stylesheet).CSS.change({
                            Styles: {
                                "[container-slideshow-items] [slideshow-item]": {
                                    height: "calc(100% - " + (height_cont + height_header) + "px)"
                                }
                            }
                        });
                    }
                    that = {
                        nextSlide: function (callback) {
                            if (options.auto) {
                                defineInterval();
                            }
                            var current = options.pos, next = options.pos;
                            if (!options.unidirectional) {
                                options.controller.previous.style.display = "";
                            }
                            if (options.controller.conclude) {
                                options.controller.conclude.style.display = "none";
                            }
                            options.controller.next.style.display = "";
                            if (options.pos < slides.length + 1) {
                                next++;
                                if (next >= slides.length && options.repeat) {
                                    next = 0;
                                } else if (next >= slides.length - 1 && !options.repeat) {
                                    if (options.show_conclude_button_last_slide) {
                                        if (options.controller.conclude) {
                                            options.controller.conclude.style.display = "";
                                        }
                                    }
                                    options.controller.next.style.display = "none";
                                }
                            }
                            options.pos = next;
                            options.slide = slides[options.pos];
                            if (callback !== undefined) {
                                if (callback.onstart) {
                                    callback.onstart(options);
                                }
                            }
                            slides[next].classList.add("current");
                    
                            // added on 30/10/2016, 15:06:34
                            if (options.onchangeslide) {
                                options.onchangeslide(options);
                            }

                            T.prototype.Utils.next({
                                top: height_header,
                                current: slides[current], 
                                next: slides[next],
                                onfinish: function () {
                                    slides[current].classList.remove("current");
                                }
                            });
                        },
                        previousSlide: function () {
                            if (options.auto) {
                                defineInterval();
                            }
                            var current = options.pos, before = options.pos;
                            if (!options.unidirectional) {
                                options.controller.previous.style.display = "";
                            }
                            options.controller.next.style.display = "";
                            if (options.controller.conclude) {
                                options.controller.conclude.style.display = "none";
                            }
                            if (options.pos > -1) {
                                before--;
                                if (before <= -1 && options.repeat) {
                                    before = slides.length - 1;
                                } else if (before <= 0 && !options.repeat) {
                                    options.controller.previous.style.display = "none";
                                }
                            } else {
                                options.controller.previous.style.display = "none";
                            }
                            options.pos = before;
                            options.slide = slides[options.pos];
                            slides[before].classList.add("current");
                    
                            // added on 30/10/2016, 15:06:07
                            if (options.onchangeslide) {
                                options.onchangeslide(options);
                            }

                            T.prototype.Utils.previous({
                                top: height_header,
                                current: slides[current], 
                                previous: slides[before],
                                onfinish: function () {
                                    slides[current].classList.remove("current");
                                }
                            });
                        },
                        cancelSlideshow: function () {
                            if (options.oncancel) {
                                options.oncancel(options);
                            }
                            options.pos = 0;
                        }
                    };
                    if (options.controller.previous) {
                        options.controller.previous.style.display = "none";
                    }
                    if (options.controller.conclude) {
                        options.controller.conclude.style.display = "none";
                    }
                    slides[options.pos].style.display = "";
                    
                    // added on 30/10/2016, 14:50:34 - 14:51:14
                    if (options.onstart) {
                        options.onstart(options);
                    }
                    
                    if (options.changes) {
                        if (options.changes.CONTAINER_BACKGROUND) {
                            container_background = document.querySelector(options.changes.CONTAINER_BACKGROUND.on);
                            default_image = container_background.src;
                        }
                    }
                    if (options.auto) {
                        defineInterval();
                    }
                    if (!options.unidirectional && options.controller.previous) {
                        options.controller.previous.onclick = function (e) {
                            if (options.onpreviousslide) {
                                if (options.onpreviousslide(e, options)) {
                                    that.previousSlide();
                                }
                            } else {
                                that.previousSlide();
                            }
                        };
                    }
                    if (options.controller.next) {
                        options.controller.next.onclick = function (e) {
                            if (options.onnextslide) {
                                if (options.onnextslide(e, options)) {
                                    that.nextSlide();
                                }
                            } else {
                                that.nextSlide();
                            }
                        };
                    }
                    if (options.controller.conclude) {
                        options.controller.conclude.onclick = function (e){
                            options.onconclude(options);
                        };
                    }
                    if (options.controller.cancel) {
                        options.controller.cancel.onclick = function (e){
                            that.cancelSlideshow();
                        };
                    }
                }
                function defineInterval() {
                    clearInterval(interval);
                    interval = setInterval(that.nextSlide, options.interval || 4000);
                }
                function change() {
                    if (container_background !== undefined) {
                        var new_image = slides[options.pos].getAttribute("trl-container-background");
                        container_background.src = new_image !== undefined && new_image !== null ? new_image : default_image;
                    }
                }
                return that;
            },
            next: function (options) {
                try {
                    options.next.style.position = "absolute";
                    options.next.style.display = "";
                    options.next.style.top = options.top + "px";
                    options.next.style.left = "100%";
                    if (options.onstart) {
                        options.onstart(options);
                    }
                    T.Animation.start({
                        duration: 200,
                        delta: function (p) {
                            return T.Animation.Easing.easeInOutQuart(p);
                        },
                        step: function (delta) {
                            try {
                                var perc = 100 * delta;
                                options.current.style.left = "-" + perc + "%";
                                options.next.style.left = (100 - perc) + "%";
                            } catch (e) {
                                console.error(e);
                            }
                        }
                    });
                    setTimeout(function () {
                        options.next.style.position = "";
                        options.next.style.top = 0;
                        if (options.onfinish) {
                            options.onfinish(options);
                        }
                    }, 201);
                } catch (e) {
                    console.error(e);
                }
            },
            previous: function (options) {
                try {
                    options.previous.style.position = "absolute";
                    options.previous.style.display = "";
                    options.previous.style.top = options.top + "px";
                    options.previous.style.left = "-100%";
                    if (options.onstart) {
                        options.onstart(options);
                    }
                    T.Animation.start({
                        duration: 200,
                        delta: function (p) {
                            return T.Animation.Easing.easeInOutQuart(p);
                        },
                        step: function (delta) {
                            try {
                                var perc = 100 * delta;
                                options.previous.style.left = "-" + (100 - perc) + "%";
                                options.current.style.left = perc + "%";
                            } catch (e) {
                                console.error(e);
                            }
                        }
                    });
                    setTimeout(function () {
                        options.previous.style.position = "";
                        options.previous.style.top = 0;
                        if (options.onfinish) {
                            options.onfinish(options);
                        }
                    }, 201);
                } catch (e) {
                    console.error(e);
                }
            },
            triggerOffset: function (options) {
                options.element = on;
                var offset_toolbar = options.trigger_offset != null ? options.trigger_offset : options.element.offsetTop;
                window.addEventListener("scroll", function (e) {
                    if (window.pageYOffset > offset_toolbar)  {
                        if (options.class_name.constructor === Array) {
                            for (var i = 0, total = options.class_name.length; i < total; i++) {
                                options.element.classList.add(options.class_name[i]);
                            }
                        } else {
                            options.element.classList.add(options.class_name);
                        }
                        if (options.hasOwnProperty("reset_top") && options.reset_top) {
                            options.element.style.top = 0;
                        }
                    } else {
                        if (options.class_name.constructor === Array) {
                            for (var i = 0, total = options.class_name.length; i < total; i++) {
                                options.element.classList.remove(options.class_name[i]);
                            }
                        } else {
                            options.element.classList.remove(options.class_name);
                        }
                        if (options.element.style.position === "absolute") {
                            options.element.style.top = offset_toolbar + "px";
                        }
                    }
                }, false);
            },
            URL: {
                Parameter: {
                    add: function (key, value) {
                        var href = location.href;
                        var url = href.split("?");
                        var pairs = href.indexOf("&") !== -1 ? url[1].split("&") : ""; 
                        var key_arr = [], parameters;
                        if (url.length >= 2) {
                        if (pairs !== "") {
                            for (var i = 0, total = pairs.length; i < total; i++) {
                                var arr = pairs[i].split("=");
                                key_arr[i] = arr[0];
                                if (arr[0] == key) {
                                    pairs[i] = arr[0] + "=" + value;
                                }
                            }
                            if (key_arr.indexOf(key) === -1) {
                                pairs.push(key + "=" + value);
                            }
                        } else {
                            var arr = url[1].split("=");
                            pairs = [];
                            pairs[0] = url[1];
                            if (arr[0] !== key) {
                                pairs.push(key + "=" + value);
                            }
                        }
                        } else {
                            pairs = [];
                            pairs.push(key + "=" + value);
                        }
                        parameters = pairs.join("&");
                    },
                    get: function (key) {
                        var href = location.href;
                        var url = href.split("?");
                        var pairs = href.indexOf("&") !== -1 ? url[1].split("&") : null;
                        if (pairs != null) {
                            var key = [], parameters;
                            if (url.length >= 2) {
                                for (var i = 0, total = pairs.length; i < total; i++) {
                                    var arr = pairs[i].split("=");
                                    if (arr[0] == key) {
                                        return arr[1];
                                    }
                                }
                            }
                        }
                        if (url[1] !== null && url[1] !== undefined) {
                            pairs = url[1].split("=");
                            if (pairs[0] == key) {
                                return pairs[1];
                            }
                        }
                        return null;
                    }
                }
            },
            handleTab: function (options) {
                var e = options.event,
                        tabs = document.querySelectorAll(options.tabs),
                        contents = document.querySelectorAll(options.contents),
                        attr = e.target.getAttribute(options.attr || "trial-corresponding-element");
                if (!e.target.classList.contains("current")) {
                    for (var i = 0, total = tabs.length; i < total; i++) {
                        var t = tabs[i],
                                c = contents[i];
                        if (t.getAttribute(options.attr || "trial-corresponding-element") === attr) {
                            t.classList.add(options.add_class || "current");
                        } else {
                            t.classList.remove(options.add_class || "current");
                        }
                        if (c !== null && c !== undefined) {
                            if (c.getAttribute(options.attr || "trial-corresponding-element") === attr) {
                                c.classList.remove(options.visibility_class_control || "not-display");
                                T.prototype.Utils.animate({
                                    duration : 100,
                                    delta : function (p) {
                                        return p;
                                    },
                                    step : function (delta) {
                                        c.style.opacity = delta;
                                    }
                                });
                            } else {
                                c.style.opacity = 1;
                                T.prototype.Utils.animate({
                                    duration : 100,
                                    delta : function (p) {
                                        return p;
                                    },
                                    step : function (delta) {
                                        c.style.opacity = 1 - (delta * 1);
                                    }
                                });
                                c.classList.add(options.visibility_class_control || "not-display");
                            }
                        }
                    }
                }
            },
            endlessScroll: function (options) {
                // all code changed on 26/11/2016, 00:03:33 - 00:48:41
                var element = on,
                        is_window = element === window,
                        attach = is_window ? window : element;
                attach.addEventListener("scroll", function() {
                    var contentHeight, yOffset, y;
                    if (is_window) {
                        contentHeight = options.target.offsetHeight;
                        yOffset = window.pageYOffset;
                        y = yOffset + window.innerHeight;
                    } else {
                        contentHeight = element.scrollHeight || element.offsetHeight || element.clientHeight;
                        yOffset = element.scrollTop;
                        y = yOffset + element.offsetHeight;
                    }
                    if(y >= contentHeight + (-options.offset || 0)) {
                        options.onend(options);
                    }
                });
            },
            toPercent: function (px, base) {
                return (px * 100) / base;
            },
            /*
             * added on 23/10/2016, 19:02:59 - 19:18:54 - 19:36:23 - 20:08:40
             * edited: 30/10/2016, 15:43:44 - 16:31:06
             */
            visibilityOnScroll: function(options) {
                var POSITIVE = "positive",
                        NEGATIVE = "negative";
                var that = this,
                        element = on,
                        invisible = false,
                        scroll = {
                            direction: POSITIVE,
                            y: 0
                        },
                        ontrigger = options.ontrigger || null;
                
                window.addEventListener("scroll", listener);
                
                function calculateScroll() {
                    var direction = window.scrollY > that.y ? POSITIVE : NEGATIVE;
                    if (scroll.direction === direction) {
                        if ((scroll.direction === POSITIVE && !invisible) || (scroll.direction === NEGATIVE && invisible)) {
                            scroll.y += Math.abs(window.scrollY - that.y);
                        }
                    } else {
                        scroll.y = 0;
                    }
                    scroll.direction = direction;
                }
                
                function listener(e) {
                    calculateScroll();

                    if (scroll.y >= options.trigger) {
                        if (invisible && scroll.direction === NEGATIVE) {
                            show();
                        } else if (!invisible && scroll.direction === POSITIVE) {
                            hide();
                        }
                    }

                    if (window.pageYOffset === 0) {
                        show();
                    }

                    assignCurrentY();
                }

                function show() {
                    scroll.y = 0;
                    invisible = false;
                    if (ontrigger) {
                        ontrigger(options, "down");
                    } else {
                        if (options.add_class.constructor === Array) {
                            for (var i = 0, total = options.add_class.length; i < total; i++) {
                                element.classList.add(options.add_class[i]);
                            }
                        } else {
                            element.classList.add(options.add_class);
                        }
                    }
                }

                function hide() {
                    scroll.y = 0;
                    invisible = true;
                    if (ontrigger) {
                        ontrigger(options, "up");
                    } else {
                        if (options.add_class.constructor === Array) {
                            for (var i = 0, total = options.add_class.length; i < total; i++) {
                                element.classList.remove(options.add_class[i]);
                            }
                        } else {
                            element.classList.remove(options.add_class);
                        }
                    }
                }

                function assignCurrentY() {
                    that.y = window.scrollY;
                }
                
                return {
                    removeListener: function () {
                        window.removeEventListener("scroll", listener);
                    }
                };
            }
        },
        Standards: {
            open: function (options) {
                
                var e, top, left, center_horizontal_padding, center_vertical_padding;
                
                calculateEventArea();
                checkOptions();
                
                
                // Last edition: 21/09/2016, 12:59:23
                options.popup = on;
                options.popup.style.display = "block";
                options.window = on.querySelector("[content]") || on;
                options.window.style.top = top + "px";
                options.window.style.left = left + "px";
                options.window.style.opacity = 0;
                
                if (!options.background) {
                    options.background = options.popup.querySelector("[background]");
                }
                
                calculateElements();
                
                if (options.background) {
                    options.background.style.top = top + "px";
                    options.background.style.left = left + "px";
                    options.background.style.display = "block";
                    options.background.style.opacity = 0;
                    if (options.dismiss_on_click_out) {
                        options.background.onclick = function (e) {
                            options.event = e;
                            T.prototype.Standards.close(options);
                        };
                        options.background.oncontextmenu = function (e) {
                            options.event = e;
                            T.prototype.Standards.close(options);
                        };
                    }
                }
                if (options.container) {
                    options.container.style.top = top + "px";
                    options.container.style.left = left + "px";
                    options.container.style.display = "block";
                    options.container.style.opacity = 0;
                    if (options.dismiss_on_click_out) {
                        options.container.onclick = function (e) {
                            options.event = e;
                            T.prototype.Standards.close(options);
                        };
                        options.container.oncontextmenu = function (e) {
                            options.event = e;
                            T.prototype.Standards.close(options);
                        };
                    }
                }
                
                configureControllers();
                
                if (options.freeze) {
                    document.documentElement.style.overflow = "hidden";
                }
                if (options.onstart) {
                    options.onstart(options);
                }
                T.prototype.Utils.animate({
                    duration: 200,
                    delta: function (p) {
                        return T.Animation.Easing.easeInOutQuart(p);
                    },
                    step: function (delta, t) {
                        if (options.fit_screen) {
                            options.window.style.top = top - (top * delta) + "px";
                            options.window.style.left = left - (left * delta) + "px";
                        }
                        if (options.center || options.center_horizontal || options.center_container) {
                            if (options.center_container) {
                                var size = (options.window.width.size) + center_horizontal_padding,
                                        real_container_left = options.container.left.unit !== "%" ? T.prototype.Utils.toPercent(options.container.left.size, window.innerWidth) : (50 - options.container.left.size) + options.container.left.size + (options.container.left.size / 2.75);
                                options.window.style.left = "calc(" + ((T.prototype.Utils.toPercent(options.window.width.size + options.container.width.size, window.innerWidth) / 2) * delta) + "% + " + (T.prototype.Utils.toPercent(left, window.innerWidth) - (T.prototype.Utils.toPercent(left, window.innerWidth) * delta)) + "%)";
                                options.window.style.marginLeft = "calc(-" + (size * delta) + "px + " + real_container_left + "%)";
                            } else {
                                var size = (options.window.width.size / 2) + center_horizontal_padding;
                                options.window.style.left = "calc(" + (50 * delta) + "% + " + (left - (left * delta)) + "px)";
                                options.window.style.marginLeft = "-" + (size * delta) + "px";
                            }
                        }
                        if (options.center || options.center_vertical || options.center_container) {
                            var size = (options.window.height.size / 2) + center_vertical_padding;
                            options.window.style.top = "calc(" + (50 * delta) + "% + " + (top - (top * delta)) + "px)";
                            options.window.style.marginTop = "-" + (size * delta) + "px";
                        }
                        if (options.background) {
                            options.background.style.top = top - (top * delta) + "px";
                            options.background.style.left = left - (left * delta) + "px";
                            options.background.style.width = 100 * delta + "%";
                            options.background.style.height = 100 * delta + "%";
                            options.background.style.opacity = delta;
                        }
                        if (options.container) {
                            options.container.style.top = top - (top * delta) + "px";
                            options.container.style.left = "calc(" + (options.container.left.size * delta) + "" + options.container.left.unit + " - " + (left - (left * delta)) + "px)";
                            options.container.style.width = options.container.width.size * delta + options.container.width.unit;
                            options.container.style.height = options.container.height.size * delta + options.container.height.unit;
                            options.container.style.opacity = delta;
                        }
                        if (!options.fit_screen) {
                            options.window.style.width = (options.window.width.size * delta) + options.window.width.unit;
                            options.window.style.height = (options.window.height.size * delta) + options.window.height.unit;
                        } else {
                            options.window.style.width = (100 * delta) + "%";
                            options.window.style.height = (100 * delta) + "%";
                        }
                        options.window.style.opacity = delta;
                        if (options.additionalStep) {
                            options.additionalStep(delta, t, options);
                        }
                    }
                });
                setTimeout(function () {
                    if (options.onfinish) {
                        options.onfinish(options);
                    }
                }, 200);
                
                function calculateEventArea() {
                    e = event;
                    var have_event = e !== null && e !== undefined;
                    if (have_event) {
                        e.preventDefault();
                        top = e.clientY ? e.clientY : window.innerHeight / 2;
                        left = e.clientX ? e.clientX : window.innerWidth / 2;
                    } else {
                        top = window.innerHeight / 2;
                        left = window.innerWidth / 2;
                    }
                }
                
                function calculateElements() {
                    if (!options.fit_screen) {
                        options.window.width = calculateStyle(options.window, "width");
                        options.window.height = calculateStyle(options.window, "height");
                    }
                    if (options.center_container) {
                        options.container = options.window.parentNode;
                        options.container.width = calculateStyle(options.container, "width");
                        options.container.height = calculateStyle(options.container, "height");
                        options.container.left = calculateStyle(options.container, "left");
                    }
                    if (options.center || options.center_horizontal || options.center_vertical || options.center_container) {
                        options.window.padding = T.prototype.Utils.getTotalPadding();
                        if (options.center || options.center_vertical || options.center_container) {
                            center_vertical_padding = ((options.window.padding.padding_top.size + options.window.padding.padding_bottom.size) / 2);
                        }
                        if (options.center || options.center_horizontal || options.center_container) {
                            center_horizontal_padding = ((options.window.padding.padding_left.size + options.window.padding.padding_right.size) / 2);
                        }
                    }
                }
                
                function configureControllers() {
                    options.controller = {
                        positive: options.window.querySelector("[positive], .positive"),
                        negative: options.window.querySelector("[negative], .negative")
                    };
                    if (options.controller) {
                        if (options.controller.hasOwnProperty("negative") && options.controller.negative !== null && options.controller.negative !== undefined) {
                            var callback_negative;
                            if (options.onnegative) {
                                callback_negative = options.onnegative;
                            }
                            options.controller.negative.addEventListener("click", (function (e) {
                                return function () {
                                    clickButton(callback_negative);
                                };
                            })(), false);
                        }
                        if (options.controller.hasOwnProperty("positive") && options.controller.positive !== null && options.controller.positive !== undefined) {
                            var callback_positive;
                            if (options.onpositive) {
                                callback_positive = options.onpositive;
                            }
                            options.controller.positive.addEventListener("click", (function (e) {
                                return function () {
                                    clickButton(callback_positive);
                                };
                            })(), false);
                        }
                    }
                }
                
                function checkOptions() {
                    if (!options.hasOwnProperty("center_container")) {
                        options.center_container = false;
                    }
                    if (!options.hasOwnProperty("center_horizontal")) {
                        options.center_horizontal = false;
                    }
                    if (!options.hasOwnProperty("center_vertical")) {
                        options.center_vertical = false;
                    }
                    if (!options.hasOwnProperty("center")) {
                        options.center = false;
                    }
                    if (!options.hasOwnProperty("dismiss_on_click_out")) {
                        options.dismiss_on_click_out = true;
                    }
                    if (!options.hasOwnProperty("autodismiss")) {
                        options.autodismiss = true;
                    }
                    if (!options.hasOwnProperty("fit_screen")) {
                        options.fit_screen = false;
                    }
                    if (!options.hasOwnProperty("freeze")) {
                        options.freeze = true;
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
                
                function clickButton(callback) {
                    T.prototype.Standards.close(options);
                    if (callback) {
                        callback(options);
                    }
                }
                
                var that = {
                    close: function () {
                        T.prototype.Standards.close(options);
                    }
                };
                
                return that;
            },
            close: function (options) {
                var e = event || options.event;
                if (options.freeze) {
                    document.documentElement.style.overflow = "auto";
                }
                var top = e.clientY, left = e.clientX;
                options.window.style.opacity = 1;
                T.prototype.Utils.animate({
                    duration: 200,
                    delta: function (p) {
                        return T.Animation.Easing.easeInOutQuart(p);
                    },
                    step: function (delta) {
                        var old_top = +(window.getComputedStyle(options.window, null).getPropertyValue("top").replace("px", ""));
                        var old_left = +(window.getComputedStyle(options.window, null).getPropertyValue("left").replace("px", ""));
                        if (options.background) {
                            options.background.style.top = (top * delta) + "px";
                            options.background.style.left = (left * delta) + "px";
                            options.background.style.width = 100 - (100 * delta) + "%";
                            options.background.style.height = 100 - (100 * delta) + "%";
                            options.background.style.opacity = 1 - delta;
                        }
                        if (options.container) {
                            options.container.style.top = (top * delta) + "px";
                            options.container.style.left = (left * delta) + "px";
                            options.container.style.width = options.container.width.size - (options.container.width.size * delta) + options.container.width.unit;
                            options.container.style.height = options.container.height.size - (options.container.height.size * delta) + options.container.height.unit;
                            options.container.style.opacity = 1 - delta;
                        }
                        options.window.style.top = old_top - ((old_top - top) * delta) + "px";
                        options.window.style.left = old_left - ((old_left - left) * delta) + "px";
                        options.window.style.width = (options.window.width.size - (options.window.width.size * delta)) + options.window.width.unit;
                        options.window.style.height = (options.window.height.size - (options.window.height.size * delta)) + options.window.height.unit;
                        options.window.style.opacity = 1 - delta;
                    }
                });
                setTimeout(function () {
                    if (options.onclose) {
                        options.onclose(options);
                    }
                    if (options.background) {
                        options.background.style.display = "none";
                    }
                    if (options.container) {
                        options.container.style.display = "none";
                        options.container.style.left = options.container.left.size + options.container.left.unit;
                    }
                    options.popup.style.display = "none";
                    options.window.style.width = options.window.width.size + options.window.width.unit;
                    options.window.style.height = options.window.height.size + options.window.height.unit;
                }, 201);
            },
            transition: function (options) {
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
                T.prototype.Utils.animate({
                    duration: options.duration | 700,
                    delta: function (p) {
                        return T.Animation.Easing.easeInOutQuart(p);
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
            },
            restore: function (options) {
                this.el_init_bottom = 30;
                this.el_init_right = 30;
                this.el_init_width = 64;
                this.el_init_height = 64;
                this.el_init_border_radius = 60;
                var self = this;
                options.current_page.style.display = "block";
                if (options.previousStart) {
                    options.previousStart(options);
                }
                T.prototype.Utils.animate({
                    duration: options.duration | 1000,
                    delta: function (p) {
                        return T.Animation.Easing.easeInOutQuart(p);
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
        },
        Cookies: function (prefix) {
            
            var cookies = {};
            
            function prepareCookies() {
                var all_cookies = document.cookie;
                
                var splited_cookies = all_cookies.split("; ");
                
                for (var i = 0, total = splited_cookies.length; i < total; i++) {
                    var parts = splited_cookies[i].split("=");
                    
                    if (prefix) {
                        if (parts[0].indexOf(prefix) !== -1) {
                            cookies[parts[0]] = parts[1];
                        } else {
                            continue;
                        }
                    } else {
                        cookies[parts[0]] = parts[1];
                    }
                }
            };
            
            prepareCookies();
            
            var prototype = {
                getAllKeys: function () {
                    var keys = [], i = 0;
                    for (var key in cookies) {
                        keys[i++] = key;
                    }
                    return keys;
                },
                get: function (name) {
                    return cookies[name];
                },
                deleteAll: function () {
                    var keys = this.getAllKeys(), domain = location.hostname === "localhost" ? "localhost" : ".trialent.com";
                    for (var key in keys) {
                        this.deleteCookie(keys[key], "/", domain);
                    }
                    cookies = {};
                },
                deleteCookie: function (name, path, domain) {
                    document.cookie = name + "=" + ((path) ? ";path=" + path : "") + ((domain) ? ";domain=" + domain : "") +
                      ";expires=Thu, 01 Jan 1970 00:00:01 GMT";
                }
            };
            
            return prototype;
        },
        CSS: {
            change: function (options) {
                try {
                    if (!(on instanceof CSSStyleSheet)) {
                        throw new TypeError("O elemento deve ser um CSS válido!");
                    }
                    var style = on,
                            rules = style.cssRules || style.rules;
                    if (options.Keyframes) {
                        var selectors = Object.getOwnPropertyNames(options.Keyframes);
                        for (var selectorIndex = 0, totalSelectors = selectors.length; selectorIndex < totalSelectors; selectorIndex++) {
                            var selector = selectors[selectorIndex],
                                    keyframes = options.Keyframes[selector];
                            var keyframes_names = Object.getOwnPropertyNames(keyframes);
                            for (var ruleIndex = 0, total = rules.length; ruleIndex < total; ruleIndex++) {
                                var rule = rules[ruleIndex];
                                if (rule instanceof CSSKeyframesRule) {
                                    if (rule.name === selector) {
                                        var css_keyframes = rule.cssRules;
                                        for (var keyframeIndex = 0, totalKeyframes = css_keyframes.length; keyframeIndex < totalKeyframes; keyframeIndex++) {
                                            var keyframe = css_keyframes[keyframeIndex];
                                            for (var keyframeNameIndex = 0, totalKeyframeName = keyframes_names.length; keyframeNameIndex < totalKeyframeName; keyframeNameIndex++) {
                                                var exploded_keyframes_names = keyframes_names[keyframeNameIndex].split(" | ");
                                                for (var control = 0, totalControl = exploded_keyframes_names.length; control < totalControl; control++) {
                                                    var exist = (exploded_keyframes_names[control] !== undefined ? exploded_keyframes_names[control] : exploded_keyframes_names) == keyframe.keyText;
                                                    //console.log((exploded_keyframes_names[control] !== undefined ? exploded_keyframes_names[control] : exploded_keyframes_names) + " : " + keyframe.keyText + " : " + exist);
                                                    if (exist) {
                                                        var real_keyframe_name = exploded_keyframes_names[control] !== undefined ? exploded_keyframes_names.join(" | ") : exploded_keyframes_names;
                                                        var styles_keyframe = keyframes[real_keyframe_name];
                                                        var names_styles_keyframe = Object.getOwnPropertyNames(styles_keyframe);
                                                        for (var i3 = 0, total3 = names_styles_keyframe.length; i3 < total3; i3++) {
                                                            var name = names_styles_keyframe[i3],
                                                                    value = styles_keyframe[name];
                                                            keyframe.style[name] = value;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if (options.Styles) {
                        var selectors = Object.getOwnPropertyNames(options.Styles);
                        for (var selectorIndex = 0, totalSelectors = selectors.length; selectorIndex < totalSelectors; selectorIndex++) {
                            var selector = selectors[selectorIndex],
                                    properties = options.Styles[selector];
                            var property_names = Object.getOwnPropertyNames(properties);
                            for (var i = 0, total = rules.length; i < total; i++) {
                                var rule = rules[i];
                                if (rule instanceof CSSStyleRule) {
                                    var exploded_selector = selector.split(" | ");
                                    for (var control = 0, totalControl = exploded_selector.length; control < totalControl; control++) {
                                        var exist = rule.selectorText === (exploded_selector[control] !== undefined ? exploded_selector[control] : exploded_selector);
                                        console.log((exploded_selector[control] !== undefined ? exploded_selector[control] : exploded_selector) + " : " + rule.selectorText + " : " + exist);
                                        if (exist) {
                                            for (var propertyNameIndex = 0, totalProperties = property_names.length; propertyNameIndex < totalProperties; propertyNameIndex++) {
                                                rule.style[property_names[propertyNameIndex]] = properties[property_names[propertyNameIndex]];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                } catch (e) {
                    console.log(e);
                }
            }
        },
        Account: {
            open: function (options) {
                // 17/10/2016, 15:51:30 - 15:57:01: added multiple popup types: a lot of ways to use TRIAL Account login, in-page & window
                var url = "/Library/account/?defs=" + btoa(JSON.stringify(options));
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
                            window.open("/Library/account/?defs=" + btoa(JSON.stringify(options)), "trl-account-page", "resizable,scrollbars,width=651,height=600");
                            break;
                    }
                }
            },
            logout: function (options) {
                T.Utils.ajax({
                    data: {r: 'lgout'},
                    url: /*(location.origin === 'http://www.trialent.com' || location.origin === 'http://trialent.com' ? './account/' : '/Library/account/')*/ '/Library/account/controldb.php',
                    onloadend: function (e) {
                        var cookies = T().Cookies('TRL_');
                        cookies.deleteAll();
                        location.reload();
                    }
                });
            },
            Clicker: {
                authenticate: function (options) {
                    T().Utils.ajax({
                        data: {r: 'clauth', id: options.id},
                        url: '/Library/account/controldb.php',
                        onloadend: options.onloadend
                    });
                }
            },
            NoRedo: {
                authenticate: function (options) {
                    T().Utils.ajax({
                        data: {r: 'nrauth', id: options.id},
                        url: '/Library/account/controldb.php',
                        onloadend: options.onloadend
                    });
                }
            },
            StreetRace: {
                authenticate: function (options) {
                    T().Utils.ajax({
                        data: {r: 'srauth', id: options.id},
                        url: '/Library/account/controldb.php',
                        onloadend: options.onloadend
                    });
                }
            }
        },
        UI: {
            Logo: {
                TRIAL: {
                    draw: function (options) {
                        var div = document.createElement("div"), 
                                into = on;
                        div.setAttribute("logo", "");
                        if (options.scale) {
                            div.style.width = ((options.width ? parseInt(options.width) : 269.395) / options.scale) + "px";
                            div.style.height = ((options.height ? parseInt(options.height) : 67.6) / options.scale) + "px";
                        } else {
                            div.style.width = options.width ? options.width : "269.395px";
                            div.style.height = options.height ? options.height : "67.6px";
                        }
                        div.innerHTML = '<!-- Created on: 18/07/2016, 08:48 -->' + 
                                        '<!-- Author: TRIAL<João Vitor Ramos> -->' + 
                                '<svg id="svg2" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:cc="http://creativecommons.org/ns#" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:dc="http://purl.org/dc/elements/1.1/" viewBox="0 0 538.79499 135.2" style="width: 100%; height: 100%">' +
                                    '<metadata id="metadata7">' +
                                     '<rdf:RDF>' +
                                      '<cc:Work rdf:about="">' +
                                       '<dc:format>image/svg+xml</dc:format>' +
                                       '<dc:type rdf:resource="http://purl.org/dc/dcmitype/StillImage"/>' +
                                       '<dc:title/>' +
                                      '</cc:Work>' +
                                     '</rdf:RDF>' +
                                    '</metadata>' +
                                    '<g id="layer2" transform="translate(0 -84.8)">' +
                                     '<g id="g4250" transform="translate(-218.75)" style="fill: ' + (options.color ? options.color : '#24c742') + '">' +
                                      '<path id="path4215" d="m95.5 0c-0.369-0.000001-0.738 0.1408-1.021 0.42383v17.869c0 1.15-0.927 2.076-2.077 2.076h-88.453c-2.1877 0-3.949 1.76-3.949 3.947v15.756c0 2.188 1.7613 3.949 3.9492 3.949h22.442c2.188 0 3.949 1.762 3.949 3.95v83.279c0 2.19 1.761 3.95 3.949 3.95h18.377c2.188 0 3.947-1.76 3.947-3.95v-83.279c0-2.188 1.764-3.95 3.951-3.95h30.651c1.364 0.003 4.056-0.919 5.18-2.042l22.545-20.323c0.57-0.566 0.57-1.477 0-2.043l-22.419-19.189c-0.283-0.2832-0.652-0.424-1.021-0.424zm232.95 20.367c-1.15 0-2.08 0.926-2.08 2.076v108.81c0 2.19 1.76 3.95 3.95 3.95h18.38c2.18 0 3.94-1.76 3.94-3.95v-25.69c0-2.19 1.77-3.95 3.95-3.95h38.2c2.19 0 3.95 1.76 3.95 3.95v25.69c0 2.19 1.77 3.95 3.95 3.95h18.38c2.19 0 3.95-1.76 3.95-3.95v-108.81c0-1.147-0.93-2.073-2.08-2.073h-94.49zm-187.21 0.002c-1.15 0-2.08 0.924-2.08 2.074l-0.13 108.81c0 2.19 1.76 3.95 3.95 3.95h18.38c2.18 0 3.94-1.76 3.94-3.95v-83.279c0-2.188 1.76-3.95 3.95-3.95h48.99c2.19 0 3.95 1.762 3.95 3.95v15.043c0 7.512 5.69 13.601 12.7 13.601l6.13-0.023c2.19 0 3.95-1.763 3.95-3.951v-23.02c-1.36-16.382-15.09-29.252-31.82-29.252h-71.91zm133.38 0c-2.19 0-3.95 1.76-3.95 3.947v106.59c0 2.19 1.76 3.95 3.95 3.95h18.37c2.19 0 3.95-1.76 3.95-3.95v-106.59c0-2.191-1.76-3.951-3.95-3.951h-18.37zm182.4 0c-2.19 0-3.95 1.76-3.95 3.947v106.93c0 2.19 1.76 3.95 3.95 3.95h77.82c2.19 0 3.95-1.76 3.95-3.95v-18.37c0-2.19-1.76-3.96-3.95-3.96h-51.54c-2.19 0-3.95-1.76-3.95-3.94v-80.664c0-2.187-1.76-3.947-3.95-3.947h-18.38zm-100.43 23.652h38.2c2.19 0 3.95 1.764 3.95 3.952v23.412c0 2.188-1.76 3.949-3.95 3.949h-38.2c-2.18 0-3.95-1.761-3.95-3.949v-23.412c0-2.188 1.77-3.952 3.95-3.952z" fill-rule="evenodd" transform="translate(218.75 84.8)"/>' +
                                     '</g>' +
                                    '</g>' +
                                   '</svg>';
                        into.appendChild(div);
                    }
                },
                Compact: {
                    TRIAL: {
                        draw: function (options) {
                            var div = document.createElement("div"), 
                                    into = on;
                            div.setAttribute("logo", "");
                            if (options.scale) {
                                div.style.width = ((options.width ? parseInt(options.width) : 118.8) / (1 / options.scale)) + "px";
                                div.style.height = ((options.height ? parseInt(options.height) : 135.2) / (1 / options.scale)) + "px";
                            } else {
                                div.style.width = options.width ? options.width : "118.8px";
                                div.style.height = options.height ? options.height : "135.2px";
                            }
                            div.innerHTML = '<!-- Created on: 18/07/2016, 09:00 -->' + 
                                            '<!-- Author: TRIAL<João Vitor Ramos> -->' + 
                                '<svg id="svg2" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:cc="http://creativecommons.org/ns#" xmlns:dc="http://purl.org/dc/elements/1.1/" viewBox="0 0 118.8 135.2" style="width: 100%; height: 100%">' + 
                                    '<metadata id="metadata7">' +
                                     '<rdf:RDF>' +
                                      '<cc:Work rdf:about="">' +
                                       '<dc:format>image/svg+xml</dc:format>' +
                                       '<dc:type rdf:resource="http://purl.org/dc/dcmitype/StillImage"/>' +
                                       '<dc:title/>' +
                                      '</cc:Work>' +
                                     '</rdf:RDF>' +
                                    '</metadata>' +
                                    '<g id="layer2" transform="translate(0 -84.8)">' +
                                     '<g id="g4250" transform="translate(-218.75)" style="fill: ' + (options.color ? options.color : '#24c742') + '">' +
                                      '<path T id="path4215" d="m313.8 84.8c-0.3676-0.000001-0.73494 0.1408-1.0166 0.42383v17.869c0 1.1501-0.92167 2.0762-2.0663 2.0762h-88.033c-2.1775 0-3.9305 1.7594-3.9305 3.9473v15.756c0 2.1879 1.753 3.9492 3.9305 3.9492h22.335c2.1775 0 3.9305 1.7613 3.9305 3.9492v83.279c0 2.1879 1.753 3.9492 3.9305 3.9492h18.29c2.1775 0 3.9285-1.7613 3.9285-3.9492v-83.279c0-2.1879 1.7549-3.9492 3.9324-3.9492h30.505c1.3578 0.003 4.0368-0.91931 5.1551-2.043l22.44-20.322c0.56338-0.56605 0.56338-1.4769 0-2.043l-22.33-19.206c-0.28168-0.28303-0.649-0.42383-1.0166-0.42383z" fill-rule="evenodd"/>' +
                                     '</g>' +
                                    '</g>' +
                                '</svg>';
                            into.appendChild(div);
                        }
                    }
                }
            },
            Copyright: {
                OWN: "own",
                DEVELOPED: "developed"
            },
            addFooter: function (options) {
                var footer = document.createElement("footer"),
                        into = on;
                
                if (!options.hasOwnProperty("type")) {
                    options.type = T.prototype.UI.Copyright.OWN;
                }
                
                var logo = document.createElement("trl-logo");
                logo.compact = true;
                logo.color = options.color ? options.color : "#fff";
                logo.scale = options.scale ? options.scale : .5;
                if (options.width || options.height) {
                    logo.width = options.width;
                    logo.height = options.height;
                }
                footer.appendChild(logo);
                
                T(footer).UI.addCopyright(options);
                footer.setAttribute("owner", "");
                footer.classList.add("center-horizontal");
                into.appendChild(footer);
            },
            addCopyright: function (options) {
                var copyright = document.createElement("span"),
                        period = "";
                
                if (options.hasOwnProperty("period")) {
                    if (options.period.hasOwnProperty("since")) {
                        period += options.period.since;
                    }
                    if (options.period.hasOwnProperty("to")) {
                        if (typeof options.period.to === "string") {
                            var current_date = new Date();
                            if (options.period.to === "Y") {
                                options.period.to = current_date.getFullYear();
                            } else if (options.period.to === "y") {
                                options.period.to = current_date.getYear();
                            }
                            if (options.period.to.toString() === options.period.since.toString()) {
                                delete options.period.to;
                            }
                        }
                    }
                    if (options.period.hasOwnProperty("to")) {
                        if (options.period.hasOwnProperty("separator")) {
                            period += " " + options.period.separator + " " + options.period.to;
                        } else {
                            period += " - " + options.period.to;
                        }
                    }
                }
                
                if (options.type === T.prototype.UI.Copyright.OWN) {
                    if (options.hasOwnProperty("phrase")) {
                        copyright.innerHTML = period + " ₢ " + options.copyright + ". " + options.phrase;
                    } else {
                        copyright.innerHTML = period + " ₢ " + options.copyright + ". Todos os direitos reservados";
                    }
                } else {
                    if (options.hasOwnProperty("phrase")) {
                        copyright.innerHTML = period + ". " + options.phrase + " " + options.copyright + ".";
                    } else {
                        copyright.innerHTML = period + ". Desenvolvido por " + options.copyright + ".";
                    }
                }
                
                copyright.setAttribute("copyright", "");
                on.appendChild(copyright);
            }
        },
        /* 
         * Loading added on 21/11/2016, 19:31:30
         * substituted Loading code to new on 05/12/2016, 19:54:25 - 20:21:24
         */
        Loading: function (color, size, duration) {
            var into = on,
                    loading = document.createElement("trl-loading");
            loading.color = color;
            loading.size = size;
            loading.duration = duration;
            return {
                self: loading,
                start: function () {
                    into.style.display = "";
                    into.appendChild(loading);
                    loading.start();
                },
                stop: function () {
                    loading.stop();
                    into.style.display = "none";
                    into.removeChild(loading);
                }
            };
        }
    };
    return T;
})();

/* created on 03/03/2017, 21:33:58
 * 
 * @returns {TL#1572.JSONObject}
 */
function JSONObject() {}
/* 
 * created on 01/03/2017, 22:57:57
 * 03/03/2017, 21:33 => renamed Object to JSONObject;
 * 
 * 
 * @param {String} name
 * @param {Object} value
 * @returns {TL#1572.JSONObject.prototype}
 */
JSONObject.prototype.put = function (name, value) {
    this[name] = value;
    return this;
};

// created on 16/03/2017, 14:17:51
JSONObject.prototype.remove = function (name) {
    delete this[name];
    return this;
};

// added function 28/10/2016, 15:37:03
(function () {
    // 27/11/2016, 11:29:29 => removed remove Array function
    /*Array.prototype.remove = function (search) {
        var position;
        while ((position = this.indexOf(search)) !== -1) {
            this.splice(position, 1);
        }
    };*/

    /* created on 01/03/2017 22:57:57
    Object.prototype.put = function (name, value) {
        this[name] = value;
        return this;
    };

    // created on 03/03/2017 21:53:08
    Object.prototype.remove = function (name) {
        delete this[name];
        return this;
    };*/

    // created on 14/09/2016, 21:24:18
    T.Constants = {
        /* added on 01/03/2017, 22:49:03 */
        ACTION: "r",
        Response: {
            JSON: "json"
        },
        URL: {
            SERVER: "action"
        },
        /* end added */
        Message: {
            EXIST: "E",
            NOT_EXIST: "NE",
            NOT_ACTIVATED: "NA",
            SAVED_WITH_SUCCESS: "SWS",
            ERROR_PASSWORD_INCORRECT: "ERR_PI",
            MEMBER_WITHOUT_INSTITUTION: "MWI"
        },
        Slideshow: {
            CONTAINER_BACKGROUND: "Cn_bCk_Ssw-Ll"
        },
        // 17/09/2016, 17:42:02
        Paths: {
            IMAGE_PROFILE: '/no-redo/images/TRIAL/logo/icon/social/min/T_icon_social_invert.png'
        }
    };

    // 14/09/2016, 21:27:43
    /*
     * 18/02/2017
     *      13:37:31 => remaned deleteCookie() to delete()
     *      13:38:19 - 14:18:38 => added set(name, value, expires, path, domain)
     */
    T.Cookies = function (prefix) {

        var cookies = {};

        function prepareCookies() {
            var splited_cookies = document.cookie.split("; ");

            for (var i = 0, total = splited_cookies.length; i < total; i++) {
                var parts = splited_cookies[i].split("=");

                if (prefix) {
                    if (parts[0].indexOf(prefix) !== -1) {
                        cookies[parts[0]] = parts[1];
                    } else {
                        continue;
                    }
                } else {
                    cookies[parts[0]] = parts[1];
                }
            }
        };

        prepareCookies();

        var prototype = {
            set: function (name, value, expires, path, domain) {
                if (!(expires instanceof Date)) {
                    throw new TypeError();
                }
                document.cookie = name + "=" + value + (expires ? ";expires=" + expires.toUTCString() : null) + (path ? ";path=" + path : null) + (domain ? ";domain=" + domain : null);
                
            },
            getAllKeys: function () {
                var keys = [], i = 0;
                for (var key in cookies) {
                    keys[i++] = key;
                }
                return keys;
            },
            get: function (name) {
                return cookies[name];
            },
            deleteAll: function () {
                var keys = this.getAllKeys(), domain = location.hostname === "localhost" ? "localhost" : ".trialent.com";
                for (var key in keys) {
                    this.delete(keys[key], "/", domain);
                }
                cookies = {};
            },
            delete: function (name, path, domain) {
                document.cookie = name + "=" + ((path) ? ";path=" + path : "") + ((domain) ? ";domain=" + domain : "") +
                  ";expires=Thu, 01 Jan 1970 00:00:01 GMT";
            }
        };

        return prototype;
    };

    // 14/09/2016, 21:30:43
    T.URL = {
        // 17/09/2016, 17:31:04 - 18:06:57
        exists: function (url, callback) {
            T.Utils.ajax({
                url: url,
                sendRequestedWithHeader: false,
                onerror: function (e) {
                    callback(false, e);
                },
                onloadend: function (e) {
                    callback(true, e);
                }
            });
        },
        Parameter: {
            add: function (key, value) {
                var href = location.href;
                var url = href.split("?");
                var pairs = href.indexOf("&") !== -1 ? url[1].split("&") : ""; 
                var key_arr = [], parameters;
                if (url.length >= 2) {
                if (pairs !== "") {
                    for (var i = 0, total = pairs.length; i < total; i++) {
                        var arr = pairs[i].split("=");
                        key_arr[i] = arr[0];
                        if (arr[0] == key) {
                            pairs[i] = arr[0] + "=" + value;
                        }
                    }
                    if (key_arr.indexOf(key) === -1) {
                        pairs.push(key + "=" + value);
                    }
                } else {
                    var arr = url[1].split("=");
                    pairs = [];
                    pairs[0] = url[1];
                    if (arr[0] !== key) {
                        pairs.push(key + "=" + value);
                    }
                }
                } else {
                    pairs = [];
                    pairs.push(key + "=" + value);
                }
                parameters = pairs.join("&");
            },
            get: function (key) {
                var href = location.href;
                var url = href.split("?");
                var pairs = href.indexOf("&") !== -1 ? url[1].split("&") : null;
                if (pairs != null) {
                    var key = [], parameters;
                    if (url.length >= 2) {
                        for (var i = 0, total = pairs.length; i < total; i++) {
                            var arr = pairs[i].split("=");
                            if (arr[0] == key) {
                                return arr[1];
                            }
                        }
                    }
                }
                if (url[1] !== null && url[1] !== undefined) {
                    pairs = url[1].split("=");
                    if (pairs[0] == key) {
                        return pairs[1];
                    }
                }
                return null;
            }
        }
    };

    // 14/09/2016, 21:33:09
    T.Utils = {
        // 14/09/2016, 21:47:55
        isNode: function (element) {
            return (typeof Node === "object" ? element instanceof Node : element && typeof element === "object" && element !== null && element.nodeType === "number" & typeof element.nodeName === "string");
        },
        isElement: function (element) {
            return (typeof HTMLElement === "object" ? element instanceof HTMLElement : element && typeof element === "object" && element !== null && element.nodeType === 1 & typeof element.nodeName === "string");
        },
        ajax: function (options) {
            var e = options.event || event,
                    xhr = new XMLHttpRequest,
                    method = "GET",
                    url,
                    data;
            
            if (e) e.preventDefault();

            if (xhr.timeout) xhr.timeout = options.timeout ? options.timeout : 30000;

            // modified block on 27/11/2016, 11:20:11 til 11:23:19 => added support to FormData too
            if (options.data) {
                method = "POST";
                if (this.isElement(options.data) || this.isNode(options.data)) {
                    method = options.data.getAttribute("method");
                    url = options.data.getAttribute("action");
                    data = new FormData(options.data);
                    prepareData();
                } else {
                    if (options.data instanceof FormData) {
                        data = options.data;
                        prepareData();
                    } else data = JSON.stringify(options.data);
                    url = options.url;
                }
            } else url = options.url || null;
            
            if (!url) throw new Error("Request url is empty");
            
            if (options.method) method = options.method;

            xhr.onloadstart = function (e) {
                eventHandler(e, options.onloadstart);
            };
            xhr.onprogress = function (e) {
                eventHandler(e, options.onprogress);
            };
            xhr.onload = function (e) {
                eventHandler(e, options.onload);
            };
            xhr.onloadend = function (e) {
                eventHandler(e, options.onloadend);
            };
            xhr.onabort = function (e) {
                eventHandler(e, options.onabort);
            };
            xhr.onerror = function (e) {
                eventHandler(e, options.onerror);
            };
            xhr.ontimeout = function (e) {
                eventHandler(e, options.ontimeout);
            };

            xhr.open(method, url);

            // added on 15/09/2016, 19:51:09
            if (options.response && options.response !== T.Constants.Response.JSON)
                xhr.responseType = options.response;

            // added on 17/09/2016, 17:56:44 - 17:59:23
            if (!options.hasOwnProperty("sendRequestedWithHeader") || options.sendRequestedWithHeader === true)
                xhr.setRequestHeader("X-REQUESTED-WITH", "xmlhttprequest");

            if (data) xhr.send(data);
            else      xhr.send();
            
            function prepareData() {
                if (options.additional_data) {
                    var names = Object.getOwnPropertyNames(options.additional_data);
                    for (var i = 0, total = names.length; i < total; i++) {
                        data.append(names[i], options.additional_data[names[i]]);
                    }
                }
            }
            
            // created on 15/09/2016, 19:44:03
            function eventHandler(e, custom) {
                if (custom) {
                    // added on 11/03/2017, 15:59 ::::
                    var response = options.response === T.Constants.Response.JSON ? (e.target.responseText ? JSON.parse(e.target.responseText) : null) : e.target.response;
                    // :::: end added
                    custom({target: {response: response, status: e.target.status}});
                }
            }
        },
        /* 24/10/2016
         *      19:30:52 => added scroll()
         *      19:59:38 => 
         *          added support to horizontal scroll
         *          options.to => options.y (vertical) / options.x (horizontal)
         */
        scroll: function (options) {
            var y = {
                    initial: window.pageYOffset,
                    distance: 0
                },
                x = {
                    initial: window.pageXOffset,
                    distance: 0
                }
            if (!options.hasOwnProperty("x")) {
                options.x = x.initial;
            }
            if (options.x !== x.initial) {
                if (options.x < x.initial) {
                    x.distance = -(x.initial - options.x);
                } else {
                    x.distance = options.x - x.initial;
                }
            }
            if (!options.hasOwnProperty("y")) {
                options.y = y.initial;
            }
            if (options.y !== y.initial) {
                if (options.y < y.initial) {
                    y.distance = -(y.initial - options.y);
                } else {
                    y.distance = options.y - y.initial;
                }
            }
            if (y.distance !== 0 || x.distance !== 0) {
                T.Animation.start({
                    duration: options.duration || 300,
                    delta: function (p) {
                        return T.Animation.Easing.easeInOutQuart(p);
                    },
                    step: function (delta) {
                        window.scrollTo(x.initial + (x.distance * delta), y.initial + (y.distance * delta));
                    }
                });
            }
        }
    };

    // 17/10/2016, 19:28:03
    T.Account = {
        open: function (options) {
            // 17/10/2016, 15:51:30 - 15:57:01: added multiple popup types: a lot of ways to use TRIAL Account login, in-page & window
            var url = "/Library/account/" + btoa(JSON.stringify(options));
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
                        window.open("/Library/account/" + btoa(JSON.stringify(options)), "trl-account-page", "resizable,scrollbars,width=651,height=600");
                        break;
                }
            }
        },
        logout: function (after) {
            // 18/10/2014, 00:42:33 => added after callback and updated the usage of deprecated T library codes
            T.Utils.ajax({
                data: {r: 'lgout'},
                url: '/Library/account/action',
                onloadend: function () {
                    var cookies = T.Cookies('TRL_');
                    cookies.deleteAll();
                    if (after) {
                        after();
                    } else {
                        location.reload();
                    }
                }
            });
        },
        Clicker: {
            authenticate: function (options) {
                T.Utils.ajax({
                    data: {r: 'clauth', id: options.id},
                    url: '/Library/account/action',
                    response: "json",
                    onloadend: options.onloadend
                });
            }
        },
        NoRedo: {
            authenticate: function (options) {
                T.Utils.ajax({
                    data: {r: 'nrauth', id: options.id},
                    url: '/Library/account/action',
                    response: "json",
                    onloadend: options.onloadend
                });
            }
        },
        StreetRace: {
            authenticate: function (options) {
                T.Utils.ajax({
                    data: {r: 'srauth', id: options.id},
                    url: '/Library/account/action',
                    response: "json",
                    onloadend: options.onloadend
                });
            }
        },
        // added on 21/10/2016, 18:09:32
        OpportunityNow: {
            authenticate: function (options) {
                T.Utils.ajax({
                    data: {r: 'onauth', id: options.id},
                    url: '/Library/account/action',
                    response: "json",
                    onloadend: options.onloadend
                });
            }
        },
        // added on 22/11/2016, 18:58:30
        FixIt: {
            authenticate: function (options) {
                T.Utils.ajax({
                    data: {r: 'fiauth', id: options.id, type: options.type},
                    url: '/Library/account/action',
                    response: "json",
                    onloadend: options.onloadend
                });
            }
        }
    };

    // added on 24/10/2016, 19:45:39
    T.Animation = {
        /*
         * Easing Functions - inspired from http://gizma.com/easing/
         * only considering the t value for the range [0, 1] => [0, 1]
         * 
         * by: gre / easing.js, GitHub
         * 
         * transfered on 24/10/2016, 19:49:04
         * renamed easing to Easing on 24/10/2016, 19:49:39
         */
        Easing: {
            // no easing, no acceleration
            linear: function (t) { return t; },
            // accelerating from zero velocity
            easeInQuad: function (t) { return t*t; },
            // decelerating to zero velocity
            easeOutQuad: function (t) { return t*(2-t); },
            // acceleration until halfway, then deceleration
            easeInOutQuad: function (t) { return t<.5 ? 2*t*t : -1+(4-2*t)*t; },
            // accelerating from zero velocity 
            easeInCubic: function (t) { return t*t*t; },
            // decelerating to zero velocity 
            easeOutCubic: function (t) { return (--t)*t*t+1; },
            // acceleration until halfway, then deceleration 
            easeInOutCubic: function (t) { return t<.5 ? 4*t*t*t : (t-1)*(2*t-2)*(2*t-2)+1; },
            // accelerating from zero velocity 
            easeInQuart: function (t) { return t*t*t*t; },
            // decelerating to zero velocity 
            easeOutQuart: function (t) { return 1-(--t)*t*t*t; },
            // acceleration until halfway, then deceleration
            easeInOutQuart: function (t) { return t<.5 ? 8*t*t*t*t : 1-8*(--t)*t*t*t; },
            // accelerating from zero velocity
            easeInQuint: function (t) { return t*t*t*t*t; },
            // decelerating to zero velocity
            easeOutQuint: function (t) { return 1+(--t)*t*t*t*t; },
            // acceleration until halfway, then deceleration 
            easeInOutQuint: function (t) { return t<.5 ? 16*t*t*t*t*t : 1+16*(--t)*t*t*t*t; }
        },
        /*
         * transfered on 24/10/2016, 19:46:07
         * renamed animate to start on 24/10/2016, 19:46:53
         */ 
        start: function (options) {
            var start = new Date,
            animation = setInterval(function () {
                var timePassed = new Date - start;
                var progress = timePassed / options.duration;
                if (progress > 1) {
                    progress = 1;
                }
                var delta = options.delta(progress);
                options.step(delta, progress);
                if (progress === 1) {
                    // 03/11/2016, 10:55:44 => onfinish callback added
                    if (options.onfinish) {
                        options.onfinish();
                    }
                    clearInterval(animation);
                }
            }, options.delay || 10);
        }
    };
    
    // added on 22/11/2016, 18:54:58
    T.App = {
        // added constants on 22/11/2016, 18:52:59
        FIX_IT: "fi",
        CLICKER: "cl",
        OPPORTUNITY_NOW: "on",
        STREET_RACE: "sr",
        NO_REDO: "nr"
    };
    
    /* Ajax Page Loading: 
     * 
     * created on 27/12/2016, 00:58:55 
     * added to Library on 26/02/2017, 18:43:25
     */
    T.DynamicPageLoading = function (options) {
        var that = this,
                state = {title: document.title, url: location.href, page: document.documentElement.innerHTML};
        this.options = options;
        if (options.onsave) {
            options.onsave(state);
        }
        T.History.replace(state);
        window.addEventListener("popstate", function (e) {
            that.load({e: e});
        });
    };
    
    T.DynamicPageLoading.prototype.load = function (options) {
        var that = this;
        
        if (!options.url) {
            loadPage(options.e.state.page);
        } else {
            T.Utils.ajax({
                event: options.e,
                url: options.url,
                method: "GET",
                onloadend: function (e) {
                    loadPage(e.target.response, true);
                }
            });
        }

        function loadStyles(html) {
            var styles = {
                    current: [],
                    loaded: html.getElementsByClassName("ajax-style")
                };

            for (var i = 0, total = document.styleSheets.length; i < total; i++) {
                styles.current.push(document.styleSheets[i].href);
            }
            var d = document.getElementsByClassName("ajax-style");
            for (i = 0, total = d.length; i < total; i++) {
                document.head.removeChild(d[i]);
            }
            for (i = 0, total = styles.loaded.length; i < total; i++) {
                if (styles.current.indexOf(styles.loaded[i].href) === -1) {
                    if (styles.loaded[i] instanceof HTMLStyleElement) {
                        var style = document.createElement("style");
                        style.innerHTML = styles.loaded[i].innerHTML;
                    } else {
                        var style = document.createElement("link");
                        style.rel = "stylesheet";
                        style.type = "text/css";
                        style.href = styles.loaded[i].href;
                    }
                    style.classList.add("ajax-style");
                    document.head.appendChild(style);
                }
            }
        }

        function loadScripts(html) {
            var scripts = {
                    current: [],
                    loaded: html.getElementsByClassName("ajax-script")
                };
            for (var i = 0, total = document.scripts.length; i < total; i++) {
                if (document.scripts[i].classList.contains("ajax-style")) {
                    document.body.removeChild(document.styleSheets[i]);
                    continue;
                }
                if (document.scripts[i].src !== undefined && document.scripts[i].src !== null && document.scripts[i].src !== "") {
                    scripts.current.push(document.scripts[i].src);
                }
            }
            for (i = 0, total = scripts.loaded.length; i < total; i++) {
                if (scripts.current.indexOf(scripts.loaded[i].src) === -1) {
                    var script = document.createElement("script");
                    script.innerHTML = scripts.loaded[i].innerHTML;
                    script.classList.add("ajax-script");
                    document.body.appendChild(script);
                }
            }
        }

        function loadPage(response, update) {
            var html = new DOMParser().parseFromString(response, "text/html"),
                    content = {
                        current: document.getElementById("ajax-content"),
                        loaded: html.getElementById("ajax-content")
                    };
            content.current.className = content.loaded.className;
            content.current.innerHTML = content.loaded.innerHTML;
            document.documentElement.className = html.documentElement.className;
            document.title = html.title;
            loadStyles(html);
            loadScripts(html);
            if (update) {
                var state = {title: document.title, url: location.href, page: response};
                if (that.options.onsave) {
                    that.options.onsave(state);
                }
                T.History.add(state, html.title, options.url);
            }
            if (that.options.onloaded) {
                var custom = options;
                custom.page = html;
                that.options.onloaded(custom);
            }
        }
    };
    
    // added on 27/12/2016, 00:55:11
    T.History = {
        add: function (state, title, url) {
            if (history.pushState) {
                try {
                    history.pushState(state, title, url);
                    document.title = title;
                } catch (e) {}
            } else {
                location.assign(url);
            }
        },
        replace: function (state, title, url) {
            try {
                history.replaceState(state, title || null, url || null);
            } catch (e) {}
        }
    };
    /* End Ajax Page Loading */
})();

// added function 28/10/2016, 15:35:12
(function () {
    
    /* replaced to Modules.Polyfills on 12/03/2017, 01:15:36
    // added 28/10/2016, 15:32:36
    var modules = [
        'https://cdnjs.cloudflare.com/ajax/libs/webcomponentsjs/0.7.24/webcomponents-lite.js',
        'https://cdnjs.cloudflare.com/ajax/libs/classlist/1.1.20150312/classList.min.js' // added on 11/03/2017, 21:09:49
        //'/Library/account/polyfills/es6-promise.auto.min.js' // added on 04/03/2017, 14:40:17
    ];
    */
    // fixed load function error on 26/11/2016, 16:10:25:
    for (var i = 0, total = Modules.Polyfills.length; i < total; i++) {
        var script = document.createElement("script");
        script.src = Modules.Polyfills[i];
        document.body.appendChild(script);
    }

    // Registering new elements
    // 26/10/2016, 02:37:48 => load -> WebComponentsReady
    window.addEventListener("WebComponentsReady", function () {
        document.registerElement("trl-table", {
            prototype: Object.create(HTMLDivElement.prototype)
        });
        document.registerElement("trl-trow", {
            prototype: Object.create(HTMLDivElement.prototype)
        });
        document.registerElement("trl-tcolumn", {
            prototype: Object.create(HTMLDivElement.prototype)
        });
        document.registerElement("trl-tabs-contents", {
            prototype: Object.create(HTMLDivElement.prototype, {
                createdCallback: {
                    value: function () {
                        //this.style.cssFloat = "left";
                        //this.style.width = "100%";
                    }
                }
            })
        });
        document.registerElement("trl-tab-content", {
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
        document.registerElement("trl-tabs", {
            prototype: Object.create(HTMLDivElement.prototype, {
                createdCallback: {
                    value: function () {
                        var tabs = this;
                        // Last edition: 17/08/2016, 23:44:35
                        var observer = new MutationObserver(function () {
                            setup();
                        });
                        observer.observe(tabs, {
                            attributes: true,
                            childList: true
                        });

                        window.addEventListener("load", function () {
                            if (tabs.children.length > 0) {
                                setup();
                            }
                        }, false);

                        setup();

                        function setup() {
                            var tabs_for = document.getElementById(tabs.getAttribute("trl-for"));
                            for (var i = 0, total = tabs.children.length; i < total; i++) {
                                var tab = tabs.children[i];
                                tab.addEventListener("click", (function (index, tabs) {
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
                                })(i, tabs));
                            }
                            var default_tab = tabs.getAttribute("trl-default") || 0;
                            if (default_tab === 0 || default_tab !== "none") {
                                tabs.children[parseInt(default_tab)].click();
                            }
                        }

                        function animShow(el) {
                            var original_width = +(window.getComputedStyle(el, null).getPropertyValue("width").replace("px", ""));
                            el.style.width = original_width + "px";
                            el.style.position = "absolute";
                            el.style.display = "";
                            el.style.opacity = 0;
                            T.Animation.start({
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

                        function animHide(el) {
                            el.style.position = "absolute";
                            T.Animation.start({
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
            }),
            extends: "nav"
        });
        document.registerElement("trl-tab", {
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
        /* 21/10/2016
         *      23:44:09
         *          added placeholder object with get and set with attr trl-placeholder (work together)
         *          trl-editable textarea type placeholder works only with placeholder object | trl-placeholder, because textarea uses innerHTML to define his value
         *      21:24:09
         *          now textarea takes the placeholder object and inserts into the placeholder attr in input
         *          
         * 23/01/2017
         *      01:30:58 - 02:20:24 => added copyAttrs(to): this function copy all trl-editable non-"trl-" prefixed attributes to the created input, select, or textarea
         */
        document.registerElement("trl-editable", {
            prototype: Object.create(HTMLDivElement.prototype, {
                createdCallback: {
                    value: function () {
                        var that = this;
                        this.observer = new MutationObserver(observerCallback);
                        this.observer.observe(this, {childList: true});
                        this.init();
                        // ultima edição ~= 29/07/2016 21:05:56
                        // ultima edição ~= 30/07/2016 21:13:54

                        function observerCallback(records, instance) {
                            // 19/10/2016, 23:48:54 => title attr is now used, if exists, to define a substitute of undefined innerHTML
                            that.undefined_innerHTML = that.innerHTML === undefined || that.innerHTML === null || that.innerHTML === "" || that.innerHTML === "Definir valor" || that.innerHTML === "Definir " + that.title;
                            if (that.undefined_innerHTML) {
                                that.innerHTML = that.placeholder || "Definir " + (that.title || "valor");
                            }
                        }

                        // 19/10/2016, 21:36:48: call observerCallback() when createdCallback is called
                        observerCallback();
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
                            if (old_value !== new_value && new_value === "false") {
                                this.activeEditMode = false;
                            }
                        }
                        if (attr_name === "trl-placeholder") {
                            if (old_value !== new_value) {
                                this.placeholder = new_value;
                            }
                        }
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
                        var that = this;
                        if (this.hasAttribute("trl-placeholder")) {
                            this.placeholder = this.getAttribute("trl-placeholder");
                        }
                        this.content = this.getAttribute("trl-content");
                        this.clickable = this.hasAttribute("trl-clickable") ? (this.getAttribute("trl-clickable").toLowerCase() === "true") : true;
                        this.keyControl = this.hasAttribute("trl-key-control") ? (this.getAttribute("trl-key-control").toLowerCase() === "true") : true;

                        // 21/10/2016, 22:29:26 => now isn't necessary to have trl-onedit
                        if (this.content) {
                            var oneditname = this.getAttribute("trl-onedit");
                            if (oneditname) {
                                var onedit = window[oneditname];
                                if (typeof onedit === "function") {
                                    this.addEventListener("edit", function (e) {
                                        onedit(e);
                                    });
                                }
                            }
                            if (this.clickable) {
                                try {
                                    this.onclick = function () {
                                        that.activeEditMode = true;
                                    };
                                } catch (e) {}
                            }
                        }

                        function error() {
                            console.error("O elemento 'trl-editable' deve possuir o atributo 'trl-content' e 'trl-onedit'");
                        }
                    }
                },
                activeEditMode: {
                    set: function (active) {
                        var that = this;

                        if (active) {
                            activeEditMode();
                        } else {
                            finishEdition();
                        }

                        function activeEditMode() {
                            that.observer.disconnect();
                            that.original_value = that.undefined_innerHTML || that.innerHTML === that.placeholder ? "" : that.innerHTML;
                            switch (that.content) {
                                case 'select':
                                    var select_data = JSON.parse(atob(that.getAttribute("data-select"))),
                                            select = document.createElement("select");
                                    for (var i = 0, total = select_data.length; i < total; i++) {
                                        var data = select_data[i],
                                                option = document.createElement("option");

                                        option.value = data.value;
                                        option.innerHTML = data.innerHTML;
                                        if (data.innerHTML === that.original_value) {
                                            option.selected = true;
                                        }

                                        select.appendChild(option);
                                    }
                                    if (that.keyControl) {
                                        select.onkeydown = function (e) {
                                            switch (e.keyCode) {
                                                case 13:
                                                    save();
                                                    break;
                                            }
                                        };
                                    }
                                    copyAttrs(select);
                                    select.onblur = save;
                                    select.style.width = "auto";
                                    select.style.minWidth = that.offsetWidth + "px";
                                    that.innerHTML = "";
                                    that.appendChild(select);
                                    select.focus();

                                    break;
                                case 'textarea':
                                    var textarea = document.createElement("textarea");
                                    textarea.placeholder = that.placeholder;
                                    textarea.innerHTML = that.original_value;
                                    if (that.keyControl) {
                                        textarea.onkeydown = function (e) {
                                            switch (e.keyCode) {
                                                case 13:
                                                    save();
                                                    break;
                                            }
                                        };
                                    }
                                    copyAttrs(textarea);
                                    textarea.onclick = function () {
                                        event.stopPropagation();
                                    };
                                    textarea.onblur = save;
                                    textarea.style.width = "auto";
                                    textarea.style.minWidth = that.offsetWidth + "px";
                                    that.innerHTML = "";
                                    that.appendChild(textarea);
                                    textarea.focus();

                                    break;
                                default:
                                    var input = document.createElement("input");
                                    input.type = that.content;
                                    // 19/10/2016, 23:51:13 => now input placeholder is defined by title attr
                                    input.placeholder = that.title || "";
                                    input.value = that.original_value;
                                    copyAttrs(input);
                                    if (that.keyControl) {
                                        input.onkeydown = function (e) {
                                            switch (e.keyCode) {
                                                case 13:
                                                    save();
                                                    break;
                                            }
                                        };
                                    }
                                    input.onclick = function () {
                                        event.stopPropagation();
                                    };
                                    input.onblur = save;
                                    input.style.width = that.offsetWidth + "px";
                                    that.innerHTML = "";
                                    that.appendChild(input);
                                    input.focus();
                            }
                            that.onclick = null;

                            function save() {
                                if (event.target.nodeName === "SELECT") {
                                    that.editing_value = event.target.options[event.target.selectedIndex].innerHTML;
                                } else {
                                    that.editing_value = event.target.value;
                                }
                                if (that.editing_value !== that.original_value) {
                                    that.saving = true;
                                }
                                that.observer.observe(that, {childList: true});
                                that.setAttribute("trl-editing", "false");
                            };
                        };
                        
                        function copyAttrs(to) {
                            if (that.hasAttributes()) {
                                for (var attr, i = 0, attrs = that.attributes, total = attrs.length; i < total; i++) {
                                    attr = attrs[i];
                                    if (attr.name !== "id" && attr.name !== "class" && attr.name !== "name" && attr.name.indexOf("trl-") === -1) {
                                        to.setAttribute(attr.name, attr.value);
                                    }
                                }
                            }
                        }

                        function finishEdition() {
                            var element = that.children[0];
                            if (that.saving && that.editing_value !== that.original_value) {
                                try {
                                    // 20/10/2016, 23:21:24 => removed typeof that.onedit check
                                    that.dispatchEvent(new CustomEvent("edit", {
                                        detail: {
                                            value: element.value
                                        }
                                    }));
                                } catch (e) {}
                            } else {
                                that.saving = false;
                                desactiveEditMode();
                            }
                        }

                        function desactiveEditMode() {
                            if (!that.saving) {
                                that.original_value = that.editing_value;
                                if (that.clickable) {
                                    that.onclick = function () {
                                        that.setAttribute("trl-editing", "true");
                                    };
                                }
                                that.innerHTML = that.original_value;
                            }
                        };
                    }
                }
            })
        });
        document.registerElement("trl-selectable-items", {
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
                        if (this.getAttribute("trl-state") === "active") {
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
                            this.selectItem(this.children[i]);
                        }
                    }
                },
                deselectAll: {
                    value: function () {
                        for (var i = 0, total = this.children.length; i < total; i++) {
                            this.deselectItem(this.children[i]);
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

        /*
         * TRIAL Circle Percentage
         * Added on: 18/08/2016, 13:38:31
         * 
         * Modification of CSS Script "Pure CSS Circular Percentage Bar"
         * in http://www.cssscript.com/pure-css-circular-percentage-bar/
         */
        document.registerElement("trl-circle-percentage", {
            prototype: Object.create(HTMLDivElement.prototype, {
                createdCallback: {
                    value: function () {
                        var circle = this,
                                loaded_style = false;
                        for (var i in document.styleSheets) {
                            if (document.styleSheets[i].href && document.styleSheets[i].href.indexOf("circle-percentage.css") !== -1) {
                                loaded_style = true;
                                break;
                            }
                        }
                        if (!loaded_style) {
                            var style = document.createElement("link");
                            style.rel = "stylesheet";
                            style.type = "text/css";
                            style.href = "/AnimacaoCSS/circle-percentage.css";
                            document.head.appendChild(style);
                        }
                        this.percentage = document.createElement("span");
                        this.slice = document.createElement("div");
                        this.bar = document.createElement("div");
                        this.fill = document.createElement("div");

                        this.bar.classList.add("bar");
                        this.fill.classList.add("fill");

                        this.slice.classList.add("slice");
                        this.slice.appendChild(this.bar);
                        this.slice.appendChild(this.fill);

                        this.percentage.innerHTML = this.getAttribute("trl-percentage") + "%";
                        circle.appendChild(this.percentage);
                        circle.appendChild(this.slice);
                    }
                },
                attributeChangedCallback: {
                    value: function (attr_name, old_value, new_value) {
                        switch (attr_name) {
                            case "trl-percentage":
                                this.percentage.innerHTML = new_value + "%";
                                break;
                        }
                    }
                }
            })
        });

        /*
         * TRIAL Circle Percentage
         * Added on: 21/09/2016, 12:45:26
         * 
         * Modification of CSS Script "Pure CSS Circular Percentage Bar"
         * in http://www.cssscript.com/pure-css-circular-percentage-bar/
         */
        document.registerElement("trl-popup", {
            prototype: Object.create(HTMLDivElement.prototype, {
                createdCallback: {
                    value: function () {
                        this.show = false;
                    }
                },
                attributeChangedCallback: {
                    value: function (attr_name, old_value, new_value) {
                        switch (attr_name) {
                            case "trl-show":
                                this.show(new_value);
                                break;
                        }
                    }
                },
                show: {
                    set: function (show) {
                        this.showing = show;
                        T(this.querySelector("[content]")).Standards.open({
                            background: this.querySelector("[background]"),
                        });
                    },
                    get: function () {
                        return this.showing;
                    }
                }
            })
        });
        
        /*
         * 21/11/2016, 19:24:49
         */
        
        document.registerElement("trl-loading", {
            prototype: Object.create(HTMLDivElement.prototype, {
                createdCallback: {
                    value: function () {
                        var that = this;
                        for (var i = 0, total = document.styleSheets.length; i < total; i++) {
                            if (document.styleSheets[i].href && document.styleSheets[i].href.indexOf("loading.css") !== -1) {
                                this.stylesheet = document.styleSheets[i];
                                break;
                            }
                        }
                        if (!this.stylesheet) {
                            var link = document.createElement("link");
                            link.rel = "stylesheet";
                            link.type = "text/css";
                            link.href = "/AnimacaoCSS/loading.css";
                            link.onload = function () {
                                for (var i = 0, total = document.styleSheets.length; i < total; i++) {
                                    if (document.styleSheets[i].href && document.styleSheets[i].href.indexOf("/AnimacaoCSS/loading.css") !== -1) {
                                        that.stylesheet = document.styleSheets[i];
                                        break;
                                    }
                                }
                                that.updateStyle();
                            };
                            document.head.appendChild(link);
                        }
                    }
                },
                // added on 26/11/2016, 11:54:04
                attributeChangedCallback: {
                    value: function (attr_name, old_value, new_value) {
                        this.updateStyle();
                    }
                },
                attachedCallback: {
                    value: function () {
                        this.updateStyle();
                    }
                },
                color: {
                    set: function (color) {
                        this.setAttribute("trl-color", color || "#24c742");
                    },
                    get: function () {
                        return this.getAttribute("trl-color") || "#24c742";
                    }
                },
                size: {
                    set: function (size) {
                        this.setAttribute("trl-size", size || "100%");
                    },
                    get: function () {
                        return this.getAttribute("trl-size") || "100%";
                    }
                },
                duration: {
                    set: function (duration) {
                        this.setAttribute("trl-duration", duration || "8s");
                    },
                    get: function () {
                        return this.getAttribute("trl-duration") || "8s";
                    }
                },
                // 26/11/2016, 12:03:59
                updateStyle: {
                    value: function () {
                        var color = this.color, size = this.size, duration = this.duration;
                        var css_changes = {
                            Styles: {
                                "[loading] | [loading] [triangle][up][left] | [loading] [triangle][up][right][mid-top] | [loading] [triangle][up][right][mid-bottom] | [loading] [triangle][down][left] | [loading] [triangle][down][right]": {
                                    animationDuration: duration
                                },
                                "[loading]": {
                                    width: size,
                                    height: size
                                },
                                "[triangle-color]": {
                                    fill: color
                                }
                            }
                        };
                        if (this.stylesheet instanceof CSSStyleSheet) {
                            T(this.stylesheet).CSS.change(css_changes);
                        }
                    }
                },
                // Added on 31/01/2017, 16:21:16
                isLoading: {
                    value: function () {
                        return this.children.length > 0;
                    }  
                },
                start: {
                    value: function () {
                        var loading = document.createElement("section"),
                                up_left = document.createElement("div"),
                                down_left = document.createElement("div"),
                                down_right = document.createElement("div"),
                                container_right = document.createElement("div"),
                                up_right_mid_bottom = document.createElement("div"),
                                up_right_mid_top = document.createElement("div"),
                                shape_number = 0;

                        loading.setAttribute("loading", "");

                        up_left.setAttribute("triangle", "");
                        up_left.setAttribute("up", "");
                        up_left.setAttribute("left", "");
                        up_left.innerHTML = createSVGShape("M50 0 L50 50 L0 50 L0 50 Z");

                        down_left.setAttribute("triangle", "");
                        down_left.setAttribute("down", "");
                        down_left.setAttribute("left", "");
                        down_left.innerHTML = createSVGShape("M0 0 L50 0 L50 0 L50 50 Z");

                        down_right.setAttribute("triangle", "");
                        down_right.setAttribute("down", "");
                        down_right.setAttribute("right", "");
                        down_right.innerHTML = createSVGShape("M0 0 L0 50 L50 0 L50 0 Z");

                        container_right.setAttribute("container-triangles", "");

                        up_right_mid_top.setAttribute("triangle", "");
                        up_right_mid_top.setAttribute("up", "");
                        up_right_mid_top.setAttribute("right", "");
                        up_right_mid_top.setAttribute("mid-top", "");
                        up_right_mid_top.innerHTML = createSVGShape("M50 50 L0 50 L50 0 L50 0 Z");

                        up_right_mid_bottom.setAttribute("triangle", "");
                        up_right_mid_bottom.setAttribute("up", "");
                        up_right_mid_bottom.setAttribute("right", "");
                        up_right_mid_bottom.setAttribute("mid-bottom", "");
                        up_right_mid_bottom.innerHTML = createSVGShape("M0 0 L50 0 L50 0 L0 50 Z");

                        container_right.appendChild(up_right_mid_top);
                        container_right.appendChild(up_right_mid_bottom);

                        loading.appendChild(up_left);
                        loading.appendChild(container_right);
                        loading.appendChild(down_left);
                        loading.appendChild(down_right);

                        this.appendChild(loading);

                        function createSVGShape(path) {
                            var id = "f" + (shape_number++);
                            return '<svg viewBox="0 0 50 50" style="width: 100%; height: 100%">' +
                                '<defs>' +
                                  '<filter id="' + id + '" x="0" y="0" width="100%" height="100%">' +
                                    '<feOffset result="offOut" in="SourceAlpha" dx="0" dy="0" />' +
                                    '<feGaussianBlur result="blurOut" in="offOut" stdDeviation="1" />' +
                                    '<feBlend in="SourceGraphic" in2="blurOut" mode="normal" />' +
                                  '</filter>' +
                                '</defs>' +
                                '<path triangle-color d="' + path + '" filter="url(#' + id + ')" />' +
                                'Sorry, your browser does not support inline SVG.' +
                            '</svg>';
                        }

                        return loading;
                    }
                },
                stop: {
                    value: function () {
                        while (this.firstChild) {
                            this.removeChild(this.firstChild);
                        }
                    }
                }
            })
        });

        /*
         * Added on: 05/12/2016, 21:30:18
         * Added this.already_setup, createdCallback(), attachedCallback() on 14/12/2016, 18:03:40
         */
        document.registerElement("trl-button", {
            prototype: Object.create(HTMLAnchorElement.prototype, {
                createdCallback: {
                    value: function () {
                        if (this.innerHTML) {
                            this.setup();
                        }
                    }
                },
                attachedCallback: {
                    value: function () {
                        this.setup();
                    }
                },
                setup: {
                    value: function () {
                        var div = document.createElement("div");
                        this.original_innerHTML = this.innerHTML;
                        div.innerHTML = this.original_innerHTML;
                        this.innerHTML = null;
                        this.appendChild(div);
                    }
                }
            }),
            extends: "a"
        });

        /*
         * Added on: 17/12/2016, 15:04:26 - 16:16:18
         */
        document.registerElement("trl-logo", {
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
                attributeChangedCallback: {
                    value: function (attr_name, old_value, new_value) {
                        this.setup();
                    }
                },
                compact: {
                    set: function (compact) {
                        this.setAttribute("trl-compact", compact === true ? "true" : "false");
                    },
                    get: function () {
                        return (this.getAttribute("trl-compact") || "false") === "true" ? true : false;
                    }
                },
                color: {
                    set: function (color) {
                        this.setAttribute("trl-color", color);
                    },
                    get: function () {
                        return this.getAttribute("trl-color") || "#24c742";
                    }
                },
                scale: {
                    set: function (scale) {
                        this.setAttribute("trl-scale", scale);
                    },
                    get: function () {
                        return this.getAttribute("trl-scale") || 1;
                    }
                },
                width: {
                    set: function (width) {
                        this.setAttribute("trl-width", width);
                    },
                    get: function () {
                        return this.getAttribute("trl-width") || (this.compact ? 118.8 : 269.395);
                    }
                },
                height: {
                    set: function (height) {
                        this.setAttribute("trl-height", height);
                    },
                    get: function () {
                        return this.getAttribute("trl-height") || (this.compact ? 135.5 : 67.6);
                    }
                },
                setup: {
                    value: function () {
                        if (this.scale) {
                            this.style.width = (this.width / (1 / this.scale)) + "px";
                            this.style.height = (this.height / (1 / this.scale)) + "px";
                        } else {
                            this.style.width = this.width + "px";
                            this.style.height = this.height + "px";
                        }
                        this.innerHTML = this.compact 
                            ? '<!-- Created on: 18/07/2016, 09:00 -->' + 
                            '<!-- Author: TRIAL<João Vitor Ramos> -->' + 
                            '<svg id="svg2" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:cc="http://creativecommons.org/ns#" xmlns:dc="http://purl.org/dc/elements/1.1/" viewBox="0 0 118.8 135.2" style="width: 100%; height: 100%">' + 
                                '<metadata id="metadata7">' +
                                 '<rdf:RDF>' +
                                  '<cc:Work rdf:about="">' +
                                   '<dc:format>image/svg+xml</dc:format>' +
                                   '<dc:type rdf:resource="http://purl.org/dc/dcmitype/StillImage"/>' +
                                   '<dc:title/>' +
                                  '</cc:Work>' +
                                 '</rdf:RDF>' +
                                '</metadata>' +
                                '<g id="layer2" transform="translate(0 -84.8)">' +
                                 '<g id="g4250" transform="translate(-218.75)" style="fill: ' + this.color + '">' +
                                  '<path T id="path4215" d="m313.8 84.8c-0.3676-0.000001-0.73494 0.1408-1.0166 0.42383v17.869c0 1.1501-0.92167 2.0762-2.0663 2.0762h-88.033c-2.1775 0-3.9305 1.7594-3.9305 3.9473v15.756c0 2.1879 1.753 3.9492 3.9305 3.9492h22.335c2.1775 0 3.9305 1.7613 3.9305 3.9492v83.279c0 2.1879 1.753 3.9492 3.9305 3.9492h18.29c2.1775 0 3.9285-1.7613 3.9285-3.9492v-83.279c0-2.1879 1.7549-3.9492 3.9324-3.9492h30.505c1.3578 0.003 4.0368-0.91931 5.1551-2.043l22.44-20.322c0.56338-0.56605 0.56338-1.4769 0-2.043l-22.33-19.206c-0.28168-0.28303-0.649-0.42383-1.0166-0.42383z" fill-rule="evenodd"/>' +
                                 '</g>' +
                                '</g>' +
                            '</svg>'
                            : '<!-- Created on: 18/07/2016, 08:48 -->' + 
                            '<!-- Author: TRIAL<João Vitor Ramos> -->' + 
                            '<svg id="svg2" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:cc="http://creativecommons.org/ns#" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:dc="http://purl.org/dc/elements/1.1/" viewBox="0 0 538.79499 135.2" style="width: 100%; height: 100%">' +
                                '<metadata id="metadata7">' +
                                 '<rdf:RDF>' +
                                  '<cc:Work rdf:about="">' +
                                   '<dc:format>image/svg+xml</dc:format>' +
                                   '<dc:type rdf:resource="http://purl.org/dc/dcmitype/StillImage"/>' +
                                   '<dc:title/>' +
                                  '</cc:Work>' +
                                 '</rdf:RDF>' +
                                '</metadata>' +
                                '<g id="layer2" transform="translate(0 -84.8)">' +
                                 '<g id="g4250" transform="translate(-218.75)" style="fill: ' + this.color + '">' +
                                  '<path id="path4215" d="m95.5 0c-0.369-0.000001-0.738 0.1408-1.021 0.42383v17.869c0 1.15-0.927 2.076-2.077 2.076h-88.453c-2.1877 0-3.949 1.76-3.949 3.947v15.756c0 2.188 1.7613 3.949 3.9492 3.949h22.442c2.188 0 3.949 1.762 3.949 3.95v83.279c0 2.19 1.761 3.95 3.949 3.95h18.377c2.188 0 3.947-1.76 3.947-3.95v-83.279c0-2.188 1.764-3.95 3.951-3.95h30.651c1.364 0.003 4.056-0.919 5.18-2.042l22.545-20.323c0.57-0.566 0.57-1.477 0-2.043l-22.419-19.189c-0.283-0.2832-0.652-0.424-1.021-0.424zm232.95 20.367c-1.15 0-2.08 0.926-2.08 2.076v108.81c0 2.19 1.76 3.95 3.95 3.95h18.38c2.18 0 3.94-1.76 3.94-3.95v-25.69c0-2.19 1.77-3.95 3.95-3.95h38.2c2.19 0 3.95 1.76 3.95 3.95v25.69c0 2.19 1.77 3.95 3.95 3.95h18.38c2.19 0 3.95-1.76 3.95-3.95v-108.81c0-1.147-0.93-2.073-2.08-2.073h-94.49zm-187.21 0.002c-1.15 0-2.08 0.924-2.08 2.074l-0.13 108.81c0 2.19 1.76 3.95 3.95 3.95h18.38c2.18 0 3.94-1.76 3.94-3.95v-83.279c0-2.188 1.76-3.95 3.95-3.95h48.99c2.19 0 3.95 1.762 3.95 3.95v15.043c0 7.512 5.69 13.601 12.7 13.601l6.13-0.023c2.19 0 3.95-1.763 3.95-3.951v-23.02c-1.36-16.382-15.09-29.252-31.82-29.252h-71.91zm133.38 0c-2.19 0-3.95 1.76-3.95 3.947v106.59c0 2.19 1.76 3.95 3.95 3.95h18.37c2.19 0 3.95-1.76 3.95-3.95v-106.59c0-2.191-1.76-3.951-3.95-3.951h-18.37zm182.4 0c-2.19 0-3.95 1.76-3.95 3.947v106.93c0 2.19 1.76 3.95 3.95 3.95h77.82c2.19 0 3.95-1.76 3.95-3.95v-18.37c0-2.19-1.76-3.96-3.95-3.96h-51.54c-2.19 0-3.95-1.76-3.95-3.94v-80.664c0-2.187-1.76-3.947-3.95-3.947h-18.38zm-100.43 23.652h38.2c2.19 0 3.95 1.764 3.95 3.952v23.412c0 2.188-1.76 3.949-3.95 3.949h-38.2c-2.18 0-3.95-1.761-3.95-3.949v-23.412c0-2.188 1.77-3.952 3.95-3.952z" fill-rule="evenodd" transform="translate(218.75 84.8)"/>' +
                                 '</g>' +
                                '</g>' +
                            '</svg>';
                    }
                }
            })
        });
        
        /*
         * Added on 18/01/2017, 00:32:10
         */
        document.registerElement("trl-slideshow", {
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
                        var slide_items = this.children;
                        this.slideshow_items = [];
                        for (var i = 0, total = slide_items.length; i < total; i++) {
                            var slide = slide_items[i];
                            if (!slide.classList.contains("controller") && slide.nodeName !== "HEADER") {
                                this.slideshow_items.push(slide);
                            }
                        }
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
                        if (this.auto) {
                            this.defineInterval();
                        }
                        current = next = this.position;
                        if (!this.unidirectional) {
                            if (this.controller.previous) {
                                this.controller.previous.style.display = "";
                            }
                        }
                        if (this.controller.conclude) {
                            this.controller.conclude.style.display = "none";
                        }
                        this.controller.next.style.display = "";
                        if (this.position < slides.length + 1) {
                            next++;
                            if (next >= slides.length && this.repeat) {
                                next = 0;
                            } else if (next >= slides.length - 1 && !this.repeat) {
                                if (this.lastslide_conclude) {
                                    if (this.controller.conclude) {
                                        this.controller.conclude.style.display = "";
                                    }
                                }
                                this.controller.next.style.display = "none";
                            }
                        }
                        this.position = next;
                        if (callback !== undefined) {
                            callback(this.slideshow_info);
                        }
                        slides[next].classList.add("current");

                        T().Utils.next({
                            top: height_header,
                            current: slides[current], 
                            next: slides[next],
                            onfinish: function () {
                                slides[current].classList.remove("current");
                                // added on 30/10/2016, 15:06:34
                                if (that.callbacks.onchangeslide) {
                                    that.callbacks.onchangeslide(that.slideshow_info);
                                }
                            }
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
                        this.controller.next.style.display = "";
                        if (this.controller.conclude) {
                            this.controller.conclude.style.display = "none";
                        }
                        if (this.position > -1) {
                            before--;
                            if (before <= -1 && this.repeat) {
                                before = slides.length - 1;
                            } else if (before <= 0 && !this.repeat) {
                                this.controller.previous.style.display = "none";
                            }
                        } else {
                            this.controller.previous.style.display = "none";
                        }
                        this.position = before;
                        if (callback !== undefined) {
                            callback(this.slideshow_info);
                        }
                        slides[before].classList.add("current");

                        T().Utils.previous({
                            top: height_header,
                            current: slides[current], 
                            previous: slides[before],
                            onfinish: function () {
                                slides[current].classList.remove("current");
                                // added on 30/10/2016, 15:06:07
                                if (that.callbacks.onchangeslide) {
                                    that.callbacks.onchangeslide(that.slideshow_info);
                                }
                            }
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
                                T(stylesheet).CSS.change({
                                    Styles: {
                                        "[container-slideshow-items] [slideshow-item]": {
                                            height: "calc(100% - " + (height_cont + height_header) + "px)"
                                        }
                                    }
                                });
                            }
                            if (this.controller.previous) {
                                this.controller.previous.style.display = "none";
                            }
                            if (this.controller.conclude) {
                                this.controller.conclude.style.display = "none";
                            }
                            slide.style.display = "";

                            // added on 30/10/2016, 14:50:34 - 14:51:14
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
        
        /*
         * Added on 24/01/2017, 01:07:55 - 01:47:20
         */
        document.registerElement("trl-login-button", {
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
                            T.Account.open({
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
    
        // created on 03/03/2017, 20:01:04
        // added template use on 12/03/2017 00:47:07
        document.registerElement("trl-rating", {
            prototype: Object.create(HTMLDivElement.prototype, {
                createdCallback: {
                    value: function () {
                        if (!this.isset) {
                            this.setup();
                        }
                    }
                },
                attachedCallback: {
                    value: function () {
                        if (!this.isset) {
                            while (this.firstChild) {
                                this.removeChild(this.firstChild);
                            }
                            this.setup();
                        }
                    }
                },
                subtitle: {
                    set: function (subtitle) {
                        this.setAttribute("trl-subtitle", subtitle ? "enabled" : "disabled");
                    },
                    get: function () {
                        return this.getAttribute("trl-subtitle") === "enabled" || false;
                    }
                },
                rate: {
                    set: function (rate) {
                        this.setAttribute("trl-rate", rate);
                        this.updateMetadataRate();
                    },
                    get: function () {
                        return this.hasAttribute("trl-rate") ? parseInt(this.getAttribute("trl-rate")) : null;
                    }
                },
                updateMetadataRate: {
                    value: function () {
                        if (this.rate) {
                            var meta = this.getElementsByTagName("meta");
                            if (meta.length <= 0) {
                                meta = document.createElement("meta");
                                this.appendChild(meta);
                            } else {
                                meta = meta[0];
                            }
                            meta.setAttribute("itemprop", "ratingValue");
                            meta.setAttribute("content", this.rate);
                        }
                    }
                },
                interactive: {
                    set: function (enable) {
                        this.setAttribute("trl-interactive", enable);
                    },
                    get: function () {
                        return this.hasAttribute("trl-interactive") ? this.getAttribute("trl-interactive") === "true" : false;
                    }
                },
                setup: {
                    value: function () {
                        var that = this;
                        this.isset = true;
                        for (var i = 5; i > 0; i--) {
                            var star = document.createElement("span");
                            star.setAttribute("trl-rate", i);
                            if (this.rate === i) {
                                star.classList.add("current");
                            }
                            if (this.interactive) {
                                star.onclick = function (e) {
                                    if (!e.target.classList.contains("current")) {
                                        try {
                                            that.getElementsByClassName("current")[0].classList.remove("current");
                                        } catch (error) {};
                                        e.target.classList.add("current");
                                        that.rate = e.target.getAttribute("trl-rate");
                                        var event = document.createEvent("CustomEvent");
                                        event.initCustomEvent("rate", true, true, {rate: that.rate});
                                        that.dispatchEvent(event);
                                    }
                                };
                            }
                            star.classList.add("star-" + i, "fa", "fa-star");
                            this.appendChild(star);
                        }
                        if (this.subtitle) {
                            var subtitle = document.createElement("h1");
                            subtitle.classList.add("subtitle");
                            this.appendChild(subtitle);
                        }
                        this.setAttribute("itemprop", "reviewRating");
                        this.setAttribute("itemscope", "true");
                        this.setAttribute("itemtype", "https://schema.org/Rating");
                        this.updateMetadataRate();
                    }
                }
            })
        });
    }, false);
})();
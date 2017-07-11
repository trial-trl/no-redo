<?php 
    $timestamp = filemtime(__FILE__);
    $tsstring = gmdate('D, d M Y H:i:s', $timestamp);
    $etag = md5('pt-BR' . $timestamp);
    $if_modified_since = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : false;
    $if_none_match = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? $_SERVER['HTTP_IF_NONE_MATCH'] : false;
    if ((($if_none_match && $if_none_match == $etag) || !$if_none_match) && ($if_modified_since && $if_modified_since == $tsstring)) {
        header('HTTP/1.1 304 Not Modified');
        exit;
    }
    header('Last-Modified: ' . $tsstring);
    header('ETag: ' . $etag);
    header('Content-Type: application/javascript');
?>
/*
 * T.js Library₢
 * Version: 1.2
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
 * 
 * 15/06/2017
 *      22:33:34 => T.js changed to T.php to support dynamic code
 *      22:38:59 => T.php now is located in TApi folder
 *      
 * 16/06/2017
 *      23:11:37 - 23:23:18 => removed all prototype and non-prototype redundant codes
 *      23:24:02 => removed handleTab() - instead use TRLTabs constructor from elements.js
 *      
 * 22/06/2017, 21:17:04 - 21:19:36 => moved all T library files from Library/account/TApi to /no-redo/repository/javascript/TApi as in HostGator trialent.com account
 */

"use strict";
(function () {
    window.T = (function () {
        var on, T = function (element) {
                    if (!(this instanceof T))
                        return new T(element);
                    if (element !== undefined)
                        on = typeof element !== "string" ? element : document.querySelector(element);
                };
        T.fn = T.prototype = {
            // created on 17/06/2017, 13:36:04
            getElement: function () {
                return on;
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
                getTotalPadding: function () {
                    var padding_left = window.getComputedStyle(T.prototype.getElement(), null).getPropertyValue("padding-left");
                    var padding_left_number = padding_left.indexOf("px") !== -1 ? +(padding_left.replace("px", "")) : +(padding_left.replace("%", ""));
                    var padding_left_unit = padding_left.indexOf("px") !== -1 ? "px" : "%";

                    var padding_right = window.getComputedStyle(T.prototype.getElement(), null).getPropertyValue("padding-right");
                    var padding_right_number = padding_right.indexOf("px") !== -1 ? +(padding_right.replace("px", "")) : +(padding_right.replace("%", ""));
                    var padding_right_unit = padding_right.indexOf("px") !== -1 ? "px" : "%";

                    var padding_top = window.getComputedStyle(T.prototype.getElement(), null).getPropertyValue("padding-top");
                    var padding_top_number = padding_top.indexOf("px") !== -1 ? +(padding_top.replace("px", "")) : +(padding_top.replace("%", ""));
                    var padding_top_unit = padding_top.indexOf("px") !== -1 ? "px" : "%";   

                    var padding_bottom = window.getComputedStyle(T.prototype.getElement(), null).getPropertyValue("padding-bottom");
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
                triggerOffset: function (options) {
                    options.element = T.prototype.getElement();
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
                endlessScroll: function (options) {
                    // all code changed on 26/11/2016, 00:03:33 - 00:48:41
                    var element = T.prototype.getElement(),
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
                            element = T.prototype.getElement(),
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
            UI: {
                Logo: {
                    TRIAL: {
                        draw: function (options) {
                            var div = document.createElement("div"), 
                                    into = T.prototype.getElement();
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
                                        into = T.prototype.getElement();
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
                            into = T.prototype.getElement();

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
            }
        };
        return T;
    })();

    // added function 28/10/2016, 15:37:03
    (function () {
        // 27/11/2016, 11:29:29 => removed remove Array function
        /*Array.prototype.remove = function (search) {
            var position;
            while ((position = this.indexOf(search)) !== -1) {
                this.splice(positiT.prototype.getElement(), 1);
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

        // added on 22/11/2016, 18:54:58
        T.App = {
            // added constants on 22/11/2016, 18:52:59
            FIX_IT: "fi",
            CLICKER: "cl",
            OPPORTUNITY_NOW: "on",
            STREET_RACE: "sr",
            NO_REDO: "nr"
        };

        /* added on 15/06/2017 22:46 */
        T.load = (function () {
            var load_scripts = [],
                    loaded_scripts = [],
                    scripts = {},
                    all_loaded = false;
            function addLoadedScript(key, callback, last) {
                var i = load_scripts.indexOf(key);
                loaded_scripts.push(key);
                if (i !== -1)
                    load_scripts.splice(i, 1);
                if (last === true)
                    call(callback);
            }
            function addCallback(url, script, callback, last) {
                script.addEventListener("load", (function (u, s, c, l) {
                    return function () {
                        addLoadedScript(u, c, l);
                    };
                })(url, script, callback, last));
            }
            function addLoadScript(url, callback, check_for, last) {
                if ((check_for && !T[check_for]) || !check_for) {
                    var load = load_scripts.indexOf(url),
                            exists = document.querySelector("script[src=\"" + url + "\"]");
                    if (loaded_scripts.indexOf(url) !== -1 && !!exists && last === true)
                        call(callback);
                    else if (load !== -1)
                        addCallback(load_scripts[load], scripts[url], callback, last);
                    else {
                        load_scripts.push(url);
                        var script = document.createElement("script");
                        scripts[url] = script;
                        document.body.appendChild(script);
                        addCallback(url, script, callback, last);
                        script.src = url;
                    }
                } else if (last === true)
                    call(callback);
            }
            function call(callback) {
                if (ready())
                    if (callback)
                        T.dispatchCallback(callback);
            }
            function ready() {
                if (!all_loaded)
                    all_loaded = load_scripts.length <= 0;
                return all_loaded;
            }
            return function (urls, callback) {
                var is_l_str = typeof urls === "string";
                if (!is_l_str && !(urls.constructor === Array))
                    throw new TypeError("(T.load) Param must be a string or array");
                urls = is_l_str ? [urls] : urls;
                for (var url, check_for, i = 0, t = urls.length; i < t; i++) {
                    url = urls[i];
                    switch (url) {
                        case "account":
                        case "cookies":
                        case "history":
                        case "navigation":
                        case "utils":
                            check_for = url.charAt(0).toUpperCase() + url.slice(1);
                            url = "/no-redo/repository/javascript/TApi/" + url + ".js";
                            break;
                        case "elements":
                            check_for = url;
                            url = "/no-redo/repository/javascript/TApi/" + url + ".js";
                            break;
                        case "design":
                            check_for = "Animation";
                            url = "/no-redo/repository/javascript/TApi/" + url + ".js";
                            break;
                        default:
                            check_for = null;
                    }
                    addLoadScript(url, callback, check_for, i === (t - 1));
                };
            };
        })();
        
        /* created on 16/06/2017, 20:52:59 */
        T.require = function (scripts) {
            if (scripts === undefined)
                throw new TypeError("(T.require) Param isn't defined");
            if (!(typeof scripts === 'array'))
                throw new TypeError("(T.require) Param must be an array");
            
            var all_loaded = false;
            
            (function () {
                for (var i in scripts) {
                    var url = scripts[i];
                    switch (url) {
                        case 'elements':
                            url = '/no-redo/repository/javascript/TApi/elements.js';
                            break;
                    }
                }
            })();
            
            return {
                then: function (callback) {
                    if (callback === undefined)
                        throw new TypeError("(T.require.then) Param isn't defined");
                    if (all_loaded) {
                        T.dispatchCallback(callback);
                    }
                }
            };
        };

        /* created on 16/06/2017, 10:28:16 */
        T.dispatchCallback = function (callback) {
            if (callback) {
                if (typeof callback === 'string') {
                    if (callback.length > 0) {
                        try {
                            window[callback]();
                        } catch (e) {
                            console.error("(T.dispatchCallback) '" + callback + "' function doesn't exist or doesn't found in this context");
                        }
                    } else
                        console.error("(T.dispatchCallback) Callback function name must be a string and length more than 0");
                } else if (typeof callback === 'function') {
                    callback();
                }
            } else 
                console.error("(T.dispatchCallback) Callback not defined");
        };
        
        // created on 23/06/2017 21:53:09
        T._callbacks = [];
        
        T.on = T.addEventListener = function (event, fn) {
            if (!T._callbacks[event])
                T._callbacks[event] = [];
            T._callbacks[event].push(fn);
        }
        
        T.dispatchEvent = function (event, lib) {
            if (T._callbacks[event])
                for (var i = 0, t = T._callbacks[event].length; i < t; i++)
                    T._callbacks[event][i](lib);
        }
        // end created
        
    })();
    (function () {
        var c = <?php echo filter_has_var(INPUT_GET, 'onload') ? '"' . filter_input(INPUT_GET, 'onload') . '"' : 'null' ?>;
        if (c)
            window.T.dispatchCallback(c);
    })();
})(window);
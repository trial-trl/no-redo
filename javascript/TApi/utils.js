/* 
 * (c) 2017 TRIAL.
 * Created on 16/06/2017, 22:55:24.
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
    
    window.T = window.T || {};
    window.T.Utils = window.T.Utils || {};
    window.T.Utils.isNode = function (element) {
        return (typeof Node === "object" ? element instanceof Node : element && typeof element === "object" && element !== null && element.nodeType === "number" & typeof element.nodeName === "string");
    };
    window.T.Utils.isElement = function (element) {
        return (typeof HTMLElement === "object" ? element instanceof HTMLElement : element && typeof element === "object" && element !== null && element.nodeType === 1 & typeof element.nodeName === "string");
    };
    window.T.Utils.ajax = function (options) {
        var e = options.event || event,
                xhr = new XMLHttpRequest,
                method = "GET",
                url,
                data;

        if (e) e.preventDefault();

        if (xhr.timeout) xhr.timeout = options.timeout ? options.timeout : 30000;

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

        if (options.response && options.response !== T.Constants.Response.JSON)
            xhr.responseType = options.response;

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

        function eventHandler(e, custom) {
            if (custom) {
                try {
                    var response = options.response === T.Constants.Response.JSON ? (e.target.responseText ? JSON.parse(e.target.responseText) : null) : e.target.response;
                } catch (e) {
                    var response = {error: e};
                }
                custom({target: {response: response, status: e.target.status}});
            }
        }
    };
    window.T.prototype.style = function (options) {
        try {
            if (!(window.T.prototype.getElement() instanceof CSSStyleSheet))
                throw new TypeError("O elemento deve ser um CSS válido!");
            var style = window.T.prototype.getElement(),
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
            console.error(e);
        }
    };
})(window);
/* 
 * (c) 2017 TRIAL.
 * Created on 15/04/2017, 22:26:10.
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

define(["./ajax", "./history"], function (ajax, history) {
    return function (config) {
        var classes = {css: config.classes && config.classes.css ? config.classes.css : "ajax-style", script: config.classes && config.classes.script ? config.classes.script : "ajax-script", content: config.classes && config.classes.content ? config.classes.content : "ajax-content"}, state = {title: document.title, url: location.href, page: document.documentElement.innerHTML}, o = {
                    load: function (e, url, plus) {
                        if (config.onload) 
                            config.onload();
                        if (e.state) {
                            apply(e.state.page, url, plus);
                        } else
                            ajax(e).to(url).onloadend(function (e) {
                                apply(e.target.response, url, plus, true);
                            }).send();
                    },
                    register: function (t, url, plus) {
                        var self = this;
                        
                        if (!t)
                            throw new TypeError("Register elements is missing");
                        
                        if (url) {
                            if (t.href && typeof url === "object")
                                plus = url;
                            else if (!t.href)
                                throw new TypeError("Url must be a string");
                        } else {
                            throw new TypeError("Url is missing");
                        }
                        
                        if (plus && typeof plus !== "object")
                            throw new TypeError("Additional data must be passed inside of an object");
                        
                        t.addEventListener("click", (function (url, plus) {
                            return function (e) {
                                var link = e.target;
                                self.load(e, link.href || (typeof url === "string" ? url : null), plus);
                            };
                        })(url, plus));
                    }
                };
        
        this.config = config;
        if (this.config.onsave)
            this.config.onsave(state);
        history.replace(state);
        window.addEventListener("popstate", function (e) {
            o.load({e: e});
        });

        function apply(page, url, plus, update) {
            try {
                var html = new DOMParser().parseFromString(page, "text/html"),
                        content = {
                            current: document.getElementById(classes.content),
                            loaded: html.getElementById(classes.content)
                        };
                
                content.current.className = content.loaded.className;
                content.current.innerHTML = content.loaded.innerHTML;
                document.documentElement.className = html.documentElement.className;
                document.title = html.title;
                
                getStyles(html);
                getScripts(html);
                
                if (update) {
                    var state = plus;
                    state.title = document.title;
                    state.url = location.href;
                    state.page = page;
                    if (this.config.onsave)
                        this.config.onsave(state);
                    history.add(state, html.title, url);
                }
                if (this.config.onloaded) {
                    plus.page = html;
                    this.config.onloaded(plus);
                }
            } catch (e) {
                if (this.config.onerror)
                    this.config.onerror(e);
            }
            
            function getStyles(html) {
                var styles = {
                        current: [],
                        loaded: html.getElementsByClassName(classes.css)
                    };

                for (var i = 0, total = document.styleSheets.length; i < total; i++)
                    styles.current.push(document.styleSheets[i].href);
                var d = document.getElementsByClassName(classes.css);
                for (i = 0, total = d.length; i < total; i++)
                    document.head.removeChild(d[i]);
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
                        style.classList.add(classes.css);
                        document.head.appendChild(style);
                    }
                }
            }

            function getScripts(html) {
                var scripts = {
                        current: [],
                        loaded: html.getElementsByClassName(classes.script)
                    };
                for (var i = 0, total = document.scripts.length; i < total; i++) {
                    if (document.scripts[i].classList.contains(classes.script)) {
                        document.body.removeChild(document.scripts[i]);
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
                        script.classList.add(classes.script);
                        document.body.appendChild(script);
                    }
                }
            }
        }
        
        return o;
    };
});
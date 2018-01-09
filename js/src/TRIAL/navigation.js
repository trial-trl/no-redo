/* 
 * (c) 2017 TRIAL.
 * Created on 16/06/2017, 22:56:53.
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
    window.T.Navigation = window.T.Navigation || {};
    window.T.Navigation.scroll = function (options) {
        var y = {
                initial: window.pageYOffset,
                distance: 0
            },
            x = {
                initial: window.pageXOffset,
                distance: 0
            };
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
            window.T.load("design", function () {
                window.T.Animation.start({
                    duration: options.duration || 300,
                    delta: function (p) {
                        return window.T.Animation.Easing.easeInOutQuart(p);
                    },
                    step: function (delta) {
                        window.scrollTo(x.initial + (x.distance * delta), y.initial + (y.distance * delta));
                    }
                });
            });
        }
    };
    window.T.Navigation.next = function (options) {
        try {
            options.next.style.position = "absolute";
            options.next.style.display = "";
            options.next.style.top = options.top + "px";
            options.next.style.left = "100%";
            if (options.onstart)
                options.onstart(options);
            window.T.load("design", function () {
                window.T.Animation.start({
                    duration: 200,
                    delta: function (p) {
                        return window.T.Animation.Easing.easeInOutQuart(p);
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
            });
        } catch (e) {
            console.error(e);
        }
    };
    window.T.Navigation.previous = function (options) {
        try {
            options.previous.style.position = "absolute";
            options.previous.style.display = "";
            options.previous.style.top = options.top + "px";
            options.previous.style.left = "-100%";
            if (options.onstart)
                options.onstart(options);
            window.T.load("design", function () {
                window.T.Animation.start({
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
            });
        } catch (e) {
            console.error(e);
        }
    };
    
    window.T.Navigation.noRefresh = function (options) {
        var state = {title: document.title, url: location.href, page: document.documentElement.innerHTML},
                api = {
                    load: function (q) {
                        if (!q.url)
                            loadPage(q.e.state.page);
                        else
                            window.T.load("utils", function () {
                                window.T.Utils.ajax({
                                    event: q.e,
                                    url: q.url,
                                    method: "GET",
                                    onloadend: function (e) {
                                        loadPage(e.target.response, true);
                                    }
                                });
                            });

                        function loadStyles(html) {
                            var styles = {
                                    current: [],
                                    loaded: html.getElementsByClassName("ajax-style")
                                };
                            for (var i = 0, t = document.styleSheets.length; i < t; i++)
                                styles.current.push(document.styleSheets[i].href);
                            var d = document.getElementsByClassName("ajax-style");
                            for (i = 0, t = d.length; i < t; i++)
                                document.head.removeChild(d[i]);
                            for (i = 0, t = styles.loaded.length; i < t; i++) {
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
                            for (var i = 0, t = document.scripts.length; i < t; i++) {
                                if (!document.scripts[i])
                                    continue;
                                if (document.scripts[i].classList.contains("ajax-script")) {
                                    document.body.removeChild(document.scripts[i]);
                                    continue;
                                }
                                if (document.scripts[i].src !== undefined && document.scripts[i].src !== null && document.scripts[i].src !== "")
                                    scripts.current.push(document.scripts[i].src);
                            }
                            for (var i = 0, t = scripts.loaded.length; i < t; i++) {
                                if (scripts.current.indexOf(scripts.loaded[i].src) === -1) {
                                    var s = document.createElement("script");
                                    if (scripts.loaded[i].src)
                                        s.src = scripts.loaded[i].src;
                                    s.innerHTML = scripts.loaded[i].innerHTML;
                                    s.classList.add("ajax-script");
                                    document.body.appendChild(s);
                                }
                            }
                        }

                        function loadPage(response, update) {
                            try {
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
                                    var data = {title: document.title, url: location.href, page: response};
                                    if (options.onsave)
                                        options.onsave(data);
                                    window.T.History.add(data, html.title, q.url);
                                }
                                if (options.onloaded) {
                                    var custom = options;
                                    custom.page = html;
                                    options.onloaded(custom);
                                }
                            } catch (e) {
                                console.error(e);
                                if (options.onerror)
                                    options.onerror(e);
                                if (options.onloaded) {
                                    var custom = options;
                                    custom.error = e;
                                    options.onloaded(custom);
                                }
                            }
                        }
                    }
                };
        if (options.onsave)
            options.onsave(state);
        window.T.load("history", function () {
            window.T.History.replace(state);
            window.addEventListener("popstate", function (e) {
                api.load({e: e});
            });
        });
        return api;
    };
})(window);
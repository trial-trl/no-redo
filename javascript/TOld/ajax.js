/* 
 * (c) 2017 TRIAL.
 * Created on 15/04/2017, 22:26:25.
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

define(function () {
    return function (e) {
        var u, r, t = 30000, m = "POST", d = null, srw = true, xhr = new XMLHttpRequest, o = {
            method: function (method) {
                if (method !== "POST" && method !== "GET")
                    throw new TypeError("Method must be POST or GET");
                m = method;
                return this;
            },
            to: function (url) {
                u = url;
                return this;
            },
            header: function (n, v) {
                xhr.setRequestHeader(n, v);
                return this;
            },
            sendRequestedWith: function (b) {
                srw = b;
                return this;
            },
            onloadstart: function (c) {
                xhr.onloadstart = wrapCallable("onstart", c);
                return this;
            },
            onload: function (c) {
                xhr.onload = wrapCallable("onload", c);
                return this;
            },
            onloadend: function (c) {
                xhr.onloadend = wrapCallable("onend", c);
                return this;
            },
            onprogress: function (c) {
                xhr.onprogress = wrapCallable("onprogress", c);
                return this;
            },
            ontimeout: function (c) {
                xhr.ontimeout = wrapCallable("ontimeout", c);
                return this;
            },
            onabort: function (c) {
                xhr.onabort = wrapCallable("onabort", c);
                return this;
            },
            onerror: function (c) {
                xhr.onerror = wrapCallable("onerror", c);
                return this;
            },
            timeout: function (ms) {
                t = ms;
                return this;
            },
            expect: function (return_type) {
                r = return_type;
                return this;
            },
            send: function (s, plus) {
                if (s) {
                    if ((typeof Node === "object" ? s instanceof Node : s && typeof s === "object" && s !== null && s.nodeType === "number" & typeof s.nodeName === "string") || (typeof HTMLElement === "object" ? s instanceof HTMLElement : s && typeof s=== "object" && s !== null && s.nodeType === 1 & typeof s.nodeName === "string")) {
                        m = s.getAttribute("method");
                        u = s.getAttribute("action");
                        d = new FormData(s);
                        addPlus();
                    } else {
                        if (s instanceof FormData) {
                            d = s;
                            addPlus();
                        } else 
                            d = JSON.stringify(s);
                    }
                } else
                    m = "GET";
                
                send();
                
                function addPlus() {
                    for (var n, p = plus ? Object.getOwnPropertyNames(plus) : [], i = 0, t = p.length; i < t; i++, n = p[i])
                        d.append(n, plus[n]);
                }
            }
        };
        
        function send() {
            if (e)
                e.preventDefault();
            if (!u)
                throw new TypeError("Missing request Url");
            xhr.open(m, u, true);
            if (xhr.timeout)
                xhr.timeout = t;
            if (xhr.responseType && r)
                xhr.responseType = r;
            if (srw)
                o.header("X-REQUESTED-WITH", "xmlhttprequest");
            xhr.send(d);
        }
        
        function wrapCallable(n, c) {
            if (typeof c !== "function")
                throw new TypeError(n + "() argument must be a function");
            return function (e) {
                c({target: {response: r === T.Constants.Response.JSON ? (e.target.responseText ? JSON.parse(e.target.responseText) : null) : e.target.response, status: e.target.status}});
            };
        }
        
        return o;
    };
});
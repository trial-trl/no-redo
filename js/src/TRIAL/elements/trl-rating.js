/* 
 * (c) 2017 TRIAL.
 * Created on 18/06/2017, 21:04:29.
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
    // created on 03/03/2017, 20:01:04
    // added template use on 12/03/2017 00:47:07
    window.T.elements.custom(window.T.elements.TRL_RATING, {
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
    }, window.T.elements.Rating);
})(window);
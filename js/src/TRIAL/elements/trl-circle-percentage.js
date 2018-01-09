/* 
 * (c) 2017 TRIAL.
 * Created on 18/06/2017, 20:57:34.
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
     * TRIAL Circle Percentage
     * Added on: 18/08/2016, 13:38:31
     * 
     * Modification of CSS Script "Pure CSS Circular Percentage Bar"
     * in http://www.cssscript.com/pure-css-circular-percentage-bar/
     */
    window.T.elements.CirclePercentage = document.registerElement(window.T.elements.TRL_CIRCLE_PERCENTAGE, {
        prototype: Object.create(HTMLDivElement.prototype, {
            createdCallback: {
                value: function () {
                    var circle = this;
                    this.stylesheet = document.getElementById("trl-circle-percentage-style");
                    if (!this.stylesheet) {
                        var s = document.createElement("link");
                        s.id = "trl-circle-percentage-style";
                        s.rel = "stylesheet";
                        s.type = "text/css";
                        s.href = "/no-redo/" + window.T.VERSION + "/css/elements/trl-circle-percentage";
                        document.head.appendChild(s);
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
})(window);
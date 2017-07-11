/* 
 * (c) 2017 TRIAL.
 * Created on 18/06/2017, 21:01:29.
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
    window.T.elements.TRIAL.Logo = document.registerElement(window.T.elements.TRL_LOGO, {
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
})(window);
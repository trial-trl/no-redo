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

'use strict';

( ( T ) => {
    /*
     * TRIAL Circle Percentage
     * Added on: 18/08/2016, 13:38:31
     * 
     * Modification of CSS Script "Pure CSS Circular Percentage Bar"
     * in http://www.cssscript.com/pure-css-circular-percentage-bar/
     */
    T.elements.custom( T.elements.TRL_CIRCLE_PERCENTAGE, class extends HTMLElement {
        
        constructor() {
            super();
        }
        
        connectedCallback() {
            var circle = this;
            this.stylesheet = document.getElementById("trl-circle-percentage-style");
            if (!this.stylesheet) {
                var s = document.createElement("link");
                s.id = "trl-circle-percentage-style";
                s.rel = "stylesheet";
                s.type = "text/css";
                s.href = T.CSS + "/elements/trl-circle-percentage";
                document.head.appendChild(s);
            }
            this.percentage_el = document.createElement("span");
            this.slice = document.createElement("div");
            this.bar = document.createElement("div");
            this.fill = document.createElement("div");

            this.bar.classList.add("bar");
            this.fill.classList.add("fill");

            this.slice.classList.add("slice");
            this.slice.appendChild(this.bar);
            this.slice.appendChild(this.fill);

            this.percentage_el.innerHTML = this.percentage + '%';
            circle.appendChild(this.percentage_el);
            circle.appendChild(this.slice);
        }
        
        static get observedAttributes() {
            return [ 'trl-percentage' ];
        }
        
        attributeChangedCallback( name, oldValue, newValue ) {
            if ( name === 'trl-percentage' && this.percentage_el ) {
                this.percentage_el.innerHTML = newValue + "%";
            }
        }
        
        set percentage( percentage ) {
            this.setAttribute( 'trl-percentage', percentage || '0' );
        }
        
        get percentage() {
            return this.getAttribute( 'trl-percentage' ) || '0';
        }
    }, 'CirclePercentage' );

    window.dispatchEvent( new CustomEvent( 'T.elements.CirclePercentage.loaded' ) );
  
} )( window.T || {} );

/* 
 * (c) 2017 TRIAL.
 * Created on 17/12/2016, 15:04:26.
 * Moved to here on 18/06/2017, 21:01:29.
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

(function (T) {
  
  T.elements.custom(T.elements.TRL_LOGO, {
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
          
          this.innerHTML = this.compact ? 
            /* 
              <!-- Created on: 18/07/2016, 09:00 -->
              <!-- Author: TRIAL<João Vitor Ramos> --> 
            */
            `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 118.8 135.2" style="width:100%;height:100%"><path T d="M95.05 0c-.37 0-.73.14-1.02.42V18.3c0 1.15-.92 2.08-2.06 2.08H3.93A3.93 3.93 0 0 0 0 24.32v15.75a3.93 3.93 0 0 0 3.93 3.95h22.34a3.93 3.93 0 0 1 3.93 3.95v83.28a3.93 3.93 0 0 0 3.93 3.95h18.29a3.93 3.93 0 0 0 3.93-3.95V47.97a3.93 3.93 0 0 1 3.93-3.95h30.5c1.36 0 4.04-.92 5.16-2.04l22.44-20.32c.56-.57.56-1.48 0-2.05L96.05.41a1.43 1.43 0 0 0-1.02-.43z" fill-rule="evenodd" fill="` + this.color + `"/></svg>` : 
            /* 
              <!-- Created on: 18/07/2016, 08:48 -->
              <!-- Author: TRIAL<João Vitor Ramos> -->
            */
            `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 538.79 135.2" style="width:100%;height:100%"><path d="M95.5 0c-.37 0-.74.14-1.02.42V18.3c0 1.15-.93 2.08-2.08 2.08H3.95A3.94 3.94 0 0 0 0 24.32v15.75a3.94 3.94 0 0 0 3.95 3.95h22.44a3.94 3.94 0 0 1 3.95 3.95v83.28a3.94 3.94 0 0 0 3.95 3.95h18.38a3.94 3.94 0 0 0 3.94-3.95V47.97a3.94 3.94 0 0 1 3.95-3.95h30.66c1.36 0 4.05-.92 5.18-2.04l22.54-20.32c.57-.57.57-1.48 0-2.05L96.52.42A1.44 1.44 0 0 0 95.5 0zm232.95 20.37c-1.15 0-2.08.92-2.08 2.07v108.81a3.94 3.94 0 0 0 3.95 3.95h18.38a3.94 3.94 0 0 0 3.94-3.95v-25.69a3.95 3.95 0 0 1 3.95-3.95h38.2a3.94 3.94 0 0 1 3.95 3.95v25.7a3.95 3.95 0 0 0 3.95 3.94h18.38a3.94 3.94 0 0 0 3.95-3.95V22.45c0-1.15-.93-2.08-2.08-2.08h-94.49zm-187.21 0c-1.15 0-2.08.92-2.08 2.07l-.13 108.81a3.94 3.94 0 0 0 3.95 3.95h18.38a3.94 3.94 0 0 0 3.94-3.95V47.97a3.94 3.94 0 0 1 3.95-3.95h48.99a3.94 3.94 0 0 1 3.95 3.95v15.05c0 7.5 5.69 13.6 12.7 13.6l6.13-.03a3.94 3.94 0 0 0 3.95-3.95V49.62a31.93 31.93 0 0 0-31.82-29.25h-71.91zm133.38 0a3.94 3.94 0 0 0-3.95 3.95V130.9a3.94 3.94 0 0 0 3.95 3.95h18.37a3.94 3.94 0 0 0 3.95-3.95V24.3c0-2.18-1.76-3.94-3.95-3.94h-18.37zm182.4 0a3.94 3.94 0 0 0-3.95 3.95v106.93a3.94 3.94 0 0 0 3.95 3.95h77.82a3.94 3.94 0 0 0 3.95-3.95v-18.37a3.95 3.95 0 0 0-3.95-3.96H483.3a3.94 3.94 0 0 1-3.95-3.94V24.3a3.94 3.94 0 0 0-3.95-3.94h-18.38zM356.59 44.02h38.2a3.94 3.94 0 0 1 3.95 3.95v23.41a3.94 3.94 0 0 1-3.95 3.95h-38.2a3.95 3.95 0 0 1-3.95-3.95v-23.4a3.95 3.95 0 0 1 3.95-3.96z" fill-rule="evenodd" fill="` + this.color + `"/></svg>`;
            
        }
      }
    })
  }, 'TRIAL.Logo');
  
  window.dispatchEvent(new CustomEvent('T.elements.TRIAL.Logo.loaded'));
  
})(window.T);
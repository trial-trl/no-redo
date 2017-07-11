/* 
 * (c) 2017 TRIAL.
 * Created on 16/06/2017, 23:20:02.
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
    window.T.Animation = {
        /*
         * Easing Functions - inspired from http://gizma.com/easing/
         * only considering the t value for the range [0, 1] => [0, 1]
         * 
         * by: gre / easing.js, GitHub
         */
        Easing: {
            // no easing, no acceleration
            linear: function (t) { return t; },
            // accelerating from zero velocity
            easeInQuad: function (t) { return t*t; },
            // decelerating to zero velocity
            easeOutQuad: function (t) { return t*(2-t); },
            // acceleration until halfway, then deceleration
            easeInOutQuad: function (t) { return t<.5 ? 2*t*t : -1+(4-2*t)*t; },
            // accelerating from zero velocity 
            easeInCubic: function (t) { return t*t*t; },
            // decelerating to zero velocity 
            easeOutCubic: function (t) { return (--t)*t*t+1; },
            // acceleration until halfway, then deceleration 
            easeInOutCubic: function (t) { return t<.5 ? 4*t*t*t : (t-1)*(2*t-2)*(2*t-2)+1; },
            // accelerating from zero velocity 
            easeInQuart: function (t) { return t*t*t*t; },
            // decelerating to zero velocity 
            easeOutQuart: function (t) { return 1-(--t)*t*t*t; },
            // acceleration until halfway, then deceleration
            easeInOutQuart: function (t) { return t<.5 ? 8*t*t*t*t : 1-8*(--t)*t*t*t; },
            // accelerating from zero velocity
            easeInQuint: function (t) { return t*t*t*t*t; },
            // decelerating to zero velocity
            easeOutQuint: function (t) { return 1+(--t)*t*t*t*t; },
            // acceleration until halfway, then deceleration 
            easeInOutQuint: function (t) { return t<.5 ? 16*t*t*t*t*t : 1+16*(--t)*t*t*t*t; }
        },
        start: function (options) {
            var start = new Date,
            animation = setInterval(function () {
                var timePassed = new Date - start;
                var progress = timePassed / options.duration;
                if (progress > 1) {
                    progress = 1;
                }
                var delta = options.delta(progress);
                options.step(delta, progress);
                if (progress === 1) {
                    if (options.onfinish) {
                        options.onfinish();
                    }
                    clearInterval(animation);
                }
            }, options.delay || 10);
        }
    };
})(window);
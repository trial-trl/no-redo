/* 
 * (c) 2017 TRIAL.
 * Created on 16/04/2017, 01:31:56.
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

// added into T.js library on 27/12/2016, 00:55:11

define({
    add: function (state, title, url) {
        if (history.pushState) {
            try {
                history.pushState(state, title, url);
                document.title = title;
            } catch (e) {}
        } else {
            location.assign(url);
        }
    },
    replace: function (state, title, url) {
        try {
            history.replaceState(state, title || null, url || null);
        } catch (e) {}
    }
});

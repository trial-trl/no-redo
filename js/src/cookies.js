/* 
 * ( c ) 2017 TRIAL.
 * Created on 16/06/2017, 23:11:26.
 *
 * Licensed under the Apache License, Version 2.0 ( the "License");
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

window.T = ( ( T ) => {
  
    T.Cookies = ( prefix ) => {

        var cookies = {};

        ( () => {
            var c = document.cookie.split( '; ' );

            for ( var i = 0, total = c.length; i < total; i++) {
                var parts = c[ i ].split( '=' );

                if ( prefix && !( parts[ 0 ].substr( 0, 4 ) === prefix && parts[ 0 ] !== 'trl_accounts' ) )
                    continue;
                
                cookies[ parts[ 0 ] ] = parts[ 1 ];
            }
        } )();

        return {

            set( name, value, expires, path, domain ) {
                if ( !( expires instanceof Date ) ) {
                    throw new TypeError();
                }
                document.cookie = name + '=' + value + ( expires ? ';expires=' + expires.toUTCString() : null ) + ( path ? ';path=' + path : null ) + ( domain ? ';domain=' + domain : null );
            },

            getAllKeys() {
                var keys = [],
                    i    = 0;
                for ( var key in cookies ) {
                    keys[ i++ ] = key;
                }
                return keys;
            },

            get( name ) {
                return cookies[ name ];
            },

            deleteAll() {
                var keys   = this.getAllKeys(),
                    domain = location.hostname === 'localhost' 
                             ? 'localhost' 
                             : '.trialent.com';
                             
                for ( var key in keys ) {
                  this.delete( keys[ key ], '/', domain );
                }
                
                cookies = {};
            },

            delete( name, path, domain ) {
                document.cookie = name + '=' + ( ( path ) ? ';path=' + path : '' ) + ( ( domain ) ? ';domain=' + domain : '' ) + ';expires=Thu, 01 Jan 1970 00:00:01 GMT';
            }

        };

    };

    return T;
    
})( window.T || {} );
    
window.dispatchEvent( new CustomEvent( 'T.Cookies.loaded' ));
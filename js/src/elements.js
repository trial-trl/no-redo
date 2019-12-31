/* 
 * (c) 2017 TRIAL.
 * Created on 15/06/2017, 22:40:20.
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

window.T = ( ( T ) => {

    T.elements = T.elements || {};
    
    T.elements.TRIAL = T.elements.TRIAL || {};

    T.elements.ALL = [
        T.elements.TRL_CIRCLE_PERCENTAGE = 'trl-circle-percentage', 
        T.elements.TRL_EDITABLE          = 'trl-editable', 
        T.elements.TRL_LOADING           = 'trl-loading', 
        T.elements.TRL_LOGO              = 'trl-logo', 
        T.elements.TRL_PAGINATION        = 'trl-pagination', 
        T.elements.TRL_POPUP             = 'trl-popup', 
        T.elements.TRL_SELECTABLE        = 'trl-selectable', 
        T.elements.TRL_SLIDESHOW         = 'trl-slideshow', 
        T.elements.TRL_SUGGESTIONS       = 'trl-suggestions', 
        T.elements.TRL_TABS              = 'trl-tabs'
    ];

    T.elements.custom = ( tag, clazz, obj, options ) => {
        window.customElements.define( tag, clazz, options );
        T.elements[ obj ] = clazz;
    };

    T.elements.register = ( elements, callback ) => {
        if ( elements === undefined ) {
            throw new TypeError( '(T.register) Param isn\'t defined' );
        }

        var is_e_str = typeof elements === 'string';

        if ( !is_e_str && Array !== elements.constructor )
            throw new TypeError( '(T.register) Param must be a string or array' );

        var arr = elements === 'all' 
                  ? T.elements.ALL
                  : ( is_e_str ? [ elements ] : elements );
                  
        var load = [];

        for ( let i = 0, t = arr.length, a; i < t, a = arr[ i ]; i++ ) {

            if ( a === T.elements.TRL_LOADING || a === T.elements.TRL_CIRCLE_PERCENTAGE ) {
                load.push( T.CSS + '/elements/' + a + '.css' );
            }

            load.push( T.API + '/elements/' + a + '.js' );

        }

        T.load( load, callback );

    };
    
    T = ( function ( T ) {

        function Element( selector ) {
            this.view = typeof selector === 'string'
                        ? document.querySelector( selector ) 
                        : selector instanceof HTMLElement 
                            ? selector 
                            : null;
            if ( this.view ) {
                this.parent = this.view.parentNode;
            }
        }

        Element.prototype.getView = function () {
            return this.view;
        };

        Element.prototype.hide = function () {
            this.view.hidden = true;
        };

        Element.prototype.show = function () {
            this.view.hidden = false;
        };

        Element.prototype.isAttached = function () {
            return this.view.parentNode instanceof HTMLElement;
        };

        Element.prototype.dettach = function () {
            if ( !this.isAttached() ) return;
            
            this.hide();
            setTimeout( function () {
                this.parent.removeChild( this.view );
            }.bind( this ), 10 );
        };

        Element.prototype.attach = function () {
            if ( this.isAttached() ) return;
            
            this.parent.appendChild( this.view );
            setTimeout( function () {
                this.show();
            }.bind( this ), 10 );
        };

        Element.prototype.clear = function () {
            while ( this.view.firstChild )
                this.view.removeChild( this.view.firstChild );
        };

        T.Element = Element;

        return T;

    } )( T || {} );

    return T;
  
} )( window.T || {} );
    
window.dispatchEvent( new CustomEvent( 'T.elements.loaded' ) );
/* 
 * (c) 2017 TRIAL.
 * Created on 03/09/2016, 13:13:30 at Fix It₢ project.
 * Moved to T on 10/05/2017, 13:32:26.
 * Moved to here on 18/06/2017, 21:05:24.
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
  
    T.elements.custom( T.elements.TRL_SUGGESTIONS, class extends HTMLElement {
        
        constructor() {
            super();
            this.accepted = [];
            
            this.setup();
        }
        
        connectedCallback() {
            this.setup();
        }
        
        static get observedAttributes() {
            return [ 'trl-store-accepted-suggestions' ];
        }
        
        attributeChangedCallback( name, oldValue, newValue ) {
            if ( name === 'trl-store-accepted-suggestions' ) {
                if ( newValue === 'true' ) this.open();
                else                       this.close();
            }
        }
        
        set opened(opened) {
            this.setAttribute( 'trl-opened', opened ? 'true' : 'false' );
        }
        
        get opened() {
            return this.getAttribute( 'trl-opened' ) === 'true';
        }
        
        close() {
            if ( this.opened ) this.opened = false;
        }
        
        open() {
            if ( !this.opened ) this.opened = true;
        }
        
        set storeAcceptedSuggestions( store ) {
            this.setAttribute( 'trl-store-accepted-suggestions', store.toString() );
        }
        
        get storeAcceptedSuggestions() {
            return this.getAttribute( 'trl-store-accepted-suggestions' ) === 'true';
        }
        
        set onacceptsuggestion(callback) {
            this.addEventListener( 'acceptsuggestion', callback );
        }
        
        setup() {
            var that     = this,
                observer = new MutationObserver( x );

            observer.observe( this, {
                childList : true,
                attributes: true
            } );

            function x() {

                for ( var i = 0, total = that.children.length; i < total; i++ ) {

                    var suggestion = that.children[ i ];

                    suggestion.onclick = ( e ) => {
                        if ( that.storeAcceptedSuggestions ) {
                            that.accepted.push( e.target );
                            that.removeChild( e.target );
                        }
                        that.dispatchEvent( new CustomEvent( 'acceptsuggestion', { detail: { suggestion: e.target } } ) );
                        that.close();
                    };

                }

            }
        }
    }, 'Suggestions' );

    window.dispatchEvent( new CustomEvent( 'T.elements.Suggestions.loaded' ) );
  
} )( window.T || {} );
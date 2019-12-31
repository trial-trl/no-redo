/* 
 * ( c ) 2017 TRIAL.
 * Created on 18/06/2017, 20:56:33.
 *
 * Licensed under the Apache License, Version 2.0 ( the 'License' );
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an 'AS IS' BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

( ( T ) => {
    
    T.elements.custom( T.elements.TRL_SELECTABLE, class extends HTMLElement {
        
        constructor() {
            super();
            this.active   = false;
            this.selected = 0;

            var observer = new MutationObserver( this.setup.bind( this ) );
            observer.observe( this, {
                childList: true
            } );
        }
        
        connectedCallback() {
            this.setup();
        }
        
        static get observedAttributes() {
            return ['trl-state'];
        }
        
        attributeChangedCallback( name, oldValue, newValue ) {
            if ( name === 'trl-state' ) {
                if ( newValue !== 'active' ) {
                    this.deselectAll();
                    this.active = false;
                } else {
                    this.active = true;
                }
            }
        }
        
        setup() {
            for ( var i = 0, total = ( this.children ? this.children.length : 0 ); i < total; i++ ) {
                this.children[ i ].onclick = ( e ) => {
                    this.toggleItem( e );
                };
            }
            if ( this.state === 'active' ) this.activeSelection();
            else                           this.desactiveSelection();
        }
        
        get length() {
            return this.children.length;
        }
        
        get state() {
            return this.getAttribute( 'trl-state' );
        }
        
        set state( state ) {
            this.setAttribute( 'trl-state', state );
        }
        
        isActive() {
            return this.active;
        }
        
        activeSelection() {
            this.setAttribute( 'trl-state', 'active' );
        }
        
        desactiveSelection() {
            this.setAttribute( 'trl-state', 'desactive' );
        }
        
        selectAll() {
            for ( var i = 0, t = ( this.children ? this.children.length : 0 ); i < t; i++ ) {
                this.selectItem( { target: this.children[ i ] } );
            }
        }
        deselectAll() {
            for ( var i = 0, a = this.selectedItems, t = a.length; i < t; i++ ) {
                this.deselectItem( { target: a[ i ] } );
            }
        }
        toggleItem( e ) {
            var that = this,
                item = e.target;
            if ( that.isActive() ) {
                e.stopPropagation();
                e.preventDefault();
                that.selected += item.classList.contains( 'selected' ) ? -1 : 1;
                item.classList.toggle( 'selected' );
                that.dispatchEvent( new CustomEvent( 'selectitem', {
                    detail: {
                        selected: item.classList.contains( 'selected' ),
                        length: that.selected,
                        item: item
                    }
                } ) );
            }
        }
        selectItem( e ) {
            var that = this,
                item = e.target;
            if ( that.isActive() ) {
                if (!item.classList.contains( 'selected' ) ) {
                    that.selected++;
                    item.classList.add( 'selected' );
                    that.dispatchEvent( new CustomEvent( 'selectitem', {
                        detail: {
                            selected: true,
                            length: that.selected,
                            item: item
                        }
                    } ) );
                }
            }
        }
        deselectItem( e ) {
            var that = this,
                item = e.target;
            if ( that.isActive() ) {
                if ( item.classList.contains( 'selected' ) ) {
                    that.selected--;
                    item.classList.remove( 'selected' );
                    that.dispatchEvent( new CustomEvent( 'selectitem', {
                        detail: {
                            selected: false,
                            length: that.selected,
                            item: item
                        }
                    } ) );
                }
            }
        }
        get selectedItems() {
            return this.querySelectorAll( '.selected' );
        }
        removeSelectedItems() {
            var selectedItems = this.selectedItems;
            for ( var i = 0, total = selectedItems.length; i < total; i++ ) {
                this.removeChild( selectedItems[ i ] );
            }
        }
        
    }, 'Selectable' );
    
    window.dispatchEvent( new CustomEvent( 'T.elements.Selectable.loaded' ) );
    
} )( window.T || {} );
/* 
 * ( c ) 2017 TRIAL.
 * Created on 18/06/2017, 20:54:48.
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

( ( T ) => {
  
    T.elements.custom( T.elements.TRL_EDITABLE, class extends HTMLElement {
        constructor() {
            super();
        }
        connectedCallback() {
            this.init();
        }
        static get observedAttributes() {
            return ['trl-editing', 'trl-saving', 'trl-placeholder'];
        }
        attributeChangedCallback( name, oldValue, newValue ) {
            if ( name === 'trl-editing' ) {
                this.isEditing = this.activeEditMode = newValue.toLowerCase() === 'true';
            }
            if ( name === 'trl-saving' && oldValue !== newValue && newValue === 'false' ) {
                this.activeEditMode = false;
            }
            if ( name === 'trl-placeholder' && oldValue !== newValue ) {
                this.placeholder = newValue;
            }
        }
        update() {
            this.undefined_innerHTML 
                    = this.innerHTML === undefined                || 
                      this.innerHTML === null                     ||
                      this.innerHTML === ''                       || 
                      this.innerHTML === 'Definir valor'          ||
                      this.innerHTML === 'Definir ' + this.title;
            if ( this.undefined_innerHTML ) {
                this.innerHTML = this.placeholder || 
                                 'Definir ' + ( this.title || 'valor' );
            }
        }
        
        get saving() {
            return this.isSaving;
        }
        
        set saving( value ) {
            this.isSaving = value;
            this.setAttribute( 'trl-saving', value.toString() );
        }
        
        get placeholder() {
            return this.placeholder_text;
        }
        
        set placeholder( value ) {
            this.placeholder_text = value;
            this.setAttribute( 'trl-placeholder', value );
        }
        
        init() {
            if ( this.hasAttribute( 'trl-placeholder' ) ) {
                this.placeholder = this.getAttribute( 'trl-placeholder' );
            }
            this.content    = this.getAttribute( 'trl-content' );
            this.clickable  = this.hasAttribute( 'trl-clickable' ) 
                              ? ( this.getAttribute( 'trl-clickable' ).toLowerCase() === 'true' ) 
                              : true;
            this.keyControl = this.hasAttribute( 'trl-key-control' ) 
                              ? ( this.getAttribute( 'trl-key-control' ).toLowerCase() === 'true' ) 
                              : true;
            if ( this.content ) {
                var oneditname = this.getAttribute( 'trl-onedit' );
                if ( oneditname ) {
                    var onedit = window[ oneditname ];
                    if ( typeof onedit === 'function' ) {
                        this.addEventListener( 'edit', onedit );
                    }
                }
                if ( this.clickable ) {
                    this.onclick = () => {
                        this.setAttribute( 'trl-editing', 'true' );
                    };
                }
            }
            this.update();
        }
        
        set activeEditMode( active ) {
            var that = this;

            if ( active ) activeEditMode.call( this );
            else           finishEdition.call( this );

            function activeEditMode() {
                var s = document.createElement( this.content === 'select' || this.content === 'textarea' ? this.content : 'input' );
                copyAttrs.call( this, s );
                this.update();
                this.original_value = this.undefined_innerHTML            ||
                                      this.innerHTML === this.placeholder 
                                      ? '' 
                                      : this.innerHTML;
                switch ( this.content ) {
                    case 'select':
                        var select_data = JSON.parse( decodeURIComponent( atob( this.getAttribute( 'data-select' ) ) ) );
                        select_data.unshift( { value: '', innerHTML: this.placeholder || this.title } );
                        var select_default_value = this.getAttribute('data-value') || null;
                        for ( var i = 0, total = select_data.length, data; i < total, data = select_data[ i ]; i++ ) {
                            var option = document.createElement( 'option' );

                            option.value     = data.value;
                            option.innerHTML = data.innerHTML;

                            if ( data.value === select_default_value || data.innerHTML === this.original_value ) {
                                option.selected = true;
                            }

                            s.appendChild( option );
                        }
                        s.style.width = 'auto';
                        break;
                    case 'textarea':
                        s.placeholder = this.placeholder || this.title;
                        s.innerHTML   = this.original_value;
                        s.onclick     = ( e ) => e.stopPropagation();
                        s.style.width = 'auto';
                        break;
                    default:
                        s.type        = this.content;
                        s.placeholder = this.placeholder || this.title;
                        s.value       = this.original_value;
                        s.onclick     = ( e ) => e.stopPropagation();
                        s.style.width = this.offsetWidth + 'px';
                }
                if ( this.keyControl ) {
                    s.onkeydown = ( e ) => {
                        switch ( e.keyCode ) {
                            case 13:
                                save.call( that, e );
                                break;
                        }
                    };
                }
                s.onblur = save.bind( that );
                s.style.minWidth = this.offsetWidth + 'px';
                this.onclick = null;
                this.innerHTML = '';
                this.appendChild( s );
                s.focus();
            };

            function save( e ) {
                var select_default_value = this.getAttribute('data-value') || null;
                if ( e.target.nodeName === 'SELECT' ) {
                    var selected_value = e.target.options[e.target.selectedIndex].value;
                    this.editing_value = e.target.options[ e.target.selectedIndex ].innerHTML;
                } else {
                    this.editing_value = e.target.value;
                }
                if ( ( selected_value && selected_value != select_default_value && this.editing_value !== this.original_value ) || ( !selected_value && this.editing_value !== this.original_value ) ) {
                    this.saving = true;
                }
                this.setAttribute( 'trl-editing', 'false' );
            };

            function finishEdition() {
                var element = this.children[ 0 ];
                if ( this.saving && this.editing_value !== this.original_value ) {
                    try {
                        this.dispatchEvent( new CustomEvent( 'edit', { detail: { 
                                value: element.value 
                        } }));
                    } catch ( e ) {}
                } else {
                    this.saving = false;
                    desactiveEditMode.call( this );
                }
            }

            function desactiveEditMode() {
                if (!this.saving ) {
                    this.original_value = this.editing_value;
                    if ( this.clickable ) {
                        this.onclick = () => {
                            this.setAttribute( 'trl-editing', 'true' );
                        };
                    }
                    this.innerHTML = this.original_value;
                    this.update();
                }
            };

            function copyAttrs( to ) {
                if ( this.hasAttributes()) {
                    for ( var attr, i = 0, attrs = this.attributes, total = attrs.length, a; i < total, a = attrs[ i ]; i++ ) {
                        if ( a.name !== 'id'                  && 
                             a.name !== 'class'               && 
                             a.name !== 'name'                && 
                             a.name.indexOf( 'trl-' )  === -1 && 
                             a.name.indexOf( 'data-' ) === -1    ) {
                            to.setAttribute( a.name, a.value );
                        }
                    }
                }
            }

        }
        
    }, 'Editable' );

    window.dispatchEvent( new CustomEvent( 'T.elements.Editable.loaded' ) );
  
} )( window.T || {} );
/* 
 * (c) 2017 TRIAL.
 * Created on 18/06/2017, 20:58:17.
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

( ( T ) =>  {
  
    T.elements.custom( T.elements.TRL_POPUP, class extends HTMLElement {
        
        constructor() {
            super();
            
            this.CONTROLLER_POSITIVE = 'positive';
            this.CONTROLLER_NEGATIVE = 'negative';
            this.ALIGNMENT_CENTER_HORIZONTAL = 1;
            this.ALIGNMENT_CENTER_VERTICAL = 2;
            this.ALIGNMENT_CENTER = 1 | 2;
            this.ALIGNMENT_ABSOLUTE = 4;
            this.ALIGNMENT_FIT_SCREEN = 8;
            
            T.load( [ 'design', 'utils' ] );
            
        }
        
        static get observedAttributes() {
            return ['trl-show'];
        }
        
        attributeChangedCallback( name, oldValue, newValue ) {
            if ( newValue !== oldValue ) {
                switch ( name ) {
                    case 'trl-show':
                        if ( newValue ) this.open.call( this );
                        else            this.close.call( this );
                        break;
                }
            }
        }
        
        getBackground() {
            this.appendChild(  !this.__bg 
                              ? this.background 
                              : this.__bg);
            return this.__bg;
        }
        
        get background() {
            if ( !this.__bg ) 
                this.__bg = this.querySelector( '[background]' );
            if ( !( !!this.__bg ) ) {
                this.__bg = document.createElement( 'div' );
                this.__bg.setAttribute( 'background', '' );
            }
            return this.__bg;
        }
        
        getContent() {
            this.appendChild( !this.__content ? this.content : this.__content );
            return this.__content;
        }
        
        get content() {
            if ( !this.__content ) 
                this.__content = this.querySelector( '[content]' );
            if ( !( !!this.__content ) ) {
                this.__content = document.createElement( 'div' );
                this.__content.setAttribute( 'content', '' );
                for ( let i = this.children.length - 1, t = 0; i >= t; i-- ) {
                    this.__content.insertBefore( this.children[ i ], this.__content.firstChild );
                }
            }
            return this.__content;
        }
        
        set outside_click( b ) {
            if ( typeof b !== 'boolean' )
                throw new TypeError( 'trl-popup outside click argument must be a boolean value' );
            this.__outside_click = b;
        }
        
        get outside_click() {
            return this.__outside_click || true;
        }
        
        set autodismiss( b ) {
            if ( typeof b !== 'boolean' )
                throw new TypeError( 'trl-popup autodismiss argument must be a boolean value' );
            this.__autodismiss = b;
        }
        
        get autodismiss() {
            return this.__autodismiss || true;
        }
        
        setAlignment( a ) {
            if ( a !== this.ALIGNMENT_CENTER_HORIZONTAL && 
                 a !== this.ALIGNMENT_CENTER_VERTICAL   && 
                 a !== this.ALIGNMENT_CENTER            && 
                 a !== this.ALIGNMENT_ABSOLUTE          && 
                 a !== this.ALIGNMENT_FIT_SCREEN )
                throw new TypeError( 'trl-popup alignment must be one of the following values: self::ALIGNMENT_CENTER_HORIZONTAL | self.ALIGNMENT_CENTER_VERTICAL | self.ALIGNMENT_CENTER | self.ALIGNMENT_ABSOLUTE | self.ALIGNMENT_FIT_SCREEN' );
            this.__alignment |= a;
        }
        
        getAlignment() {
            if ( !this.__alignment ) 
                this.setAlignment( this.ALIGNMENT_CENTER );
            return this.__alignment;
        }
        
        set freeze( b ) {
            if ( typeof b !== 'boolean' )
                throw new TypeError( 'trl-popup freeze page argument must be a boolean value' );
            this.setAttribute( 'trl-freeze', b );
        }
        
        get freeze() {
            return this.hasAttribute( 'trl-freeze' ) ? this.getAttribute( 'trl-freeze' ) === 'true' : true;
        }
        
        setController( which, element ) {
            if ( which !== this.CONTROLLER_POSITIVE && which !== this.CONTROLLER_NEGATIVE )
                throw new TypeError( 'trl-popup setController() argument must be one of the following values: self::CONTROLLER_POSITIVE | self::CONTROLLER_NEGATIVE' );
            if ( !this.__controller )
                this.__controller = [];
            this.__controller[ which ] = element;
        }
        
        getController( which ) {
            if ( which !== this.CONTROLLER_POSITIVE && which !== this.CONTROLLER_NEGATIVE )
                throw new TypeError( 'trl-popup getController() argument must be one of the following values: self::CONTROLLER_POSITIVE | self::CONTROLLER_NEGATIVE' );
            if ( !this.__controller )
                this.__controller = [];
            return this.__controller[ which ] || this.querySelector( '[' + which + '], .' + which );
        }
        
        set width( n ) {
            if ( typeof n !== 'string' )
                throw new TypeError( 'trl-popup width argument must be a css number value' );
            this.setAttribute( 'trl-width', n );
        }
        
        get width() {
            return this.getAttribute( 'trl-width' ) || 'auto';
        }
        
        set height( n ) {
            if ( typeof n !== 'string' )
                throw new TypeError( 'trl-popup width argument must be a css number value' );
            this.setAttribute( 'trl-height', n );
        }
        
        get height() {
            return this.getAttribute( 'trl-height' ) || 'auto';
        }
        
        set fluid( b ) {
            if ( typeof b !== 'boolean' )
                throw new TypeError('trl-popup width argument must be an boolean value');
            this.setAttribute( 'trl-fluid', b );
        }
        
        get fluid() {
            return this.hasAttribute( 'trl-fluid' ) ? this.getAttribute( 'trl-fluid' ) === 'true' : true;
        }
        
        set keep( b ) {
            if ( typeof b !== 'boolean' )
                throw new TypeError( 'trl-popup keep argument must be a boolean value' );
            this.setAttribute( 'trl-keep', b ? 'true' : 'false' );
        }
        
        get keep() {
            return this.hasAttribute( 'trl-keep' ) ? this.getAttribute( 'trl-keep' ) === 'true' : true;
        }
        
        open( e ) {
            if ( this.isOpen() !== false ) {
                return;
            }
            
            let that = this,
                top,
                left,
                center_horizontal_padding,
                center_vertical_padding, 
                center_container,
                container; // MUST be removed

            calculateEventArea.call( this, e );

            if ( !center_container ) center_container = false;

            let popup_content = this.getContent();
            let popup_bg      = this.getBackground();

            popup_content.style.top = top + 'px';
            popup_content.style.left = left + 'px';
            popup_content.style.width = this.width;
            popup_content.style.height = this.height;
            popup_content.style.opacity = 0;

            if ( popup_bg ) {
                popup_bg.style.top     = top + 'px';
                popup_bg.style.left    = left + 'px';
                popup_bg.style.display = 'block';
                popup_bg.style.opacity = 0;
                if ( this.outside_click )
                    popup_bg.onclick = popup_bg.oncontextmenu = this.close.bind( this );
            }
            if ( container ) {
                container.style.top     = top + 'px';
                container.style.left    = left + 'px';
                container.style.display = 'block';
                container.style.opacity = 0;
                if ( this.outside_click )
                    container.onclick = container.oncontextmenu = this.close.bind( this );
            }

            if ( this.freeze ) {
                document.documentElement.style.overflow = 'hidden';
            }
          
            if ( that.getAlignment() & that.ALIGNMENT_FIT_SCREEN ) {
                this.className = 'alignment--fit-screen full';
            }

            T.load( 'design', () => {
                document.body.appendChild( that );
                configureControllers.call( that );
                that.style.display = 'block';
                calculateElements.call( that );
                that.dispatchEvent( new CustomEvent( 'openstart' ) );
                T.Animation.start( {
                    animation: 'inverval',
                    duration: 200,
                    delta: ( p ) =>  {
                      return T.Animation.Easing.easeInOutQuart( p );
                    },
                    step: ( delta, t ) =>  {
                        if ( !( that.getAlignment() & that.ALIGNMENT_ABSOLUTE ) ) {
                            if (    that.getAlignment() & that.ALIGNMENT_FIT_SCREEN || 
                                 !( that.getAlignment() & that.ALIGNMENT_CENTER_VERTICAL ) ) {
                                popup_content.style.top = top - ( top * delta ) + 'px';
                            }
                            if (    that.getAlignment() & that.ALIGNMENT_FIT_SCREEN || 
                                 !( that.getAlignment() & that.ALIGNMENT_CENTER_HORIZONTAL ) ) {
                                popup_content.style.left = left - ( left * delta ) + 'px';
                            }
                        }
                        if ( that.getAlignment() & that.ALIGNMENT_CENTER_HORIZONTAL ||
                             center_container ) {
                            if ( center_container === true ) {
                                var size = (that.sizes.width.size) + center_horizontal_padding,
                                        real_container_left = container.left.unit !== '%' ? T.prototype.Utils.toPercent(container.left.size, window.innerWidth) : (50 - container.left.size) + container.left.size + (container.left.size / 2.75);
                                popup_content.style.left = 'calc(' + ((T.prototype.Utils.toPercent(that.sizes.width.size + container.width.size, window.innerWidth) / 2) * delta) + '% + ' + (T.prototype.Utils.toPercent(left, window.innerWidth) - (T.prototype.Utils.toPercent(left, window.innerWidth) * delta)) + '%)';
                                popup_content.style.marginLeft = 'calc(-' + (size * delta) + 'px + ' + real_container_left + '%)';
                            } else {
                                var size = (that.sizes.width.size / 2) + center_horizontal_padding;
                                popup_content.style.left = 'calc(' + (50 * delta) + '% + ' + (left - (left * delta)) + that.sizes.width.unit + ')';
                                popup_content.style.marginLeft = '-' + (size * delta) + that.sizes.width.unit;
                            }
                        }
                        if ( that.getAlignment() & that.ALIGNMENT_CENTER_VERTICAL ||
                             center_container === true ) {
                            var size = ( that.sizes.height.size / 2 ) + center_vertical_padding;
                            popup_content.style.top       = 'calc(' + ( 50 * delta ) + '% + ' + ( top - ( top * delta ) ) + 'px)';
                            popup_content.style.marginTop = '-' + ( size * delta ) + 'px';
                        }
                        if ( popup_bg ) {
                            popup_bg.style.top     = top - ( top * delta ) + 'px';
                            popup_bg.style.left    = left - ( left * delta ) + 'px';
                            popup_bg.style.width   = 100 * delta + '%';
                            popup_bg.style.height  = 100 * delta + '%';
                            popup_bg.style.opacity = delta;
                        }
                        if ( container ) {
                            container.style.top     = top - ( top * delta ) + 'px';
                            container.style.left    = 'calc(' + ( container.left.size * delta ) + '' + container.left.unit + ' - ' + (left - (left * delta)) + 'px)';
                            container.style.width   = container.width.size  * delta + container.width.unit;
                            container.style.height  = container.height.size * delta + container.height.unit;
                            container.style.opacity = delta;
                        }
                        if ( !( that.getAlignment() & that.ALIGNMENT_FIT_SCREEN ) ) {
                            if ( that.width !== 'auto' || that.fluid ) {
                                popup_content.style.width  = that.width === 'auto' ? 'auto' : ( that.sizes.width.size * delta ) + that.sizes.width.unit;
                            }
                            if ( that.height !== 'auto' || that.fluid ) {
                                popup_content.style.height = that.height === 'auto' ? 'auto' : ( that.sizes.height.size * delta ) + that.sizes.height.unit;
                            }
                        } else {
                            popup_content.style.width  = ( 100 * delta ) + '%';
                            popup_content.style.height = ( 100 * delta ) + '%';
                        }
                        popup_content.style.opacity = delta;
                        that.dispatchEvent( new CustomEvent( 'opening', { detail: {
                            delta : delta,
                            time  : t
                        } } ) );
                    }
                } );
                setTimeout( () =>  {
                    that.dispatchEvent( new CustomEvent( 'openend' ) );
                    that.opened = true;
                }, 200 );
            } );

            function calculateEventArea( e ) {
                if ( e ) {
                    if ( e instanceof Event ) e.preventDefault();
                    top  = e.clientY ? e.clientY : window.innerHeight / 2;
                    left = e.clientX ? e.clientX : window.innerWidth  / 2;
                } else {
                    top  = window.innerHeight / 2;
                    left = window.innerWidth  / 2;
                }
            }

            function calculateElements() {
                if ( !( this.getAlignment() & this.ALIGNMENT_FIT_SCREEN ) ) {
                    this.sizes = {
                        width:  calculateStyle( popup_content, 'width' ),
                        height: calculateStyle( popup_content, 'height' )
                    };
                }
                if ( center_container === true ) {
                    container = popup_content.parentNode;
                    container.width  = calculateStyle( container, 'width' );
                    container.height = calculateStyle( container, 'height' );
                    container.left   = calculateStyle( container, 'left' );
                }
                if ( ( this.getAlignment() & this.ALIGNMENT_CENTER )            || 
                     ( this.getAlignment() & this.ALIGNMENT_CENTER_HORIZONTAL ) || 
                     ( this.getAlignment() & this.ALIGNMENT_CENTER_VERTICAL )   || 
                       center_container ) {
                    popup_content.padding = T(popup_content).Utils.getTotalPadding();
                    if ( ( this.getAlignment() & this.ALIGNMENT_CENTER_VERTICAL ) || 
                           center_container === true) {
                        center_vertical_padding = ( ( popup_content.padding.padding_top.size + popup_content.padding.padding_bottom.size ) / 2 );
                    }
                    if ( ( this.getAlignment() & this.ALIGNMENT_CENTER_HORIZONTAL ) || 
                           center_container === true ) {
                        center_horizontal_padding = ( ( popup_content.padding.padding_left.size + popup_content.padding.padding_right.size ) / 2 );
                    }
                }
            }

            function configureControllers() {
                var that     = this;
                var negative = this.getController( this.CONTROLLER_NEGATIVE );
                var positive = this.getController( this.CONTROLLER_POSITIVE );
                
                if (negative) negative.onclick = c( 'negative' );
                if (positive) positive.onclick = c( 'positive' );

                function c( controller ) {
                    return ( e ) => {
                        that.close.call( that, e );
                        that.dispatchEvent( new CustomEvent( controller ) );
                    };
                }
            }

            function calculateStyle( element, property ) {
                let size;
                let actual_size = window.getComputedStyle( element, null ).getPropertyValue( property );
                if ( actual_size !== 'auto' && 
                     actual_size !== 0      && 
                     actual_size !== '0px'  && 
                     actual_size !== '0%'      ) {
                    let is_px = actual_size.indexOf( 'px' ) !== -1;
                    size = {
                        size: is_px ? +( actual_size.replace( 'px', '' ) ) : +( actual_size.replace( '%', '' ) ),
                        unit: is_px ? 'px' : '%'
                    };
                } else {
                    size = { size: 100, unit: '%' };
                }
                return size;
            }
        }
        
        close( e ) {
            if ( this.isOpen() !== true ) {
                return;
            }
            
            var that          = this;
            var popup_content = this.getContent();
            var popup_bg      = this.getBackground();
            var container;
            
            if ( this.freeze ) document.documentElement.style.overflow = 'auto';
            
            var top  = e ? e.clientY : 0;
            var left = e ? e.clientX : 0;
            
            popup_content.style.opacity = 1;
            
            T.Animation.start({
                duration: 200,
                delta: ( p ) =>  {
                    return T.Animation.Easing.easeInOutQuart( p );
                },
                step: ( delta ) =>  {
                    let old_top  = +( window.getComputedStyle( popup_content, null ).getPropertyValue( 'top' ).replace( 'px', '' ) );
                    let old_left = +( window.getComputedStyle( popup_content, null ).getPropertyValue( 'left' ).replace( 'px', '' ) );
                    if ( popup_bg ) {
                        popup_bg.style.top     =       ( top  * delta ) + 'px';
                        popup_bg.style.left    =       ( left * delta ) + 'px';
                        popup_bg.style.width   = 100 - ( 100  * delta ) + '%';
                        popup_bg.style.height  = 100 - ( 100  * delta ) + '%';
                        popup_bg.style.opacity =   1 -          delta;
                    }
                    if ( container ) {
                        container.style.top     = ( top  * delta ) + 'px';
                        container.style.left    = ( left * delta ) + 'px';
                        container.style.width   = container.width.size  - ( container.width.size * delta ) + container.width.unit;
                        container.style.height  = container.height.size - ( container.height.size * delta ) + container.height.unit;
                        container.style.opacity = 1 - delta;
                    }
                    popup_content.style.top     = old_top - ( ( old_top - top ) * delta ) + 'px';
                    popup_content.style.left    = old_left - ( ( old_left - left ) * delta ) + 'px';
                    popup_content.style.width   = ( that.sizes.width.size - ( that.sizes.width.size * delta ) ) + that.sizes.width.unit;
                    popup_content.style.height  = ( that.sizes.height.size - ( that.sizes.height.size * delta ) ) + that.sizes.height.unit;
                    popup_content.style.opacity = 1 - delta;
                }
            });
            setTimeout( () =>  {
                that.opened = false;
                that.dispatchEvent( new CustomEvent( 'closeend' ) );
                if ( !that.keep ) document.body.removeChild( that );
                else              that.style.display = '';
            }, 201 );
        }
        
        isOpen() {
            return this.opened || false;
        }
        
    }, 'Popup' );

    window.dispatchEvent( new CustomEvent( 'T.elements.Popup.loaded' ) );
    
} )( window.T || {} );
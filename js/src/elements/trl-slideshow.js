/* 
 * ( c ) 2017 TRIAL.
 * Created on 18/06/2017, 21:02:16.
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
    
    T.elements.custom( T.elements.TRL_SLIDESHOW, class extends HTMLElement {
        
        constructor() {
            super();
            this.setup();
        }
        
        connectedCallback() {
            this.setup();
        }
        
        set auto( auto ) {
            this.setAttribute( 'trl-auto', auto ? 'true' : 'false' );
        }
        
        get auto() {
            return this.getAttribute( 'trl-auto' ) === 'true';
        }
        
        set unidirectional( unidirectional ) {
            this.setAttribute( 'trl-unidirectional', unidirectional ? 'true' : 'false' );
        }
        
        get unidirectional() {
            return this.getAttribute( 'trl-unidirectional' ) === 'true';
        }
        
        set repeat( repeat ) {
            this.setAttribute( 'trl-repeat', repeat ? 'true' : 'false' );
        }
        
        get repeat() {
            return this.getAttribute( 'trl-repeat' ) === 'true';
        }
        
        set lastslide_conclude( lastslide_conclude ) {
            this.setAttribute( 'trl-lastslide-conclude', lastslide_conclude ? 'true' : 'false' );
        }
        
        get lastslide_conclude() {
            return this.hasAttribute( 'trl-lastslide-conclude' ) ? this.getAttribute( 'trl-lastslide-conclude' ) === 'true' : true;
        }
        
        set position( position ) {
            this.setAttribute( 'trl-position', position );
        }
        
        get position() {
            return parseInt( this.getAttribute( 'trl-position' )) || 0;
        }
        
        recalcSlides() {
            var slides =   this.slideSelector !== null 
                         ? this.querySelectorAll( this.slideSelector ) 
                         : this.children;
            this.slideshow_items = [];
            for ( var i = 0, t = slides.length, s; i < t, s = slides[ i ]; i++) {
                if ( !s.classList.contains( 'controller' ) && s.nodeName !== 'HEADER' ) {
                    s.classList.add( 'slide' );
                    this.slideshow_items.push( s );
                }
            }
        }
        
        set slideSelector( selector ) {
            this.setAttribute( 'trl-slide-selector', selector );
        }
        
        get slideSelector() {
            return this.hasAttribute( 'trl-slide-selector' ) ? this.getAttribute( 'trl-slide-selector' ) : null;
        }
        
        get slides() {
            if ( this.slideshow_items === null || this.slideshow_items === undefined || this.slideshow_items.length <= 0 ) {
                this.recalcSlides();
            }
            return this.slideshow_items;
        }
        
        set callbacks( callbacks ) {
            this.slideshow_callbacks = callbacks;
        }
        
        get callbacks() {
            return this.slideshow_callbacks || {};
        }
        
        next( callback ) {
            var that   = this,
                slides = this.slides,
                current,
                next,
                height_header = 0;
                
            if ( this.auto )
                this.defineInterval();
            
            current = next = this.position;
            
            if (!this.unidirectional ) {
                if ( this.controller.previous )
                    this.controller.previous.style.display = '';
            }
            
            if ( this.controller.conclude )
                this.controller.conclude.style.display = 'none';
            
            if ( this.controller.next )
                this.controller.next.style.display = '';
            
            if ( this.position < slides.length + 1 ) {
                next++;
                
                if ( next >= slides.length && this.repeat ) {
                    next = 0;
                } else if ( next >= slides.length - 1 && !this.repeat ) {
                    if ( this.lastslide_conclude && this.controller.conclude )
                        this.controller.conclude.style.display = '';
                    
                    if ( this.controller.next )
                        this.controller.next.style.display = 'none';
                }
            }
            
            this.position = next;
            
            if ( callback !== undefined )
                callback( this.slideshow_info );
            
            slides[ next ].classList.add( 'current' );
            
            T.load( 'navigation', () => {
                T.Navigation.next( {
                    top      : height_header,
                    current  : slides[ current ], 
                    next     : slides[ next ],
                    onfinish : () => {
                        slides[ current ].classList.remove( 'current' );
                        
                        if ( that.callbacks.onchangeslide )
                            that.callbacks.onchangeslide( that.slideshow_info );
                    }
                } );
            });
        }
        
        previous( callback ) {
            var that = this, slides = this.slides, current, before, height_header = 0;
            if ( this.auto ) {
                this.defineInterval();
            }
            current = before = this.position;
            if (!this.unidirectional )
                if ( this.controller.previous )
                    this.controller.previous.style.display = '';
            if ( this.controller.next )
                this.controller.next.style.display = '';
            if ( this.controller.conclude )
                this.controller.conclude.style.display = 'none';
            if ( this.position > -1 ) {
                before--;
                if ( before <= -1 && this.repeat ) {
                    before = slides.length - 1;
                } else if ( before <= 0 && !this.repeat && this.controller.previous )
                    this.controller.previous.style.display = 'none';
            } else {
                if ( this.controller.previous )
                    this.controller.previous.style.display = 'none';
            }
            this.position = before;
            if ( callback !== undefined )
                callback( this.slideshow_info );
            slides[ before ].classList.add( 'current' );
            T.load( 'navigation', () => {
                T.Navigation.previous({
                    top: height_header,
                    current: slides[ current ], 
                    previous: slides[ before ],
                    onfinish: () => {
                        slides[ current ].classList.remove( 'current' );
                        if ( that.callbacks.onchangeslide ) {
                            that.callbacks.onchangeslide( that.slideshow_info );
                        }
                    }
                });
            });
        }
        
        set interval( interval ) {
            this.setAttribute( 'trl-changeslide-ms', interval );
        }
        
        get interval() {
            return this.getAttribute( 'trl-changeslide-ms' ) || 4000;
        }
        
        defineInterval() {
            clearInterval( this.interval_controller );
            this.interval_controller = setInterval( () => {
                this.next();
            }, this.interval );
        }
        
        get slideshow_info() {
            return {
                position   : this.position,
                controller : this.controller,
                slide      : this.slides[ this.position ]
            };
        }
        
        setup() {
            var observer = new MutationObserver( ( m ) => {
                this.recalcSlides();
                this.dispatchEvent( new CustomEvent( 'append' ), {
                    detail: m
                } );
            } );
            observer.observe( this, { childList: true } );

            var slides = this.slides;
            
            this.controller = {};
            try {
                this.controller.cancel   = this.getElementsByClassName( 'cancel' )[ 0 ];
            } catch ( e ) {}
            try {
                this.controller.previous = this.getElementsByClassName( 'previous' )[ 0 ];
            } catch ( e ) {}
            try {
                this.controller.next     = this.getElementsByClassName( 'next' )[ 0 ];
            } catch ( e ) {}
            try {
                this.controller.conclude = this.getElementsByClassName( 'conclude' )[ 0 ];
            } catch ( e ) {}
            
            var header = this.getElementsByTagName( 'header' )[ 0 ],
                cont = this.getElementsByClassName( 'controller' )[ 0 ];
                
            if ( slides !== null && slides !== undefined && slides.length > 0 ) {
                var slide = slides[ this.position ];
                slide.classList.add( 'current' );
                
                var height_header = 0,
                    height_cont   = 0;
                    
                if ( cont || header ) {
                    var stylesheet;
                    
                    if ( cont ) {
                        height_cont = +( window.getComputedStyle( cont, null ).getPropertyValue( 'height' ).replace( 'px', '' ));
                    }
                    if ( header ) {
                        height_header = +( window.getComputedStyle( header, null ).getPropertyValue( 'height' ).replace( 'px', '' ));
                    }
                    
                    for ( var i in document.styleSheets ) {
                        if ( document.styleSheets[ i ].href && document.styleSheets[ i ].href.indexOf( 'standards' )) {
                            stylesheet = document.styleSheets[ i ];
                            break;
                        }
                    }
                    
                    if ( !T.prototype.style )
                        T.load( 'utils', applyStyles );
                    else
                        applyStyles();
                    
                    function applyStyles() {
                        T( stylesheet ).style({
                            Styles: {
                                '[ container-slideshow-items ] [ slideshow-item ]': {
                                    height: 'calc( 100% - ' + ( height_cont + height_header ) + 'px )'
                                }
                            }
                        });
                    }
                }
                if ( this.controller.conclude ) {
                    this.controller.conclude.style.display = 'none';
                }
                
                slide.style.display = '';

                if ( this.callbacks.onstart ) {
                    this.callbacks.onstart( this.slideshow_info );
                }

                if ( this.auto ) {
                    this.defineInterval();
                }

                if ( !this.unidirectional && this.controller.previous ) {
                    this.controller.previous.onclick = ( e ) => {
                        if ( this.callbacks.onpreviousslide ) {
                            var slideshow_info = this.slideshow_info;
                            slideshow_info.event = e;
                            if ( this.callbacks.onpreviousslide( slideshow_info )) {
                                this.previous();
                            }
                        } else {
                            this.previous();
                        }
                    };
                }
                if ( this.controller.next ) {
                    this.controller.next.onclick = ( e ) => {
                        if ( this.callbacks.onnextslide ) {
                            var slideshow_info = this.slideshow_info;
                            slideshow_info.event = e;
                            if ( this.callbacks.onnextslide( slideshow_info )) {
                                this.next();
                            }
                        } else {
                            this.next();
                        }
                    };
                }
                if ( this.controller.conclude ) {
                    this.controller.conclude.onclick = ( e ) =>{
                        if ( this.callbacks.onconclude ) {
                            var slideshow_info = this.slideshow_info;
                            slideshow_info.event = e;
                            this.callbacks.onconclude( slideshow_info );
                        }
                    };
                }
                if ( this.controller.cancel ) {
                    this.controller.cancel.onclick = ( e ) =>{
                        if ( this.callbacks.oncancel ) {
                            var slideshow_info = this.slideshow_info;
                            slideshow_info.event = e;
                            if ( this.callbacks.oncancel( slideshow_info )) {
                                this.position = 0;
                            }
                        } else {
                            this.position = 0;
                        }
                    };
                }
            }
        }
        
    }, 'Slideshow' );
    
    window.dispatchEvent( new CustomEvent( 'T.elements.Slideshow.loaded' ));
    
} )( window.T || {} );
/* 
 * ( c ) 2017 TRIAL.
 * Created on 18/06/2017, 20:52:01.
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
  
    T.elements.custom( 'trl-tabs-contents', class extends HTMLElement {}, 'TabsContents' );

    T.elements.custom( 'trl-tab-content', class extends HTMLElement {
        
        constructor() {
            super();
            if ( !this.getAttribute( 'index' ) || +( this.getAttribute( 'index' ) ) !== 1 ) {
                this.style.display = 'none';
            }
            this.style.width = '100%';
        }
        
    }, 'TabContent' );

    T.elements.custom( T.elements.TRL_TABS, class extends HTMLElement {
        
        constructor() {
            super();
            var tabs = this;

            var observer = new MutationObserver( () => {
                tabs.setup();
            });

            observer.observe( tabs, {
                attributes: true,
                childList: true
            });

            window.addEventListener( 'load', () => {
                if ( tabs.children.length > 0 )
                    tabs.setup();
            }, false );

            this.setup();
        }
        
        attachedCallback() {
            this.setup();
        }
        
        setup() {
            var tabs_for    = document.getElementById( this.getAttribute( 'trl-for' ) ),
                default_tab = this.getAttribute( 'trl-default' ) || 0;
            for ( var i = 0, t = this.children.length, tab; i < t, tab = this.children[ i ]; i++) {
                tab.onclick = ( ( index, tabs ) => {
                    return () => {
                        var tab = tabs.children[ index ];
                        if (!tab.classList.contains( 'current' ) ) {
                          var current_displayed = tabs_for.querySelector( '.current' ),
                                  display = tabs_for.children[ index ];

                          if ( current_displayed ) {
                              animHide( current_displayed );
                          }
                          try {
                              animShow( display );
                          } catch ( e ) {}
                          try {
                              tabs.querySelector( '.current' ).classList.remove( 'current' );
                          } catch ( e ) {}

                          tabs.dispatchEvent( new CustomEvent( 'selecttab', { detail: {
                            index   : index,
                            tab     : tab,
                            display : display
                          } }) );

                          tab.classList.add( 'current' );
                        }
                    };
                } )( i, this );
            }

            if ( default_tab === 0 || default_tab !== 'none' ) {
                this.children[ parseInt( default_tab ) ].click();
            }

            function animShow( el ) {

                if ( !T.Animation ) T.load( 'design', anim );
                else                anim();

                function anim() {

                    var original_width = +( window.getComputedStyle( el, null ).getPropertyValue( 'width' ).replace( 'px', '' ) );

                    el.style.width    = original_width + 'px';
                    el.style.position = 'absolute';
                    el.style.display  = '';
                    el.style.opacity  = 0;

                    T.Animation.start( {
                        duration: 100,
                        delta: ( p ) => {
                            return p;
                        },
                        step: ( delta ) => {
                            el.style.opacity = delta;
                        }
                    } );

                    setTimeout( () => {
                        el.style.position = '';
                    }, 101 );

                    el.classList.add( 'current' );

                }

            }

            function animHide( el ) {

                if ( !T.Animation ) T.load( 'design', anim );
                else                anim();

                function anim() {

                    el.style.position = 'absolute';

                    T.Animation.start({
                        duration: 100,
                        delta: ( p ) => {
                            return p;
                        },
                        step: ( delta ) => {
                            el.style.opacity = 1 - delta;
                        }
                    });

                    setTimeout( () => {
                        el.style.position = '';
                        el.style.display = 'none';
                    }, 101 );

                    el.classList.remove( 'current' );

                }

            }

        }
          
    }, 'Tabs', {
        extends: 'nav'
    } );

    T.elements.custom( 'trl-tab', class extends HTMLElement {
        
        constructor() {
            super();
            this.style.webkitUserSelect = 'none';
            this.style.mozUserSelect = 'none';
            this.style.webkitUserSelect = 'none';
            this.style.userSelect = 'none';
        }
        
    }, 'Tab' );

    window.dispatchEvent( new CustomEvent( 'T.elements.Tabs.loaded' ) );
  
})( window.T );
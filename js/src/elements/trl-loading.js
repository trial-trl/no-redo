/* 
 * ( c ) 2017 TRIAL.
 * Created on 18/06/2017, 20:59:23.
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

    T.elements.custom( T.elements.TRL_LOADING, class extends HTMLElement {
        
        constructor() {
            super();
            this.updateStyle();
        }
        
        static get observedAttributes() {
            return ['trl-color', 'trl-size', 'trl-duration'];
        }
        
        attributeChangedCallback() {
            this.updateStyle();
        }
        
        connectedCallback() {
            this.updateStyle();
        }
        
        set color( color ) {
            this.setAttribute( 'trl-color', color || '#24c742' );
        }
        
        get color() {
            return this.getAttribute( 'trl-color' ) || '#24c742';
        }
        
        set size( size ) {
            this.setAttribute( 'trl-size', size || '100%' );
        }
        
        get size() {
            return this.getAttribute( 'trl-size' ) || '100%';
        }
        
        set duration( duration ) {
            this.setAttribute( 'trl-duration', duration || '8s' );
        }
        
        get duration() {
            return this.getAttribute( 'trl-duration' ) || '8s';
        }
        
        updateStyle() {
            var that     = this,
                color    = this.color,
                size     = this.size,
                duration = this.duration;
                
            var css_changes = {
                Styles: {
                    '[loading] | [loading] [triangle][up][left] | [loading] [triangle][up][right][mid-top] | [loading] [triangle][up][right][mid-bottom] | [loading] [triangle][down][left] | [loading] [triangle][down][right]': {
                        animationDuration: duration
                    },
                    '[loading]': {
                        width  : size,
                        height : size
                    },
                    '[triangle-color]': {
                        fill: color
                    }
                }
            };
            
            missing( () => {
                if ( !that.stylesheet ) {
                    for ( let i in document.styleSheets ) {
                        if ( document.styleSheets[ i ].href && document.styleSheets[ i ].href.indexOf( 'trl-loading.css' ) !== -1 ) {
                            that.stylesheet = document.styleSheets[ i ];
                            break;
                        }
                    }
                }
                T( that.stylesheet ).style( css_changes );
            } );
  
            function missing( x ) {
                if ( !T.Utils && !T.style ) T.load( 'utils', x );
                else                        x();
            }
        }
        
        isLoading() {
            return this.children.length > 0;
        }
        
        start() {
            var loading             = document.createElement( 'section' ),
                container_right     = document.createElement( 'div' ),
                shape_number        = 0;

            loading.setAttribute( 'loading', '' );
            container_right.setAttribute( 'container-triangles', '' );
            
            addPieceInto( container_right )
                    .at( [ 'up', 'right', 'mid-top'    ], 'M50 50 L0 50 L50 0 L50 0 Z' )
                    .at( [ 'up', 'right', 'mid-bottom' ], 'M0 0 L50 0 L50 0 L0 50 Z'   );
            
            addPieceInto( loading )
                    .at( [ 'up',   'left'  ], 'M50 0 L50 50 L0 50 L0 50 Z' )
                    .append( container_right )
                    .at( [ 'down', 'left'  ], 'M0 0 L50 0 L50 0 L50 50 Z'  )
                    .at( [ 'down', 'right' ], 'M0 0 L0 50 L50 0 L50 0 Z'   );

            this.appendChild( loading );
            
            function addPieceInto( into ) {
                return {
                    at( positionArr, svgPath ) {
                        var piece = document.createElement( 'div' );
                        piece.setAttribute( 'triangle', '' );

                        for ( let i = 0, t = positionArr.length, attr; i < t, attr = positionArr[ i ]; i++ ) {
                            piece.setAttribute( attr, '' );
                        }
                        piece.innerHTML = svg( svgPath );
                        return this.append( piece );
                    },
                    append( el ) {
                        into.appendChild( el );
                        return this;
                    }
                };
            }

            function svg( path ) {
                var id = 'f' + ( shape_number++ );
                return `
                    <svg viewBox="0 0 50 50" style="width: 100%; height: 100%">
                        <defs>
                            <filter id="${ id }" x="0" y="0" width="100%" height="100%">
                                <feOffset result="offOut" in="SourceAlpha" dx="0" dy="0" />
                                <feGaussianBlur result="blurOut" in="offOut" stdDeviation="1" />
                                <feBlend in="SourceGraphic" in2="blurOut" mode="normal" />
                            </filter>
                        </defs>
                        <path triangle-color d="${ path }" filter="url(#${ id })" />
                        Sorry, your browser does not support inline SVG.
                    </svg>
                `;
            }

            return loading;
        }
        
        stop() {
            while ( this.firstChild ) {
                this.removeChild( this.firstChild );
            }
        }
        
    }, 'TRIAL.Loading' );

    window.dispatchEvent( new CustomEvent( 'T.elements.TRIAL.Loading.loaded' ) );
    
} )( window.T || {} );
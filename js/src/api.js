/*
 * T.js Library₢
 * Version: 1.3
 * 
 * 2016 ₢ TRIAL. All rights reserved.
 * 
 * Created on   : 10/05/2016, 18:59:09
 * Author       : Matheus Leonardo dos Santos Martins
 *
 * Licensed under the Apache License, Version 2.0 ( the "License" );
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

window.T = ( () => {

    var _ = ( () => {

        var on;

        var T = function ( element ) {
            if ( !( this instanceof T ) ) {
                return new T( element );
            }

            if ( element !== undefined ) {
                on = typeof element !== 'string' 
                     ? element 
                     : document.querySelector( element );
            }
        };

        T.fn = T.prototype = {
            getElement() {
                return on;
            },
            Utils: {
                getTotalPadding: function () {
                    var padding_left = window.getComputedStyle(T.prototype.getElement(), null).getPropertyValue('padding-left');
                    var padding_left_number = padding_left.indexOf('px') !== -1 ? +(padding_left.replace('px', '')) : +(padding_left.replace('%', ''));
                    var padding_left_unit = padding_left.indexOf('px') !== -1 ? 'px' : '%';

                    var padding_right = window.getComputedStyle(T.prototype.getElement(), null).getPropertyValue('padding-right');
                    var padding_right_number = padding_right.indexOf('px') !== -1 ? +(padding_right.replace('px', '')) : +(padding_right.replace('%', ''));
                    var padding_right_unit = padding_right.indexOf('px') !== -1 ? 'px' : '%';

                    var padding_top = window.getComputedStyle(T.prototype.getElement(), null).getPropertyValue('padding-top');
                    var padding_top_number = padding_top.indexOf('px') !== -1 ? +(padding_top.replace('px', '')) : +(padding_top.replace('%', ''));
                    var padding_top_unit = padding_top.indexOf('px') !== -1 ? 'px' : '%';   

                    var padding_bottom = window.getComputedStyle(T.prototype.getElement(), null).getPropertyValue('padding-bottom');
                    var padding_bottom_number = padding_bottom.indexOf('px') !== -1 ? +(padding_bottom.replace('px', '')) : +(padding_bottom.replace('%', ''));
                    var padding_bottom_unit = padding_bottom.indexOf('px') !== -1 ? 'px' : '%';   

                    var padding =  {
                      padding_left: {
                        size: padding_left_number,
                        unit: padding_left_unit
                      },
                      padding_right: {
                        size: padding_right_number,
                        unit: padding_right_unit
                      },
                      padding_top: {
                        size: padding_top_number,
                        unit: padding_top_unit
                      },
                      padding_bottom: {
                        size: padding_bottom_number,
                        unit: padding_bottom_unit
                      }
                    };

                    return padding;
                },
                controlVisibility( options ) {
                    var exhibiting;
                    var scrollY = window.pageYOffset;

                    options.when_hide = options.when_hide || 200;
                    options.interval  = options.interval  || 100;

                    window.onscroll = ( e ) => {
                        if ( ( exhibiting && window.pageYOffset >= scrollY ) || 
                             ( !exhibiting && window.pageYOffset <= scrollY )    ) {
                            options.current += options.interval;
                        }

                        scrollY = window.pageYOffset;

                        if ( options.current >= options.when_hide ) {
                            showHideElement( exhibiting );
                            
                            options.current = 0;
                            scrollY         = window.pageYOffset;
                        }
                        if ( window.pageYOffset === 0 ) {
                            showHideElement( false );
                            
                            options.current = 0;
                            scrollY         = window.pageYOffset;
                        }
                    };
                },
                triggerOffset( options ) {
                  options.element = T.prototype.getElement();
                  var offset_toolbar = options.trigger_offset !== null ? options.trigger_offset : options.element.offsetTop;
                  window.addEventListener( 'scroll', ( e ) => {
                    if ( window.pageYOffset > offset_toolbar ){
                      if ( options.class_name.constructor === Array ) {
                        for ( var i = 0, total = options.class_name.length; i < total; i++ ) {
                          options.element.classList.add( options.class_name[ i ] );
                        }
                      } else {
                        options.element.classList.add( options.class_name );
                      }
                      if ( options.hasOwnProperty( 'reset_top' ) && options.reset_top ) {
                        options.element.style.top = 0;
                      }
                    } else {
                      if ( options.class_name.constructor === Array ) {
                        for ( var i = 0, total = options.class_name.length; i < total; i++ ) {
                          options.element.classList.remove( options.class_name[ i ] );
                        }
                      } else {
                        options.element.classList.remove( options.class_name );
                      }
                      if ( options.element.style.position === 'absolute' ) {
                        options.element.style.top = offset_toolbar + 'px';
                      }
                    }
                  }, false );
                },
                endlessScroll( options ) {
                  var element = T.prototype.getElement(),
                      is_window = element === window,
                      attach = is_window ? window : element;
                  attach.addEventListener( 'scroll', function( ) {
                    var contentHeight, yOffset, y;
                    if ( is_window ) {
                      contentHeight = options.target.offsetHeight;
                      yOffset = window.pageYOffset;
                      y = yOffset + window.innerHeight;
                    } else {
                      contentHeight = element.scrollHeight || element.offsetHeight || element.clientHeight;
                      yOffset = element.scrollTop;
                      y = yOffset + element.offsetHeight;
                    }
                    if ( y >= contentHeight + (-options.offset || 0 ) ) {
                      options.onend( options );
                    }
                  } );
                },
                toPercent( px, base ) {
                  return ( px * 100 ) / base;
                },
                visibilityOnScroll( options ) {
                    var POSITIVE  = 'positive';
                    var NEGATIVE  = 'negative';
                    var that      = this;
                    var element   = T.prototype.getElement();
                    var invisible = false;
                    var scroll    = { direction: POSITIVE, y: 0 };
                    var ontrigger = options.ontrigger || null;

                    window.addEventListener( 'scroll', listener );

                    function calculateScroll() {
                        var direction = window.scrollY > that.y 
                                        ? POSITIVE 
                                        : NEGATIVE;
                        if ( scroll.direction === direction ) {
                            if ( ( scroll.direction === POSITIVE && !invisible ) || 
                                 ( scroll.direction === NEGATIVE &&  invisible )    ) {
                                scroll.y += Math.abs( window.scrollY - that.y );
                            }
                        } else {
                            scroll.y = 0;
                        }
                        scroll.direction = direction;
                    }

                    function listener( e ) {
                        calculateScroll();

                        if ( scroll.y >= options.trigger ) {
                            if ( invisible && scroll.direction === NEGATIVE ) {
                                show();
                            } else if ( !invisible && scroll.direction === POSITIVE ) {
                                hide();
                            }
                        }

                        if ( window.pageYOffset === 0 ) {
                            show();
                        }

                        assignCurrentY();
                    }

                    function show() {
                        scroll.y = 0;
                        invisible = false;
                        if ( ontrigger ) {
                            ontrigger( options, 'down' );
                        } else {
                            if ( Array === options.add_class.constructor ) {
                                for ( let i = 0, t = options.add_class.length; i < t; i++ ) {
                                    element.classList.add( options.add_class[ i ] );
                                }
                            } else {
                                element.classList.add( options.add_class );
                            }
                        }
                    }

                    function hide() {
                        scroll.y = 0;
                        invisible = true;
                        if ( ontrigger ) {
                            ontrigger( options, 'up' );
                        } else {
                            if ( Array === options.add_class.constructor ) {
                                for ( let i = 0, t = options.add_class.length; i < t; i++ ) {
                                    element.classList.remove( options.add_class[ i ] );
                                }
                            } else {
                                element.classList.remove( options.add_class );
                            }
                        }
                    }

                    function assignCurrentY() {
                        that.y = window.scrollY;
                    }

                    return {
                        removeListener: () => {
                            window.removeEventListener( 'scroll', listener );
                        }
                    };
                },
                closest( selector ) {
                    var matchesFn;
                    [ 'matches', 'webkitMatchesSelector', 'mozMatchesSelector', 'msMatchesSelector', 'oMatchesSelector' ].some( ( fn ) => {
                        if ( typeof document.body[ fn ] === 'function' ) {
                            matchesFn = fn;
                            return true;
                        }
                        return false;
                    } );
                    try {
                        var parent = on;
                        do {
                            if ( parent && parent[ matchesFn ]( selector ) )
                                return parent;
                        } while ( parent = parent.parentElement )
                    } catch ( e ) {}
                    return null;
                }
            },
            UI: {
                Copyright: {
                    OWN       : 'own',
                    DEVELOPED : 'developed'
                },
                addFooter( options ) {
                    options.type = options.type || T.prototype.UI.Copyright.OWN;
                    
                    var period = '';
                    
                    if ( options.period ) {
                        
                        options.period.separator = options.period.separator || '-';
                        options.period.to = options.period.to || 'Y';
                        
                        if ( options.period.since ) {
                            period += options.period.since.toString();
                        }
                        if ( options.period.to && typeof options.period.to === 'string' ) {
                            if ( options.period.to === 'Y' ) {
                                options.period.to = new Date().toLocaleDateString( 'pt-BR', {
                                    year: 'numeric'
                                } )
                            }
                            if ( options.period.since && options.period.to.toString() === options.period.since.toString() ) {
                                delete options.period.to;
                            }
                        }
                        if ( options.period.to ) {
                            period += ( options.period.since ? ' ' + options.period.separator + ' ' : '' ) + options.period.to;
                        }
                    }

                    var trl = '<a href=\'https://trialent.com\'>{}</a>';
                    var trialLogo = trl.replace( 
                            '{}', 
                            `<trl-logo trl-slogan="false" trl-color="${ options.color || options.theme && options.theme === 'dark' ? '#fff' : '' }" trl-theme="${ options.theme }" trl-scale="${ options.scale || .5 }" ${ options.width ? ' trl-width="' + options.width + '"' : '' }" ${ options.height ? ' trl-height="' + options.height + '"' : '' }"></trl-logo>`
                    );
                    var trialName = trl.replace( '{}', 'TRIAL' );
                    var p = options.type === T.prototype.UI.Copyright.OWN 
                            ? `${ period } ₢ ${ trialName }.<br />${ options.phrase || 'Todos os direitos reservados' }`
                            : `${ period }. ${ options.phrase || 'Desenvolvido pela' } ${ trialName }`;
                  
                    T.prototype.getElement().innerHTML = `
                        <footer trl-owner class="center-horizontal ${ options.theme || 'dark' }">
                            ${ trialLogo }
                            <span trl-copyright>${ p }</span>
                        </footer>
                    `;
                }
            }
        };

        return T;

    } )();

    _ = ( ( T ) => {
        
        T.HOST    = location.protocol + '//' + location.hostname + 
                      ( location.port ? ':' + location.port : '' );
        T.BOWER   = T.HOST    + '/bower_components';
        T.LIBRARY = T.BOWER   + '/trl-no-redo';
        T.CSS     = T.LIBRARY + '/css';
        T.API     = T.LIBRARY + '/js/src';
        T.LOADJS  = T.BOWER   + '/loadjs/dist/loadjs.min.js';
        
        T.setHost = function ( url ) {
            T.HOST    = url;
            T.BOWER   = T.HOST    + '/bower_components';
            T.LIBRARY = T.BOWER   + '/trl-no-redo';
            T.CSS     = T.LIBRARY + '/css';
            T.API     = T.LIBRARY + '/js/src';
            T.LOADJS  = T.BOWER   + '/loadjs/dist/loadjs.min.js';
        };

        T.Constants = {
            Response: {
                JSON: 'json'
            },
            Message: {
                EXIST: 'E',
                NOT_EXIST: 'NE',
                NOT_ACTIVATED: 'NA',
                SAVED_WITH_SUCCESS: 'SWS',
                ERROR_PASSWORD_INCORRECT: 'ERR_PI',
                MEMBER_WITHOUT_INSTITUTION: 'MWI'
            },
            Slideshow: {
                CONTAINER_BACKGROUND: 'Cn_bCk_Ssw-Ll'
            },
            Paths: {
                IMAGE_PROFILE: 'http://no-redo.trialent.com/repository/images/TRIAL/logo/icon/social/min/T_icon_social_invert.png'
            }
        };

        T.URL = {
            exists( url, callback ) {
                T.Utils.ajax( {
                    url: url,
                    onerror: ( e ) => {
                        callback( false, e );
                    },
                    onloadend: ( e ) => {
                        callback( true, e );
                    }
                } );
            },
            Parameter: {
                add( key, value ) {
                    var href    = location.href;
                    var url     = href.split( '?' );
                    var pairs   = href.indexOf( '&' ) !== -1 ? url[ 1 ].split( '&' ) : ''; 
                    var key_arr = [],
                        parameters;
                    if ( url.length >= 2 ) {
                        if ( pairs !== '' ) {
                            for ( var i = 0, total = pairs.length; i < total; i++ ) {
                                var arr = pairs[ i ].split( '=' );
                                key_arr[ i ] = arr[ 0 ];
                                if ( arr[ 0 ] === key ) {
                                    pairs[ i ] = arr[ 0 ] + '=' + value;
                                }
                            }
                            if ( key_arr.indexOf( key ) === -1 ) {
                                pairs.push( key + '=' + value );
                            }
                        } else {
                            var arr = url[ 1 ].split( '=' );
                            pairs = [];
                            pairs[ 0 ] = url[ 1 ];
                            if ( arr[ 0 ] !== key ) {
                                pairs.push( key + '=' + value );
                            }
                        }
                    } else {
                        pairs = [];
                        pairs.push( key + '=' + value );
                    }
                    parameters = pairs.join( '&' );
                },
                get( key ) {
                    var href = location.href;
                    var url = href.split( '?' );
                    var pairs = href.indexOf( '&') !== -1 ? url[ 1 ].split('&' ) : null;
                    if ( pairs !== null ) {
                        var key = [], parameters;
                        if ( url.length >= 2 ) {
                            for ( var i = 0, total = pairs.length; i < total; i++ ) {
                                var arr = pairs[ i ].split( '=' );
                                if ( arr[ 0 ] === key ) {
                                    return arr[ 1 ];
                                }
                            }
                        }
                    }
                    if ( url[ 1 ] !== null && url[ 1 ] !== undefined ) {
                        pairs = url[ 1 ].split( '=' );
                        if ( pairs[ 0 ] === key ) {
                            return pairs[ 1 ];
                        }
                    }
                    return null;
                }
            }
        };

        T.load = ( jss, fn ) => {
            var js      = [];
            var lib     = window[ 'loadjs' ] && typeof window[ 'loadjs' ] === 'function' && !!document.querySelector( 'script[ src*=\'loadjs\' ]' );
            var jss_str = typeof jss === 'string';

            if ( !jss_str && !( jss.constructor === Array ) ) {
                throw new TypeError( '( T.load ) Param must be a string or array' );
            }

            jss = jss_str ? [ jss ] : jss;

            for ( let i in jss ) {
                let url = jss[ i ];
                switch ( url ) {
                    case 'account':
                    case 'cookies':
                    case 'history':
                    case 'navigation':
                    case 'utils':
                    case 'elements':
                    case 'design':
                        var firstChar = url.substring(0, 1);
                        var remainingStr = url.substring(1, url.length);
                        if ( ( !T[ firstChar.toUpperCase() + remainingStr ] && !T[ firstChar + remainingStr ] ) || ( url === 'design' && !T.Animation && !T.Color ) ) {
                            url = T.API + '/' + url + '.js';
                        } else {
                            url = null;
                        }
                        break;
                }
                if ( url ) {
                    js.push( url );
                }
            };

            if ( !lib ) {
                var s    = document.createElement( 'script' );
                s.src    = T.LOADJS;
                s.onload = load.bind( this, fn );
                document.body.appendChild( s );
            } else
                load( fn );

            function load( callback ) {
                if ( js.length > 0 ) {
                    loadjs( js, {
                        success() {
                            T.dispatchCallback( callback );
                        }
                    } );
                } else {
                    T.dispatchCallback( callback );
                }
            }
        };

        T.dispatchCallback = ( callback ) => {            
            if ( typeof callback === 'string' ) {
                try {
                    window[ callback ]();
                } catch ( e ) {}
            } else if ( typeof callback === 'function' ) {
                callback();
            }
        };

        T._callbacks = [];

        T.on = T.addEventListener = ( event, fn ) => {
            if ( !T._callbacks[ event ] ) {
                T._callbacks[ event ] = [ ];
            }
            T._callbacks[ event ].push( fn );
        };

        T.dispatchEvent = ( event, lib ) => {
            if ( T._callbacks[ event ] ) {
                for ( var i = 0, t = T._callbacks[ event ].length; i < t; i++ ) {
                    T._callbacks[ event ][ i ]( lib );
                }
            }
        };

        T.storageAvailable = ( type ) => {

            if ( type !== 'localStorage' && type !== 'sessionStorage' ) {

                throw new SyntaxError( 'Unknown storage. The supporting types are the following: localStorage and sessionStorage' );

            }

            try {

                var storage = window[ type ],
                    k       = 'storage_test';

                storage.setItem( k, k );
                storage.removeItem( k );

                return true;

            } catch ( e ) {

                return  e instanceof DOMException && ( 
                            e.code === 22                           || 
                            e.code === 1014                         || 
                            e.name === 'QuotaExceededError'         || 
                            e.name === 'NS_ERROR_DOM_QUOTA_REACHED'    ) && 
                        storage.length > 0;

            }

        };

        return T;

    } )( _ || {} );

    /**
     * ( c ) 2018 TRIAL.
     * Created on 20/08/2018, 17:34:25.
     * Adapted from Simple MVC of Todd Zebert
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
    _ = ( ( T ) => {

        /*
         * Simple MVC
         *
         * 2016 Todd Zebert
         *
         * repetative module pattern ( and eslint directives ) are so it can be
         * easily customized and broken into multiple files
         */


        /**
         * Simple MVC, 2016 Todd Zebert
         * Event Listeners and notifications module
         * 
         * @param {window.T} T 
         */
        T.MVC = ( ( T ) => {
            'use strict';

            // sender is the context of the Model or View which originates the event
            T._Event = function ( sender ) {
                this._sender = sender;
                this._listeners = [];
            };

            T._Event.prototype = {
                // add listener closures to the list
                attach( listener ) {
                    this._listeners.push( listener );
                },
                // loop through, calling attached listeners
                notify( args ) {

                    this._listeners.forEach( function( v, i ) {
                        this._listeners[ i ]( this._sender, args );
                    }.bind( this ) );

                }
            };

            return T;

        } )( T.MVC || {} );


        /**
         * Simple MVC, 2016 Todd Zebert
         * Model module
         * 
         * @param {window.T} T 
         */
        T.MVC = ( function( T ) { // eslint-disable-line no-redeclare, no-shadow
            'use strict';

            T.Model = function ( data ) {

                this._data = [];

                var properties = Object.getOwnPropertyNames( data );

                for ( var i in properties ) {

                    var property = properties[ i ];

                    if ( property === 'length' ) continue;

                    if ( typeof data[ property ] === 'object' && data[ property ] !== null && data[ property ] !== undefined ) {

                        this._data[ property ] = new T.Model( data[ property ] );

                    } else {

                        this._data[ property ] = ( ( _v ) => {

                            var value = _v;

                            var property = {
                                // get just returns the value
                                get() {
                                    return value;
                                },
                                // sets the value and notifies any even listeners
                                set( data ) {
                                    value = data;
                                    this.onSet.notify( data );
                                },
                                onSet: new T._Event( this )
                            };

                            return property;

                        } )( data[ property ] );

                    }

                }

                return this._data;

            };

            return T;
        } )( T.MVC || {} ); // eslint-disable-line no-use-before-define, no-redeclare, no-shadow


        /**
         * A 1-way View Module
         * 
         * @param {window.T} T 
         */
        T.MVC = ( ( T ) => { // eslint-disable-line no-redeclare, no-shadow
            'use strict';

            T.OneWayView = function ( selector, binder ) {

                this._selector = selector;
                if ( !( !!this._selector ) ) {
                    this._selector = document.querySelector( selector );
                }

                this._binder = binder;

                // since not a 2-way, don't need to set this.onChanged

                var attributes = Object.getOwnPropertyNames( binder );

                for ( var i in attributes ) {

                    var attribute = attributes[ i ];

                    // attach model listeners
                    binder[ attribute ].onSet.attach( function ( attribute ) {

                        return function ( sender, args ) {

                            this.bind( attribute, args );

                        }.bind( this );

                    }.call( this, attribute ) );

                }

            };

            T.OneWayView.prototype = {

                bind( attribute, args ) {

                    this._selector[ attribute ] = args;

                },

                show() {

                    var attributes = Object.getOwnPropertyNames( this._binder );

                    for ( var i in attributes ) {

                        var attribute = attributes[ i ];

                        this._selector[ attribute ] = this._binder[ attribute ].get();

                    }

                }

            };

            return T;
        } )( T.MVC || {} ); // eslint-disable-line no-use-before-define, no-redeclare, no-shadow


        /**
         * A 2-way View Module
         * 
         * @param {window.T} T 
         */
        T.MVC = ( ( T ) => { // eslint-disable-line no-redeclare, no-shadow
            'use strict';

            // selector is a DOM element that supports .onChanged and .value
            T.TwoWayView = function ( selector, binder ) {

                this._selector = selector;
                if ( !( this._selector instanceof HTMLElement ) ) {
                    this._selector = document.querySelector( selector );
                }

                if ( !( this._selector instanceof HTMLElement ) ) {
                    return null;
                }

                this._binder = binder;

                // for 2-way binding
                this.onChanged = new T._Event( this );

                var attributes = Object.getOwnPropertyNames( binder );

                for ( var i in attributes ) {

                    var attribute = attributes[ i ];

                    // attach model listeners
                    binder[ attribute ].onSet.attach( function ( attribute ) {

                        return function ( sender, args ) {

                            this.bind( attribute, args );

                        }.bind( this );

                    }.call( this, attribute ) );

                }

                // attach change listener for two-way binding
                this._selector.addEventListener( 'change', function ( e ) {
                    this.onChanged.notify( e.target.value );
                }.bind( this ) );

            };

            T.TwoWayView.prototype = {

                bind( attribute, args ) {

                    this._selector[ attribute ] = args;

                },

                show() {

                    var attributes = Object.getOwnPropertyNames( this._binder );

                    for ( var i in attributes ) {

                        var attribute = attributes[ i ];

                        this._selector[ attribute ] = this._binder[ attribute ].get();

                    }

                }

            };

            return T;
        } )( T.MVC || {} ); // eslint-disable-line no-use-before-define, no-redeclare, no-shadow


        /**
         * Controller module
         * 
         * @param {window.T} T 
         */
        T.MVC = ( ( T ) => { // eslint-disable-line no-redeclare, no-shadow
            'use strict';

            T.Controller = function ( binder, view ) {

                this._binder = binder;
                this._view = view;

                var attributes = Object.getOwnPropertyNames( binder );

                for ( var i in attributes ) {

                    var attribute = attributes[ i ];

                    if ( this._view.hasOwnProperty( 'onChanged' ) ) {

                        this._view.onChanged.attach( function ( model ) {

                            return function ( sender, data ) {

                                this.update( model, data );

                            }.bind( this );

                        }.call( this, this._binder[ attribute ] ) );

                    }

                }

            };

            T.Controller.prototype = {

                update( model, data ) {

                    model.set( data );

                }

            };

            return T;
        } )( T.MVC || {} ); // eslint-disable-line no-use-before-define, no-redeclare, no-shadow

        T.MVC = ( ( MVC ) => {

            MVC.Block = function ( blockName, options ) {

                options = options || {};

                this.__rawBlockName = blockName;
                this.__blockName = blockName.split( '/' ).pop();
                this.__into = options.into || document.body;
                this.__template = options.template || {};
                this.__template.auto = this.__template.auto || options.templateAuto || options.lazyLoadTemplate || true;
                this.__template.extension = this.__template.extension || options.templateExtension || 'php';
                this.__template.mimeType = this.__template.mimeType || options.templateMimeType || 'text/html';
                this.__template.args = this.__template.args || options.templateArgs || {};
                this.__template.loaded = false;
                this.__listeners = {};

                var definedProperties = Object.getOwnPropertyNames( options );
                
                for ( var i in definedProperties ) {

                    var property = definedProperties[ i ];

                    if ( property.lastIndexOf( 'on', 0 ) === 0 ) {

                        var listener = property.slice( 2 );

                        this.on( listener, options[ 'on' + listener ] );

                    }

                }

                Object.defineProperty( this, 'cssPath', {

                    get() {
                        return this.getFileUrl( 'css' );
                    }

                } );

                Object.defineProperty( this, 'jsPath', {

                    get() {
                        return this.getFileUrl( 'js' );
                    }

                } );

                this.name = this.__blockName.replace( /^([A-Z])|[\s-_](\w)/g, function ( match, p1, p2 ) {

                    if ( p2 ) return p2.toUpperCase( );

                    return p1.toLowerCase();

                } );

                MVC.Block.config.exports[ this.name ] = this;

                this.__element = this.__into.querySelector( '#' + this.__blockName );

                if ( !( this.__element instanceof HTMLElement ) ) {
                    _init.call( this, options );
                }

            };

            MVC.Block.config = {

                exports : window,
                dir     : 'blocks'

            };

            MVC.Block.prototype = {

                getFileUrl( ext ) {
                
                    var dir = MVC.Block.config.dir;
                    if ( this.__rawBlockName.indexOf( '/' ) !== -1 ) {
                        var splittedPath = this.__rawBlockName.split( '/' );
                        splittedPath.pop();
                        dir += '/' + splittedPath;
                    }
                    return dir + '/' + this.__blockName + '/' + this.__blockName + '.' + ext;

                },

                hide() {

                    this.__element.hidden = true;

                },

                show() {

                    this.__element.hidden = false;

                },

                updateView( data ) {
                    this.__template.args = data || this.__template.args;
                    
                    _whenTemplateIsLoaded.call( this, function () {
                        _parseTemplate.call( this );
                        _insertTemplate.call( this );
                    }.bind( this ) );
                },

                on( eventType, listener, onElement ) {

                    onElement = ( onElement === true ? true : false ) || false;

                    if ( typeof listener !== 'function' ) {

                        throw new TypeError();

                    }

                    if ( onElement && !this.__element ) {

                        throw new Error();

                    }

                    if ( onElement ) {
                        
                        modifyListeners.call( this.__listeners, eventType, this.__element, 'remove' );
                        pushArray.call( this.__listeners, eventType, listener );
                        modifyListeners.call( this.__listeners, eventType, this.__element, 'add' );

                    } else {
                        
                        pushArray.call( this.__listeners, eventType, listener );
                        pushArray.call( this.__listeners, 'on' + eventType, listener );

                    }
                    
                    function pushArray( eventType, listener ) {
                        if ( !this[ eventType ] || this[ eventType ].constructor !== Array ) {
                            this[ eventType ] = [];
                        }
                        this[ eventType ].push( listener );
                    }
                    
                    function modifyListeners( eventType, el, operation ) {
                        if ( Array.constructor === this[ eventType ] && this[ eventType ].length > 0 ) {
                            for ( var i in this[ eventType ] ) {
                               el[ operation + 'EventListener' ]( eventType, this[ eventType ][ i ] );
                            }
                        }
                    }

                },

                hasListener( eventType ) {

                    var allEventKeys = Object.getOwnPropertyNames( this.__listeners );

                    return allEventKeys.indexOf( eventType ) !== -1;

                },

                findElement( id ) {

                    if ( !this.__element ) {

                        return null;

                    }

                    return this.__element.querySelector( '#' + this.__blockName + ' #' + this.__blockName + '__' + id );

                },

                dispatch( event, data ) {

                    var eventType;
                    var dispatch;

                    if ( typeof event === 'object' ) {

                        eventType = event.eventType;
                        dispatch = event.emitter;

                        if ( dispatch === 'el' ) {

                            dispatch = this.__element;

                        }

                    } else {

                        eventType = event;
                        dispatch = this;

                    }

                    if ( !dispatch ) {

                        dispatch = this;

                    }

                    if ( dispatch instanceof HTMLElement ) {

                        dispatch.dispatchEvent(
                                new CustomEvent( eventType, data || null )
                        );

                    } else if ( this.__listeners ) {

                        var call = this.__listeners[ 'on' + eventType ];
                        var result;
                        
                        for ( var i in call ) {
                            
                            var fn = call[ i ];
                            if ( typeof fn === 'function' ) {
                                var called = fn.call( this, data || null );
                                if ( called && !result ) {
                                    result = called;
                                }
                            }
                        
                        }
                        
                        return result;

                    }

                },

                destroy() {

                    if ( this.__element ) {

                        this.__element.removeFromPage();
                        this.__element.removeFromPage = null;
                        this.__element.addToPage = null;
                        this.__element = null;

                    }

                    if ( this.cssFile ) {

                        this.cssFile.parentNode.removeChild( this.cssFile );

                    }

                    if ( this.jsFile ) {

                        this.jsFile.parentNode.removeChild( this.jsFile );

                    }

                    this.cssFile = null;
                    this.jsFile  = null;

                    MVC.Block.config.exports[ this.name ] = null;

                    this.__blockName = null;
                    this.name        = null;

                    this.dispatch( {
                        eventType: 'destroy',
                        emitter: null
                    } );
                    
                    this.__listeners = null;

                }

            };
                
            function _loadTemplate() {

                var that = this;

                var config = {};

                config.ext = this.__template.extension;
                config.type = this.__template.mimeType;

                if ( this.hasListener( 'loadtemplate' ) ) {

                    config = this.dispatch( {
                      eventType: 'loadtemplate',
                      emitter: null
                    }, this.__template.args );

                }

                if ( config.ext ) {
                    config.url = this.getFileUrl( config.ext );
                }

                if ( config.ext === 'php' ) {

                    var args = [];

                    for ( var i in this.__template.args ) {
                        args.push( i + '=' + this.__template.args[ i ] );
                    }

                    if ( args.length > 0 ) {
                        config.url += '?' + args.join( '&' );
                    }

                    T.load( 'utils', ( ) => {

                        T.Utils.ajax( {
                            url: config.url,
                            response: config.type || 'document',
                            onloadend: ( e ) => {
                                var onload = that.dispatch( 'load', e );
                                if ( typeof onload === 'undefined' || onload ) {
                                    var response = e.target.response;
                                    that.__template._ = response && response.body ? response.body.firstChild : ( response ? response : document.createElement( 'div' ) );
                                    _notifyTemplateIsLoaded.call( that );
                                }

                            }
                        } );

                    } );
                    
                } else {
                    T.load( config.url, _notifyTemplateIsLoaded.bind( that ) );
                }
            }
            
            function _notifyTemplateIsLoaded() {
                this.__template.loaded = true;
                this.dispatch( 'templateloaded' );
            }
            
            function _parseTemplate() {
                if ( this.hasListener( 'parsetemplate' ) ) {

                    this.__template._ = this.dispatch( {
                        eventType: 'parsetemplate',
                        emitter: null
                    }, this.__template._ );

                }
            }

            function _insertTemplate() {
                if ( !this.__template._ ) return;
                
                if ( typeof this.__template._ === 'string' ) {
                    var shadow = document.createElement( 'div' );
                    shadow.innerHTML = this.__template._;
                    this.__template._ = shadow.firstChild;
                }

                this.__element = this.__template._;
                this.__element.addToPage = _addToPage.bind( this );
                this.__element.removeFromPage = _removeFromPage.bind( this );

                this.__element.addToPage();
                    
                if ( this.__template._ ) {
                    this.dispatch( 'templateready' );
                }
            }

            function _init( options ) {

                var that = this;
                var load = [];

                if ( !options.hasOwnProperty( 'hasCss' ) || options.hasCss === true ) {
                    load.push( this.cssPath );
                }

                if ( !options.hasOwnProperty( 'hasJs' )  || options.hasJs === true ) {
                    load.push( this.jsPath );
                }

                _loadTemplate.call( that );

                loadjs( load, {

                    before( path, script ) {

                        if ( script instanceof HTMLLinkElement ) {
                            that.cssFile = script;
                        }

                        if ( script instanceof HTMLScriptElement ) {
                            that.jsFile = script;
                        }

                        return true;

                    },

                    success() {

                        var blockInstanceClass = MVC.Block.config.exports[ that.name.charAt( 0 ).toUpperCase() + that.name.slice( 1 ) ];
                        
                        _constructor();
                        
                        if ( that.__template.auto === true ) {
                            _whenTemplateIsLoaded.call( that, that.updateView );
                        }
                        _whenTemplateIsReady.call( that, __init );
                        
                        function _constructor() {
                            if ( that.jsFile ) {

                                var inject = [ null, that ];

                                if ( options.inject && Array === options.inject.constructor ) {
                                    inject = inject.concat( options.inject );
                                }

                                that.instance = new ( blockInstanceClass.bind.apply( blockInstanceClass, inject ) );
                                that.on( 'initialize', function () {
                                    if ( that.instance.init ) {
                                        that.instance.init();
                                    }
                                } );

                            }
                        }
                        
                        function __init() {
                            that.dispatch( {
                                eventType: 'initialize',
                                emitter: null
                            } );
                        }

                    }

                } );

                return false;

            };
                        
            function _whenTemplateIsLoaded( _do ) {
                if ( this.__template.loaded === true ) {
                    _do.call( this );
                } else {
                    this.on( 'templateloaded', _do );
                }
            }
                        
            function _whenTemplateIsReady( _do ) {
                if ( this.__template._ ) {
                    _do.call( this );
                } else {
                    this.on( 'templateready', _do );
                }
            }

            function _addToPage() {
                this.__into.appendChild( this.__element );
            }

            function _removeFromPage() {
                this.__into.removeChild( this.__element );
            }

            return MVC;

        } )( T.MVC || {} );

        return T;

    } )( _ || {} );

    return _;
    
} )();

window.T = ( ( T ) => {
    
    T.PushSubscribe = {
        applicationServerKey: null,
        config( options ) {
            this.applicationServerKey = options.applicationServerKey || null;
        },
        request() {
            if ( !( 'serviceWorker' in navigator ) || 
                 !( 'PushManager'   in window    ) ) {
                if ( typeof this.onNotAvailable === 'function' )
                    this.onNotAvailable();
                return;
            }

            return new Promise( function ( resolve, reject ) {
                const permissionResult = Notification.requestPermission( (result) => {
                    // Handling deprecated version with callback.
                    resolve( result );

                    if ( permissionResult ) {
                        permissionResult.then( resolve, reject );
                    }
                } )
                .then( ( permissionResult ) => {
                    if ( permissionResult !== 'granted' ) {
                        throw new Error( 'Permission not granted' );
                    }
                    subscribeUserToPush();
                } );
            } );

            function subscribeUserToPush() {
                navigator.serviceWorker.ready
                    .then( ( registration ) => {
                        return registration.pushManager.subscribe( {
                            userVisibleOnly: true,
                            applicationServerKey: urlBase64ToUint8Array( this.applicationServerKey )
                        } );
                    } )
                    .then( ( pushSubscription ) => {
                        if ( typeof this.onSubscribe === 'function' )
                            return this.onSubscribe( pushSubscription );
                        return null;
                    } );
            }

            function urlBase64ToUint8Array( base64String ) {
                base64String = base64String || null;
                if ( !base64String ) return;
                
                const padding = '='.repeat( ( 4 - base64String.length % 4 ) % 4 );
                const base64  = ( base64String + padding )
                                    .replace( /\-/g, '+' )
                                    .replace( /_/g, '/' );

                const rawData     = window.atob( base64 );
                const outputArray = new Uint8Array( rawData.length );

                for ( let i = 0; i < rawData.length; ++i ) {
                    outputArray[ i ] = rawData.charCodeAt( i );
                }
                return outputArray;
            }
        }
    };

    return T;
  
} )( window.T || {} );

T.isLoaded = true;
    
window.dispatchEvent( new CustomEvent( 'T.Event.loaded' ) );
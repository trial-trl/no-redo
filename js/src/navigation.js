/* 
 * (c) 2017 TRIAL.
 * Created on 16/06/2017, 22:56:53.
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
    
    // module Router created at E3L on 06/03/2019
    // added here on 23/03/2019
    // based on article: http://krasimirtsonev.com/blog/article/A-modern-JavaScript-router-in-100-lines-history-api-pushState-hash-url
    T.Router = {
        routes: [],
        interceptors: [],
        handlerInterceptors: [],
        root: '/',
        config( options ) {
            if ( options ) {
                this.root = options.root ? '/' + this.clearSlashes( options.root ) : '/';
                
                if ( options.filterRouter && 'function' === typeof options.filterRouter ) {
                    this.__filterRouter = options.filterRouter;
                }
            }
            return this;
        },
        getFragment( removeQueryString ) {
            var fragment = '';
            fragment = this.clearSlashes( decodeURI( location.pathname + location.search + location.hash ) );
            if ( typeof removeQueryString === 'undefined' || removeQueryString === true ) {
                fragment = fragment.replace( /\?(.*)$/, '' ).replace( /\#(.*)$/, '' );
            }
            fragment = this.root !== '/' ? fragment.replace( this.clearSlashes( this.root ), '' ) : fragment;
            fragment = this.clearSlashes( fragment );
            return '/' + fragment;
        },
        clearSlashes( path ) {
            return path.toString().replace( /\/$/, '' ).replace( /^\//, '' );
        },
        add( regex, handler ) {
            if ( typeof regex === 'function' ) {
                handler = regex;
                regex = '';
            }
            this.routes.push( {
                regex: regex,
                handler: handler
            } );
            return this;
        },
        remove( param ) {
            for ( var i = 0, r; i < this.routes.length, r = this.routes[ i ]; i++ ) {
                if ( r.handler === param || r.regex.toString() === param.toString() ) {
                    this.routes.splice( i, 1 );
                    return this;
                }
            }
            return this;
        },
        flush() {
            this.routes = [];
            this.interceptors = [];
            this.root = '/';
            return this;
        },
        addInterceptor: function ( fc ) {
            if ( typeof fc !== 'function' ) {
                throw new TypeError();
            }
            
            this.interceptors.push( fc );
            return this;
        },
        removeInterceptor: function ( fc ) {
            if ( typeof fc !== 'function' ) {
                throw new TypeError();
            }
            
            var i = this.interceptors.indexOf( fc );
            if ( i !== -1 ) {
                this.interceptors.splice( i, 1 );
            }
            return this;
        },
        addHandlerInterceptor: function ( fc ) {
            if ( typeof fc !== 'function' ) {
                throw new TypeError();
            }
            
            this.handlerInterceptors.push( fc );
            return this;
        },
        removeHandlerInterceptor: function ( fc ) {
            if ( typeof fc !== 'function' ) {
                throw new TypeError();
            }
            
            var i = this.handlerInterceptors.indexOf( fc );
            if ( i !== -1 ) {
                this.handlerInterceptors.splice( i, 1 );
            }
            return this;
        },
        setData( data, persist ) {
            persist = typeof persist === 'boolean' ? persist : false;
            this.__data = data || null;
            
            if ( persist === true ) {
                history.replaceState( this.__data, null, this.root + this.clearSlashes( this.getFragment( false ) ) );
            }
        },
        check( f ) {
            var fragment = f || this.getFragment( false );
            
            for ( var i in this.interceptors ) {
                var returned = this.interceptors[ i ]( fragment, this.__data );
                if ( typeof returned === 'boolean' && returned === false ) {
                    return this;
                }
                if ( typeof returned === 'string' && returned !== fragment ) {
                    T.Router.navigate( returned, this.__data );
                    return this;
                }
            }
            
            fragment = this.getFragment();
            
            for ( var i = 0, r; i < this.routes.length, r = this.routes[ i ]; i++ ) {
                var match = fragment.match( '^' + r.regex + '$' );
                if ( match ) {
                    match.shift();
                    var groups = match.groups;
                    var q = this.getFragment( false );
                    var qs = {};
                    
                    if ( q.indexOf( '?' ) !== -1 ) {
                        var search = q.split( '?' )[ 1 ];
                        search = search.split( '&' );
                        for ( var i in search ) {
                            var pair = search[ i ].split( '=' );
                            qs[ decodeURIComponent( pair[ 0 ] ) ] = decodeURIComponent( pair[ 1 ] || '' );
                        }
                    }
                    
                    if ( q.indexOf( '#' ) !== -1 ) {
                        qs.hash = q.split( '#' )[ 1 ];
                    }
                    
                    if ( groups ) {
                        Object.assign( groups, qs );
                    } else {
                        groups = qs;
                    }
                    
                    var old = this.lastGroup;
                    this.lastGroup = groups;
            
                    for ( var i in this.handlerInterceptors ) {
                        var returned = this.handlerInterceptors[ i ]( groups, this.__data, old );
                        if ( typeof returned === 'boolean' && returned === false ) {
                            delete this.__data;
                            return this;
                        }
                    }
            
                    r.handler.call( null, groups, this.__data );
                    delete this.__data;
                    return this;
                }
            }
            return this;
        },
        listen() {
            var self = this;
            var current = this.getFragment( false );
            var fn = () => {
                if ( current !== self.getFragment( false ) ) {
                    current = self.getFragment( false );
                    self.check( current );
                }
            };
            clearInterval( this.interval );
            this.interval = setInterval( fn, 50 );
            return this;
        },
        refresh() {
            this.check( this.getFragment() );
            return this;
        },
        navigate( path, data ) {
            path = path || '';
            this.setData( data );
            
            for ( var i in this.interceptors ) {
                var returned = this.interceptors[ i ]( path, this.__data );
                if ( typeof returned === 'boolean' && returned === false ) {
                    return this;
                }
                if ( typeof returned === 'string' && returned !== path ) {
                    T.Router.navigate( returned, this.__data );
                    return this;
                }
            }
            
            history.pushState( this.__data, null, this.root + '/' + this.clearSlashes( path ) );
            return this;
        },
        applyLinkRouting( into ) {
            into = into || document.body;
            
            var links = into.querySelectorAll( 'a[href]' );
            if ( links.length > 0 ) {
                for ( var i = 0, t = links.length, link; i < t, link = links[ i ]; i++ ) {
                    if ( link && !link.target ) {
                        link.removeEventListener( 'click', check.bind( this ) );
                        link.addEventListener( 'click', check.bind( this ) );
                    }
                }
            }

            function check( e ) {
                e.preventDefault();
                T.Router.navigate( 
                        e.target.getAttribute( 'href' ), 
                        this.filterRouterData( 
                                e.target.dataset.router || null
                        )
                );
            }
        },
        filterRouterData( data ) {
            if ( !data ) {
                return undefined;
            }
            return   this.__filterRouter 
                   ? this.__filterRouter( data ) 
                   : JSON.parse( decodeURIComponent( atob( data ) ) );
        }
    };
  
    T.Navigation = {};

    T.Navigation.scroll = ( options ) => {
        var y = { initial: window.pageYOffset, distance: 0 },
            x = { initial: window.pageXOffset, distance: 0 };
        if ( !options.hasOwnProperty( 'x' ) ) {
            options.x = x.initial;
        }
        if ( options.x !== x.initial ) {
            if ( options.x < x.initial ) {
                x.distance = -( x.initial - options.x );
            } else {
                x.distance = options.x - x.initial;
            }
        }
        if ( !options.hasOwnProperty( 'y' ) ) {
            options.y = y.initial;
        }
        if ( options.y !== y.initial ) {
            if ( options.y < y.initial ) {
                y.distance = -( y.initial - options.y );
            } else {
                y.distance = options.y - y.initial;
            }
        }
        if ( y.distance !== 0 || x.distance !== 0 ) {
            T.load( 'design', () => {
                T.Animation.start( {
                    duration: options.duration || 300,
                    delta: ( p ) => {
                        return T.Animation.Easing.easeInOutQuart( p );
                    },
                    step: ( delta ) => {
                        window.scrollTo( x.initial + ( x.distance * delta ), y.initial + ( y.distance * delta ) );
                    }
                } );
            } );
        }
    };

    T.Navigation.next = ( options ) => {
        try {
            options.next.style.position = 'absolute';
            options.next.style.display = '';
            options.next.style.top = options.top + 'px';
            options.next.style.left = '100%';
            
            if ( options.onstart ) options.onstart( options );
            
            T.load( 'design', () => {
                T.Animation.start( {
                    duration: 200,
                    delta: ( p ) => {
                        return T.Animation.Easing.easeInOutQuart( p );
                    },
                    step: (delta) => {
                        try {
                            var perc = 100 * delta;
                            options.current.style.left = '-' + perc + '%';
                            options.next.style.left = ( 100 - perc ) + '%';
                        } catch ( e ) {
                            console.error( e );
                        }
                    }
                } );
                setTimeout( () => {
                    options.next.style.position = '';
                    options.next.style.top = 0;
                    if ( options.onfinish ) {
                        options.onfinish( options );
                    }
                }, 201 );
            } );
        } catch (e) {
            console.error(e);
        }
    };

    T.Navigation.previous = ( options ) => {
        try {
            options.previous.style.position = 'absolute';
            options.previous.style.display = '';
            options.previous.style.top = options.top + 'px';
            options.previous.style.left = '-100%';
            if ( options.onstart ) options.onstart( options );
            T.load('design', () => {
                T.Animation.start( {
                    duration: 200,
                    delta: ( p ) => {
                        return T.Animation.Easing.easeInOutQuart( p );
                    },
                    step: ( delta ) => {
                        try {
                            var perc = 100 * delta;
                            options.previous.style.left = '-' + ( 100 - perc ) + '%';
                            options.current.style.left = perc + '%';
                        } catch ( e ) {
                            console.error( e );
                        }
                    }
                } );
                setTimeout( () => {
                    options.previous.style.position = '';
                    options.previous.style.top = 0;
                    if ( options.onfinish ) {
                        options.onfinish( options );
                    }
                }, 201 );
            });
        } catch ( e ) {
            console.error( e );
        }
    };

    T.Navigation.noRefresh = ( options ) => {

        var api = {};

        options.into      = options.into     || 'ajax-content';
        options.jsClass   = options.jsClass  || 'ajax-script';
        options.cssClass  = options.cssClass || 'ajax-style';

        api.load = ( ( api ) => {

            function loadStyles( html ) {
                var styles = {
                    current : [],
                    loaded  : html.getElementsByClassName(options.cssClass)
                };
                
                for ( var i = 0, t = document.styleSheets.length; i < t; i++ ) {
                    styles.current.push( document.styleSheets[ i ].href );
                }
                
                var d = document.getElementsByClassName( options.cssClass );
                for ( i = 0, t = d.length; i < t; i++ ) {
                    try { document.head.removeChild(d[i]); } 
                    catch (e) {}
                }
                
                for ( i = 0, t = styles.loaded.length, x; i < t, x = styles.loaded[ i ]; i++ ) {
                    if ( styles.current.indexOf( x.href ) === -1 ) {
                        if ( x instanceof HTMLStyleElement ) {
                            var style = document.createElement( 'style' );
                            style.innerHTML = x.innerHTML;
                        } else {
                            var style = document.createElement( 'link' );
                            style.rel = 'stylesheet';
                            style.type = 'text/css';
                            style.href = x.href;
                        }
                        style.classList.add( options.cssClass );
                        document.head.appendChild( style );
                    }
                }
            }

            function loadScripts( html ) {
                var scripts = {
                    current : [],
                    loaded  : html.getElementsByClassName(options.jsClass)
                };
                for ( let i = 0, t = document.scripts.length, d; i < t, d = document.scripts[ i ]; i++ ) {
                    if ( !d ) continue;
                    
                    if ( d.classList.contains( options.jsClass ) ) {
                        try { 
                            document.body.removeChild( d );
                        } catch ( e ) {}
                        continue;
                    }
                    
                    if ( d.src !== undefined &&
                         d.src !== null && 
                         d.src !== '' )
                        scripts.current.push( d.src );
                }
                for ( let i = 0, t = scripts.loaded.length, x; i < t, x = scripts.loaded[ i ]; i++ ) {
                    if ( -1 === scripts.current.indexOf( x.src ) ) {
                        var s = document.createElement( 'script' );
                        if ( x.src ) s.src = x.src;
                        s.innerHTML = x.innerHTML;
                        s.classList.add( options.jsClass );
                        document.body.appendChild( s );
                    }
                }
            }

            function loadPage( html, update, url ) {
                try {
                    var str_html =   html instanceof HTMLDocument 
                                   ? html.documentElement.outerHTML 
                                   : html;
                    var content = {
                            current : document.getElementById( options.into ),
                            loaded  : html.getElementById( options.into )
                        };
                        
                    content.current.className = content.loaded.className;
                    document.documentElement.className = html.documentElement.className;
                    
                    loadStyles( html );
                    
                    content.current.innerHTML = content.loaded.innerHTML;
                    document.title = html.title;
                    
                    loadScripts( html );
                    
                    if ( update ) {
                        var data = {
                            title : document.title,
                            url   : location.href,
                            page  : str_html
                        };
                        if ( options.onsave ) options.onsave( data );
                        T.History.add( data, html.title, url );
                        api.currentState = data;
                    }
                    if ( options.onloaded ) {
                        var custom = options;
                        custom.page = html;
                        options.onloaded( custom );
                    }
                } catch ( e ) {
                    console.error( e );
                    if ( options.onerror ) options.onerror( e );
                    if ( options.onloaded ) {
                        var custom = options;
                        custom.error = e;
                        options.onloaded( custom );
                    }
                }
            }

            return ( q ) => {
                if ( typeof q.url === 'undefined' ) {
                    if ( q.e.state && q.e.state.page ) {
                        loadPage( new DOMParser().parseFromString( q.e.state.page, 'text/html' ) );
                    }
                } else {
                    T.load( 'utils', () => {
                        T.Utils.ajax( {
                            event: q.e,
                            url: q.url,
                            method: 'GET',
                            response: 'document',
                            onloadend: ( e ) => {
                                loadPage( e.target.response, true, q.url );
                            }
                        } );
                    } );
                }
            };

        } )( api );

        var prestate = {
            title : document.title,
            url   : location.href,
            page  : document.documentElement.outerHTML
        };

        if ( options.onsave ) {

            options.onsave( prestate, true );

        }

        T.load( 'history', () => {

            T.History.replace( prestate );

            window.addEventListener( 'popstate', ( e ) => {

                if ( typeof options.onnavigate === 'undefined' ||
                   ( typeof options.onnavigate === 'function'  && 
                     options.onnavigate.call( api, e )            ) ) {

                    api.load( { e: e } );

                }

                api.currentState = e.state;

            } );

        } );

        return api;

    };

    return T;
  
} )( window.T || {} );
    
window.dispatchEvent( new CustomEvent( 'T.Navigation.loaded' ) );
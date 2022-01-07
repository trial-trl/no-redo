/* 
 * ( c ) 2017 TRIAL.
 * Created on 16/06/2017, 23:01:24.
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

window.T = ( ( T ) => {
  
    var _auth;
    var _raw_defs;
    var _defs;
    var _window_cardSelector;

    function _( c, cookies ) {
        var m = [];
        if ( !T.Utils )        m.push( 'utils' );
        if ( m.length > 0 )    T.load( m, c );
        else                   c();
    }
    
    function _checkAuthorization() {
        if ( !_auth ) {
            throw new SyntaxError( 'Must set authorization before. Call T.Account.setAuthorization( accountId )' );
        }
    }
    
    function _customize( defs ) {
        _raw_defs = defs;
        _defs = btoa( JSON.stringify( defs ) );
    }
    
    function _request( url, onsuccess ) {
        _checkAuthorization();
        _( () => {
            T.Utils.ajax( {
                url: url,
                headers: [
                    'Authorization: Client-ID ' + _auth
                ],
                response: T.Constants.Response.JSON,
                onloadend: ( e ) => {
                    onsuccess( e.target.response );
                }
            } );
        } );
    }

    T.Account = {
        
        URL: 'https://conta.trialent.com',
        
        LOGIN: this.URL + '/api/login',
        
        LOGOUT: this.URL + '/api/logout',
        
        open() {
            var url = this.URL + '/entrar/' + _defs;
            return window.open( url + ( _raw_defs.hasOwnProperty( 'keep' ) && _raw_defs.keep ? '#' + _raw_defs.keep : '' ), 'trl-account-page', 'resizable,scrollbars,width=413,height=554' );
        },
        
        openCardSelector: function ( args ) {
            args = args || {};
            var q = [];
            if ( _defs ) {
                q.push( 'defs=' + _defs );
            }
            if ( args.newCard ) {
                q.push( 'new_card=' + btoa( JSON.stringify( args.newCard ) ) );
            }
            if ( args.requireConfirmation === true ) {
                q.push( 'require_confirmation=true' );
            }
            var url = this.URL + '/escolher-cartao?' + q.join( '&' );
            return _window_cardSelector = window.open( url, 'trl-account__card-selector', 'resizable,scrollbars,width=413,height=554' );
        },
        
        getCardByLastDigits: function ( lastDigits, onsuccess ) {
            _request( this.URL + '/api/cards/by-digits/' + lastDigits, onsuccess );
        },
        
        isCardInAccount: function ( lastDigits, onsuccess ) {
            _request( this.URL + '/api/cards/by-digits/' + lastDigits + '?just_confirm=true', onsuccess );
        },
        
        closeCardSelector: function () {
            _window_cardSelector.close();
        },
        
        getIconUrlForCardBrand: function ( brand ) {
            return this.URL + '/images/card/' + ( brand || 'card' ) + '.svg';
        },
        
        setAuthorization( accountId ) {
            if ( typeof accountId !== 'number' ) {
                throw new TypeError( 'accountId must be a number' );
            }
            _auth = accountId;
        },
        
        removeAuthorization() {
            _auth = undefined;
        },
        
        customWindow() {
            var attrs = {};
            
            return {
                
                setMode: function ( mode ) {
                    attrs.popup = mode;
                    return this;
                },
                
                setDataToPersist: function ( keep ) {
                    attrs.keep = keep;
                    return this;
                },
                
                setApplication: function ( name, icon ) {
                    attrs.appName = name;
                    attrs.appIcon = icon;
                    return this;
                },
                
                setAppearance: function ( login, signup ) {
                    attrs.appearance = {
                        colorPrimary: login,
                        colorSecondary: signup
                    };
                    return this;
                },
                
                allowProfiles: function ( profilesToAllow ) {
                    attrs.allowProfile = profilesToAllow;
                    return this;
                },
                
                setReturnUrl: function ( url ) {
                    attrs.returnUrl = url;
                    return this;
                },
                
                setSubAccountLoginUrl: function ( url ) {
                    attrs.subAccountLoginUrl = url;
                    return this;
                },
                
                construct: function () {
                    if ( _auth ) {
                        attrs.authToken = _auth;
                    }
                    _customize( attrs );
                    return T.Account;
                }
                
            };
        },
        
        getProfile( fields, onsuccess ) {
            _request( this.URL + '/api/profile?fields=' + fields.join( ',' ), onsuccess );
        },
        
        inviteUser( email, onsuccess ) {
            _checkAuthorization();
            _( () => {
                T.Utils.ajax( {
                    url: this.URL + '/api/invite',
                    headers: [
                        'Authorization: Client-ID ' + _auth
                    ],
                    data: {
                        email: email
                    },
                    response: 'json',
                    onloadend: ( e ) => {
                        onsuccess( e.target.response, e.target.status );
                    }
                } );
            } );
        },
        
        editProfile( field, newValue, onsuccess ) {
            _checkAuthorization();
            _( () => {
                T.Utils.ajax( {
                    url: this.URL + '/api/update-account',
                    headers: [
                        'Authorization: Client-ID ' + _auth
                    ],
                    data: {
                        field: field,
                        value: newValue
                    },
                    response: 'json',
                    onloadend: ( e ) => {
                        onsuccess( e.target.response, e.target.status );
                    }
                } );
            } );
        },
        
        login( options ) {
            this.open( options );
        },
        
        logout( after ) {
            _( () => {
                T.Utils.ajax( {
                    method: 'POST',
                    url: T.Account.LOGOUT,
                    onloadend: ( e ) => {
                        var refresh = e.target.response;
                        
                        if ( after )            refresh = after( refresh );
                        if ( refresh === true ) location.reload();
                    }
                } );
            }, true );
        },
        
        edit( onfinishedition ) {
            var q = [];
            if ( _auth ) {
                q.push( 'defs=' + btoa( JSON.stringify( { authToken: _auth } ) ) );
            }
            var qsa = q.length > 0 ? '&' + q.join( '&' ) : '';
            var url = this.URL + '/perfil/?mode=edition' + qsa;
            var popup = window.open( url, 'trl-account-profile-page', 'resizable,scrollbars,width=413,height=554' );
            if ( onfinishedition ) {
                window.addEventListener( 'message', onprofileupdated );

                function onprofileupdated( e ) {
                    if ( e.data.eventType === 'T.Account.Event.profileUpdated' ) {
                        onfinishedition( true );
                        window.removeEventListener( 'message', onprofileupdated );
                    }
                }
            }
            return popup;
        }
        
    };

    return T;
    
} )( window.T || {} );
    
window.dispatchEvent( new CustomEvent( 'T.Account.loaded' ) );
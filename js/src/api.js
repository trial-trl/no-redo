/*
 * T.js Library₢
 * Version: 1.3
 * 
 * 2016 ₢ TRIAL. All rights reserved.
 * 
 * Created on   : 10/05/2016, 18:59:09
 * Author       : Matheus Leonardo dos Santos Martins
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

window.T = (function () {
  
  var HOST    = location.protocol + '//' + location.hostname + 
                ( location.port ? ':' + location.port : '' );
  var BOWER   = HOST + '/bower_components';
  var LIBRARY = BOWER + '/trl-no-redo';
  var CSS     = LIBRARY + '/css';
  var API     = LIBRARY + '/js/src';
  var LOADJS  = BOWER + '/loadjs/dist/loadjs.min.js';

  var _ = (function () {
    
    var on;

    var T = function (element) {
      
      if (!(this instanceof T))
        return new T(element);
      
      if (element !== undefined)
        on = typeof element !== 'string' ? element : document.querySelector(element);
      
    };

    T.fn = T.prototype = {
      getElement: function () {
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
        controlVisibility: function (options) {
          options.when_hide = options.when_hide || 200;
          options.interval = options.interval || 100;
          var exhibiting, scrollY = window.pageYOffset;
          window.onscroll = function (e) {
            if ((exhibiting && window.pageYOffset >= scrollY) || (!exhibiting && window.pageYOffset <= scrollY)) {
              options.current += options.interval;
            }

            scrollY = window.pageYOffset;

            if (options.current >= options.when_hide) {
              showHideElement(exhibiting);
              options.current = 0;
              scrollY = window.pageYOffset;
            }
            if (window.pageYOffset === 0) {
              showHideElement(false);
              options.current = 0;
              scrollY = window.pageYOffset;
            }
          };
        },
        triggerOffset: function (options) {
          options.element = T.prototype.getElement();
          var offset_toolbar = options.trigger_offset !== null ? options.trigger_offset : options.element.offsetTop;
          window.addEventListener('scroll', function (e) {
            if (window.pageYOffset > offset_toolbar)  {
              if (options.class_name.constructor === Array) {
                for (var i = 0, total = options.class_name.length; i < total; i++) {
                  options.element.classList.add(options.class_name[i]);
                }
              } else {
                options.element.classList.add(options.class_name);
              }
              if (options.hasOwnProperty('reset_top') && options.reset_top) {
                options.element.style.top = 0;
              }
            } else {
              if (options.class_name.constructor === Array) {
                for (var i = 0, total = options.class_name.length; i < total; i++) {
                  options.element.classList.remove(options.class_name[i]);
                }
              } else {
                options.element.classList.remove(options.class_name);
              }
              if (options.element.style.position === 'absolute') {
                options.element.style.top = offset_toolbar + 'px';
              }
            }
          }, false);
        },
        endlessScroll: function (options) {
          var element = T.prototype.getElement(),
              is_window = element === window,
              attach = is_window ? window : element;
          attach.addEventListener('scroll', function() {
            var contentHeight, yOffset, y;
            if (is_window) {
              contentHeight = options.target.offsetHeight;
              yOffset = window.pageYOffset;
              y = yOffset + window.innerHeight;
            } else {
              contentHeight = element.scrollHeight || element.offsetHeight || element.clientHeight;
              yOffset = element.scrollTop;
              y = yOffset + element.offsetHeight;
            }
            if (y >= contentHeight + (-options.offset || 0)) {
              options.onend(options);
            }
          });
        },
        toPercent: function (px, base) {
          return (px * 100) / base;
        },
        visibilityOnScroll: function(options) {
          var POSITIVE = 'positive',
              NEGATIVE = 'negative',
              that = this,
              element = T.prototype.getElement(),
              invisible = false,
              scroll = {
                  direction: POSITIVE,
                  y: 0
              },
              ontrigger = options.ontrigger || null;

          window.addEventListener('scroll', listener);

          function calculateScroll() {
            var direction = window.scrollY > that.y ? POSITIVE : NEGATIVE;
            if (scroll.direction === direction) {
              if ((scroll.direction === POSITIVE && !invisible) || (scroll.direction === NEGATIVE && invisible)) {
                scroll.y += Math.abs(window.scrollY - that.y);
              }
            } else {
              scroll.y = 0;
            }
            scroll.direction = direction;
          }

          function listener(e) {
            calculateScroll();

            if (scroll.y >= options.trigger) {
              if (invisible && scroll.direction === NEGATIVE) {
                show();
              } else if (!invisible && scroll.direction === POSITIVE) {
                hide();
              }
            }

            if (window.pageYOffset === 0) {
              show();
            }

            assignCurrentY();
          }

          function show() {
            scroll.y = 0;
            invisible = false;
            if (ontrigger) {
              ontrigger(options, 'down');
            } else {
              if (options.add_class.constructor === Array) {
                for (var i = 0, total = options.add_class.length; i < total; i++) {
                  element.classList.add(options.add_class[i]);
                }
              } else {
                element.classList.add(options.add_class);
              }
            }
          }

          function hide() {
            scroll.y = 0;
            invisible = true;
            if (ontrigger) {
              ontrigger(options, 'up');
            } else {
              if (options.add_class.constructor === Array) {
                for (var i = 0, total = options.add_class.length; i < total; i++) {
                  element.classList.remove(options.add_class[i]);
                }
              } else {
                element.classList.remove(options.add_class);
              }
            }
          }

          function assignCurrentY() {
            that.y = window.scrollY;
          }

          return {
            removeListener: function () {
              window.removeEventListener('scroll', listener);
            }
          };
        },
        closest: function (selector) {
          var matchesFn;
          ['matches', 'webkitMatchesSelector', 'mozMatchesSelector', 'msMatchesSelector', 'oMatchesSelector'].some(function (fn) {
            if (typeof document.body[fn] === 'function') {
              matchesFn = fn;
              return true;
            }
            return false;
          });
          try {
            var parent;
            while (on) {
              parent = on.parentElement;
              if (parent && parent[matchesFn](selector))
                  return parent;
              on = parent;
            }
          } catch (e) {
            console.error(e);
          }
          return null;
        }
      },
      UI: {
        Copyright: {
          OWN: 'own',
          DEVELOPED: 'developed'
        },
        addFooter: function (options) {
          var copyright = document.createElement('span'),
              period = '',
              footer = document.createElement('footer'),
              into = T.prototype.getElement();

          if (!options.hasOwnProperty('type')) {
            options.type = T.prototype.UI.Copyright.OWN;
          }

          var a = document.createElement('a'),
              logo = document.createElement('trl-logo');
          logo.compact = true;
          logo.color = options.color ? options.color : '#fff';
          logo.scale = options.scale ? options.scale : .5;
          if (options.width || options.height) {
            logo.width = options.width;
            logo.height = options.height;
          }
          a.appendChild(logo);
          a.href = 'https://trialent.com';
          footer.appendChild(a);

          if (options.hasOwnProperty('period')) {
            if (options.period.hasOwnProperty('since')) {
              period += options.period.since;
            }
            if (options.period.hasOwnProperty('to')) {
              if (typeof options.period.to === 'string') {
                var current_date = new Date();
                if (options.period.to === 'Y') {
                  options.period.to = current_date.getFullYear();
                } else if (options.period.to === 'y') {
                  options.period.to = current_date.getYear();
                }
                if (options.period.to.toString() === options.period.since.toString()) {
                  delete options.period.to;
                }
              }
            }
            if (options.period.hasOwnProperty('to')) {
              if (options.period.hasOwnProperty('separator')) {
                period += ' ' + options.period.separator + ' ' + options.period.to;
              } else {
                period += ' - ' + options.period.to;
              }
            }
          }

          if (options.type === T.prototype.UI.Copyright.OWN) {
            if (options.hasOwnProperty('phrase')) {
              copyright.innerHTML = period + ' ₢ ' + options.copyright + '. ' + options.phrase;
            } else {
              copyright.innerHTML = period + ' ₢ ' + options.copyright + '. Todos os direitos reservados';
            }
          } else {
            if (options.hasOwnProperty('phrase')) {
              copyright.innerHTML = period + '. ' + options.phrase + ' <a href=\'https://trialent.com\'>' + options.copyright + '</a>';
            } else {
              copyright.innerHTML = period + '. Desenvolvido por <a href=\'https://trialent.com\'>' + options.copyright + '</a>';
            }
          }

          copyright.setAttribute('copyright', '');
          footer.appendChild(copyright);

          footer.setAttribute('owner', '');
          footer.classList.add('center-horizontal');
          into.appendChild(footer);
        }
      }
    };
    
    Object.defineProperty(T, 'HOST', {value: HOST});
    Object.defineProperty(T, 'BOWER', {value: BOWER});
    Object.defineProperty(T, 'LIBRARY', {value: LIBRARY});
    Object.defineProperty(T, 'CSS', {value: CSS});
    Object.defineProperty(T, 'API', {value: API});
    Object.defineProperty(T, 'LOADJS', {value: LOADJS});
    
    return T;
    
  })();

  _ = (function (T) {

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
          IMAGE_PROFILE: '/no-redo/img/TRIAL/logo/icon/social/min/T_icon_social_invert.png'
        }
      };

      T.URL = {
        exists: function (url, callback) {
            T.Utils.ajax({
              url: url,
              onerror: function (e) {
                callback(false, e);
              },
              onloadend: function (e) {
                callback(true, e);
              }
            });
        },
        Parameter: {
          add: function (key, value) {
            var href = location.href;
            var url = href.split('?');
            var pairs = href.indexOf('&') !== -1 ? url[1].split('&') : ''; 
            var key_arr = [], parameters;
            if (url.length >= 2) {
              if (pairs !== '') {
                for (var i = 0, total = pairs.length; i < total; i++) {
                  var arr = pairs[i].split('=');
                  key_arr[i] = arr[0];
                  if (arr[0] === key) {
                    pairs[i] = arr[0] + '=' + value;
                  }
                }
                if (key_arr.indexOf(key) === -1) {
                  pairs.push(key + '=' + value);
                }
              } else {
                var arr = url[1].split('=');
                pairs = [];
                pairs[0] = url[1];
                if (arr[0] !== key) {
                  pairs.push(key + '=' + value);
                }
              }
            } else {
              pairs = [];
              pairs.push(key + '=' + value);
            }
            parameters = pairs.join('&');
          },
          get: function (key) {
            var href = location.href;
            var url = href.split('?');
            var pairs = href.indexOf('&') !== -1 ? url[1].split('&') : null;
            if (pairs !== null) {
              var key = [], parameters;
              if (url.length >= 2) {
                for (var i = 0, total = pairs.length; i < total; i++) {
                  var arr = pairs[i].split('=');
                  if (arr[0] === key) {
                    return arr[1];
                  }
                }
              }
            }
            if (url[1] !== null && url[1] !== undefined) {
              pairs = url[1].split('=');
              if (pairs[0] === key) {
                return pairs[1];
              }
            }
            return null;
          }
        }
      };

      T.load = function (jss, fn) {
          var js = [];
          var lib = window['loadjs'] && typeof window['loadjs'] === 'function' && !!document.querySelector('script[src*=\'loadjs\']');
          var jss_str = typeof jss === 'string';

          if (!jss_str && !(jss.constructor === Array))
            throw new TypeError('(T.load) Param must be a string or array');

          jss = jss_str ? [jss] : jss;

          for (var i in jss) {
            var url = jss[i];
            switch (url) {
              case 'account':
              case 'cookies':
              case 'history':
              case 'navigation':
              case 'utils':
              case 'elements':
              case 'design':
                url = T.API + '/' + url + '.js';
                break;
            }
            js.push(url);
          };

          if (!lib) {
            var s = document.createElement('script');
            s.src = T.LOADJS;
            s.onload = load.bind(this, fn);
            document.body.appendChild(s);
          } else
            load(fn);

          function load(callback) {
            loadjs(js, {
              success: function () {
                T.dispatchCallback(callback);
              }
            });
          }
      };

      T.dispatchCallback = function (callback) {
        if (callback) {
          if (typeof callback === 'string') {
            if (callback.length > 0) {
              try {
                window[callback]();
              } catch (e) {
                console.error('(T.dispatchCallback) \'' + callback + '\' function doesn\'t exist or doesn\'t found in this context');
              }
            } else
              console.error('(T.dispatchCallback) Callback function name must be a string and length more than 0');
          } else if (typeof callback === 'function')
            callback();
        } else 
          console.error('(T.dispatchCallback) Callback not defined');
      };

      T._callbacks = [];

      T.on = T.addEventListener = function (event, fn) {
        if (!T._callbacks[event])
          T._callbacks[event] = [];
        T._callbacks[event].push(fn);
      };

      T.dispatchEvent = function (event, lib) {
        if (T._callbacks[event])
          for (var i = 0, t = T._callbacks[event].length; i < t; i++)
            T._callbacks[event][i](lib);
      };

      T.storageAvailable = function (type) {
        
        if ( type !== 'localStorage' && type !== 'sessionStorage' ) {
          
          throw new SyntaxError('Unknown storage. The supporting types are the following: localStorage and sessionStorage');
          
        }
        
        try {
          
          var storage = window[ type ],
              k       = 'storage_test';
              
          storage.setItem( k, k );
          storage.removeItem( k );
          
          return true;
          
        } catch (e) {
          
          return  e instanceof DOMException && ( 
                  e.code === 22 || 
                  e.code === 1014 || 
                  e.name === 'QuotaExceededError' || 
                  e.name === 'NS_ERROR_DOM_QUOTA_REACHED' ) && 
                  storage.length > 0;
          
        }
        
      };
      
      return T;
        
    })(_ || {});
    
  /**
   * (c) 2018 TRIAL.
   * Created on 20/08/2018, 17:34:25.
   * Adapted from Simple MVC of Todd Zebert
   *
   * Licensed under the Apache License, Version 2.0 (the 'License');
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
  _ = (function (T) {

    /*
     * Simple MVC
     *
     * 2016 Todd Zebert
     *
     * repetative module pattern (and eslint directives) are so it can be
     * easily customized and broken into multiple files
     */


    /**
     * Simple MVC, 2016 Todd Zebert
     * Event Listeners and notifications module
     * 
     * @param {window.T} T 
     */
    T.MVC = (function (T) {
      'use strict';

      // sender is the context of the Model or View which originates the event
      T._Event = function (sender) {
        this._sender = sender;
        this._listeners = [];
      };

      T._Event.prototype = {
        // add listener closures to the list
        attach: function (listener) {
          this._listeners.push(listener);
        },
        // loop through, calling attached listeners
        notify: function (args) {

          this._listeners.forEach(function(v, i) {
            this._listeners[i](this._sender, args);
          }.bind(this));

        }
      };

      return T;

    })(T.MVC || {});


    /**
     * Simple MVC, 2016 Todd Zebert
     * Model module
     * 
     * @param {window.T} T 
     */
    T.MVC = (function(T) { // eslint-disable-line no-redeclare, no-shadow
      'use strict';

      T.Model = function(data) {

        this._data = [];

        var properties = Object.getOwnPropertyNames(data);

        for (var i in properties) {

          var property = properties[i];

          if (property === 'length') continue;

          if (typeof data[property] === 'object' && data[property] !== null && data[property] !== undefined) {

            this._data[property] = new T.Model(data[property]);

          } else {

            this._data[property] = (function (_v) {

              var value = _v;

              var property = {
                // get just returns the value
                get: function () {
                  return value;
                },
                // sets the value and notifies any even listeners
                set: function (data) {
                  value = data;
                  this.onSet.notify(data);
                },
                onSet: new T._Event(this)
              };

              return property;

            })(data[property]);

          }

        }

        return this._data;

      };

      return T;
    })(T.MVC || {}); // eslint-disable-line no-use-before-define, no-redeclare, no-shadow


    /**
     * A 1-way View Module
     * 
     * @param {window.T} T 
     */
    T.MVC = (function (T) { // eslint-disable-line no-redeclare, no-shadow
      'use strict';

      T.OneWayView = function (selector, binder) {

        this._selector = selector;
        if (!(!!this._selector)) {
          this._selector = document.querySelector(selector);
        }

        this._binder = binder;

        // since not a 2-way, don't need to set this.onChanged

        var attributes = Object.getOwnPropertyNames(binder);

        for (var i in attributes) {

          var attribute = attributes[i];

          // attach model listeners
          binder[attribute].onSet.attach((function (attribute) {

            return function (sender, args) {

              this.bind(attribute, args);

            }.bind(this);

          }.call(this, attribute)));

        }

      };

      T.OneWayView.prototype = {

        bind: function (attribute, args) {

          this._selector[attribute] = args;

        },

        show: function () {

          var attributes = Object.getOwnPropertyNames(this._binder);

          for (var i in attributes) {

            var attribute = attributes[i];

            this._selector[attribute] = this._binder[attribute].get();

          }

        }

      };

      return T;
    })(T.MVC || {}); // eslint-disable-line no-use-before-define, no-redeclare, no-shadow


    /**
     * A 2-way View Module
     * 
     * @param {window.T} T 
     */
    T.MVC = (function (T) { // eslint-disable-line no-redeclare, no-shadow
      'use strict';

      // selector is a DOM element that supports .onChanged and .value
      T.TwoWayView = function (selector, binder) {

        this._selector = selector;
        if (!(this._selector instanceof HTMLElement)) {
          this._selector = document.querySelector(selector);
        }

        if (!(this._selector instanceof HTMLElement)) {
          return null;
        }

        this._binder = binder;

        // for 2-way binding
        this.onChanged = new T._Event(this);

        var attributes = Object.getOwnPropertyNames(binder);

        for (var i in attributes) {

          var attribute = attributes[i];

          // attach model listeners
          binder[attribute].onSet.attach((function (attribute) {

            return function (sender, args) {

              this.bind(attribute, args);

            }.bind(this);

          }.call(this, attribute)));

        }

        // attach change listener for two-way binding
        this._selector.addEventListener('change', function (e) {
          this.onChanged.notify(e.target.value);
        }.bind(this));

      };

      T.TwoWayView.prototype = {

        bind: function (attribute, args) {

          this._selector[attribute] = args;

        },

        show: function () {

          var attributes = Object.getOwnPropertyNames(this._binder);

          for (var i in attributes) {

            var attribute = attributes[i];

            this._selector[attribute] = this._binder[attribute].get();

          }

        }

      };

      return T;
    })(T.MVC || {}); // eslint-disable-line no-use-before-define, no-redeclare, no-shadow


    /**
     * Controller module
     * 
     * @param {window.T} T 
     */
    T.MVC = (function (T) { // eslint-disable-line no-redeclare, no-shadow
      'use strict';

      T.Controller = function (binder, view) {

        this._binder = binder;
        this._view = view;

        var attributes = Object.getOwnPropertyNames(binder);

        for (var i in attributes) {

          var attribute = attributes[i];

          if (this._view.hasOwnProperty('onChanged')) {

            this._view.onChanged.attach((function (model) {

              return function (sender, data) {

                this.update(model, data);

              }.bind(this);

            }).call(this, this._binder[attribute]));

          }

        }

      };

      T.Controller.prototype = {

        update: function(model, data) {

          model.set(data);

        }

      };

      return T;
    })(T.MVC || {}); // eslint-disable-line no-use-before-define, no-redeclare, no-shadow

    T.MVC = (function (MVC) {

      MVC.Block = function (blockName, options) {

        options = options || {};

        this.__blockName = blockName;
        this.__into = options.into || document.body;
        this.__lazyLoadTemplate = options.lazyLoadTemplate || false;
        this.__listeners = {};

        var definedProperties = Object.getOwnPropertyNames( options );
        for (var i in definedProperties) {

          var property = definedProperties[ i ];

          if ( property.lastIndexOf( 'on', 0 ) === 0 ) {

            var listener = property.slice( 2 );

            this.on( listener, options[ 'on' + listener ] );

          }

        }

        Object.defineProperty( this, 'cssPath', {

          get: function () {
            return this.getFileUrl('css');
          }

        } );

        Object.defineProperty( this, 'jsPath', {

          get: function () {
            return this.getFileUrl('js');
          }

        } );

        this.name = this.__blockName.replace( /^([A-Z])|[\s-_](\w)/g, function (match, p1, p2) {

          if ( p2 ) return p2.toUpperCase();

          return p1.toLowerCase();

        } );

        MVC.Block.config.exports[ this.name ] = this;

        this.__element = document.getElementById( this.__blockName );

        if ( !( this.__element instanceof HTMLElement ) ) {

          if ( this.__lazyLoadTemplate ) {

            initBlock.call( this, null, options );

          } else {

            this.updateView( options.templateArgs, function (template) {

             return initBlock.call( this, template, options );

            }.bind(this) );

          }

        }

      };

      MVC.Block.config = {

        exports : window,
        dir     : 'blocks'

      };

      MVC.Block.prototype = {
        
        getFileUrl: function (ext) {
          
          return MVC.Block.config.dir + '/' + this.__blockName + '/' + this.__blockName + '.' + ext;
          
        },
        
        hide: function () {
          
          this.__element.hidden = true;
          
        },
        
        show: function () {
          
          this.__element.hidden = false;
          
        },

        updateView: function (templateArgs, ontemplate, onupdate) {

          var that = this;

          var url = '';

          var args = [];

          for (var i in templateArgs) {
            args.push( i + '=' + templateArgs[ i ] );
          }

          if ( this.hasListener('loadtemplateurl') ) {

            url = this.dispatch({
              eventType: 'loadtemplateurl',
              emitter: null
            }, templateArgs);

          } else {

            url = this.getFileUrl('php');

          }

          if ( args.length > 0 ) {

            url += '?' + args.join('&');

          }
          
          T.load('utils', function () {
            
            T.Utils.ajax( {
              url: url,
              response: 'document',
              onloadend: function (e) {

                parseTemplate.call( that, e.target.response );

              }
            } );
            
          });

          function parseTemplate(response) {

            var template = response.body.firstChild || document.createElement('div');

            var appendTemplate = ontemplate && ontemplate( template ) === false ? false : true;

            if ( appendTemplate === true && template ) {

              this.__element = template;
              this.__element.addToPage = addToPage.bind( this );
              this.__element.removeFromPage = removeFromPage.bind( this );

              this.__element.addToPage();

              onupdate();

            }

          }

        },

        on: function (eventType, listener, onElement) {

          onElement = ( onElement === true ? true : false ) || false;

          if ( typeof listener !== 'function' ) {

            throw new TypeError();

          }

          if ( onElement && !this.__element ) {

            throw new Error();

          }

          if ( onElement ) {

            if (this.__listeners[eventType]) {

              this.__element.removeEventListener(eventType, this.__listeners[eventType]);

            }

            this.__listeners[eventType] = listener;

            this.__element.addEventListener(eventType, this.__listeners[eventType]);

          } else {

            this['on' + eventType] = this.__listeners[eventType] = listener;

          }

        },

        hasListener: function (eventType) {

          var allEventKeys = Object.getOwnPropertyNames( this.__listeners );

          return allEventKeys.indexOf( eventType ) !== -1;

        },

        findElement: function (id) {

          if ( !this.__element ) {

            return null;

          }

          return this.__element.querySelector('#' + this.__blockName + ' #' + this.__blockName + '__' + id);

        },

        dispatch: function (event, data) {

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

          } else {

            var call = dispatch[ 'on' + eventType ];

            if ( typeof call === 'function' ) {

              return call.call( this, data || null );

            }

          }

        },

        destroy: function () {

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
          this.jsFile = null;

          MVC.Block.config.exports[ this.name ] = null;

          this.__blockName = null;
          this.name = null;

          this.dispatch({
            eventType: 'destroy',
            emitter: null
          });
          this.__listeners = null;

        }

      };

      function initBlock(template, options) {

        var that = this;
        var load = [];

        if ( !options.hasOwnProperty('hasCss') || options.hasCss === true ) {
          load.push( this.cssPath );
        }

        if ( !options.hasOwnProperty('hasJs') || options.hasJs === true ) {
          load.push( this.jsPath );
        }

        loadjs( load, {

          before: function ( path, script ) {

            if (script instanceof HTMLLinkElement) {
              that.cssFile = script;
            }

            if (script instanceof HTMLScriptElement) {
              that.jsFile = script;
            }

            return true;

          },

          success: function () {

            var blockInstanceClass = MVC.Block.config.exports[ that.name.charAt(0).toUpperCase() + that.name.slice(1) ];

            if ( template ) {

              if ( that.hasListener('parsetemplate') ) {

                template = that.dispatch({
                  eventType: 'parsetemplate',
                  emitter: null
                }, template);

              }

              if ( template ) {

                that.__element = template;
                that.__element.addToPage = addToPage.bind( that );
                that.__element.removeFromPage = removeFromPage.bind( that );

                that.__element.addToPage();

              }

            }

            if ( that.jsFile ) {

              var inject = [ null, that ];

              if ( options.inject && Array === options.inject.constructor ) {

                inject = inject.concat( options.inject );

              }

              that.instance = new ( blockInstanceClass.bind.apply( blockInstanceClass, inject ) );

            }

            that.dispatch({
              eventType: 'initialize',
              emitter: null
            });

          }

        } );

        return false;

      };

      function addToPage() {

        this.__into.appendChild( this.__element );

      }

      function removeFromPage() {

        this.__into.removeChild( this.__element );

      }

      return MVC;

    })(T.MVC || {});

    return T;

  })(_ || {});
    
  return _;
    
})();
    
window.dispatchEvent(new CustomEvent('T.Event.loaded'));
/* 
 * ( c ) 2017 TRIAL.
 * Created on 16/06/2017, 23:20:02.
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
  
    T.Animation = {
        Easing: {
            // no easing, no acceleration
            linear( t ) { return t; },
            // accelerating from zero velocity
            easeInQuad( t ) { return t * t; },
            // decelerating to zero velocity
            easeOutQuad( t ) { return t * ( 2 - t ); },
            // acceleration until halfway, then deceleration
            easeInOutQuad( t ) { return t < .5 ? 2 * t * t : -1 + ( 4 - 2 * t ) * t; },
            // accelerating from zero velocity 
            easeInCubic( t ) { return t*t*t; },
            // decelerating to zero velocity 
            easeOutCubic( t ) { return ( --t ) * t * t + 1; },
            // acceleration until halfway, then deceleration 
            easeInOutCubic( t ) { return t < .5 ? 4 * t * t * t : ( t - 1 ) * ( 2 * t - 2 ) * ( 2 * t - 2 ) + 1; },
            // accelerating from zero velocity 
            easeInQuart( t ) { return t * t * t * t; },
            // decelerating to zero velocity 
            easeOutQuart( t ) { return 1 - ( --t ) * t * t * t; },
            // acceleration until halfway, then deceleration
            easeInOutQuart( t ) { return t < .5 ? 8 * t * t * t * t : 1 - 8 * ( --t ) * t * t * t; },
            // accelerating from zero velocity
            easeInQuint( t ) { return t * t * t * t * t; },
            // decelerating to zero velocity
            easeOutQuint( t ) { return 1 + ( --t ) * t * t * t * t; },
            // acceleration until halfway, then deceleration 
            easeInOutQuint( t ) { return t < .5 ? 16 * t * t * t * t * t : 1 + 16 * ( --t ) * t * t * t * t; }
        },
        start( options ) {
            options.animation = options.animation || "frame";
            if ( options.animation === "frame" ) {
                var start;
                window.requestAnimationFrame( step );
                
                function step( timestamp ) {
                    if ( !start ) 
                        start = timestamp;
                    
                    var timePassed = timestamp - start;
                    var progress   = timePassed / options.duration;
                    options.step( options.delta ? options.delta( progress ) : progress, progress );
                    
                    if ( timePassed < options.duration )
                        window.requestAnimationFrame( step );
                    else if ( options.onfinish )
                        options.onfinish();
                }
            } else {
                var start = new Date,
                animation = setInterval( () => {
                    var timePassed = new Date - start;
                    var progress   = timePassed / options.duration;
                    if ( progress > 1 )
                        progress = 1;
                    var delta = options.delta( progress );
                    options.step( delta, progress );
                    if ( progress === 1 ) {
                        if ( options.onfinish )
                            options.onfinish();
                        clearInterval( animation );
                    }
                }, options.delay || 10 );
            }
        }
    };
    
    /**
     * A simple color converter realized with CoffeeScript
     * https://github.com/GMchris/CoffeeColors
     */
    T.Color = (function() {
      var CMYK, HSL, HSV, RGB, clamp, hasKeys, isObject, isString, randomBetween, toPrecision;

      RGB = (function() {
        function RGB(r1, g1, b1, a1) {
          this.r = r1 != null ? r1 : 0;
          this.g = g1 != null ? g1 : 0;
          this.b = b1 != null ? b1 : 0;
          this.a = a1 != null ? a1 : 1;
        }

        return RGB;

      })();

      CMYK = (function() {
        function CMYK(c1, m1, y1, k1) {
          this.c = c1 != null ? c1 : 0;
          this.m = m1 != null ? m1 : 0;
          this.y = y1 != null ? y1 : 0;
          this.k = k1 != null ? k1 : 0;
        }

        return CMYK;

      })();

      HSL = (function() {
        function HSL(h1, s1, l1) {
          this.h = h1 != null ? h1 : 0;
          this.s = s1 != null ? s1 : 0;
          this.l = l1 != null ? l1 : 0;
        }

        return HSL;

      })();

      HSV = (function() {
        function HSV(h1, s1, v1) {
          this.h = h1 != null ? h1 : 0;
          this.s = s1 != null ? s1 : 0;
          this.v = v1 != null ? v1 : 0;
        }

        return HSV;

      })();

      toPrecision = function(number, precision) {
        number = parseFloat(number.toFixed(precision));
        if (number < 0) {
          number *= -1;
        }
        return number;
      };

      hasKeys = function(object, keys) {
        var j, key, len;
        for (j = 0, len = keys.length; j < len; j++) {
          key = keys[j];
          if (!(key in object)) {
            return false;
          }
        }
        return true;
      };

      isString = function(item) {
        return toString.call(item) === '[object String]';
      };

      isObject = function(item) {
        return item !== null && typeof item === 'object';
      };

      randomBetween = function(min, max) {
        return Math.floor(Math.random() * (max - min + 1) + min);
      };

      clamp = function(subject, min, max) {
        if (subject > min) {
          if (subject < max) {
            return subject;
          } else {
            return max;
          }
        } else {
          return min;
        }
      };
      
      return (function() {
        var getRgb;

        getRgb = function(r, g, b, a, formatted) {
          if (formatted) {
            return "rgb(" + r + "," + g + "," + b + ((a != null) && a < 1 ? ',' + a : '') + ")";
          } else {
            return new RGB(r, g, b, a);
          }
        };

        function Color(value) {
          if (value == null) {
            value = {
              r: 0,
              g: 0,
              b: 0
            };
          }
          this._setRgb(value);
        }

        Color.prototype.to = function(property, formatted) {
          if (formatted == null) {
            formatted = false;
          }
          switch (property) {
            case 'hex':
              return Color.rgbToHex(this);
            case 'rgb':
              return Color.formatRgb(this, formatted);
            case 'hsl':
              return Color.rgbToHsl(this, formatted);
            case 'hsv':
              return Color.rgbToHsv(this, formatted);
            case 'cmyk':
              return Color.rgbToCmyk(this, formatted);
          }
        };

        Color.prototype.set = function(value) {
          this._setRgb(value);
          return this;
        };

        Color.prototype.brightness = function() {
          return toPrecision(Math.max(this.r, this.g, this.b) / 255, 2);
        };

        Color.prototype.temperature = function() {
          var h, ref, s, v;
          ref = Color.rgbToHsv(this), h = ref.h, s = ref.s, v = ref.v;
          if (h > 270 || h < 90) {
            return 'warm';
          } else {
            return 'cold';
          }
        };

        Color.prototype.alpha = function(amount) {
          if (amount != null) {
            this.a = clamp(amount, 0, 1);
            return this;
          } else {
            return this.a;
          }
        };

        Color.prototype.angle = function(deg) {
          return Color.angle(this, deg);
        };

        Color.prototype.complementary = function() {
          return Color.complementary(this);
        };

        Color.prototype.balanced = function(amount) {
          return Color.balanced(this, amount);
        };

        Color.prototype.triad = function() {
          return Color.triad(this);
        };

        Color.prototype.square = function() {
          return Color.square(this);
        };

        Color.prototype.analogous = function() {
          return Color.analogous(this);
        };

        Color.prototype.splitComplementary = function() {
          return Color.splitComplementary(this);
        };

        Color.prototype.r = 0;

        Color.prototype.g = 0;

        Color.prototype.b = 0;

        Color.prototype.a = 1;

        Color.prototype._setRgb = function(value) {
          var ref;
          return ref = (function() {
            switch (Color.getFormat(value)) {
              case 'hex':
                return Color.hexToRgb(value);
              case 'rgb':
                return Color.formatRgb(value);
              case 'hsl':
                return Color.hslToRgb(value);
              case 'hsv':
                return Color.hsvToRgb(value);
              case 'cmyk':
                return Color.cmykToRgb(value);
              default:
                return new RGB(0, 0, 0);
            }
          })(), this.r = ref.r, this.g = ref.g, this.b = ref.b, this.a = ref.a, ref;
        };

        Color.HEX_REGEX = /#(?:[a-f\d]{3}){1,2}\b/;

        Color.RGB_REGEX = /rgba?\((?:(?:\s*0*(?:25[0-5]|2[0-4]\d|1?\d?\d)\s*,){2}\s*0*(?:25[0-5]|2[0-4]\d|1?\d?\d)|\s*0*(?:100(?:\.0+)?|\d?\d(?:\.\d+)?)%(?:\s*,\s*0*(?:100(?:\.0+)?|\d?\d(?:\.\d+)?)%){2})\s*(?:,\s*(0(\.\d+)?|1(\.0+)?)\s*)?\)/;

        Color.HSL_REGEX = /hsla?\(\s*0*(?:360|3[0-5]\d|[12]?\d?\d)\s*(?:,\s*0*(?:100(?:\.0+)?|\d?\d(?:\.\d+)?)%?\s*){2}(?:,\s*(0(\.\d+)?|1(\.0+)?)\s*)?\)/;

        Color.HSV_REGEX = /hsva?\(\s*0*(?:360|3[0-5]\d|[12]?\d?\d)\s*(?:,\s*0*(?:100(?:\.0+)?|\d?\d(?:\.\d+)?)%?\s*){2}(?:,\s*(0(\.\d+)?|1(\.0+)?)\s*)?\)/;

        Color.CMYK_REGEX = /cmyka?\((?:\s*(0(\.\d+)?|1(\.0+)?)\s*(?:,?)){4,5}\)/;

        Color.getFormat = function(value) {
          if (value != null) {
            if (isString(value)) {
              switch (false) {
                case !value.match(Color.RGB_REGEX):
                  return 'rgb';
                case !value.match(Color.HEX_REGEX):
                  return 'hex';
                case !value.match(Color.HSL_REGEX):
                  return 'hsl';
                case !value.match(Color.HSV_REGEX):
                  return 'hsv';
                case !value.match(Color.CMYK_REGEX):
                  return 'cmyk';
              }
            } else if (isObject(value)) {
              switch (false) {
                case !hasKeys(value, ['r', 'g', 'b']):
                  return 'rgb';
                case !hasKeys(value, ['h', 's', 'l']):
                  return 'hsl';
                case !hasKeys(value, ['h', 's', 'v']):
                  return 'hsv';
                case !hasKeys(value, ['c', 'm', 'y', 'k']):
                  return 'cmyk';
              }
            }
          }
        };

        Color.formatRgb = function(rgb, formatted) {
          var a, b, g, r, ref;
          if (isString(rgb)) {
            if (!rgb.match(Color.RGB_REGEX)) {
              return;
            }
            ref = rgb.match(/rgba?\((.+?)\)/)[1].split(',').map(function(value) {
              value.trim();
              return parseFloat(value);
            }), r = ref[0], g = ref[1], b = ref[2], a = ref[3];
          } else if (isObject(rgb)) {
            r = rgb.r, g = rgb.g, b = rgb.b, a = rgb.a;
          }
          return getRgb(r, g, b, a, formatted);
        };

        Color.rgbToHex = function(rgb) {
          var b, g, r, ref, vals;
          ref = this.formatRgb(rgb), r = ref.r, g = ref.g, b = ref.b;
          vals = [rgb.r, rgb.g, rgb.b].map(function(value) {
            return ('0' + value.toString(16)).slice(-2);
          });
          return "#" + vals[0] + vals[1] + vals[2];
        };

        Color.rgbToHsl = function(rgb, formatted) {
          var b, d, g, h, l, max, min, r, ref, s;
          ref = this.formatRgb(rgb), r = ref.r, g = ref.g, b = ref.b;
          r /= 255;
          g /= 255;
          b /= 255;
          max = Math.max(r, g, b);
          min = Math.min(r, g, b);
          l = Math.round(((max + min) / 2) * 100);
          if (max === min) {
            h = s = 0;
          } else {
            d = max - min;
            s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
            switch (max) {
              case r:
                h = (g - b) / d + (g < b ? 6 : 0);
                break;
              case g:
                h = (b - r) / d + 2;
                break;
              case b:
                h = (r - g) / d + 4;
            }
            h = Math.round((h / 6) * 360);
            s = Math.round(s * 100);
            l = Math.round(l * 100);
          }
          if (formatted) {
            return "hsl(" + h + "," + s + "%," + l + "%)";
          } else {
            return new HSL(h, s, l);
          }
        };

        Color.rgbToHsv = function(rgb, formatted) {
          var b, dif, g, h, max, r, ref, s, v;
          ref = this.formatRgb(rgb), r = ref.r, g = ref.g, b = ref.b;
          max = Math.max(r, g, b);
          dif = max - Math.min(r, g, b);
          s = max === 0 ? 0 : 100 * dif / max;
          h = (function() {
            switch (false) {
              case s !== 0:
                return 0;
              case r !== max:
                return 60 * (g - b) / dif;
              case g !== max:
                return 120 + 60 * (b - r) / dif;
              case b !== max:
                return 240 + 60 * (r - g) / dif;
            }
          })();
          if (h < 0) {
            h = 360.0;
          }
          h = Math.round(h);
          s = Math.round(s);
          v = Math.round(max * 100 / 255);
          if (formatted) {
            return "hsv(" + h + "," + s + "," + v + ")";
          } else {
            return new HSV(h, s, v);
          }
        };

        Color.rgbToCmyk = function(rgb, formatted) {
          var b, c, g, k, m, r, ref, y;
          ref = this.formatRgb(rgb), r = ref.r, g = ref.g, b = ref.b;
          r /= 255;
          g /= 255;
          b /= 255;
          k = toPrecision(1 - Math.max(r, g, b), 2);
          c = toPrecision((1 - r - k) / (1 - k), 2);
          m = toPrecision((1 - g - k) / (1 - k), 2);
          y = toPrecision((1 - b - k) / (1 - k), 2);
          if (formatted) {
            return "cmyk(" + c + "," + m + "," + y + "," + k + ")";
          } else {
            return new CMYK(c, m, y, k);
          }
        };

        Color.hexToRgb = function(hex, formatted) {
          var rgb;
          if (!hex.match(Color.HEX_REGEX)) {
            return;
          }
          hex = hex.replace('#', '');
          if (hex.length === 3) {
            hex += hex;
          }
          rgb = hex.match(/.{1,2}/g).map(function(val) {
            return parseInt(val, 16);
          });
          return getRgb(rgb[0], rgb[1], rgb[2], 1, formatted);
        };

        Color.hslToRgb = function(hsl, formatted) {
          var a, b, g, h, l, p, q, r, ref, s;
          if (isString(hsl)) {
            if (!hsl.match(Color.HSL_REGEX)) {
              return;
            }
            ref = hsl.match(/hsla?\((.+?)\)/)[1].split(',').map(function(value) {
              value.trim();
              return parseFloat(value);
            }), h = ref[0], s = ref[1], l = ref[2], a = ref[3];
          } else if ((isObject(hsl)) && (hasKeys(hsl, ['h', 's', 'l']))) {
            h = hsl.h, s = hsl.s, l = hsl.l, a = hsl.a;
          } else {
            return;
          }
          h /= 360;
          s /= 100;
          l /= 100;
          if (s === 0) {
            r = g = b = l;
          } else {
            q = l < 0.5 ? l * (1 + s) : l + s - l * s;
            p = 2 * l - q;
            r = Color.hueToRgb(p, q, h + 1 / 3);
            g = Color.hueToRgb(p, q, h);
            b = Color.hueToRgb(p, q, h - 1 / 3);
          }
          return getRgb(Math.round(r * 255), Math.round(g * 255), Math.round(b * 255), a, formatted);
        };

        Color.hueToRgb = function(p, q, t) {
          switch (false) {
            case !(t < 0):
              return t += 1;
            case !(t > 1):
              return t -= 1;
            case !(t < 1 / 6):
              return p + (q - p) * 6 * t;
            case !(t < 1 / 2):
              return q;
            case !(t < 2 / 3):
              return p + (q - p) * (2 / 3 - t) * 6;
            default:
              return p;
          }
        };

        Color.hsvToRgb = function(hsv, formatted) {
          var a, b, f, g, h, i, p, q, r, ref, s, t, v;
          if (isString(hsv)) {
            if (!hsv.match(Color.HSV_REGEX)) {
              return;
            }
            ref = hsv.match(/hsva?\((.+?)\)/)[1].split(',').map(function(value) {
              var numeric;
              value.trim();
              numeric = parseFloat(value);
              return numeric = value.indexOf('%') >= 0 ? numeric / 100 : numeric;
            }), h = ref[0], s = ref[1], v = ref[2], a = ref[3];
          } else if ((isObject(hsv)) && (hasKeys(hsv, ['h', 's', 'v']))) {
            h = hsv.h, s = hsv.s, v = hsv.v, a = hsv.a;
          } else {
            return;
          }
          if (s === 0) {
            r = g = b = Math.round(v * 2.55);
          } else {
            h /= 60;
            s /= 100;
            v /= 100;
            i = Math.floor(h);
            f = h - i;
            p = v * (1 - s);
            q = v * (1 - s * f);
            t = v * (1 - s * (1 - f));
            switch (i) {
              case 0:
                r = v;
                g = t;
                b = p;
                break;
              case 1:
                r = q;
                g = v;
                b = p;
                break;
              case 2:
                r = p;
                g = v;
                b = t;
                break;
              case 3:
                r = p;
                g = q;
                b = v;
                break;
              case 4:
                r = t;
                g = p;
                b = v;
                break;
              default:
                r = v;
                g = p;
                b = q;
            }
            r = Math.round(r * 255);
            g = Math.round(g * 255);
            b = Math.round(b * 255);
          }
          return getRgb(r, g, b, a, formatted);
        };

        Color.cmykToRgb = function(cmyk, formatted) {
          var a, b, c, g, k, m, r, ref, y;
          if (isString(cmyk)) {
            if (!cmyk.match(this.CMYK_REGEX)) {
              return;
            }
            ref = cmyk.match(/cmyka?\((.+?)\)/)[1].split(',').map(function(value) {
              return parseFloat(value.trim());
            }), c = ref[0], m = ref[1], y = ref[2], k = ref[3], a = ref[4];
          } else if ((isObject(cmyk)) && (hasKeys(cmyk, ['c', 'm', 'y', 'k']))) {
            c = cmyk.c, m = cmyk.m, y = cmyk.y, k = cmyk.k, a = cmyk.a;
          }
          r = Math.ceil(255 * (1 - c) * (1 - k));
          g = Math.ceil(255 * (1 - m) * (1 - k));
          b = Math.ceil(255 * (1 - y) * (1 - k));
          return getRgb(r, g, b, a, formatted);
        };

        Color.random = function() {
          return new Color({
            r: randomBetween(0, 255),
            g: randomBetween(0, 255),
            b: randomBetween(0, 255)
          });
        };

        Color.angle = function(color, angle) {
          var h, ref, s, v;
          ref = Color.rgbToHsv(color), h = ref.h, s = ref.s, v = ref.v;
          h += angle;
          while (h >= 360) {
            h -= 360;
          }
          while (h < 0) {
            h += 360;
          }
          return new Color(Color.hsvToRgb(new HSV(h, s, v)));
        };

        Color.complementary = function(color) {
          return this.angle(color, 180);
        };

        Color.triad = function(color) {
          return this.balanced(color, 3);
        };

        Color.square = function(color) {
          return this.balanced(color, 4);
        };

        Color.analogous = function(color) {
          return [this.angle(color, 30), this.angle(color, -30)];
        };

        Color.splitComplementary = function(color) {
          return [this.angle(color, 150), this.angle(color, -150)];
        };

        Color.balanced = function(color, amount) {
          var angle, cIdx, j, palette, ref;
          if (amount == null) {
            amount = 3;
          }
          palette = [];
          angle = 360 / amount;
          for (cIdx = j = 1, ref = amount - 1; 1 <= ref ? j <= ref : j >= ref; cIdx = 1 <= ref ? ++j : --j) {
            palette.push(this.angle(color, angle * cIdx));
          }
          return palette;
        };

        return Color;

      })();

    }).call(this);

    return T;
    
} )( window.T || {} );
    
window.dispatchEvent( new CustomEvent( 'T.Design.loaded' ) );
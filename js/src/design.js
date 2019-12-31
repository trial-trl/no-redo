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

    return T;
    
} )( window.T || {} );
    
window.dispatchEvent( new CustomEvent( 'T.Design.loaded' ) );
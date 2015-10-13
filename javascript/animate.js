/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function animate(options) {
    var start = new Date,
        animation = setInterval(function () {
        var timePassed = new Date - start;
        var progress = timePassed / options.duration;
        //alert(timePassed + " : " + progress + " : " + options.duration);
        if (progress > 1) {
            progress = 1;
        }
        var delta = options.delta(progress);
        options.step(delta);
        if (progress === 1) {
            clearInterval(animation);
        }
    }, options.delay || 10);
}

function move(element, options) {
    animate({
        delay: options.delay || 10,
        duration: options.duration || 1000,
        delta: options.delta,
        step: function (delta) {
            if (options.direction === "left") {
                (function () {
                element.style.left = options.to * delta + "px";
                })();
            } else if (options.direction === "right") {
                (function () {
                element.style.right = options.to * delta + "px";
                })();
            } else if (options.direction === "top") {
                (function () {
                element.style.top = options.to * delta + "px";
                })();
            } else {
                (function () {
                element.style.bottom = options.to * delta + "px";
                })();
            }
        }
    });
}
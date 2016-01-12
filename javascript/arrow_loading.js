/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var LOADING_STATE_NORMAL = "arrow_loading";
var LOADING_STATE_ERROR = "arrow_loading_error";
var LOADING_STATE_WAITING = "arrow_loading_waiting";

var sizeLoading = 128;
var state = "arrow_loading";

var support = false;

function showLoading(container, add_attr) {
    var movie = document.createElement("video");
    container.style.display = "block";
    container.style.opacity = 0;
    animate({
        duration: 100,
        delta: function (p) {
    	    return p;
        },
    	step: function (delta) {
    	    container.style.opacity = 1 * delta;
    	}
    });/*
    if (movie.canPlayType("video/mp4; codecs='avc1.4D401E, mp4a.40.2'") == "probably") {
    	movie.src = "http://no-redo.trialent.com/repository/images/TRIAL/loading/" + sizeLoading + "/" + state + ".mp4";
    	support = true;
    } else if (movie.canPlayType("video/ogg; codecs='theora, vorbis'") == "probably") {
    	movie.src = "http://no-redo.trialent.com/repository/images/TRIAL/loading/" + sizeLoading + "/" + state + ".ogg";
    	support = true;
    } else if (movie.canPlayType("video/webm; codecs='vp8.0, vorbis'") == "probably") {
    	movie.src = "http://no-redo.trialent.com/repository/images/TRIAL/loading/" + sizeLoading + "/" + state + ".webm";
    	support = true;
    }
    if (support) {
	movie.width = sizeLoading;
	movie.autoplay = true;
    	movie.loop = true;
    	movie.style.marginLeft = -(sizeLoading / 2);
    	container.appendChild(movie);
    } else {*/
        var gif = document.createElement("img");
        if (add_attr == null) {
            gif.setAttribute("id", "loading");
        } else {
            gif.setAttribute(add_attr[0][0], add_attr[0][1]);
        }
        gif.src = "http://no-redo.trialent.com/repository/images/TRIAL/loading/" + sizeLoading + "/" + state + ".gif";
        gif.width = sizeLoading;
    	gif.style.marginLeft = -(sizeLoading / 2) + "px";
    	gif.style.marginTop = "-48px";
    	container.appendChild(gif);
    //}
}

function dismissLoading(container) {
    var loading = container.children[0];
    container.style.opacity = 1;
    animate({
        duration: 100,
        delta: function (p) {
    	    return p;
        },
    	step: function (delta) {
    	    container.style.opacity = 1 - delta;
    	}
    });
    setTimeout(function () {
        container.style.display = "none";
    }, 100);
    container.removeChild(loading);
}
/*
 * Easing Functions - inspired from http://gizma.com/easing/
 * only considering the t value for the range [0, 1] => [0, 1]
 */
var easing = {
  // no easing, no acceleration
  linear: function (t) { return t },
  // accelerating from zero velocity
  easeInQuad: function (t) { return t*t },
  // decelerating to zero velocity
  easeOutQuad: function (t) { return t*(2-t) },
  // acceleration until halfway, then deceleration
  easeInOutQuad: function (t) { return t<.5 ? 2*t*t : -1+(4-2*t)*t },
  // accelerating from zero velocity 
  easeInCubic: function (t) { return t*t*t },
  // decelerating to zero velocity 
  easeOutCubic: function (t) { return (--t)*t*t+1 },
  // acceleration until halfway, then deceleration 
  easeInOutCubic: function (t) { return t<.5 ? 4*t*t*t : (t-1)*(2*t-2)*(2*t-2)+1 },
  // accelerating from zero velocity 
  easeInQuart: function (t) { return t*t*t*t },
  // decelerating to zero velocity 
  easeOutQuart: function (t) { return 1-(--t)*t*t*t },
  // acceleration until halfway, then deceleration
  easeInOutQuart: function (t) { return t<.5 ? 8*t*t*t*t : 1-8*(--t)*t*t*t },
  // accelerating from zero velocity
  easeInQuint: function (t) { return t*t*t*t*t },
  // decelerating to zero velocity
  easeOutQuint: function (t) { return 1+(--t)*t*t*t*t },
  // acceleration until halfway, then deceleration 
  easeInOutQuint: function (t) { return t<.5 ? 16*t*t*t*t*t : 1+16*(--t)*t*t*t*t }
}, element, triggerValue, scrollValue, interval, currentData, currentValue, exhibiting = true, onElement = false, MESSAGE_EXIST = "E", MESSAGE_SAVED_WITH_SUCCESS = "SWS"; MESSAGE_NOT_EXIST = "NE";

function sendAjax(e, data, verification, beforesend, onreadystatechange) {
    if (e != null) {
        e.preventDefault();
    }
    if (verification == null) {
        verification = {
            verify : function () {
                return true;
            }
        };
    }
    if (verification.verify()) {
    if (beforesend != null) {
        beforesend();
    }
    var xhr = new XMLHttpRequest(), additionalData = data.additional_data, method = "POST", action = "controldb.php", send;
    if (data.data != null) {
    if (data.type === "json") {
        send = JSON.stringify(data.data);
        if (data.url != null) {
            action = data.url;
        }
    } else if (data.type === "form") {
        method = data.data.getAttribute("method");
        action = data.data.getAttribute("action");
        send = new FormData(data.data);
        if (data.additional_data != null) {
            for (var i = 0, total = data.additional_data.length; i < total; i++) {
                var currentData = additionalData[i];
                send.append(currentData[0], currentData[1]);
            }
	}
    } else {
        send = data.data;
    }
    } else {
        if (data.url) {
            action = data.url;
            if (data.url.indexOf("?") != -1) {
                method = "GET";
                send = null;
            }
        }
    }
    xhr.open(method, action, true);
    xhr.send(send);
    xhr.onreadystatechange = function () { 
        onreadystatechange(xhr.readyState, xhr.responseText);
    };
    } else {
        verification.error();
    }
}

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
        options.step(delta, progress);
        if (progress === 1) {
            clearInterval(animation);
        }
    }, options.delay || 10);
}

function addGetParameter(addKey, addValue) {
    var href = location.href;
    var url = href.split("?");
    var pairs = href.indexOf("&") !== -1 ? url[1].split("&") : ""; 
    var key = [], parameters;
    if (url.length >= 2) {
    if (pairs !== "") {
        for (var i = 0, total = pairs.length; i < total; i++) {
    	    var arr = pairs[i].split("=");
    	    key[i] = arr[0];
    	    if (arr[0] == addKey) {
    	        pairs[i] = arr[0] + "=" + addValue;
    	    }
        }
        if (key.indexOf(addKey) === -1) {
    	    pairs.push(addKey + "=" + addValue);
        }
    } else {
    	var arr = url[1].split("=");
    	pairs = [];
    	pairs[0] = url[1];
        if (arr[0] !== addKey) {
    	    pairs.push(addKey + "=" + addValue);
        }
    }
    } else {
    	pairs = [];
    	pairs.push(addKey + "=" + addValue);
    }
    parameters = pairs.join("&");
}

function getUrlParameter(getKey) {
    var href = location.href;
    var url = href.split("?");
    var pairs = href.indexOf("&") !== -1 ? url[1].split("&") : null;
    if (pairs != null) { 
    var key = [], parameters;
    if (url.length >= 2) {
        for (var i = 0, total = pairs.length; i < total; i++) {
    	    var arr = pairs[i].split("=");
    	    if (arr[0] == getKey) {
    	        return arr[1];
    	    }
        }
    }
    }
    pairs = url[1].split("=");
    if (pairs[0] == getKey) {
    	return pairs[1];
    }
    return null;
}

function defineElement(e) {
    element = e;
}

function controlElementExhibition(options) {
    triggerValue = options.triggerValue || 200;
    scrollValue = window.pageYOffset;
    interval = options.interval || 100;
    window.onscroll = function (e) {
        if ((exhibiting && window.pageYOffset >= scrollValue) || (!exhibiting && window.pageYOffset <= scrollValue)) {
            currentValue += interval;
        }
            
        // É atribuido constantemente o valor atual da rolagem vertical, 
        // para saber se deve ou não mostrar o elemento.
        scrollValue = window.pageYOffset;
            
        if (currentValue >= triggerValue) {
            showHideElement(exhibiting);
            currentValue = 0;
            scrollValue = window.pageYOffset;
        }
        if (window.pageYOffset === 0) {
            showHideElement(false);
            currentValue = 0;
            scrollValue = window.pageYOffset;
        }
    };
}

function onHoverElement() {
    element.onmouseover = function (e) {
        onElement = true;
    };
    element.onmouseout = function (e) {
        onElement = false;
    };
}

function showHideElement(action) {
    /*
     * Aqui é controlado a exibição do elemento.
     */
    if (!onElement) {
        var height = +(window.getComputedStyle(element, null).getPropertyValue("height").replace("px", ""));
        if (action === true) {
            animate({
                duration: 250,
                delta: function (p) {
                    return p;
                },
                step: function (delta) {
                    element.style.top = -(height * delta) + "px";
                }
            });
            exhibiting = false;
        } else {
            animate({
                duration: 250,
                delta: function (p) {
                    return p;
                },
                step: function (delta) {
                    element.style.top = -height + (height * delta) + "px";
                }
            });
            exhibiting = true;
        }
    }
}

function scrollToTarget(options) {
    var initialScroll = options.orientation === "vertical" ? window.pageYOffset : window.pageXOffset, goTo = options.orientation === "vertical" ? options.target.offsetTop : options.target.offsetLeft;
    if (initialScroll > goTo) {
        goTo = -(initialScroll - goTo);
    }
    if (options.additional_scroll) {
    	var additional_scroll = options.additional_scroll.split("|");
    	if (additional_scroll[0] === "add") {
    	    goTo += additional_scroll[1];
    	} else {
    	    goTo -= additional_scroll[1];
    	}
    }
    animate({
        duration: options.duration | 200,
        delta: function (p) {
            return options.animation_function ? options.animation_function(p) : easing.easeInOutQuart(p);
        },
        step: function (delta) {
            if (options.orientation === "vertical") {
                window.scrollTo(0, initialScroll + (goTo * delta));
            } else {
                //alert(initialScroll + " ===== " + goTo);
                window.scrollTo(initialScroll + (goTo * delta), 0);
            }
        }
    });
}

function handleTabClick(options) {
    var dataContent = options.current_tab.getAttribute(options.attr || "trial-corresponding-element");
    if (currentData !== dataContent) {
        for (var i = 0, total = options.tabs.length; i < total; i++) {
            var tab = options.tabs[i];
            if (tab === null || tab === undefined) {
                tab.classList.remove(options.add_class || "current-tab");
                if (tab.getAttribute(options.attr || "trial-corresponding-element") === dataContent) {
                    tab.classList.add(options.add_class || "current-tab");
                    continue;
                }
                continue;
            } else {
                tab.classList.remove(options.add_class || "current-tab");
                if (tab.getAttribute(options.attr || "trial-corresponding-element") === dataContent) {
                    tab.classList.add(options.add_class || "current-tab");
                    continue;
                }
            }
        }
        for (var i = 0, total = options.contents.length; i < total; i++) {
            var c = options.contents[i];
            if (c.getAttribute(options.attr || "trial-corresponding-element") !== dataContent) {
	        c.style.opacity = 1;
	        animate({
	            duration : 100,
	            delta : function (p) {
	                return p;
	            },
	            step : function (delta) {
	                c.style.opacity = 1 - (delta * 1);
	            }
	        });
	        c.classList.add(options.visibility_class_control || "not-display");
	        continue;
	    }
	    c.classList.remove(options.visibility_class_control || "not-display");
	    animate({
	        duration : 100,
	        delta : function (p) {
	            return p;
	        },
	        step : function (delta) {
	            c.style.opacity = delta;
	        }
	    });
	}
    }
    currentData = dataContent;
}

function loadPage(options) {
    sendAjax(options.event, {data: null, url: options.url, type: "json"}, null, options.beforestart ? options.beforestart() : null, function (readyState, response) {
        if (readyState === 4) {
            var section = document.createElement("section");
            section.innerHTML = response;
            if (options.onloadpage) {
                options.onloadpage(response, section, options);
            }
            var r = section.getElementsByClassName("ajax-content")[0];
            createHistory(response, options);
            options.into.innerHTML = r.innerHTML;
            if (options.onfinish) {
            	options.onfinish();
            }
        }
    });
}

function recoverPage(e, data, options) {
    if (data === null) {
        loadPage({
            event: event,
            url: document.URL,
            into: options.into
        });
    } else {
        document.write(data);
    }
}

function popState(options) {
    window.onpopstate = function (e) {
        recoverPage(e, history.state, options);
    };
}

function createHistory(data, options) {
    if (history.pushState) {
        var html = document.createElement("html");
        html.innerHTML = data;
        var title = html.getElementsByTagName("title")[0].innerHTML;
        history.pushState(data, title, options.url);
        document.title = title;
        popState(options);
    } else {
        
    }
}

function handleElementPosition(options) {
    var offset_toolbar = options.trigger_offset != null ? options.trigger_offset : options.element.offsetTop;
    window.onscroll = function (e) {
        if (window.pageYOffset > offset_toolbar)  {
            options.element.classList.add(options.class_name);
            options.element.style.top = 0;
        } else {
            options.element.classList.remove(options.class_name);
            options.element.style.top = offset_toolbar + "px";
        }
    }
}

function closest(el, selector) {
    var matches;
    
    ['matches', 'webkitMatchesSelector', 'mozMatchesSelector', 'msMatchesSelector', 'oMatchesSelector'].some(function (fn) {
        if (typeof document.body[fn] == 'function') {
            matches = fn;
            return true;
        }
        return false;
    });
    
    while (el !== null) {
        parent = el.parentElement;
        
        if (parent !== null && parent[matches](selector)) {
            return parent;
        }
        
        el = parent;
    }
}
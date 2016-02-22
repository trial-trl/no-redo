function openWindow(e, openwindow, finished, options) {
    e.preventDefault();
    var top = e != null ? e.clientY : window.innerHeight / 2, left = e != null ? e.clientX : window.innerWidth / 2;
    options.window = options.window != null ? options.window : openwindow;
    options.window.style.top = top + "px";
    options.window.style.left = left + "px";
    options.background.style.top = top + "px";
    options.background.style.left = left + "px";
    options.window.style.opacity = 0;
    options.window.style.display = "block";
    if (options.window.width == null) {
    	var width, literal_size = window.getComputedStyle(options.window, null).getPropertyValue("width");
    	if (literal_size !== "auto" && literal_size !== 0 && literal_size !== "0px" && literal_size !== "0%") {
    	if (literal_size.indexOf("px") !== -1) {
    	    width = {
    	    	size: +(literal_size.replace("px", "")),
    	    	unit: "px"
    	    };
    	} else {
    	    width = {
    	    	size: +(literal_size.replace("%", "")),
    	    	unit: "%"
    	    };
    	}
    	} else {
    	    width = {
    	    	size: 100,
    	    	unit: "%"
    	    };
    	}
    	options.window.width = width;
    }
    if (options.window.height == null) {
    	var height, literal_size = window.getComputedStyle(options.window, null).getPropertyValue("height");
    	if (literal_size !== "auto" && literal_size !== 0 && literal_size !== "0px" && literal_size !== "0%") {
    	if (literal_size.indexOf("px") !== -1) {
    	    height = {
    	    	size: +(literal_size.replace("px", "")),
    	    	unit: "px"
    	    };
    	} else {
    	    height = {
    	    	size: +(literal_size.replace("%", "")),
    	    	unit: "%"
    	    };
    	}
    	} else {
    	    height = {
    	    	size: 100,
    	    	unit: "%"
    	    };
    	}
    	options.window.height = height;
    }
    if (options.center_horizontal == null) {
    	options.center_horizontal = false;
    }
    if (options.center_vertical == null) {
    	options.center_vertical = false;
    }
    if (options.center == null) {
    	options.center = false;
    }
    if (options.center || options.center_horizontal || options.center_vertical) {
    	options.window.padding = checkForElementPadding(options.window);
    	if (options.center || options.center_vertical) {
    	    var center_vertical_padding = ((options.window.padding.padding_top.size + options.window.padding.padding_bottom.size) / 2);
    	}
    	if (options.center || options.center_horizontal) {
    	    var center_horizontal_padding = ((options.window.padding.padding_left.size + options.window.padding.padding_right.size) / 2);
    	}
    }
    if (options.background) {
    	options.background.style.display = "block";
    	options.background.style.opacity = 0;
    	options.background.onclick = function (e) {
    	    if (options.onclose) {
    	        options.onclose(e, options);
    	    }
    	    closeWindow(e, null, options);
    	};
    	options.background.oncontextmenu = function (e) {
    	    if (options.onclose) {
    	        options.onclose(e, options);
    	    }
    	    closeWindow(e, null, options);
    	};
    }
    if (options.onstart) {
        options.onstart(e, options);
    }
    animate({
    	duration: 200,
    	delta: function (p) {
    	    return easing.easeInOutQuart(p);
    	},
    	step: function (delta, t) {
    	    if (options.fit_screen != null && options.fit_screen == true) {
		options.window.style.top = top - (top * delta) + "px";
    	        options.window.style.left = left - (left * delta) + "px";
    	    }
    	    if (options.center || options.center_horizontal) {
    	    	var size = (options.window.width.size / 2) + center_horizontal_padding;
    	    	options.window.style.left = "calc(" + (50 * delta) + "% + " + (left - (left * delta)) + "px)";
    	    	options.window.style.marginLeft = "-" + (size * delta) + "px";
    	    }
    	    if (options.center || options.center_vertical) {
    	    	var size = (options.window.height.size / 2) + center_vertical_padding;
    	    	options.window.style.top = "calc(" + (50 * delta) + "% + " + (top - (top * delta)) + "px)";
   	    	options.window.style.marginTop = "-" + (size * delta) + "px";
    	    }
    	    options.background.style.top = top - (top * delta) + "px";
    	    options.background.style.left = left - (left * delta) + "px";
    	    options.background.style.width = 100 * delta + "%";
    	    options.background.style.height = 100 * delta + "%";
    	    options.background.style.opacity = delta;
    	    options.window.style.width = (options.window.width.size * delta) + options.window.width.unit;
    	    options.window.style.height = (options.window.height.size * delta) + options.window.height.unit;
    	    options.window.style.opacity = delta;
    	    if (options.additionalStep) {
    	        options.additionalStep(delta, t, options);
    	    }
    	}
    });
    setTimeout(function () {
        if (finished) {
            finished(options);
        }
        if (options.onfinish) {
            options.onfinish(e, options);
        }
    }, 200);
}

function closeWindow(e, openedwindow, options) {
    e.preventDefault();
    var top = e.clientY, left = e.clientX, window_opened = options.window != null ? options.window : openedwindow;
    window_opened.style.opacity = 1;
    animate({
    	duration: 200,
    	delta: function (p) {
    	    return easing.easeInOutQuart(p);
    	},
    	step: function (delta) {
    	    var old_top = +(window.getComputedStyle(window_opened, null).getPropertyValue("top").replace("px", ""));
    	    var old_left = +(window.getComputedStyle(window_opened, null).getPropertyValue("left").replace("px", ""));
    	    options.background.style.top = (top * delta) + "px";
    	    options.background.style.left = (left * delta) + "px";
    	    options.background.style.width = 100 - (100 * delta) + "%";
    	    options.background.style.height = 100 - (100 * delta) + "%";
    	    options.background.style.opacity = 1 - delta;
    	    window_opened.style.top = old_top - ((old_top - top) * delta) + "px";
    	    window_opened.style.left = old_left - ((old_left - left) * delta) + "px";
    	    window_opened.style.width = (options.window.width.size - (options.window.width.size * delta)) + options.window.width.unit;
    	    window_opened.style.height = (options.window.height.size - (options.window.height.size * delta)) + options.window.height.unit;
    	    window_opened.style.opacity = 1 - delta;
    	}
    });
    setTimeout(function () {
    	options.background.style.display = "none";
        window_opened.style.display = "none";
        window_opened.style.width = options.window.width.size + options.window.width.unit;
        window_opened.style.height = options.window.height.size + options.window.height.unit;
    }, 201);
}

function checkForElementPadding(element) {
    var padding_left = window.getComputedStyle(element, null).getPropertyValue("padding-left");
    var padding_left_number = padding_left.indexOf("px") !== -1 ? +(padding_left.replace("px", "")) : +(padding_left.replace("%", ""));
    var padding_left_unit = padding_left.indexOf("px") !== -1 ? "px" : "%";
    
    var padding_right = window.getComputedStyle(element, null).getPropertyValue("padding-right");
    var padding_right_number = padding_right.indexOf("px") !== -1 ? +(padding_right.replace("px", "")) : +(padding_right.replace("%", ""));
    var padding_right_unit = padding_right.indexOf("px") !== -1 ? "px" : "%";
    
    var padding_top = window.getComputedStyle(element, null).getPropertyValue("padding-top");
    var padding_top_number = padding_top.indexOf("px") !== -1 ? +(padding_top.replace("px", "")) : +(padding_top.replace("%", ""));
    var padding_top_unit = padding_top.indexOf("px") !== -1 ? "px" : "%";   
    
    var padding_bottom = window.getComputedStyle(element, null).getPropertyValue("padding-bottom");
    var padding_bottom_number = padding_bottom.indexOf("px") !== -1 ? +(padding_bottom.replace("px", "")) : +(padding_bottom.replace("%", ""));
    var padding_bottom_unit = padding_bottom.indexOf("px") !== -1 ? "px" : "%";   
    
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
    }
    
    return padding;
}
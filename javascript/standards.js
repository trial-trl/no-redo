function openWindow(options) {
    options.event.preventDefault();
    var top = options.event !== null && options.event !== undefined ? options.event.clientY : window.innerHeight / 2, left = options.event !== null && options.event !== undefined ? options.event.clientX : window.innerWidth / 2;
    options.window.style.top = top + "px";
    options.window.style.left = left + "px";
    options.window.style.opacity = 0;
    options.window.style.display = "block";
    if (options.window.width === null || options.window.width === undefined) {
    	var width, literal_size = window.getComputedStyle(options.window, null).getPropertyValue("width");
    	if (literal_size !== "auto" && literal_size !== 0 && literal_size !== "0px" && literal_size !== "0%") {
            var is_px = literal_size.indexOf("px") !== -1;
            width = {
    	    	size: is_px ? +(literal_size.replace("px", "")) : +(literal_size.replace("%", "")),
    	    	unit: is_px ? "px" : "%"
    	    };
    	} else {
    	    width = {
    	    	size: 100,
    	    	unit: "%"
    	    };
    	}
    	options.window.width = width;
    }
    if (options.window.height === null || options.window.height === undefined) {
    	var height, literal_size = window.getComputedStyle(options.window, null).getPropertyValue("height");
    	if (literal_size !== "auto" && literal_size !== 0 && literal_size !== "0px" && literal_size !== "0%") {
            var is_px = literal_size.indexOf("px") !== -1;
    	    height = {
    	    	size: is_px ? +(literal_size.replace("px", "")) : +(literal_size.replace("%", "")),
    	    	unit: is_px ? "px" : "%"
    	    };
    	} else {
    	    height = {
    	    	size: 100,
    	    	unit: "%"
    	    };
    	}
    	options.window.height = height;
    }
    if (options.center_horizontal === null || options.center_horizontal === undefined) {
    	options.center_horizontal = false;
    }
    if (options.center_vertical === null || options.center_vertical === undefined) {
    	options.center_vertical = false;
    }
    if (options.center === null || options.center === undefined) {
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
        options.background.style.top = top + "px";
        options.background.style.left = left + "px";
    	options.background.style.display = "block";
    	options.background.style.opacity = 0;
    	options.background.onclick = function (e) {
    	    closeWindow(e, null, options);
    	};
    	options.background.oncontextmenu = function (e) {
    	    closeWindow(e, null, options);
    	};
    }
    if (options.onstart) {
        options.onstart(options);
    }
    animate({
    	duration: 200,
    	delta: function (p) {
    	    return easing.easeInOutQuart(p);
    	},
    	step: function (delta, t) {
    	    if ((options.fit_screen !== null && options.fit_screen !== undefined) && options.fit_screen === true) {
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
            if (options.background) {
                options.background.style.top = top - (top * delta) + "px";
                options.background.style.left = left - (left * delta) + "px";
                options.background.style.width = 100 * delta + "%";
                options.background.style.height = 100 * delta + "%";
                options.background.style.opacity = delta;
            }
    	    options.window.style.width = (options.window.width.size * delta) + options.window.width.unit;
    	    options.window.style.height = (options.window.height.size * delta) + options.window.height.unit;
    	    options.window.style.opacity = delta;
    	    if (options.additionalStep) {
    	        options.additionalStep(delta, t, options);
    	    }
    	}
    });
    setTimeout(function () {
        if (options.onfinish) {
            options.onfinish(options);
        }
    }, 200);
}

function openWindoww(e, openwindow, finished, options) {
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
    var top = e.clientY, left = e.clientX;
    options.window.style.opacity = 1;
    animate({
    	duration: 200,
    	delta: function (p) {
    	    return easing.easeInOutQuart(p);
    	},
    	step: function (delta) {
    	    var old_top = +(window.getComputedStyle(options.window, null).getPropertyValue("top").replace("px", ""));
    	    var old_left = +(window.getComputedStyle(options.window, null).getPropertyValue("left").replace("px", ""));
    	    options.background.style.top = (top * delta) + "px";
    	    options.background.style.left = (left * delta) + "px";
    	    options.background.style.width = 100 - (100 * delta) + "%";
    	    options.background.style.height = 100 - (100 * delta) + "%";
    	    options.background.style.opacity = 1 - delta;
    	    options.window.style.top = old_top - ((old_top - top) * delta) + "px";
    	    options.window.style.left = old_left - ((old_left - left) * delta) + "px";
    	    options.window.style.width = (options.window.width.size - (options.window.width.size * delta)) + options.window.width.unit;
    	    options.window.style.height = (options.window.height.size - (options.window.height.size * delta)) + options.window.height.unit;
    	    options.window.style.opacity = 1 - delta;
    	}
    });
    setTimeout(function () {
        options.onclose(e, options);
    	options.background.style.display = "none";
        options.window.style.display = "none";
        options.window.style.width = options.window.width.size + options.window.width.unit;
        options.window.style.height = options.window.height.size + options.window.height.unit;
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
    };
    
    return padding;
}

function pageTransition(options) {
    this.el_init_bottom = +(window.getComputedStyle(options.element_triggered, null).getPropertyValue("bottom").replace("px", ""));
    this.el_init_right = +(window.getComputedStyle(options.element_triggered, null).getPropertyValue("right").replace("px", ""));
    this.el_init_width = +(window.getComputedStyle(options.element_triggered, null).getPropertyValue("width").replace("px", ""));
    this.el_init_height = +(window.getComputedStyle(options.element_triggered, null).getPropertyValue("height").replace("px", ""));
    this.el_init_border_radius = +(window.getComputedStyle(options.element_triggered, null).getPropertyValue("border-radius").replace("px", ""));
    var self = this;
    options.transition_to.style.top = "100%";
    options.transition_to.style.left = "100%";
    options.transition_to.style.display = "block";
    options.element_triggered.style.transition = "none";
    if (options.beforeStart) {
        options.beforeStart(options);
    }
    animate({
        duration: options.duration | 700,
        delta: function (p) {
            return easing.easeInOutQuart(p);
        },
        step: function (delta, t) {
            options.transition_to.style.top = 100 - (100 * delta) + "%";
            options.current_page.style.left = "-" + (100 * delta) + "%";
            options.transition_to.style.left = 100 - (100 * delta) + "%";
            if (options.additionalStep) {
                options.additionalStep(delta, t, options, self);
            }
        }
    });
    setTimeout(function () {
        if (options.transitionFinished) {
            options.transitionFinished(options);
        }
    }, options.duration | 1000);
}

function restorePage(options) {
    this.el_init_bottom = 30;
    this.el_init_right = 30;
    this.el_init_width = 64;
    this.el_init_height = 64;
    this.el_init_border_radius = 60;
    var self = this;
    options.current_page.style.display = "block";
    if (options.beforeStart) {
        options.beforeStart(options);
    }
    animate({
        duration: options.duration | 1000,
        delta: function (p) {
            return easing.easeInOutQuart(p);
        },
        step: function (delta, t) {
            options.current_page.style.top = 100 * delta + "%";
            options.current_page.style.left = 100 * delta + "%";
            options.transition_to.style.left = "-" + (100 - (100 * delta)) + "%";
            if (options.additionalStep) {
                options.additionalStep(delta, t, options, self);
            }
        }
    });
    setTimeout(function () {
        options.current_page.style.display = "none";
        options.element_triggered.style.transition = "0.1s all ease-in-out";
        if (options.transitionFinished) {
            options.transitionFinished(options);
        }
    }, options.duration | 1000);
}
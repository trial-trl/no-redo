/* 
 * Copyright 2015 TRIAL.
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
/*
 * Created on : 09/10/2015, 13:13:05
 * Author     : Matheus Leonardo dos Santos Martins
 */

var TRANSFORM_CONTEXT_MENU = 1;

function confirmationDialog(dialog, background, data, clickPositiveButton) {
	dialog.style.opacity = "0";
	background.style.opacity = "0";
    	dialog.style.display = "block";
    	background.style.display = "block";
    	animate({
    	    duration : 100,
    	    delta : function (p) {
    	    	return p;
    	    },
    	    step : function (delta) {
    	    	dialog.style.opacity = 1 * delta;
    	    	background.style.opacity = 1 * delta;
    	    }
    	});
        background.onclick = function () {
            dismissDialog(dialog, background);
        };
        dialog.getElementsByTagName("a")[0].onclick = function () {
            dismissDialog(dialog, background);
            clickPositiveButton(data);
        };
        dialog.getElementsByTagName("a")[1].onclick = function () {
            dismissDialog(dialog, background);
        };
}

function alertDialog(dialog, background) {
    dialog.style.display = 'block';
    background.style.display = 'block';
        background.onclick = function () {
            dismissDialog(dialog, background);
        };
        dialog.getElementsByTagName("a")[0].onclick = function () {
            dismissDialog(dialog, background);
        };
}

function dismissDialog(dialog, background) {
    animate({
    	duration : 100,
    	delta : function (p) {
    	    return p;
    	},
    	step : function (delta) {
	    dialog.style.opacity = window.getComputedStyle(dialog, null).getPropertyValue("opacity") - delta;
	    background.style.opacity = window.getComputedStyle(background, null).getPropertyValue("opacity") - delta;
    	}
    });
    dialog.style.display = 'none';
    background.style.display = 'none';
}

function showPopup(target, background, top) {
	if (top === null || isNaN(top)) {
	    top = 100;
	}
        target.style.opacity = "0";
        target.style.display = "block";
        target.style.top = "100%";
        background.style.opacity = "0";
        background.style.display = 'block';
        animate({
            duration : 200,
            delta : function (p) {
            	return p;
            },
            step : function (delta) {
        	target.style.opacity = 1 * delta;
        	if (top == 50) {
        	    target.style.top = (100 - (delta * 54)) + "%";
        	} else {
        	    target.style.top = (top - (delta * top)) + "%";
        	}
        	background.style.opacity = 1 * delta;
            }
        });
        background.onclick = function () {
            closePopup(target, background, top);
        };
}

function closePopup(target, background, top) {
    if (top === null || isNaN(top)) {
        top = 100;
    }
    animate({
        duration : 200,
        delta : function (p) {
            return p;
        },
        step : function (delta) {
       	    target.style.opacity = 1 - (delta * 1);
            if (top == 50) {
                target.style.top = ((delta * 50) + 50) + "%";
	    } else {
            	target.style.top = (delta * 100) + "%";
            }
            background.style.opacity = 1 - (delta * 1);
        }
    });
    setTimeout(function () { 
    	target.style.display = 'none';
	background.style.display = 'none'; 
    }, 200);
}

function transformFloatingActionButton(fab, transform) {
        if (transform === TRANSFORM_CONTEXT_MENU) {
            fab.children[0].style.display = 'none';
            fab.style.padding = '0';
            animate({
            	duration : 400,
            	delta : function (p) {
            	    return p; 
           	},
           	step : function (delta) {
           	    fab.style.width = (370 * delta) + "px";
           	    fab.style.borderRadius = ((60 * delta) - 60) + "px";
           	    fab.style.right = (-30 * delta) + "px";
           	}
            });
            //fab.animate({borderRadius: '1px', right: '-30px', width: '370px'}, 400);
            fab.style.backgroundColor = '#eee';
            fab.children[1].style.display = "block";
            fab.children[1].style.opacity = "0";
	    setTimeout(function () { fab.children[1].style.opacity = "1"; }, 400);
            fab.children[1].children[0].children[0].onclick = function (e) {
	        e.stopPropagation();
                restoreFloatingActionButton(fab, transform);
            };
        }
}

function restoreFloatingActionButton(fab, restore) {
        if (restore === TRANSFORM_CONTEXT_MENU) {
            fab.children[1].style.display = "none";
            fab.style.padding = "21px 21px";
            fab.style.width = "370px";
            animate({
            	duration : 300,
            	delta : function (p) { 
            	    return p; 
           	}, 
           	step : function (delta) {
           	    if ((+(window.getComputedStyle(fab, null).getPropertyValue("width").replace("px", "")) - 5) >= 69) {
           	    	fab.style.width = (+(window.getComputedStyle(fab, null).getPropertyValue("width").replace("px", "")) - 5) + "px";
           	    } else {
           	    	fab.style.width = "64px";
           	    }
           	    fab.style.borderRadius = (60 * delta) + "px";
           	    fab.style.right = (0.1 * delta) + "px";
           	}
            });
            //fab.animate({borderRadius: '60px', right: '0', width: '64px'}, 300);
            fab.style.backgroundColor = '#d50000';
            fab.children[0].style.display = "block";
        }
}

function openNavigationDrawer(drawer, background, page) {
    var width = +(window.getComputedStyle(drawer, null).getPropertyValue("width").replace("px", ""));
    drawer.style.display = "block";
    background.style.display = "block";
    if (drawer.classList.contains("below-of-page")) {
    animate({
    	duration : 200,
    	delta : function (p) {
    	    return p;
    	},
    	step : function (delta) {
    	    page.style.left = (delta * width) + "px";
    	    background.style.left = (delta * width) + "px";
    	}
    });
    background.onclick = function () {
    	closeNavigationDrawer(drawer, background, width, page);
    };
    } else {
    background.style.opacity = 0;
    animate({
    	duration : 200,
    	delta : function (p) {
    	    return p;
    	},
    	step : function (delta) {
    	    drawer.style.left = -width + (delta * width) + "px";
    	    background.style.opacity = delta;
    	}
    });
    background.onclick = function () {
    	closeNavigationDrawer(drawer, background, width);
    };
    }
    document.getElementsByTagName("html")[0].style.overflowY = "hidden";
}

function closeNavigationDrawer(drawer, background, to, page) {
    if (drawer.classList.contains("below-of-page")) {
    animate({
    	duration : 200,
    	delta : function (p) {
    	    return p;
    	},
    	step : function (delta) {
   	    page.style.left = (to - (delta * to)) + "px";
    	    background.style.left = (to - (delta * to)) + "px";
    	}
    });
    } else {
    animate({
    	duration : 200,
    	delta : function (p) {
    	    return p;
    	},
    	step : function (delta) {
    	    drawer.style.left = "-" + (to * delta) + "px";
    	    background.style.opacity = 1 - delta;
    	}
    });
    }
    background.style.display = "none";
    setTimeout(function () { drawer.style.display = "none"; }, 300);
    document.getElementsByTagName("html")[0].style.overflowY = "auto";
}

function handleToolbarPosition(toolbar) {
    var offsetToolbar = toolbar.offsetTop;
    window.onscroll = function (e) {
        if (window.pageYOffset > offsetToolbar)  {
            //toolbar.classList.remove("toolbar-relative");
            toolbar.classList.add("toolbar-fixed");
            toolbar.style.top = 0;
        } else {
            toolbar.classList.remove("toolbar-fixed");
            //toolbar.classList.add("toolbar-relative");
            toolbar.style.top = offsetToolbar + "px";
        }
    }
}
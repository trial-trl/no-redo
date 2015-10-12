/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var currentRotationValue = 0;
var rotatingInterval;
var loadingElement;
var sizeLoading = 128;
var isShowingLoading = false;

var LOADING_STATE_NORMAL = "arrow_loading";
var LOADING_STATE_ERROR = "arrow_loading_error";
var LOADING_STATE_WAITING = "arrow_loading_waiting";

function instanceLoading(loading) {
    loadingElement = loading;
}

function showLoading() {
    loadingElement.style.width = sizeLoading;
    loadingElement.style.height = sizeLoading;
    loadingElement.style.display = 'block';
    loadingElement.style.marginTop = "-" + ((sizeLoading / 2) + 10) + "px";
    loadingElement.style.marginLeft = "-" + ((sizeLoading / 2) + 10) + "px";
    rotatingInterval = setInterval(rotateLoading, 0);
}

function setSize(size) {
    sizeLoading = size;
}

function setLoading(state) {
    loadingElement.src = "http://no-redo.trialent.com/repository/images/TRIAL/loading/" + sizeLoading + "/" + state + ".png";
    currentLoadingState = state;
}

function rotateLoading() {
    switch (currentLoadingState) { 
        case LOADING_STATE_NORMAL:
            if (currentRotationValue <= 360) {
                loadingElement.style.transform = 'rotateZ(' + currentRotationValue + 'deg)';
                currentRotationValue += 1.2;
            } else {
                currentRotationValue = 1.2;
            }
            break;
        case LOADING_STATE_WAITING:
            if (currentRotationValue <= 360) {
                loadingElement.style.transform = 'rotateZ(' + currentRotationValue + 'deg)';
                currentRotationValue += 0.3;
            } else {
                currentRotationValue = 0.3;
            }
            break;
        case LOADING_STATE_ERROR:
            if (currentRotationValue > 0) {
                loadingElement.style.transform = 'rotateZ(' + currentRotationValue + 'deg)';
                currentRotationValue -= 0.4;
            } else {
                currentRotationValue = 360;
            }
            break;
    }
}

function dismissLoading() {
    loadingElement.style.display = 'none';
    clearInterval(rotatingInterval);
}
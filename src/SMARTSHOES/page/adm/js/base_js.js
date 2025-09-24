$(document).ready(function () {
    var varUA = navigator.userAgent.toLowerCase(); //userAgent 값 얻기
    if (varUA.match('android') != null) {
        //안드로이드면 webviewReady()를 호출하기 때문에 아무것도안함
    } else if (varUA.indexOf("iphone") > -1 || varUA.indexOf("ipad") > -1 || varUA.indexOf("ipod") > -1) {
        //아이폰이면 webviewReady()를 호출하기 때문에 아무것도안함
    } else {
        //아이폰, 안드로이드 외 처리(PC웹)
        webviewReady();
    }
});

function webviewReady(){
    
}


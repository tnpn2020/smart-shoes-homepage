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

function login(){
    var elem_id = lb.getElem("id");
    var elem_pw = lb.getElem("pw");

    if(elem_id.value == ""){
        gu.alert({
            description : "아이디를 입력해주세요", 
            title : null, 
            response_method : null
        });
        return;
    }

    if(elem_pw.value ==""){
        gu.alert({
            description : "비밀번호를 입력해주세요", //내용(string 문자열) 필수
            title : null,  //제목(string 문자열)  null이면 "알림"으로 처리함
            response_method : null, //확인버튼을 눌럿을경우 호출될 메소드function 이름(string 문자열)  null일 경우 메소드를 실행하지않음
            response_param : null //확인버튼을 눌렀을 경우 호출될 메소드function에게 전달할 데이터(json 데이터형)
        });
        return;
    }

    lb.ajax({
        type:"JsonAjaxPost",
        list : {
            ctl:"AdminUser",
            param1:"login",
            id: elem_id.value,
            pw: elem_pw.value
        },
        action : lb.obj.address,
        response_method : "login_result",
        havior : function(result){
            console.log(result);
            result = JSON.parse(result);
            login_result(result);
        }    
    });
}



function login_result(result){
    if(result.result == "1"){
        move_page("menu1_inquiry");
    }else{
        gu.alert({
            description : result.message, //내용(string 문자열) 필수
            title : null,  //제목(string 문자열)  null이면 "알림"으로 처리함
            response_method : null, //확인버튼을 눌럿을경우 호출될 메소드function 이름(string 문자열)  null일 경우 메소드를 실행하지않음
            response_param : null //확인버튼을 눌렀을 경우 호출될 메소드function에게 전달할 데이터(json 데이터형)
        });
    }
}


function enterkey() {
    if (window.event.keyCode == 13) {
         // 엔터키가 눌렸을 때 실행할 내용
         login();
    }
}

function test(result){
    console.log(result);
}
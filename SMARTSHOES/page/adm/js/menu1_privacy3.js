obj.flag.double_click = true;
$(document).ready(function () {
    init_privacy();
});


const init_privacy = () => {
    lb.ajax({
        type:"JsonAjaxPost",
        list : {
            ctl:"AdminMenu1",
            param1:"init_privacy",
            idx:3
        },
        action : lb.obj.address, //웹일경우 ajax할 주소
        havior : function(result){
            //웹일 경우 호출될 메소드
            console.log(result);
            result = JSON.parse(result);
            console.log(result);
            if(result.result==1){
                var datas = result.value[0];
                obj.elem.content_value.value = datas.content;
            }
            
        }  
    });
}

const update_privacy = () => {
    if(obj.elem.content_value.value.length ==0){
        gu.alert({
            description : '내용을 입력해주세요.', //내용(string 문자열) 필수
            title : null,  //제목(string 문자열)  null이면 "알림"으로 처리함
            response_method : null, //확인버튼을 눌럿을경우 호출될 메소드function 이름(string 문자열)  null일 경우 메소드를 실행하지않음
            response_param : null //확인버튼을 눌렀을 경우 호출될 메소드function에게 전달할 데이터(json 데이터형)
        });
        return;
    }
    $(".loading").fadeIn();
    lb.ajax({
        type:"JsonAjaxPost",
        list : {
            ctl:"AdminMenu1",
            param1:"update_privacy",
            content:obj.elem.content_value.value,
            idx:3
        },
        action : lb.obj.address, //웹일경우 ajax할 주소
        havior : function(result){
            //웹일 경우 호출될 메소드
            console.log(result);
            result = JSON.parse(result);
            console.log(result);
            $(".loading").fadeOut();
            if(result.result==1){
                // var datas = result.value[0];
                // obj.elem.content_value.value = datas.content;
                gu.alert({
                    description : '적용되었습니다.', //내용(string 문자열) 필수
                    title : null,  //제목(string 문자열)  null이면 "알림"으로 처리함
                    response_method : null, //확인버튼을 눌럿을경우 호출될 메소드function 이름(string 문자열)  null일 경우 메소드를 실행하지않음
                    response_param : null //확인버튼을 눌렀을 경우 호출될 메소드function에게 전달할 데이터(json 데이터형)
                });
                
            }
            
        }  
    });

}

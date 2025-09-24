function arrive_fcm(custom_data){ //fcm 메세지가 도착했을때 네이티브에서 호출해주는 함수
    if(typeof(after_arrive_fcm) == "function"){
        after_arrive_fcm(custom_data);
    }
}

function select_fcm(custom_data){ //fcm의 노티피케이션을 선택했을경우 네이티브에서 호출해주는 함수
    if(typeof(after_select_fcm) == "function"){
        after_select_fcm(custom_data);
    }
}
(function(global){
    var gu = function(){


        //alert, confirm 애니메이션 속도
        this.fade_time = 100;

        //alert관련 변수
        this.alert_method = null;
        this.alert_param = null;
        
        //confirm관련 변수
        this.confirm_positive_method = null;
        this.confirm_negative_method = null;
        this.confirm_positive_param = null;
        this.confirm_negative_param = null;
        /*
            Android로 전송
            JSON 형식으로 보내야함
            {
             "flag" : "send",
             "data" : { JSON   }
        }
        */
        this.sendNative = function(json){
             var varUA = navigator.userAgent.toLowerCase(); //userAgent 값 얻기
             if (varUA.match('android') != null) {
                 //안드로이드 일때 처리
                 window.android.script(json);
             } else if (varUA.indexOf("iphone")>-1||varUA.indexOf("ipad")>-1||varUA.indexOf("ipod")>-1) {
                 //IOS 일때 처리
                 webkit.messageHandlers.callbackHandler.postMessage(json);
             } else {
                 //아이폰, 안드로이드 외 처리
             }

        }
        this.loadingComplete = function(){
            //setTimeout("document.body.style.visibility = 'visible'", 10000); // 3000ms(3초)가 경과하면 ozit_timer_test() 함수를 실행합니다.
            var body = document.body;
            body.style.visibility = "visible";
            body.oncontextmenu = function(){return false;};
            body.ondragstart = function(){return false;};
            body.onselectstart = function(){return false;};
            // window.android.webLoadingComplete();
        }
        /*
        page : 이동할 화면
        value : 이동할 화면에 전달할 데이터(형식은 상관없지만 웬만하면 json 방식이 좋음)
        is_finish : 화면이동후 현재 화면 종료 여부(true::종료, false::유지)
        */
        this.pageMove = function(page, value, is_finish){
            var varUA = navigator.userAgent.toLowerCase(); //userAgent 값 얻기
            console.log(JSON.stringify(varUA));
            if (varUA.match('android') != null || varUA.indexOf("iphone")>-1 || varUA.indexOf("ipad")>-1 || varUA.indexOf("ipod")>-1) { //아이폰,안드로이드 os일 경우 네이티브로 전달
                if(typeof(is_finish) == "undefined" || typeof(is_finish) == undefined){
                    is_finish = false;
                }
                var json;
                if(page != null && typeof(page) != "undefined"){
                    if(varUA.indexOf("iphone")>-1 || varUA.indexOf("ipad")>-1 || varUA.indexOf("ipod")>-1){ //아이폰일경우 소문자로 page변환
                        page = page.toLowerCase();
                    }
                    json = {action: "move", page : page, value: value, is_finish : is_finish};
                    this.sendNative(JSON.stringify(json));
                }
            }else {
                //아이폰, 안드로이드 외 처리
                var os_name = lb.osCheck();
                if(os_name != "Unknown OS"){ //모르는 OS가 아니면 ajax 실행
                    location.href=lb.obj.address + "?ctl=AppMove&param1=" + page.toLowerCase();
                }else{
                    alert("AJAX 통신불가(OS 모름)");
                }
            }
        }
        this.action = function(json){
            //코드
            console.log(json);
            this.sendNative(JSON.stringify(json));

        }
        this.finish = function(){ //해당 페이지 종료일경우
            var json = {action: "finish"};
            this.sendNative(JSON.stringify(json));
        }
        this.back = function(value){
            var json = {action: "back", value:value};
            this.sendNative(JSON.stringify(json));
        }
        this.getUser = function(kind){ //value는 네이티브에서 script로 보낼 구분자
            var json = {action: "getUser", value:kind};
            this.sendNative(JSON.stringify(json));
        }

        this.getCertiryUser = function(kind){ //인증중인 유저정보 가져오기 kakao_id땜시
            var json = {action: "certiry_profile", value:kind};
            this.sendNative(JSON.stringify(json));
        }

        this.download_img = function(file_name){
            // console.log(file_name);
            var json = {action: "download_img", value:file_name};
            this.sendNative(JSON.stringify(json));
        }

        this.logout = function(){
            var json = {action: "logout"};
            this.sendNative(JSON.stringify(json));
        }

        this.backPress = function(){
            var json = {action: "backPress"};
            this.sendNative(JSON.stringify(json));
        }
        /*
        description : alert 텍스트
        title : title 텍스트
        response_method : 확인을 눌렀을경우 호출될 response_method  *인자값이 없어야함, 필요없으면 null
        */
        this.alert = function(data){ //나중에 native에서 다이얼로그 띄울것

            var description = data["description"];
            var title = data["title"]; 
            var response_method = data["response_method"];
            var response_param = data["response_param"];

            var varUA = navigator.userAgent.toLowerCase(); //userAgent 값 얻기
            if(title == null || typeof(title) == "undefined"){
                title = "알림";
            }
            if(response_method == null || typeof(response_method) == "undefined"){
                response_method = null;
            }
            var json = {action: "alert", title: title, description: description, response_method: response_method};
            
            if (varUA.match('android') != null || (varUA.indexOf("iphone")>-1||varUA.indexOf("ipad")>-1||varUA.indexOf("ipod")>-1)) {
                this.sendNative(JSON.stringify(json));
            }else { //안드로이드,아이폰이 아닐경우(웹일경우)
                if(typeof(lb.getElem("alert")) == "undefined"){
                    alert(description);
                    if(response_method != null){
                        eval(response_method + "();"); //response 함수 실행
                    }
                }else{
                    var elem_alert = lb.getElem("alert");
                    var elem_alert_title = lb.getElem("alert_title");
                    var elem_alert_content = lb.getElem("alert_content");
                    elem_alert_title.innerHTML = title;
                    elem_alert_content.innerHTML = description;
                    this.alert_method = response_method;
                    this.alert_param = response_param;
                    $(elem_alert).fadeIn(this.fade_time);
                    // elem_alert.style.display = "block";
                }
            }
        }

        this.alert_close = function(){
            var elem_alert = lb.getElem("alert");
            if(typeof(elem_alert) != "undefined"){
                if(this.alert_method != null){
                    if(this.alert_param == null){
                        eval(this.alert_method + "();"); //response 함수 실행    
                    }else{
                        eval(this.alert_method + "(" + JSON.stringify(this.alert_param) +");");
                    }
                }
                $(elem_alert).fadeOut(this.fade_time);
            }
        }

        /*
        description : alert 텍스트
        title : title 텍스트
        positive_method : 확인을 눌렀을경우 호출될 response_method  *인자값이 없어야함 , 필요없으면 null
        negative_method : 취소를 눌렀을경우 호출될 response_method  *인자값이 없어야함 , 필요없으면 null
        */
        this.confirm = function(data){
            var description = data["description"];
            var title = data["title"]; 
            var positive_method = data["positive_method"];
            var negative_method = data["negative_method"];
            var positive_param = data["positive_param"];
            var negative_param = data["negative_param"];
            var varUA = navigator.userAgent.toLowerCase(); //userAgent 값 얻기
            if(title == null || typeof(title) == "undefined"){
                title = "알림";
            }
            if(typeof(positive_method) == "undefined"){
                positive_method = null;
            }
            if(typeof(negative_method) == "undefined"){
                negative_method = null;
            }

            if(typeof(positive_param) == "undefined"){
                negative_method = null;
            }

            if(typeof(negative_param) == "undefined"){
                negative_method = null;
            }

            var json = {action: "confirm", title: title, description: description, positive_method: positive_method, negative_method: negative_method, positive_param : positive_param, negative_param:negative_param};
            // this.log(JSON.stringify(json));
            if (varUA.match('android') != null || (varUA.indexOf("iphone")>-1||varUA.indexOf("ipad")>-1||varUA.indexOf("ipod")>-1)) {
                this.sendNative(JSON.stringify(json));
            }else { //안드로이드,아이폰이 아닐경우(웹일경우)
                if(typeof(lb.getElem("confirm")) == "undefined"){
                    if(confirm(description)){
                        //확인을 눌럿을경우
                        if(positive_method != null){
                            if(this.confirm_negative_param == null){
                                eval(positive_method + "(" + null +");");
                            }else{
                                eval(positive_method + "('" + positive_param +"');");
                            }
                        }
                    }else{
                        //취소를 눌럿을경우
                        if(negative_method != null){
                            if(this.confirm_negative_param == null){
                                eval(negative_method + "(" + null +");");
                            }else{
                                eval(negative_method + "('" + negative_param +"');");
                            }
                        }
                    }
                }else{
                    var elem_confirm = lb.getElem("confirm");
                    var elem_confirm_title = lb.getElem("confirm_title");
                    var elem_confirm_content = lb.getElem("confirm_content");
                    elem_confirm_title.innerHTML = title;
                    elem_confirm_content.innerHTML = description;
                    this.confirm_positive_method = positive_method;
                    this.confirm_positive_param = positive_param;
                    this.confirm_negative_method = negative_method;
                    this.confirm_negative_param = negative_param;
                    $(elem_confirm).fadeIn(this.fade_time);
                }
            }
        }

        this.confirm_positive = function(){
            var elem_confirm = lb.getElem("confirm");
            if(this.confirm_positive_method != null){
                if(this.confirm_positive_param == null){
                    eval(this.confirm_positive_method + "(" + null +");");
                }else{
                    eval(this.confirm_positive_method + "(" + JSON.stringify(this.confirm_positive_param) +");");
                }
                
            }
            $(elem_confirm).fadeOut(this.fade_time);
        }

        this.confirm_negative = function(){
            var elem_confirm = lb.getElem("confirm");
            if(this.confirm_negative_method != null){
                if(this.confirm_negative_param == null){
                    eval(this.confirm_negative_method + "(" + null +");");
                }else{
                    eval(this.confirm_negative_method + "(" + JSON.stringify(this.confirm_negative_param) +");");
                }
                
            }
            $(elem_confirm).fadeOut(this.fade_time);
        }

        this.toast = function(text){ //나중에 native에서 다이얼로그 띄울것
            // alert(text);
            var json = {action: "toast", title:"알림", description: text};
            this.sendNative(JSON.stringify(json));
        }
        this.console = function(text){
            console.log(text);
        }
        this.numberWithCommas = function(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
        this.convertDate = function(dateString){
            const monthNames = ["Jan.", "Feb.", "Mar.", "Apr.", "May", "Jun.",
            "Jul.", "Aug.", "Sep.", "Oct.", "Nov.", "Dec."
            ];
            //01 MAY 2019
            const d = new Date(dateString);

            return this.leadingZeros(d.getDate(),2) + " " + monthNames[d.getMonth()] + " " + d.getFullYear();
        }
        this.convertDay = function(dateString){
            
            var week = ['Sun.', 'Mon.', 'Tue.', 'Wed.', 'Thu.', 'Fri.', 'Sat.'];
            var d = new Date(dateString);
            var dayOfWeek = week[d.getDay()];
            return dayOfWeek;
        }

        this.leadingZeros = function(n, digits) {
            var zero = '';
            n = n.toString();
          
            if (n.length < digits) {
              for (var i = 0; i < digits - n.length; i++)
                zero += '0';
            }
            return zero + n;
        }

        this.log = function(value){
            var debug_flag = true; //디버그모드 일경우 true
            if(debug_flag){
                var varUA = navigator.userAgent.toLowerCase(); //userAgent 값 얻기
                if (varUA.match('android') != null || (varUA.indexOf("iphone")>-1||varUA.indexOf("ipad")>-1||varUA.indexOf("ipod")>-1)) {
                    //모바일일 경우 네이티브에게 log 출력 요청
                    var json = {action: "log", value:value};
                    this.sendNative(JSON.stringify(json));
                }else { //안드로이드,아이폰이 아닐경우(웹일경우)
                    console.log(value);   
                }
            }
        }
    }
    global.gu = new gu();
})(window);
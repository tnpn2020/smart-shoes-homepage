var custom_language_name;
var obj = {
    elem : {},
    value : {},
    flag : {
        move : true,
    },
    fn : {
        init : function(){
            lb.traverse(document,function(elem){
                if(typeof(elem.id)!="undefined"){
                    if(elem.id!=""){
                        obj.elem[elem.id] = elem;
                    }
                }
            });
            var cookieEnabled = cookie_enable();
            if(cookieEnabled){ //쿠키 허용시
                lb.cookie({
                    type:"get",
                    name:custom_language_name,
                    havior:function(result){
                        obj.language = result;
                    }
                });
    
                if(obj.language==null){
                    lb.cookie({
                        type:"set",
                        name : custom_language_name,
                        value : 1,
                        day : 3000
                    });
                    obj.language = 1;
                }
            }else{ //쿠키 거부시
                if(typeof data.lang == "undefined"){ //lang parameter가 없는 경우
                    obj.language = 1;
                }else{
                    obj.language = location.href.substring(location.href.length - 1 , location.href.length);
                }
            }

            if(typeof(add_lang_init)=="function"){
                add_lang_init();
            }
            if(typeof(add_tab_init)=="function"){
                add_tab_init();
            }
            if(typeof(add_init)=="function"){
                add_init();
            }
        },
        
        page_process : function(object){
            var start = parseInt((obj.page.move_page-1) / obj.page.page_count) * obj.page.page_count + 1;
            var end = parseInt((((obj.page.move_page-1)+obj.page.page_count) / obj.page.page_count)) * obj.page.page_count;//마지막까지 나타나야할 페이지수
            var page_list = parseInt((obj.page.total_count+(obj.page.page_size-1)) / obj.page.page_size);//총 나타나야할 페이지 개수
            function getElem(str){
                var div = lb.createElem("DIV");
                div.innerHTML = str;
                return div.firstChild;
            }
            
            if(start > 1){//첫페이지이동 존재
                var prev_first = getElem(object.prev_first);
                var prev_one = getElem(object.prev_one);

                prev_first.onclick = (function(){
                    var data = {page:1,name:object.page_name};
                    return function(){
                        return obj.fn.page_move(data);
                    }
                })();

                prev_one.onclick = (function(){
                    var data = {page:start-1,name:object.page_name};
                    return function(){
                        return obj.fn.page_move(data);
                    }
                })();

                object.elem.appendChild(prev_first);
                object.elem.appendChild(prev_one);
            }


            for(var i=start;i<=end;i++){
                if(obj.page.move_page==i){//현재 페이지 표시
                    var number_active = getElem(object.number_active);
                    // 아래 자식태그가 있을경우 innerHTML 로 인해 css가 안먹는 현상 해결의 위한 if
                    if(number_active.children.length == 0){
                        number_active.innerHTML = i;
                    }else{
                        number_active.children[0].innerHTML = i;
                    }
                    object.elem.appendChild(number_active);
                }else{
                    var number = getElem(object.number);
                    // 아래 자식태그가 있을경우 innerHTML 로 인해 css가 안먹는 현상 해결의 위한 if
                    if(number.children.length == 0){
                        number.innerHTML = i;
                    }else{
                        number.children[0].innerHTML = i;
                    }
                    number.onclick = (function(){
                        var data = {page:i,name:object.page_name};
                        return function(){
                            return obj.fn.page_move(data);
                        }
                    })();
                    object.elem.appendChild(number);
                }
        
                if(i>=page_list){
                    break;
                }
            }

            if(end < page_list){
                var next_last = getElem(object.next_last);
                var next_one = getElem(object.next_one);

                next_one.onclick = (function(){
                    var data = {page:end+1,name:object.page_name};
                    return function(){
                        return obj.fn.page_move(data);
                    }
                })();

                next_last.onclick = (function(){
                    var data = {page:page_list,name:object.page_name};
                    return function(){
                        return obj.fn.page_move(data);
                    }
                })();

                object.elem.appendChild(next_one);
                object.elem.appendChild(next_last);
            }
        },
        page_move : function(object){//숨겨야하는 부분은 list에 넣고, 페이지 부분은 move_page로 대체 한다.
            obj.page.move_list = JSON.parse(obj.page.move_list);
            obj.page.move_list.move_page=object.page;
            // lb.ajax({
            //     type:"post",
            //     list : obj.page.move_list,
            //     address : "/"
            // });
            
            var load = "";
            var count =0;

            // console.log(obj.page.move_list);
            for(var key in obj.page.move_list){
                if(count==0){
                    load = "?"+key+"="+obj.page.move_list[key];
                }else{
                    load = load+"&"+key+"="+obj.page.move_list[key];
                }
                count = count+1;
            }

            // console.log(load);

            location.href=load;
        },
        page_calc : function(){
            //실행 될때 내부에서 계속적으로 값을 가지고 있으면서 더해준다.
            if(typeof(obj.page.row_count)=="undefined"){
                obj.page.row_count=0;
            }else{
                obj.page.row_count = obj.page.row_count+1;
            }
            return (obj.page.total_count+obj.page.page_size) - (obj.page.move_page*obj.page.page_size)-obj.page.row_count;
        },
        page_list : function(object){
            // console.log(object);
            if(typeof(object.wrap_array)!="undefined"){
                lb.wrap_delete(object.wrap_array);
            }
            // console.log(obj.page);
            lb.ajax({
                type:"JsonAjaxPost",
                list: obj.page,
                havior:function(result){
                    console.log(result);
                    var json=json = JSON.parse(result);
                    if(json.result=="1"){
                        obj.page.total_count = parseInt(json.total_count);
                        obj.page.current_count = json.value.length;
                        object.havior(json.value);
                    
                        //페이징 처리
                        obj.fn.page_process(object.page_num);
                    }
                }
            });
        },
        // 최진혁 -> havior에서 value값만 넘어와서 result전체가 넘어오도록 수정
        page_list_v2 : function(object){
            if(typeof(object.wrap_array)!="undefined"){
                lb.wrap_delete(object.wrap_array);
            }
            lb.ajax({
                type:"JsonAjaxPost",
                list: obj.page,
                havior:function(result){
                    console.log(result);
                    var json = JSON.parse(result); 
                    if(json.result=="1"){
                        obj.page.total_count = parseInt(json.total_count);
                        obj.page.current_count = json.value.length;
                        object.havior(json);
                        if(typeof(object.object)!="undefined"){
                            object.object(json);
                        }
                        //페이징 처리
                        obj.fn.page_process(object.page_num);
                    }else{
                        object.havior(json);
                        if(typeof(object.object)!="undefined"){
                            object.object(json);
                        }
                    }
                }
            });
        },
        move_page : function(list){
            // lb.ajax({
            //     type:"post",
            //     list : list,
            //     address : "/"
            // });
            var load = "";
            var count = 0;
            if(typeof(list.data)!="undefined"){
                list.data = JSON.stringify(list.data);
            }
            for(var key in list){
                if(count==0){
                    load = "?"+key+"="+list[key];
                }else{
                    load = load+"&"+key+"="+list[key];
                }
                count = count+1;
            }
            location.href=load;
        },
        file_check : function(object){            
            /*
                함수 설명 : 
                fileSize : 파일 사이즈
                maxSize : 최대 파일 사이즈
                error_code : 1 성공, 2 최대파일 사이즈초과, 3 확장자틀림
            */
            // 사이즈체크
            var maxSize  = object.max_size * 1024 * 1024;    
            var fileSize = 0;
            var file_flag = false;
            object.error_code = true;
            
            // 브라우저 확인
            var browser=navigator.appName;
            var thumbext = object.file.value; //파일을 추가한 input 박스의 값
            thumbext = thumbext.slice(thumbext.indexOf(".") + 1).toLowerCase(); //파일 확장자를 잘라내고, 비교를 위해 소문자로 만듭니다.
            
            function init(){
                if(typeof(object.name_view)!="undefined"){
                    if(object.name_view.type=="html"){
                        object.name_view.elem.innerHTML = "";
                    }else{
                        object.name_view.elem.value = "";
                    }
                    
                }
                object.file.value = "";
            }
            // 익스플로러일 경우
            if (browser=="Microsoft Internet Explorer"){
                var oas = new ActiveXObject("Scripting.FileSystemObject");
                fileSize = oas.getFile( object.file.value ).size;
            }else{
                // console.log(object.file.files[0])
                fileSize = object.file.files[0].size;
            }
            
            if(fileSize > maxSize){
                init();
                object.error_code = 2;
                object.havior(object);
            }else{
                for(var i=0;i<object.extension.length;i++){
                    if(object.extension[i]===thumbext){
                        file_flag=true;
                    }
                }
                // console.log(object.name_view);
                if(file_flag){
                    if(typeof(object.name_view)!="undefined"){
                        if(object.name_view.type=="html"){
                            object.name_view.elem.innerHTML=object.file.files[0].name;
                        }else{
                            object.name_view.elem.value=object.file.files[0].name;
                        }
                    }

                    if(typeof(object.thumnail)!="undefined"){
                        var reader = new FileReader();
                        reader.__proto__.lb = this;
                        reader.onload = function(){
                            var output = null;
                            if(typeof(object.thumnail)=="object"){
                                output = object.thumnail;
                            }else{
                                output = this.lb.getElem(object.thumnail);
                            }
                            output.src = reader.result;
                        };
                        reader.readAsDataURL(event.target.files[0]);
                    }

                    object.havior(object);
                }else{
                    init();
                    object.error_code = 3;
                    object.havior(object);
                }
            }
        }
    }
}

//앱 페이지이름 set
var app_page_name = null;
var is_app = false; //앱인지 아닌지 판단
function set_is_app(){
    is_app = true;
}

function set_page_name(page_name){
    app_page_name = page_name;
}

//여러 페이지 종료(배열)
function finish_pages(pages){
    var json = {action: "finish_pages", pages: pages};
    gu.sendNative(JSON.stringify(json)); 
}

function set_lang(lang){
    console.log(lang);
    if(lang=="KR"){
        // obj.elem.lang_change_span.innerHTML = "KOR<span class=\"arrow-down\"></span>";
        // obj.elem.m_lang_change_span.innerHTML = "KOR<span class=\"arrow-down\"></span>";
    }else if(lang=="EN"){
        // obj.elem.lang_change_span.innerHTML = "ENG<span class=\"arrow-down\"></span>";
        // obj.elem.m_lang_change_span.innerHTML = "ENG<span class=\"arrow-down\"></span>";
    }else{
        // obj.elem.lang_change_span.innerHTML = "KOR<span class=\"arrow-down\"></span>";
        // obj.elem.m_lang_change_span.innerHTML = "KOR<span class=\"arrow-down\"></span>";
    }
}


//쿠키 허용 여부를 체크하여 return 해주는 함수
function cookie_enable(){
    var cookieEnabled;
    if (document.all){
        cookieEnabled = navigator.cookieEnabled;
    }else{
        var cookieName = 'CookieAccess' + (new Date().getTime());
        // console.log(cookieName);
        document.cookie = cookieName + '=cookieValue';
        // console.log(document.cookie);
        cookieEnabled = document.cookie.indexOf(cookieName) != -1;

        document.cookie = cookieName + '=; expires=Thu, 01 Jan 1999 00:00:10 GMT;';
    }
    return cookieEnabled;
}




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
                    location.href=lb.obj.address + "?param=app&param1=" + page.toLowerCase();
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

        this.show_loading = function(){
            var json = {action: "show_loading"};
            this.sendNative(JSON.stringify(json));
        }
        this.dismiss_loading = function(){
            var json = {action: "dismiss_loading"};
            this.sendNative(JSON.stringify(json));
        }
        this.finish = function(){ //해당 페이지 종료일경우
            var json = {action: "finish"};
            this.sendNative(JSON.stringify(json));
        }

        this.setvalue_finish = function(json){ //해당 페이지 종료후 데이터를 이전페이지로 넘길 경우
            var json = {action: "value_finish",json:json};
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
                if(lb.getElem("alert") == null){
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
                    // $(elem_alert).fadeIn(this.fade_time);
                    elem_alert.style.display = "block";
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
            console.log(data);
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
                if(lb.getElem("confirm") == null){
                    if(confirm(description)){
                        //확인을 눌럿을경우
                        if(positive_method != null){
                            if(positive_param == null){
                                eval(positive_method + "(" + null +");");
                            }else{
                                eval(positive_method + "('" + positive_param +"');");
                            }
                        }
                    }else{
                        // 취소를 눌럿을경우
                        if(negative_method != null){
                            if(negative_param == null){
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
            var varUA = navigator.userAgent.toLowerCase(); //userAgent 값 얻기
            if (is_app == true && varUA.match('android') != null) {
                var json = {action: "toast", title:"알림", description: text};
                this.sendNative(JSON.stringify(json));
            } else if (is_app == true ||varUA.indexOf("iphone") > -1 || varUA.indexOf("ipad") > -1 || varUA.indexOf("ipod") > -1) {
                var json = {action: "toast", title:"알림", description: text};
                this.sendNative(JSON.stringify(json));
            } else {
                console.log(text);
            }
            
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
        //채팅 관련코드 추가 2020-11-29 안정환
        this.chat = function(json){ 
            json = {action: "chat", json: json};
            if(typeof(app_page_name) != "undefined" && app_page_name != "" && app_page_name != null){
                json["page_name"] = app_page_name;
            }
            this.sendNative(JSON.stringify(json));
        }

        this.chatConnect = function(){
            var json = {action: "chat_connect", json: {}};
            if(typeof(app_page_name) != "undefined" && app_page_name != "" && app_page_name != null){
                json["page_name"] = app_page_name;
            }
            this.sendNative(JSON.stringify(json));
        }

        this.chatDisconnect = function(){
            var json = {action: "chat_disconnect", json: {}};
            if(typeof(app_page_name) != "undefined" && app_page_name != "" && app_page_name != null){
                json["page_name"] = app_page_name;
            }
            this.sendNative(JSON.stringify(json));
        }

        this.url_file_download = function(url,file_name){
            var json = {action: "url_file_download", json: {url : url, file_name : file_name}};
            this.sendNative(JSON.stringify(json));
        }
    }
    global.gu = new gu();
})(window);
obj.value.cookieEnabled = true;
obj.value.today_confirm_idx = []; //오늘 확인

$(document).ready(function(){ 
    var body = document.getElementsByTagName("body");
    // $(body).attr("oncontextmenu", "return false");
    //네이버톡톡 버튼 선택시 이벤트
    // obj.elem.naver_talk_btn.onclick = function(){
        // var talk_area = $('#talk_banner_div>.talk_preview_area>a');
		// $(talk_area).trigger('click');
	// }

    autocomplete_off();

    var url = location.href;

    //공유기능으로 페이지 접근시
    if(location.href.indexOf('flag=sns') != -1){
        //console.log(location.href);

        var url_array = location.href.split('param=');
        var param_data = url_array[1];
        param_data = param_data.replace(/\//gi, "&")

        //flag paramter 제거
        var index = param_data.indexOf('flag=');
        param_data = param_data.substring(0, index - 1);
        location.href = "?param=" + param_data;
    }

    //마우스 오른쪽, 블록 막기
    // document.oncontextmenu= function(){
    //     if(data.param != "coupon_register"){
    //         alert("마우스 오른쪽 버튼은 사용할 수 없습니다.");
    //         return false;
    //     }
    // };

    // 이거 주석 풀면 관리자페이지말고 홈페이지에서 써머노트 사용시 포커스 에러가 난다.
    // document.onselectstart = function(){
    //     return false;
    // }
    document.ondragstart = function(){
        return false;
    }

    //쿠키 허용 체크
    if (document.all){
        obj.value.cookieEnabled = navigator.cookieEnabled;
    }else{
        obj.value.cookieEnabled = navigator.cookieEnabled;
        var cookieName = 'CookieAccess' + (new Date().getTime());
        // //console.log(cookieName);
        document.cookie = cookieName + '=cookieValue';
        // //console.log(document.cookie);
        obj.value.cookieEnabled = document.cookie.indexOf(cookieName) != -1;

        document.cookie = cookieName + '=; expires=Thu, 01 Jan 1999 00:00:10 GMT;';
    }

    
    // if(user_idx == -1){
    //     //로그인 상태
    //     var elem = document.querySelectorAll('[data-login]');
    //     for(var i = 0; i < elem.length; i++){
    //         elem[i].style.display = "block";
    //     }
    // }else{
    //     var elem = document.querySelectorAll('[data-un-login]');
    //     for(var i = 0; i < elem.length; i++){
    //         elem[i].style.display = "block";
    //     }
    // }

    // request_cart_count();

    // localStorageEvent(); ---> 동산에서 기능 제거 요청함
});


function autocomplete_off(){
    var input = document.querySelectorAll("input");
    $(input).attr("autocomplete","off");
}

function localStorageEvent(){
    //localStorage에 저장된 오늘하루확인한 제품이 오늘 저장된 목록이 아니면 전부 제거시켜주기
    var today = new Date();
    var year = today.getFullYear();
    var month = today.getMonth() + 1;
    var date = today.getDate();
    var current_date = year + "" + month + date;

    var local_data = localStorage.getItem(project_name + "_today");
    if(local_data != null){
        var local_date = local_data.split("_")[1];
        if(local_date != current_date){
            localStorage.removeItem(project_name + "_today");
        }
    }

    //오늘 확인한 제품 idx array
    var local_data = localStorage.getItem(project_name + "_today");
    if(local_data != null){
        var idx_array = local_data.split('_')[0];
        obj.value.today_confirm_idx = JSON.parse(idx_array);
    }
}

function disableclick(event){
    if (event.button==2) {
        alert("마우스 오른쪽 클릭 방지");
        return false;
    }
}

function blockRightClick(){
    alert("오른쪽 버튼은 사용할 수 없습니다.");
    return false;
}
function blockSelect(){
    alert("내용을 선택할 수 없습니다.");
    return false;
}

// 언디파인드, 널 체크
function null_exception(data){
    if(typeof data != "undefined" && typeof data != undefined && data != "null" && data != null){
        return 1;
    }else{
        return 0;
    }
}

//가격 3자리 마다 , 찍어주기 ( 10000 -> 10,000 )
function number_comma(x) {
    if(null_exception(x)){
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }else{
        return 0;
    }
}

//콤마제거하기
function removeComma(str){
    if(obj.language != 1){
        n = parseInt(str.replace(/,/g,"").replace(/￦/g, ''));
    }else{
        n = parseInt(str.replace(/,/g,""));
    }
    return n;
}

//modal창 사용시 cart, buy, wishlist modal은 type만 입력해주고
//alert modal은 type과 msg를 입력 받아 알맞은 modal창을 띄어줌
//modal_text 함수는 파라미터 순서대로 (modal 제목, modal 내용, 첫번째 btn text, 두번째 btn text)
//btn text 값이 none이면 해당 btn의 display를 none
function open_modal(type, msg, link, form){
    obj.elem.modal.style.zIndex = "9999";
    obj.elem.modal.style.display = "block";
    obj.elem.modal_btn_1.style.display = "block";
    obj.elem.modal_btn_2.style.width = "50%";
    
    if(type == "alert" || type == "alert2"){ //alert창 대신 사용할 경고 modal
        if(type == "alert"){
            modal_text("알림", lang_check(msg), "none", "확인");
        }else{ //msg가 문자 + 데이터 + 문자 형식이라 따로 번역하기 위해 alert2로 구분
            modal_text("알림", msg, "none", "확인");
        }

        obj.elem.modal_btn_2.onclick = function(){
            if(typeof link == "undefined"){ //link가 undefined면 display none
                obj.elem.modal.style.display = "none";
            }else{ //link 값이 있으면 해당 link로 location.href
                location.href = link;
            }
        }
        document.querySelector('.modal-close').onclick = function(){
            if(typeof link == "undefined"){ //link가 undefined면 display none
                obj.elem.modal.style.display = "none";
            }else{ //link 값이 있으면 해당 link로 location.href
                location.href = link;
            }
        };
    }else if(type == "cart"){ //장바구니 등록시 modal
        modal_text("장바구니에 등록되었습니다.", lang_check("지금 확인하시겠습니까?"), "네", "계속쇼핑");
        obj.elem.modal_btn_1.onclick = function(){ //장바구니로 이동
            location.href = "?param=shopping_basket";
        }
        obj.elem.modal_btn_2.onclick = function(){
            obj.elem.modal.style.display = "none";
        }
    }else if(type == "buy"){ //상품 구매시 modal
        modal_text("상품 구매", lang_check("로그인하거나 비회원으로 구매하실 수 있습니다."), "비회원 구매", "로그인");
        obj.elem.modal_btn_1.onclick = function(){ //비회원 구매 btn
            form.submit();
        }
        obj.elem.modal_btn_2.onclick = function(){ //로그인 페이지 이동
            location.href = "?param=login&tab=member";
        }
    }else if(type == "wishlist"){ //위시리스트 등록시 modal
        modal_text("위시리스트에 등록되었습니다.", lang_check("마이페이지에서 확인하실 수 있습니다."), "none", "이동");
        obj.elem.modal_btn_2.onclick = function(){ //위시리스트 이동
            location.href = "?param=mypage_interested_post";
        }
    }else if(type == "qna_write"){ //위시리스트 등록시 modal
        modal_text("알림", lang_check("로그인 후 이용하실 수 있습니다."), "닫기", "로그인");
        obj.elem.modal_btn_1.onclick = function(){ 
            obj.elem.modal.style.display = "none";
        }
        obj.elem.modal_btn_2.onclick = function(){ //로그인 페이지 이동
            location.href = "?param=login&tab=member";
        }
    }
}

//msg는 confirm modal창 내용
//method는 확인 버튼 클릭시 실행될 함수고
//param은 함수 실행시 파라미터가 있으면 넣고 없으면 파라미터 없이 실행 ( 배열로 받음 )
//parameter가 없으면 parameter로 null 값을 넣어주면 parameter가 없는 method 실행
function open_confirm(data){
    var msg = data["description"];
    var positive_method = data["positive_method"];
    var positive_param = data["positive_param"];

    obj.elem.modal.style.display = "block"; //모달창 open
    obj.elem.modal_btn_1.style.display = "block"; //다른 모달 이벤트에 btn_1 none 시키는 부분이 있어서 모달 열때마다 block
    modal_text("알림", lang_check(msg), "취소", "확인"); //modal창 text들
    obj.elem.modal_btn_1.onclick = function(){ //취소 버튼 클릭시 모달창 닫음
        obj.elem.modal.style.display = "none";
    }
    obj.elem.modal_btn_2.onclick = function(){  //확인 버튼 클릭
        if(positive_param == null){ //param가 없으면 파라미터 없이 실행
            eval(positive_method + "(" + null +");")
        }else{ //param가 있으면 파라미터 넣어주고 함수 실행
            eval(positive_method + "(" + JSON.stringify(positive_param) + ");");
        } 
    }
}


//modal에 들어가는 text 정리
function modal_text(title, content, btn_1, btn_2){
    title = lang_check(title);
    btn_2 = lang_check(btn_2);
    obj.elem.modal_title.innerHTML = title;
    obj.elem.modal_content.innerHTML = content;
    obj.elem.modal_btn_2.innerHTML = btn_2;
    if(btn_1 == "none"){
        obj.elem.modal_btn_1.style.display = "none";
        obj.elem.modal_btn_2.style.width = "100%";
    }else{
        btn_1 = lang_check(btn_1);
        obj.elem.modal_btn_1.innerHTML = btn_1;
    }
}

//modal창 close 함수 넣기
function close_modal(id){
    if(id == null){
        obj.elem.modal.style.display = "none";    
    }else{
        obj.elem[id].style.display = "none";
    }
}

//리뷰 image를 클릭하면 이미지 modal open
function open_img_modal(src){
    obj.elem.image_modal.style.display ="block";
    obj.elem.modal_img.src = src;
}

function close_img_modal(){
    obj.elem.image_modal.style.display ="none";
}

//이미지 모달이 떴을때 빈공간을 클릭하면 modal close
function windowOnClick(event){
    if (event.target === obj.elem.image_modal) {
        close_img_modal();
    }
}

// 번호 체크
function number_check(elem){
    $(elem).on('propertychange change keyup paste input', function(){
        $(this).val($(this).val().replace(/[^0-9]/g,""));
    })
}

function account_number(){
    return; //번역으로 인하여 사용하지않음
    lb.ajax({
        type : "JsonAjaxPost",
        list : {
            ctl : "Order",
            param1 : "account_number",

        },
        action : "index.php",
        havior : function(result){
            //console.log(result);
            result = JSON.parse(result);
            if(result.result == 1){
                if(result.value.length != 0){
                    obj.elem.footer_account_number.innerHTML = result.value[0].account_number;
                    obj.elem.footer_bank_name.innerHTML = result.value[0].bank_name;
                    obj.elem.footer_depositor.innerHTML = result.value[0].depositor;
                    if(data.param == "order2"){
                        obj.elem.account_number.innerHTML = result.value[0].bank_name + "("+result.value[0].account_number+") 예금주 : " + result.value[0].depositor;
                    }
                }
            }
        }
    })
}

//한국어일때는 10000원, 영문, 중문은 ￦10000으로 하기 위한 함수
function price_location(price){
    if(obj.language == 1){
        return price + obj.lang["common"][obj.language]["원"];
    }else{
        return obj.lang["common"][obj.language]["원"] + price;
    }
}

//name을 입력하면 value를 return해주는 함수
function get_cookie(name) {
    var project = "dongsan_" + name;
    var value = document.cookie.match('(^|;) ?' + project + '=([^;]*)(;|$)');
    return value? value[2] : null;
}

function set_cookie(name, value, exp) {
    var str = "";
    for(var i = 0; i < value.length; i++){
        if(str != ""){
            str += "@";
        }
        str += JSON.stringify(value[i]);
    }

    name = "dongsan_" + name;
    
    var date = new Date();
    date.setTime(date.getTime() + exp*24*60*60*1000);
    document.cookie = name + '=' + str + ';expires=' + date.toUTCString() + ';path=/';
}

//lang.js에 해당 언어 data가 없으면 기본 언어로 반환시켜주는 함수
function lang_check(data){
    var lang_str = obj.lang["common"][obj.language][data]
    if(lang_str == "" || lang_str == null || typeof lang_str == "undefined" || lang_str == undefined){
        return data;
    }else{
        return lang_str;
    }
}

//뒤로가기 했을경우 자동스크롤 값 삭제
function null_scroll(){
    var myStorage = window.sessionStorage;
    myStorage.setItem('scroll', null);
}

//동산 스포츠 제품에서 제품 이미지보기 버튼 선택시 실행되는 함수
function open_other_colors_img_modal(product_idx){
    obj.elem.other_colors_modal.style.display = "block";
    lb.ajax({
        type : "JsonAjaxPost",
        list : {
            ctl : "Product",
            param1 : "request_other_colors",
            product_idx : product_idx,
        },
        response_method : "response_other_colors",
        action : "index.php",
        havior : function(result){
            // //console.log(result);
            result = JSON.parse(result);
            // //console.log(result);
            response_other_colors(result);
        }
    })
}

function response_other_colors(result){
    if(result.result == 1){
        if(result.value.length > 0){
            obj.elem.other_color_no_post.style.display = "none";
            for(var i = 0; i < result.value.length; i++){
                var copy = obj.elem.other_colors_img_copy.cloneNode(true);
                copy.id = "";
                copy.querySelector('[data-img]').src = obj.link.product_other_color_orign_path + result.value[i].file_name;
                obj.elem.other_colors_img_wrap.appendChild(copy);
            }
        }else{
            obj.elem.other_color_no_post.style.display = "";
        }
    }
}

function close_other_colors_img_modal(){
    obj.elem.other_colors_img_wrap.innerHTML = "";
    obj.elem.other_colors_modal.style.display = "none";
}


//Date 형식의 날짜 xxxx.xx.xx로 변환
function date_format(date){
    var year = date.substring(0, 4);
    var month = date.substring(5, 7);
    var day = date.substring(8, 10);
    return year + "." + month + "." + day;
}
function get_parentNode(elem, index){
    for(var i = 0; i < index; i++){
        elem = elem.parentNode;
    }
    return elem;
}

function open_cart_modal(target){
    if(!null_exception(target)){
        open_modal("alert", "옵션 선택창을 조회 할 수 없습니다.");
        return;
    }
    if(null_exception(obj.elem.cart_modal)){
        obj.elem.cart_modal.style.display = "block";
        $(obj.elem.modal_option_select_wrap).empty();
        lb.ajax({
            type : "JsonAjaxPost",
            list : {
                ctl : "Product",
                param1 : "request_cart_modal_list",
                target : target,
                lang_idx : obj.language,
            },
            response_method : "response_cart_modal_list",
            action : "index.php",
            havior : function(result){
                //console.log(result);
                result = JSON.parse(result);
                response_cart_modal_list(result);
            }
        })
    }
}

function response_cart_modal_list(result){
    if(result.result == 1){
        init_cart_modal_list(result);
    }
}

function init_cart_modal_list(result){
    var product_data = result.value[0];
    var option_data = result.option;

    //console.log(option_data);
    
    obj.elem.modal_total_product_price.innerHTML = 0;
    obj.elem.modal_product_name.innerHTML = product_data.product_name;
    obj.elem.modal_product_img.style = "background:url("+obj.link.product_thumnail_orign_path + product_data.thumnail_file+") no-repeat center center; background-size:120px";
    obj.elem.modal_option_1.style.display = "none";
    obj.elem.modal_option_1_title_name.innerHTML = "";
    obj.elem.modal_option_1_wrap.onchange = function(){}
    obj.elem.modal_option_2.style.display = "none";
    obj.elem.modal_option_2_title_name.innerHTML = "";
    obj.elem.modal_option_2_wrap.onchange = function(){}
    obj.elem.modal_option_3.style.display = "none";
    obj.elem.modal_option_3_title_name.innerHTML = "";
    obj.elem.modal_option_3_wrap.onchange = function(){}
    obj.elem.modal_option_4.style.display = "none";
    obj.elem.modal_option_4_title_name.innerHTML = "";
    obj.elem.modal_option_4_wrap.onchange = function(){}
    $(obj.elem.modal_option_1_wrap).empty();
    $(obj.elem.modal_option_2_wrap).empty();
    $(obj.elem.modal_option_3_wrap).empty();
    $(obj.elem.modal_option_4_wrap).empty();


    if(option_data.length == 0){
        // 해당 상품에 옵션이 없는 경우
        add_option_elem(result, 0);
    }else{
        // 해당 상품에 옵션이 있는 경우
        obj.elem.modal_option_1.style.display = "flex";
        obj.elem.modal_option_1_title_name.innerHTML = option_data[0].name;
        var option = document.createElement("option");
        option.value = 0;
        option.innerHTML = "선택";
        obj.elem.modal_option_1_wrap.appendChild(option);
        for(var i = 0; i<option_data.length; i++){
            var option = document.createElement("option");
            option.value = JSON.stringify(option_data[i]);
            option.innerHTML = option_data[i].option_1_name;
            obj.elem.modal_option_1_wrap.appendChild(option);
        }
        obj.elem.modal_option_1_wrap.onchange = function(){
            // 옵션2 change 초기화
            obj.elem.modal_option_2_wrap.onchange = function(){};
            $(obj.elem.modal_option_2_wrap).empty();
            // 옵션2 초기화
            obj.elem.modal_option_2.style.display = "none";
            obj.elem.modal_option_2_title_name.innerHTML = "";
            // 옵션3 change 초기화
            obj.elem.modal_option_3_wrap.onchnage = function(){};
            $(obj.elem.modal_option_3_wrap).empty();
            // 옵션3 초기화
            obj.elem.modal_option_3.style.display = "none";
            obj.elem.modal_option_3_title_name.innerHTML = "";
            // 옵션4 change 초기화
            obj.elem.modal_option_4_wrap.onchange = function(){};
            $(obj.elem.modal_option_4_wrap).empty();
            // 옵션4 초기화
            obj.elem.modal_option_4.style.display = "none";
            obj.elem.modal_option_4_title_name.innerHTML = "";

            if(this.value != 0){
                var data = JSON.parse(this.value);
                if(data.option_2.length == 0){
                    // 해당 상품에 옵션 1까지만 있음
                    add_option_elem(result, 1, data.idx);
                }else{
                    var option_1_idx = data.idx;
                    var option_2_data = data.option_2;
                    obj.elem.modal_option_2.style.display = "flex";
                    obj.elem.modal_option_2_title_name.innerHTML = option_2_data[0].name;
                    var option = document.createElement("option");
                    option.value = 0;
                    option.innerHTML = "선택";
                    obj.elem.modal_option_2_wrap.appendChild(option);
                    for(var i = 0; i<option_2_data.length; i++){
                        var option = document.createElement("option");
                        option.value = JSON.stringify(option_2_data[i]);
                        option.innerHTML = option_2_data[i].option_2_name;
                        obj.elem.modal_option_2_wrap.appendChild(option);
                    }

                    obj.elem.modal_option_2_wrap.onchange = function(){
                        // 옵션3 change 초기화
                        obj.elem.modal_option_3_wrap.onchnage = function(){};
                        $(obj.elem.modal_option_3_wrap).empty();
                        // 옵션3 초기화
                        obj.elem.modal_option_3.style.display = "none";
                        obj.elem.modal_option_3_title_name.innerHTML = "";
                        // 옵션4 change 초기화
                        obj.elem.modal_option_4_wrap.onchange = function(){};
                        $(obj.elem.modal_option_4_wrap).empty();
                        // 옵션4 초기화
                        obj.elem.modal_option_4.style.display = "none";
                        obj.elem.modal_option_4_title_name.innerHTML = "";
                        if(this.value != 0){
                            var data = JSON.parse(this.value);
                            if(data.option_3.length == 0){
                                // 해당 상품에 옵션 2까지 있음
                                add_option_elem(result, 2, option_1_idx, data.idx);
                            }else{
                                var option_2_idx = data.idx;
                                var option_3_data = data.option_3;
                                obj.elem.modal_option_3.style.display = "flex";
                                obj.elem.modal_option_3_title_name.innerHTML = option_3_data[0].name;
                                var option = document.createElement("option");
                                option.value = 0;
                                option.innerHTML = "선택";
                                obj.elem.modal_option_3_wrap.appendChild(option);
                                for(var i= 0; i<option_3_data.length; i++){
                                    var option = document.createElement("option");
                                    option.value = JSON.stringify(option_3_data[i]);
                                    option.innerHTML = option_3_data[i].option_3_name;
                                    obj.elem.modal_option_3_wrap.appendChild(option);
                                }
                                
                                obj.elem.modal_option_3_wrap.onchange = function(){
                                    // 옵션4 change 초기화
                                    obj.elem.modal_option_4_wrap.onchange = function(){};
                                    $(obj.elem.modal_option_4_wrap).empty();
                                    // 옵션4 초기화
                                    obj.elem.modal_option_4.style.display = "none";
                                    obj.elem.modal_option_4_title_name.innerHTML = "";
                                    if(this.value != 0){
                                        var data = JSON.parse(this.value);
                                        if(data.option_4.length == 0){
                                            // 해당 상품에 옵션 3까지 있음
                                            add_option_elem(result, 3, option_1_idx, option_2_idx, data.idx);
                                        }else{
                                            var option_3_idx = data.idx;
                                            var option_4_data = data.option_4;
                                            obj.elem.modal_option_4.style.display = "flex";
                                            obj.elem.modal_option_4_title_name.innerHTML = option_4_data[0].name;
                                            var option = document.createElement("option");
                                            option.value = 0;
                                            option.innerHTML  = "선택";
                                            obj.elem.modal_option_4_wrap.appendChild(option);
                                            for(var i = 0; i<option_4_data.length; i++){
                                                var option = document.createElement("option");
                                                option.value = JSON.stringify(option_4_data[i]);
                                                option.innerHTML = option_4_data[i].option_4_name;
                                                obj.elem.modal_option_4_wrap.appendChild(option);
                                            }
                                        }
                                        obj.elem.modal_option_4_wrap.onchange = function(){
                                            if(this.value != 0){
                                                var data = JSON.parse(this.value);
                                                //console.log(data);
                                                add_option_elem(result, 4, option_1_idx, option_2_idx, option_3_idx, data.idx);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

function add_option_elem(data, type, option_1_idx, option_2_idx, option_3_idx, option_4_idx){
    obj.value.order_data = {
        product_idx : "0",
        product_is_stock : "0",
        product_cnt : "0",
        option_1_idx : "0",
        option_1_cnt : "0",
        option_2_idx : "0",
        option_2_cnt : "0",
        option_3_idx : "0",
        option_3_cnt : "0",
        option_4_idx : "0",
        option_4_cnt : "0",
    };

    var wrap = obj.elem.modal_option_select_wrap;
    var add_elem = obj.elem.cart_modal_select_option_copy.cloneNode(true);
    add_elem.classList.add("modal_add_product");
    var title_elem = add_elem.querySelector(".product_tit");
    var option_elem = add_elem.querySelector(".select_product");
    var price_elem = add_elem.querySelector(".span_price");
    var give_point_elem = add_elem.querySelector(".span_accumulate");
    var close_elem = add_elem.querySelector(".close");
    option_elem.style.display = "none";
    option_elem.innerHTML = "";
    var price = 0;
    var product_data = data.value[0];
    title_elem.innerHTML = product_data.product_name;
    obj.value.order_data.product_idx = product_data.idx;
    obj.value.order_data.product_is_stock = product_data.is_stock;
    if(type == 0){
        // 옵션이 없는 경우
        if(null_exception(document.getElementById("modal_add_product_"+product_data.idx))){
            open_modal("alert", "이미 추가된 상품입니다.");
            return;
        }else{
            add_elem.id = "modal_add_product_"+product_data.idx;
        }
        close_elem.style.display = "none";
        obj.value.order_data.key = "product";
    }else if(type == 1){
        if(null_exception(document.getElementById("modal_add_product_"+product_data.idx+"_"+option_1_idx))){
            open_modal("alert", "이미 추가된 상품입니다.");
            return;
        }else{
            add_elem.id = "modal_add_product_"+product_data.idx+"_"+option_1_idx;
        }
        // 옵션1까지 있는 경우
        var option_data = data.option;
        var target_option_1 = null;
        for(var i = 0; i<option_data.length; i++){
            if(option_1_idx == option_data[i].idx){
                target_option_1 = option_data[i];
                option_elem.style.display = "block";
                option_elem.innerHTML = "- " + option_data[i].option_1_name;
            }
        }
        obj.value.order_data.option_1_idx = target_option_1.idx;
        obj.value.order_data.key = "option_1";
    }else if(type == 2){
        if(null_exception(document.getElementById("modal_add_product_"+product_data.idx+"_"+option_1_idx + "_" +option_2_idx))){
            open_modal("alert", "이미 추가된 상품입니다.");
            return;
        }else{
            add_elem.id = "modal_add_product_"+product_data.idx+"_"+option_1_idx + "_" +option_2_idx;
        }
        // 옵션2까지 있는 경우
        var option_data = data.option;
        var target_option_1 = null;
        for(var i = 0; i<option_data.length; i++){
            if(option_1_idx == option_data[i].idx){
                target_option_1 = option_data[i];
                option_elem.style.display = "block";
                option_elem.innerHTML = "- " + option_data[i].option_1_name;
            }
        }
        var target_option_2 = null;
        var option_2_data = target_option_1.option_2;
        for(var i = 0; i<option_2_data.length; i++){
            if(option_2_idx == option_2_data[i].idx){
                target_option_2 = option_2_data[i];
                option_elem.style.display = "block";
                option_elem.innerHTML += " > " + option_2_data[i].option_2_name;
            }
        }
        obj.value.order_data.option_1_idx = target_option_1.idx;
        obj.value.order_data.option_2_idx = target_option_2.idx;
        obj.value.order_data.key = "option_2";
    }else if(type == 3){
        if(null_exception(document.getElementById("modal_add_product_"+product_data.idx+"_"+option_1_idx + "_" + option_2_idx + "_" + option_3_idx))){
            open_modal("alert", "이미 추가된 상품입니다.");
            return;
        }else{
            add_elem.id = "modal_add_product_" + product_data.idx + "_" + option_1_idx + "_" +option_2_idx + "_" + option_3_idx;
        }

        // 옵션 3까지 있는 경우
        var option_data = data.option;
        var target_option_1 = null;
        for(var i =0; i<option_data.length; i++){
            if(option_1_idx == option_data[i].idx){
                target_option_1 = option_data[i];
                option_elem.style.display = "block";
                option_elem.innerHTML = "- " + option_data[i].option_1_name;
            }
        }
        var target_option_2 = null;
        var option_2_data = target_option_1.option_2;
        for(var i = 0; i<option_2_data.length; i++){
            if(option_2_idx == option_2_data[i].idx){
                target_option_2 = option_2_data[i];
                option_elem.style.display = "block";
                option_elem.innerHTML += " > " + option_2_data[i].option_2_name;
            }
        }
        var target_option_3 = null;
        var option_3_data = target_option_2.option_3;
        for(var i = 0; i<option_3_data.length; i++){
            if(option_3_idx == option_3_data[i].idx){
                target_option_3 = option_3_data[i];
                option_elem.style.display = "block";
                option_elem.innerHTML += " > " + option_3_data[i].option_3_name;
            }
        }
        
        obj.value.order_data.option_1_idx = target_option_1.idx;
        obj.value.order_data.option_2_idx = target_option_2.idx;
        obj.value.order_data.option_3_idx = target_option_3.idx;
        obj.value.order_data.key = "option_3";
    }else if(type == 4){
        if(null_exception(document.getElementById("modal_add_product_"+product_data.idx+"_"+option_1_idx + "_" + option_2_idx + "_" + option_3_idx + "_" + option_4_idx))){
            open_modal("alert", "이미 추가된 상품입니다.");
            return;
        }else{
            add_elem.id = "modal_add_product_" + product_data.idx + "_" + option_1_idx + "_" +option_2_idx + "_" + option_3_idx + "_" + option_4_idx;
        }

        // 옵션 3까지 있는 경우
        var option_data = data.option;
        var target_option_1 = null;
        for(var i =0; i<option_data.length; i++){
            if(option_1_idx == option_data[i].idx){
                target_option_1 = option_data[i];
                option_elem.style.display = "block";
                option_elem.innerHTML = "- " + option_data[i].option_1_name;
            }
        }
        var target_option_2 = null;
        var option_2_data = target_option_1.option_2;
        for(var i = 0; i<option_2_data.length; i++){
            if(option_2_idx == option_2_data[i].idx){
                target_option_2 = option_2_data[i];
                option_elem.style.display = "block";
                option_elem.innerHTML += " > " + option_2_data[i].option_2_name;
            }
        }
        var target_option_3 = null;
        var option_3_data = target_option_2.option_3;
        for(var i = 0; i<option_3_data.length; i++){
            if(option_3_idx == option_3_data[i].idx){
                target_option_3 = option_3_data[i];
                option_elem.style.display = "block";
                option_elem.innerHTML += " > " + option_3_data[i].option_3_name;
            }
        }

        var target_option_4 = null;
        var option_4_data = target_option_3.option_4;
        for(var i = 0; i<option_4_data.length; i++){
            if(option_4_idx == option_4_data[i].idx){
                target_option_4 = option_4_data[i];
                option_elem.style.display = "block";
                option_elem.innerHTML += " > " + option_4_data[i].option_4_name;
            }
        }

        obj.value.order_data.option_1_idx = target_option_1.idx;
        obj.value.order_data.option_2_idx = target_option_2.idx;
        obj.value.order_data.option_3_idx = target_option_3.idx;
        obj.value.order_data.option_4_idx = target_option_4.idx;
        obj.value.order_data.key = "option_4";
    }

    if(product_data.is_discount == 0){
        if(!null_exception(target_option_1)){
            price = Number(product_data.price);
        }else if(!null_exception(target_option_2)){
            price = Number(product_data.price) + Number(target_option_1.price);
        }else if(!null_exception(target_option_3)){
            price = Number(product_data.price) + Number(target_option_1.price) + Number(target_option_2.price);
        }else if(!null_exception(target_option_4)){
            price = Number(product_data.price) + Number(target_option_1.price) + Number(target_option_2.price) + Number(target_option_3.price);
        }else{
            price = Number(product_data.price) + Number(target_option_1.price) + Number(target_option_2.price) + Number(target_option_3.price) + Number(target_option_4.price);
        }
        price_elem.innerHTML = number_comma((price));
    }else{
        if(!null_exception(target_option_1)){
            price = Number(product_data.discount_price);
        }else if(!null_exception(target_option_2)){
            price = Number(product_data.discount_price) + Number(target_option_1.price);
        }else if(!null_exception(target_option_3)){
            price = Number(product_data.discount_price) + Number(target_option_1.price) + Number(target_option_2.price);
        }else if(!null_exception(target_option_4)){
            price = Number(product_data.discount_price) + Number(target_option_1.price) + Number(target_option_2.price) + Number(target_option_3.price);
        }else{
            price = Number(product_data.discount_price) + Number(target_option_1.price) + Number(target_option_2.price) + Number(target_option_3.price) + Number(target_option_4.price);
        }
        price_elem.innerHTML = number_comma((price));
    }

    price_elem.value = price;
    price_elem.classList.add("modal_price");
    if(null_exception(product_data.point_discount)){
        give_point_elem.innerHTML = number_comma((Number(price)/100 * Number(product_data.point_discount)));
        give_point_elem.value = product_data.point_discount;
    }else{
        give_point_elem.value = 0;
    }
    
    var count_wrap = add_elem.querySelector(".count-container");
    var minus_elem = count_wrap.querySelectorAll("span")[0];
    var input_elem = count_wrap.querySelectorAll("span")[1].querySelector("input");
    var plus_elem = count_wrap.querySelectorAll("span")[2];
    close_elem.onclick = function(){
        $(add_elem).remove();
        total_product_price_init();
    }
    add_elem.value = JSON.stringify(obj.value.order_data);
    wrap.appendChild(add_elem);
    minus_elem.onclick = function(){
        if(input_elem.value == 1){
            return;
        }
        input_elem.value--;
        price_elem.innerHTML = number_comma((Number(price_elem.value) * Number(input_elem.value)));
        total_product_price_init();
    }
    plus_elem.onclick = function(){
        input_elem.value++;
        price_elem.innerHTML = number_comma((Number(price_elem.value) * Number(input_elem.value)));
        total_product_price_init();
    }
    total_product_price_init();
}

function close_cart_modal(){
    obj.elem.cart_modal.style.display = "none";
}


function total_product_price_init(){
    var modal_price_elems = document.getElementsByClassName("modal_price");
    var total_price = 0;
    for(var i = 0; i<modal_price_elems.length; i++){
        total_price += removeComma(modal_price_elems[i].innerHTML);
    }
    obj.elem.modal_total_product_price.innerHTML = number_comma((total_price));
}



//구매하기 버튼을 누르면 선택된 상품 정보를 가지고 order_page로 이동
function modal_move_order_page(){
    obj.value.order_data_arr = [];
    var add_product_elems = document.getElementsByClassName("modal_add_product");
    for(var i = 0; i<add_product_elems.length; i++){
        var data = JSON.parse(add_product_elems[i].value);
        var count = add_product_elems[i].querySelector(".count-container").querySelectorAll("span")[1].querySelector("input").value;
        if(data.key == "product"){
            data.product_cnt = count;
        }else if(data.key == "option_1"){
            data.option_1_cnt = count;
        }else if(data.key == "option_2"){
            data.option_2_cnt = count;
        }else if(data.key == "option_3"){
            data.option_3_cnt = count;
        }else if(data.key == "option_4"){
            data.option_4_cnt = count;
        }
        obj.value.order_data_arr.push(data);
    }

    if(obj.value.order_data_arr.length != 0){
        sendPost(obj.value.order_data_arr);
    }else{
        open_modal("alert", "구매할 상품을 선택해주세요.");
    }
}


function modal_move_push_cart(){
    obj.value.order_data_arr = [];
    var add_product_elems = document.getElementsByClassName("modal_add_product");
    for(var i = 0; i<add_product_elems.length; i++){
        var data = JSON.parse(add_product_elems[i].value);
        var count = add_product_elems[i].querySelector(".count-container").querySelectorAll("span")[1].querySelector("input").value;
        if(data.key == "product"){
            data.product_cnt = count;
        }else if(data.key == "option_1"){
            data.option_1_cnt = count;
        }else if(data.key == "option_2"){
            data.option_2_cnt = count;
        }else if(data.key == "option_3"){
            data.option_3_cnt = count;
        }else if(data.key == "option_4"){
            data.option_4_cnt = count;
        }
        obj.value.order_data_arr.push(data);
    }

    
    if(obj.value.order_data_arr.length != 0){
        modal_push_cart(obj.value.order_data_arr);
    }else{
        open_modal("alert", "장바구니에 담을 선택해주세요.");
    }

}

//상품을 장바구니에 추가하는 함수
function modal_push_cart(order_data){
    //장바구니 테이블에 담을 데이터 설정
    if(user_idx == -1){ //-1이면 회원 ( 회원이면 DB에 저장 )
        lb.ajax({
            type : "JsonAjaxPost",
            list : {
                ctl : "Product",
                param1 : "push_cart",
                order_data : JSON.stringify(order_data),
            },
            response_method : "response_push_cart",
            action : "index.php",
            havior : function(result){
                //console.log(result);
                result = JSON.parse(result);
                // //console.log(result);
                response_push_cart(result);
            }    
        });
    }else{ //0이면 비회원 ( 비회원이면 쿠키로 관리 )
        if(obj.value.cookieEnabled){ //쿠키 허용시
            //비회원 고유 코드를 랜덤으로 생성하여 쿠키에 저장하고
            //저장된 쿠키로 비회원 장바구니 테이블의 정보를 조회
            var code = "";
            if(get_cookie("n_code") != null){
                code = get_cookie("n_code");
            }else{
                code = rand_generateRandomString();
                lb.cookie({
                    type:"set",
                    name : "dongsan_n_code",
                    value : code,
                    day : 1
                });
            }
            lb.ajax({
                type : "JsonAjaxPost",
                list : {
                    ctl : "Product",
                    param1 : "n_push_cart",
                    order_data : JSON.stringify(order_data),
                    code : code,
                },
                response_method : "response_n_push_cart",
                action : "index.php",
                havior : function(result){
                    //console.log(result);
                    result = JSON.parse(result);
                    response_n_push_cart(result);
                    // //console.log(result);
                }    
            });
        }else{ //쿠키가 허용되지 않았을때 알림창
            open_modal("alert", "쿠키를 허용하지 않으면 장바구니를 사용하실 수 없습니다.\n로그인 또는 쿠키 허용 후 사용해주세요.");
        }
    }
}


function response_push_cart(result){
    if(result.result == 1){
        open_modal("cart");
    }
}

function response_n_push_cart(result){
    if(result.result == 1){
        open_modal("cart");
    }
}

function login_check(link){
    if(user_idx == -1){ //로그인 상태
        location.href = link;
    }else{
        open_modal("alert", "로그인 후 이용 가능한 서비스입니다");
    }
}

function logout(flag){
    //console.log('asdf');
    lb.ajax({
        type: "JsonAjaxPost",
        list: {
            ctl: "User",
            param1: "logout",
        },
        action: "index.php",
        response_method : "response_logout",
        havior: function (result) {
            //console.log(result);
            response_logout(result);
        }
    });
    if(null_exception(flag)){
        if(flag == "secession"){}
    }else{
        // kakao_logout();
    }
}

function response_logout(result){
    if(result == 1) {
        alert('로그아웃이 되었습니다.');
        location.href = "/?param=index";
    }
}

// //카카오톡 로그아웃 함수
// function kakao_logout(){
//     //현재 로그인중이면 로그아웃 실행 ( 토큰값이 존재하면 로그인 상태 )
//     if(get_kakao_token()){
//         Kakao.Auth.logout(function(){
//             //로그아웃 이후 동작
//             location.href = "?param=login&tab=member";
//         });
//     }else{
//         location.href = "?param=login&tab=member";
//     }
// }

// //카카오 토큰을 가져오는 함수
// //토큰이 있으면 토큰을 return 없으면 null return
// function get_kakao_token(){
//     if(Kakao.Auth.getAccessToken()){
//         return Kakao.Auth.getAccessToken();
//     }else{
//         return null;
//     }
// }

//header에 넣을 Cart에 있는 상품 개수를 가져오는 함수
function request_cart_count(){
    if(user_idx == -1){ //회원일때
        lb.ajax({
            type : "JsonAjaxPost",
            list : {
                ctl : "Product",
                param1 : "request_cart_count",
            },
            response_method : "response_cart_count",
            action : "index.php",
            havior : function(result){
                //console.log(result);
                result = JSON.parse(result);
                response_cart_count(result);
            }    
        });
    }else{ //비회원일때는 code 값으로 n_shopping_basket table의 개수로 설정
        if(get_cookie("n_code") != null){
            var code = get_cookie("n_code");
            lb.ajax({
                type : "JsonAjaxPost",
                list : {
                    ctl : "Product",
                    param1 : "request_n_cart_count",
                    code : code,
                },
                action : "index.php",
                response_method : "response_n_cart_count",
                havior : function(result){
                    result = JSON.parse(result);
                    response_n_cart_count(result);
                }    
            });
        }else{
            obj.elem.cart_count.innerHTML = 0;
        }
    }
}

function response_cart_count(result){
    if(result.result == 1){
        var count = Number(result.value[0].count);
        if(count > 99){ //장바구니 상품개수가 99개 보다 많아지면 99개로 표시
            count = 99;
        }
        obj.elem.cart_count.innerHTML = count;
    }
}

function response_n_cart_count(result){
    if(result.result == 1){
        var count = Number(result.value[0].count);
        if(count > 99){ //장바구니 상품개수가 99개 보다 많아지면 99개로 표시
            count = 99;
        }
        obj.elem.cart_count.innerHTML = count;
    }
}

//post 방식으로 데이터를 보내기 위해 form 생성후 넘기기
function sendPost(arr){
    var form = document.createElement('form');
    var objs;
    objs = document.createElement('input');
    objs.setAttribute('type', 'hidden');
    objs.setAttribute('name', 'order_data');
    objs.setAttribute('value', JSON.stringify(arr));
    form.appendChild(objs);
    form.setAttribute('method', 'post');
    if(user_idx == -1){ //회원이면 order1 페이지로 이동
        form.setAttribute('action', "/?param=payment_view");
        document.body.appendChild(form);
        form.submit();
    }else{ //비회원이면 nonmember 페이지로 이동
        form.setAttribute('action', "/?param=payment_view");
        document.body.appendChild(form);
        open_modal("buy", "", "", form); 
    } 
}

function label_click(elem){
    $(elem.previousElementSibling).trigger('click');
}

function move_order_state_page(index){
    location.href = "?param=mypage_orderlist&state=" + index;
}

function move_global(link){
    location.href = link;
    //테스트 코드
    //테스트 계정만 접근할 수 있도록 하기
    // lb.ajax({
    //     type : "JsonAjaxPost",
    //     list : {
    //         ctl : "CS",
    //         param1 : "request_user_idx",
    //         link : link,
    //     },
    //     response_method : "response_user_idx",
    //     action : "index.php",
    //     havior : function(result){
    //         // //console.log(result);   
    //         result = JSON.parse(result);
    //         response_user_idx(result);
    //     }
    // });
}

function response_user_idx(result){
    if(result.user_idx == 2){
        location.href = result.link;
    }else{
        alert("준비중입니다.");
    }
}
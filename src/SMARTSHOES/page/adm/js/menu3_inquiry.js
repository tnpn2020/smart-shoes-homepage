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
    if(typeof(data.tab) == "undefined"){
        data["tab"] = "total";
    }
    
    request_project_setting();
    $('a[href="#"]').click(function(e) {
        e.preventDefault();
    });
}


//프로젝트 셋팅값 가져오기
function request_project_setting(){
    lb.ajax({
        type:"JsonAjaxPost",
        list : {
            ctl:"ProjectSetting",
            param1:"request_project_setting",
        },
        action : lb.obj.address, //웹일경우 ajax할 주소
        response_method : "reponse_project_setting", //앱일경우 호출될 메소드
        havior : function(result){
            //웹일 경우 호출될 메소드
            // console.log(result);
            result = JSON.parse(result);
            reponse_project_setting(result);
        }    
    });
}

function reponse_project_setting(result){
    if(result.result == "1"){
        var datas = result["value"];
        if(datas.length == 0){ //셋팅값이 없다면 알림
            gu.alert({
                description : "프로젝트 셋팅값이 없습니다.", //내용(string 문자열) 
                title : null,  //제목(string 문자열) 
                response_method : null //확인버튼을 눌럿을경우 호출될 메소드function 이름(string 문자열) 
            });
        }else{
            obj.value.category_count = result.value[0]["category_count"]; //카테고리 설정 갯수 (상품목록의 카테고리를 뿌릴때 사용)
            init_search_form();
            page_init(); // 페이지 ajax 통신
        }
    }else{
        gu.alert({
            description : result.message, //내용(string 문자열) 
            title : null,  //제목(string 문자열) 
            response_method : null //확인버튼을 눌럿을경우 호출될 메소드function 이름(string 문자열) 
        });
    }
}

// get방식 페이지 파라미터 값
var page_param = data;

function init_search_form(){
    // console.log(page_param);
    if(typeof(page_param.search_type) != "undefined"){ //키워드가 있다면 set
        lb.getElem("search_type").value = page_param.search_type;
        $(lb.getElem("search_type")).trigger('change');
    }

    if(typeof(page_param.search_text) != "undefined"){
        lb.getElem("search_text").value = page_param.search_text;
    }
}

function page_init(){
    $('.loading').fadeIn();
    $(obj.elem.paging).empty();
    obj.value.data = page_param;


    var param = null;
    if(typeof(page_param.search_type) != "undefined" && typeof(page_param.search_text) != "undefined" && page_param.search_text != "" ){ //검색이라면
        param = {
            ctl : "AdminMenu3",
            param1 : "request_product_list",
            search_type : page_param.search_type,
            search_text : page_param.search_text,
            tab : data.tab
        }
    }else{
        param = {
            ctl : "AdminMenu3",
            param1 : "request_product_list",
            tab : data.tab
        }
        
    }
    obj.page = {
        page_count :10,
        page_size : 10,
        move_page : obj.value.data.move_page,
        move_list : JSON.stringify(obj.value.data),
    }
    
    for(var key in param){
        obj.page[key]=param[key];
    }

    obj.fn.page_list_v2({
        page_num : {
            elem : obj.elem.paging,
            page_name : "move_page",
            prev_first : '<div class="page_item arrow prev">«</div>',
            prev_one : '<div class="page_item arrow prev">‹</div>',
            number_active : '<div class="page_item active"></div>',
            number : '<div class="page_item "></div>',
            next_one : '<div class="page_item arrow next">›</div>', 
            next_last : '<div class="page_item arrow next">»</div>'
        },
        havior : function(result){
            $('.loading').fadeOut();
            init_list(result.value);
            init_other_tab(result.other_category_count);
            lb.getElem("total_count").innerHTML = "전체";
            // 탭 카운드 개수 입력
            // obj.elem.all.children[0].innerHTML = "전체("+result.total+")";
            // obj.elem.second.children[0].innerHTML = "답변완료("+result.answer_count+")";
            // obj.elem.third.children[0].innerHTML = "미답변("+result.unanswer_count+")";
        }
    });
}

function init_other_tab(tab_data){
    lb.getElem("indicate").innerHTML = "표시(" + tab_data["indicate"] + ")";
    lb.getElem("not_indicate").innerHTML = "미표시(" + tab_data["not_indicate"] + ")";
    lb.getElem(data["tab"]+"_li").classList.add("current");
}

function move_tab(number){
    if(number == 0){ // 전체
        location.href = "?ctl=move&param=adm&param1=menu3_inquiry&move_page=1&project_name=synicsray&tab=total";
    }else if(number == 1){ // indicate
        location.href = "?ctl=move&param=adm&param1=menu3_inquiry&move_page=1&project_name=synicsray&tab=indicate";
    }else if(number == 2){ // not_indicate
        location.href = "?ctl=move&param=adm&param1=menu3_inquiry&move_page=1&project_name=synicsray&tab=not_indicate";
    }
}

function init_list(datas){
    // lb.clear_wrap(lb.getElem("wrap"));
    if(datas.length == 0){//데이터가 없을경우
        lb.getElem("total_count").innerHTML = "전체";
        lb.getElem("wrap").innerHTML = '<tr><td colspan="9"  class="align-center table-nodata"><div class="table-nodata-con">등록된 내용이 없습니다.</div></td></tr>';
    }else{
        lb.auto_view({
            wrap : "wrap",
            copy : "copy",
            attr : '["data-attr"]',
            json : datas,
            havior : add_list,
        });
    }
}

function add_list(elem, data, name, copy_elem){
    if(copy_elem.getAttribute("data-copy") != ""){
        copy_elem.setAttribute("data-copy", "");
    }
    if(name == "num"){
        elem.innerHTML = obj.fn.page_calc();
    }else if(name == "thumnail_file"){
        elem.src =  obj.link.product_thumnail_orign_path + data.thumnail_file;
        $(elem.parentNode).mouseenter(function(){
            $(this).clone().addClass('cloned').appendTo(this);
        })
        $(elem.parentNode).mouseleave(function(){
            $(".cloned").remove();
        })
    }else if(name == "state"){ //
        var state = data["lang_state"];
        var value = "미표시";
        if(state == 1){
            value = "표시";
        }else if(state == 2){
            value = "미표시";
        }
        elem.innerHTML = value;
    }else if(name == "btn_modify"){
        elem.onclick = function(){
            location.href="?ctl=move&param=adm&param1=menu3_pd_upload_modify&product_idx=" + data["idx"] + "&prev_page_move=" + globalThis.data.move_page;
        }
    }else if(name == "regdate"){
        var date = lb.getDate(data[name]);
        var dateString = "";
        var year = date.getFullYear();              //yyyy
        var month = (1 + date.getMonth());          //M
        month = lb.leadingZeros(month,2);  //month 두자리로 저장
        var day = date.getDate();                   //d
        day = lb.leadingZeros(day,2);          //day 두자리로 저장
        dateString = year + '-' + month + '-' + day;
        elem.innerHTML = dateString;
    }else if(name == "modify_regdate"){
        var date = lb.getDate(data["regdate"]);
        if(data[name] != null){
            date = lb.getDate(data["modify_regdate"]);
        }
        
        var dateString = "";
        var year = date.getFullYear();              //yyyy
        var month = (1 + date.getMonth());          //M
        month = lb.leadingZeros(month,2);  //month 두자리로 저장
        var day = date.getDate();                   //d
        day = lb.leadingZeros(day,2);          //day 두자리로 저장
        dateString = year + '-' + month + '-' + day;
        elem.innerHTML = dateString;
    }else if(name == "category_view"){
        var category_data = data["category_data"];
        if(typeof(category_data) != "undefined"){ //설정된 카테고리가 없을경우 체크
            
            for(var i=0; i<category_data.length; i++){
                //카테고리가 있을경우 p태그 생성 '<p class="ct">스마트폰 거치대 > 핑거밴드</p>';
                var elem_p = lb.createElem("p");
                elem_p.classList.add("ct");


                var category_string = ""; //카테고리 문자열 만들기
                if(obj.value.category_count == 0){ // 0이면 대분류(메인카테고리)만 사용
                    category_string = category_data[i]["main_category_name"];
                }else if(obj.value.category_count == 1){ //1이면 대분류 -> 중분류 까지 사용
                    category_string = category_data[i]["main_category_name"];
                    category_string = category_string + " > " + category_data[i]["category_1_name"];
                }else if(obj.value.category_count == 2){
                    category_string = category_data[i]["main_category_name"];
                    category_string = category_string + " > " + category_data[i]["category_1_name"];
                    category_string = category_string + " > " + category_data[i]["category_2_name"];
                }else if(obj.value.category_count == 3){
                    category_string = category_data[i]["main_category_name"];
                    category_string = category_string + " > " + category_data[i]["category_1_name"];
                    category_string = category_string + " > " + category_data[i]["category_2_name"];
                    category_string = category_string + " > " + category_data[i]["category_3_name"];
                }

                elem_p.innerHTML = category_string;
                elem.appendChild(elem_p);
            }
        }
        
    }else if(name == "btn_option"){
        elem.onclick = function(){
            location.href="?ctl=move&param=adm&param1=menu3_pd_option&product_idx=" + data["idx"];
        }
    }else if(name == "product_name"){
        if(data["admin_product_name"] == null){
            elem.innerHTML = data["product_name"];
        }else{
            elem.innerHTML = data["admin_product_name"];
        }

        
        elem.onclick = function(){
            location.href="?ctl=move&param=adm&param1=menu3_pd_upload_modify&product_idx=" + data["idx"];
        }

    }else{
        elem.innerHTML = data[name];
    }
}

//검색
function search(){
    var search_type = lb.getElem("search_type").value; //검색 타입
    var search_text = lb.getElem("search_text").value;//검색 단어
    if(search_text == ""){ //검색어 없을경우
        gu.alert({
            description : "검색어를 입력해주세요", //내용(string 문자열) 
            title : null,  //제목(string 문자열) 
            response_method : null //확인버튼을 눌럿을경우 호출될 메소드function 이름(string 문자열) 
        });
    }else{
        location.href="?ctl=move&param=adm&param1=menu3_inquiry&search_type=" + search_type + "&search_text=" + search_text;
    }
}

//검색 초기화
function init_search(){
    location.href="?ctl=move&param=adm&param1=menu3_inquiry";
}


//상품 적립금 일괄 변경하기 버튼
function open_product_point_modal(){
    obj.elem.product_point_modal.style.display = "block";
}

function close_product_point_modal(){
    obj.elem.product_point_modal.style.display = "none";
    obj.elem.change_product_point.value = "";
}

function modify_product_point_confirm(){
    gu.confirm({
        description : "모든 상품의 상품별 적립금을 변경하시겠습니까?", //내용(string 문자열) 필수
        title : null,  //제목(string 문자열) null이면 "알림"으로 처리함
        positive_method : "modify_product_point", //예를 눌렀을경우 호출될 메소드function 이름(string 문자열) null일 경우 메소드를 실행하지않음
        negative_method : null, //아니오를 눌렀을경우 호출될 메소드function 이름(string 문자열) null일 경우 메소드를 실행하지않음
        positive_param : null, //예를 눌렀을경우 호출될 메소드function에게 전달할 데이터(json 데이터형)
        negative_param : null, //아니오를 눌렀을경우 호출될 메소드function에게 전달할 데이터(json 데이터형)
    });
}

function modify_product_point(){
    lb.ajax({
        type:"JsonAjaxPost",
        list : {
            ctl:"AdminMenu3",
            param1:"modify_product_point",
            point : obj.elem.change_product_point.value, //변경할 상품별 적립금 %
        },
        action : lb.obj.address, //웹일경우 ajax할 주소
        havior : function(result){
            //웹일 경우 호출될 메소드
            // console.log(result);
            result = JSON.parse(result);
            if(result.result == "1"){
                gu.alert({
                    description : "모든 상품의 상품별 적립금이 변경되었습니다.", //내용(string 문자열) 
                    title : null,  //제목(string 문자열) 
                    response_method : "reload" //확인버튼을 눌럿을경우 호출될 메소드function 이름(string 문자열) 
                });
            }
        }    
    });
}

function reload(){
    location.reload();
}
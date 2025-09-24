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

var lang_elem = []; //언어 버튼 elem

var type = 1; //배너 종류
var number = 1;//번호 순서

var popup_relation_array = []; //배너 순서를 위한 array
var select_popup_idx = null;
var elem_select_tr = null;
var now_datas = null; //현재 데이터(초기화 버튼을 위해 필요)
var now_lang = null; //현재 언어

function move_upload(){
    location.href="?ctl=move&param=adm&param1=menu1_popup_upload";
}

function webviewReady(){
    request_lang_list();
}

//View init을 위해 등록된 언어 목록 가져오기
function request_lang_list(){
    lb.ajax({
        type:"JsonAjaxPost",
        list : {
            ctl:"Lang",
            param1:"request_lang_list",
        },
        action : lb.obj.address, //웹일경우 ajax할 주소
        response_method : "response_lang_list", //앱일경우 호출될 메소드
        havior : function(result){
            //웹일 경우 호출될 메소드
            // console.log(result);
            result = JSON.parse(result);
            response_lang_list(result);
        }    
    });
}

function response_lang_list(result){
    if(result.result == "1"){
        init_lang_view(result.value);
    }else{
        alert(result.message);
    }
}

function init_lang_view(lang_data){
    console.log(lang_data);
    // var elem_select = lb.getElem("lang_select");
    // for(var i=0; i<lang_data.length; i++){
    //     var elem_option = lb.createElem("option");
    //     elem_option.text = lang_data[i].name;
    //     elem_option.value = lang_data[i].idx;
    //     elem_select.add(elem_option);
    // }

    // elem_select.onchange = function(){
    //     request_popup_list(this.value);
    // }

    lb.auto_view({
        wrap: "lang_wrap",
        copy: "lang",
        attr: '["data-attr"]',
        json: lang_data,
        havior: function(elem, data, name, copy_elem){
            if(copy_elem.getAttribute("data-copy") != ""){
                copy_elem.setAttribute("data-copy", "");
                copy_elem.setAttribute("lang_idx", data["idx"]);
                lang_elem.push(copy_elem);
                copy_elem.onclick = function(){
                    request_popup_list(this.getAttribute("lang_idx"));
                }
            }
            if(name == "name"){
                elem.innerHTML = data[name];
            }
        },
        end : function(){
            //첫번째 언어 데이터 조회
            request_popup_list(1);
        }
    });
    
}

function change_lang(lang){
    for(var i=0; i<lang_elem.length; i++){
        lang_elem[i].classList.remove("current");
        if(lang_elem[i].getAttribute("lang_idx") == lang){
            lang_elem[i].classList.add("current");
        }
    }
}

function request_popup_list(lang){
    change_lang(lang);
    now_lang = lang;
    lb.ajax({
        type:"JsonAjaxPost",
        list : {
            ctl:"AdminMenu1",
            param1:"request_popup_list",
            lang: lang
        },
        action : lb.obj.address, //웹일경우 ajax할 주소
        response_method : "request_popup_list", //앱일경우 호출될 메소드
        havior : function(result){
            //웹일 경우 호출될 메소드
            console.log(result);
            result = JSON.parse(result);
            response_popup_list(result);
        }    
    });
}

function response_popup_list(result){
    if(result.result == "1"){
        init_table(result.value);
    }else{
        alert(result.message);
    }
}

//등록된 배너 뿌리기
function init_table(datas){
    now_datas = datas;
    number = 1;
    lb.clear_wrap(lb.getElem("wrap"));
    if(datas.length == 0){
        lb.getElem("wrap").innerHTML = '<tr><td colspan="7" style="text-align:center">등록된 팝업이 없습니다.</td></tr>';   
    }else{
        lb.auto_view({
            wrap: "wrap",
            copy: "copy",
            attr: '["data-attr"]',
            json: datas,
            havior: add_popup,
            end : function(){
                init_select();
            }
        });
    }
    
}

function add_popup(elem, data, name, copy_elem){
    if(copy_elem.getAttribute("data-copy") != ""){
        copy_elem.setAttribute("data-copy", "");
        copy_elem.id = "popup_" + data["idx"];
        copy_elem.setAttribute("popup_idx", data["idx"]);
        popup_relation_array.push(data["idx"]);
        copy_elem.onclick = function(){ //tr을 클릭했을경우
            select_popup(this.getAttribute("popup_idx"));
        }
    }
    if(name == "checkbox"){
        console.log("checkbox");
        elem.name = "checkbox[]";
        elem.value = data["idx"];
        elem.onclick = function(){
            un_check(this);//전체 체크가 체크일 경우 
        }
    }else if(name == "img_pc_popup"){
        elem.src = obj.link.pc_popup_img_origin_path + data["pc_file_name"];
        $(elem.parentNode).mouseenter(function(){
            $(this).clone().addClass('cloned').appendTo(this);
        })
        $(elem.parentNode).mouseleave(function(){
            $(".cloned").remove();
        })
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
    }else if(name == "number"){
        elem.innerHTML = number;
        number = number+1;
    }else if(name == "is_use"){
        elem.value = data["is_use"];
        elem.setAttribute("popup_idx", data["idx"]);
        elem.classList.add("select-custom");
        $(elem).trigger("change");
        elem.onchange = function(){
            request_popup_is_use_change(this.getAttribute("popup_idx"),this.value, this);
        }
    }else if(name == "name"){
        elem.innerHTML = data.name;

        elem.onclick = function(){
            location.href = "?param=adm&param1=menu1_popup_modify&idx=" + data.idx;
        }
    }
    else{
        elem.innerHTML = data[name];
    }
}

function init_select(){
    $('.select-custom').select2({
        minimumResultsForSearch: -1
    });

    $('.tog-btn').click(function(){
        $('.tog-btn').toggleClass('open')
        $('.tog-container').toggle()
    });
}

function un_check(elem){
    var elem_all_check = lb.getElem("all_check");
    if(elem.checked == false &&  elem_all_check.checked == true){ 
        elem_all_check.checked = false;
    }
}

function all_check_list(elem){
    var checkboxs = document.getElementsByName("checkbox[]");
    if(elem.checked){
        for(var i=0; i<checkboxs.length; i++){
            console.log(checkboxs[i]);
            checkboxs[i].checked = true;
        }
    }else{
        for(var i=0; i<checkboxs.length; i++){
            checkboxs[i].checked = false;
        }
    }
}

function request_delete_popup(){
    //삭제할게있는지 확인
    var elem_checkboxs = document.getElementsByName("checkbox[]");
    console.log(elem_checkboxs);
    var delete_idxs = [];
    for(var i=0; i<elem_checkboxs.length; i++){
        if(elem_checkboxs[i].checked){
            delete_idxs.push(elem_checkboxs[i].value);
        }
    }
    
    if(delete_idxs.length > 0){ //삭제할게 있다면
        lb.ajax({
            type:"JsonAjaxPost",
            list : {
                ctl:"AdminMenu1",
                param1:"request_delete_popup",
                delete_idxs : JSON.stringify(delete_idxs)
            },
            action : lb.obj.address, //웹일경우 ajax할 주소
            response_method : "response_popup_delete", //앱일경우 호출될 메소드
            havior : function(result){
                //웹일 경우 호출될 메소드
                console.log(result);
                result = JSON.parse(result);
                response_popup_delete(result);
            }    
        });
    }else{
        alert("삭제할 팝업을 체크해주세요");
    }

    
}


function response_popup_delete(result){
    if(result.result == "1"){
        request_popup_list(now_lang);
    }else{
        alert(result.message);
    }
}

//제품 선택
function select_popup(popup_idx){
    var elem_tr = lb.getElem("popup_" + popup_idx);
    if(elem_tr != null){
        if(elem_select_tr != null){
            elem_select_tr.style.backgroundColor = "white";
        }
        select_popup_idx = popup_idx;
        elem_select_tr = elem_tr;
        elem_tr.style.backgroundColor = "#d8dce0";
    }
}

function check_select_tr(){
    if(elem_select_tr == null){ //선택된게 없다면
        alert("순서를 변경할 배너를 선택해주세요");
        return false;
    }else{
        return true;
    }
}

//위로
function btn_up(){
    var move_count = lb.getElem("move_count").value;
    if(move_count == ""){
        move_count = 1;
        lb.getElem("move_count").value = 1;
    }
    if(check_select_tr()){
        for(var i=0; i<move_count; i++){
            var $tr = $(elem_select_tr);  // 클릭한 버튼이 속한 tr 요소
            $tr.prev().before($tr);
        }
        
    }
}

//아래로
function btn_down(){
    var move_count = lb.getElem("move_count").value;
    if(move_count == ""){
        move_count = 1;
        lb.getElem("move_count").value = 1;
    }

    if(check_select_tr()){
        for(var i=0; i<move_count; i++){
            var $tr = $(elem_select_tr);  // 클릭한 버튼이 속한 tr 요소
            $tr.next().after($tr); 
        }
        
    }
}

//맨위로
function btn_top(){
    if(check_select_tr()){
        var $tr = $(elem_select_tr);  // 클릭한 버튼이 속한 tr 요소
        $tr.closest('tbody').find('tr:first').before($tr);
    }
    
}

//마지막으로
function btn_end(){
    if(check_select_tr()){
        var $tr = $(elem_select_tr);  // 클릭한 버튼이 속한 tr 요소
        $tr.closest('tbody').find('tr:last').after($tr);
    }
}

//초기화
function btn_init(){
    if(now_datas != null){
        init_table(now_datas);
    }
}


//순서 적용하기
function btn_save(){
    var elem_tbody = lb.getElem("wrap"); //tbody가져와서 tr에 있는것들 순서대로 relation_idx를 만듬
    var childs = elem_tbody.children; //tbody에 있는 tr배열
    if(childs.length == 0){
        alert("적용할 제품이 없습니다.");
    }else{
        var relation_array = [];
        //tr에 있는 relation_idx값을 가져와서 배열에 담음
        for(var i=0; i<childs.length; i++){
            relation_array.push(childs[i].getAttribute("popup_idx"));
        }
        //원래 배열과 비교해서 변경된게 있는지 확인후 변경된게 있다면 api호출
        if(JSON.stringify(popup_relation_array) == JSON.stringify(relation_array)){ //변경할게 없다면
            alert("순서 변동이 없습니다.");
        }else{
            //변경 api 전송
            request_banner_relation_change(relation_array);
        }
        
    }
}

//순서 변경
function request_banner_relation_change(relation_array){
    lb.ajax({
        type:"JsonAjaxPost",
        list : {
            ctl:"AdminMenu1",
            param1:"request_popup_relation_change",
            relation_array : JSON.stringify(relation_array),
        },
        action : lb.obj.address, //웹일경우 ajax할 주소
        response_method : "response_popup_relation_change", //앱일경우 호출될 메소드
        havior : function(result){
            //웹일 경우 호출될 메소드
            console.log(result);
            result = JSON.parse(result);
            response_popup_relation_change(result);
        }    
    });
}

function response_popup_relation_change(result){
    if(result.result == "1"){
        alert("적용되었습니다");
        refresh_popup_list({value:now_lang});
    }else{
        alert(result.message);
    }
}

//제품 새로고침
function refresh_popup_list(json){
    request_popup_list(json.value);
}


function request_popup_is_use_change(popup_idx, is_use, elem){
    lb.ajax({
        type:"JsonAjaxPost",
        list : {
            ctl:"AdminMenu1",
            param1:"request_popup_is_use_change",
            popup_idx : popup_idx,
            is_use : is_use,
            lang_idx : now_lang,
        },
        action : lb.obj.address, //웹일경우 ajax할 주소
        response_method : "response_popup_is_use_change", //앱일경우 호출될 메소드
        havior : function(result){
            //웹일 경우 호출될 메소드
            console.log(result);
            result = JSON.parse(result);
            response_popup_is_use_change(result, elem);
        }    
    });
}

function response_popup_is_use_change(result, elem){
    if(result.result == "1"){
        if(result.flag == 0){ //미사용 -> 사용 변경 실패시 다시 미사용으로 value 값 변경
            elem.value = 0;
            $('.select-custom').select2({
                minimumResultsForSearch: -1
            });
        }
        alert(result.message);
    }else{
        alert(result.message);
    }
} 


const delete_confirm = () =>{
    if(confirm("정말로 삭제하시겠습니까?")) {
        request_delete_popup();
    }
}


function request_delete_popup(){
    //삭제할게있는지 확인
    var elem_checkboxs = document.getElementsByName("checkbox[]");
    var delete_idxs = [];
    for(var i=0; i<elem_checkboxs.length; i++){
        if(elem_checkboxs[i].checked){
            delete_idxs.push(elem_checkboxs[i].value);
        }
    }
    // console.log(delete_idxs);
    // return
    
    if(delete_idxs.length > 0){ //삭제할게 있다면
        lb.ajax({
            type:"JsonAjaxPost",
            list : {
                ctl:"AdminMenu1",
                param1:"request_delete_popup",
                delete_idxs : JSON.stringify(delete_idxs)
            },
            action : lb.obj.address, //웹일경우 ajax할 주소
            response_method : "response_popup_delete", //앱일경우 호출될 메소드
            havior : function(result){
                //웹일 경우 호출될 메소드
                console.log(result);
                result = JSON.parse(result);
                response_popup_delete(result);
            }    
        });
    }else{
        alert("삭제할 팝업을 체크해주세요");
    }

    
}

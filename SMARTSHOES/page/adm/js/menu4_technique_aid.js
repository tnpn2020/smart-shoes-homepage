$(document).ready(function(){
    elem_init(); // html 태그 초기세팅
    page_init(); // 페이지 ajax 통신
})

// get방식 페이지 파라미터 값
var page_param = data;


// 태그 초기셋팅
function elem_init(){


    // 검색버튼 클릭함수 초기값 설정
    var select_btn = document.getElementById('select_btn');
    select_btn.onclick = function(){
        // 상세 검새 체크박스가 true 일때
        page_init();
    }

    // 초기화 버튼 클릭함수 초기화 설정
    var init_btn= document.getElementById('init_btn');
    init_btn.onclick = function(){
        // 첫페이지로 이동
        obj.value.data.move_page = 1;
        // 검색 분류 초기값 - > 제목
        obj.elem.search_kind.value = "title";
        // select2 라이브러리 change 이벤트 연결
        $(obj.elem.search_kind).trigger("change");
        // 검색어 초기화
        obj.elem.keyword.value = "";
        // 입력된 초기값으로 리스트 다시 불러오기
        page_init();
    }

    
    // 탭 초기값 설정
    if(null_exception(page_param.tab)){
        if(page_param.tab == "all"){
            obj.elem.all.classList.add("current");
        }else if(page_param.tab == "second"){
            obj.elem.second.classList.add("current");
        }else if(page_param.tab == "third"){
            obj.elem.third.classList.add("current");
        }else{
            obj.elem.all.classList.add("current");
        }
    }else{
        obj.elem.all.classList.add("current");
    }


    
    var tab_elems = obj.elem.all.parentNode.children;
    for(var i = 0; i<tab_elems.length; i++){
        // 탭에 따른 리스트 뿌리기
        tab_elems[i].onclick = function(){
            page_param.move_page = 1;
            page_param.tab = this.id;
            page_init();
        }
    }
    // 라디오박스 전체 요소
    var radio_elem = $("input[name='condition']");
    // 상세 검색 체크 박스 초기갑 설정( 체크 되면 상세 검색창 on )
    $(obj.elem.detail_search_check).on('change', function(){
        if(this.checked ==  true){
            obj.elem.detail_search_content.style.display = "block";
        }else{
            obj.elem.detail_search_content.style.display = "none";
            // 상세검색 체크 풀시 상세검색창 초기화
            // 상세검색 기간 초기화
            obj.elem.all_search.checked = true;
            $(obj.elem.all_search).trigger("change");
            // 상세검색 분류 초기화
            obj.elem.select_kind.value = "0";
            $(obj.elem.select_kind).trigger("change");
        }
    })

    // 상세검색 체크박스 세팅(검색이후)
    if(null_exception(page_param.detail_search_check)){
        if(page_param.detail_search_check == "1"){
            obj.elem.detail_search_check.checked = true;
        }else{
            obj.elem.detail_search_check.checked = false;
        }
        $(obj.elem.detail_search_check).trigger("change");
    }

    if(null_exception(page_param.select_kind)){
        obj.elem.select_kind.value = page_param.select_kind;
        $(obj.elem.select_kind).trigger('change');
    }
    
    // 검색기간 초기값 설정(검색이후)
    if(null_exception(page_param.start_date)){
        if(null_exception(page_param.date_kind)){
            obj.elem[page_param.date_kind].checked = true;
            $(obj.elem[page_param.date_kind]).trigger("change");
        }else{
            for(var i= 0; i<radio_elem.length; i++){
                radio_elem[i].checked = false;
            }
            obj.elem.start_date = page_param.start_date;
            obj.elem.end_date = page_param.end_date;
        }
    }
}

function page_init(){
    $('.loading').fadeIn();
    $(obj.elem.paging).empty();
    obj.value.data = page_param;

    // 검색시 파라미터값으로 넘김(검색어, 검색분류)
    obj.value.data.keyword = obj.elem.keyword.value;
    obj.value.data.search_kind = obj.elem.search_kind.value;

    // 탭 초기값 설정
    var tab_elems = obj.elem.all.parentNode.children;
    for(var i= 0; i<tab_elems.length; i++){
        if(tab_elems[i].classList.contains("current")){
            tab_elems[i].classList.remove("current");
        }
    }

    // 창 파라미터 값에 따른 탭 지정
    if(null_exception(page_param.tab)){
        if(page_param.tab == "all"){
            obj.elem.all.classList.add("current");
        }else if(page_param.tab == "second"){
            obj.elem.second.classList.add("current");
        }else if(page_param.tab == "third"){
            obj.elem.third.classList.add("current");
        }else{
            obj.elem.all.classList.add("current");
        }
    }else{
        obj.elem.all.classList.add("current");
    }

    var param = {
        ctl : "AdminMenu4",
        param1 : "technique_aid_list",
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
            // 탭 카운드 개수 입력
            obj.elem.all.children[0].innerHTML = "전체("+result.total+")";
            obj.elem.second.children[0].innerHTML = "답변완료("+result.answer_count+")";
            obj.elem.third.children[0].innerHTML = "미답변("+result.unanswer_count+")";
        }
    });
}

function init_list(datas){
    lb.clear_wrap(lb.getElem("wrap"));
    if(datas.length == 0){//데이터가 없을경우
        lb.getElem("wrap").innerHTML = '<tr><td colspan="8"  class="align-center table-nodata"><div class="table-nodata-con">등록된 내용이 없습니다.</div></td></tr>';
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
    }else if(name == "title"){
        elem.innerHTML = data.title;
        var idx = {technique_aid_idx: data.idx};
        elem.onclick = function(){
            gu.confirm({
                description : "기술지원 상세 내용을 확인하시겠습니까?", //내용(string 문자열) 필수
                title : null,  //제목(string 문자열) null이면 "알림"으로 처리함
                positive_method : "technique_aid_detail", //예를 눌렀을경우 호출될 메소드function 이름(string 문자열) null일 경우 메소드를 실행하지않음
                negative_method : null, //아니오를 눌렀을경우 호출될 메소드function 이름(string 문자열) null일 경우 메소드를 실행하지않음
                positive_param : JSON.stringify(idx), //예를 눌렀을경우 호출될 메소드function에게 전달할 데이터(json 데이터형)
                negative_param : null, //아니오를 눌렀을경우 호출될 메소드function에게 전달할 데이터(json 데이터형)
            });
        }
    }else if(name == "name"){
        elem.innerHTML = data.name;
    }else if(name == "company"){
        elem.innerHTML = data.company;
    }else if(name == "email"){
        elem.innerHTML = data.email;
    }else if(name == "regdate"){
        if(null_exception(data.regdate)){
            elem.innerHTML = date_format(data.regdate);
        }
    }else if(name == "is_answer") {
        elem.value = data["type"];
        elem.setAttribute("technique_aid_idx", data["idx"]);
        elem.classList.add("select-custom");
        $(elem).trigger("change");
        elem.onchange = function(){
            request_technique_aid_is_answer_change(this.getAttribute("technique_aid_idx"),this.value);
        }
    }
}


function date_format(data){
    var year = data.substring(2,4);
    var month = data.substring(5, 7);
    var day = data.substring(8, 10);
    var date = data.substring(11, 19);

    return year + "." + month + "." + day + " " + date;
}

function technique_aid_detail(json) {
    json = JSON.parse(json);
    location.href = "?ctl=move&param=adm&param1=menu4_technique_aid_detail&technique_aid_idx=" + json.technique_aid_idx;
}

function request_technique_aid_is_answer_change(technique_aid_idx, is_answer) {
    lb.ajax({
        type:"JsonAjaxPost",
        list : {
            ctl:"AdminMenu4",
            param1:"request_technique_aid_is_answer_change",
            target : technique_aid_idx,
            is_answer : is_answer
        },
        action : lb.obj.address, //웹일경우 ajax할 주소
        response_method : "response_technique_aid_is_answer_change", //앱일경우 호출될 메소드
        havior : function(result){
            //웹일 경우 호출될 메소드
            // console.log(result);
            result = JSON.parse(result);
            response_technique_aid_is_answer_change(result);
        }    
    });
}

function response_technique_aid_is_answer_change(result){
    if(result.result == "1"){
        gu.alert({
            description : "적용되었습니다", //내용(string 문자열) 
            title : null,  //제목(string 문자열) 
            response_method : "page_reload", //확인버튼을 눌럿을경우 호출될 메소드function 이름(string 문자열) 
            response_param : null
        });
    }else{
        gu.alert({
            description : result.message, //내용(string 문자열) 
            title : null,  //제목(string 문자열) 
            response_method : null //확인버튼을 눌럿을경우 호출될 메소드function 이름(string 문자열) 
        });
    }
} 

function page_reload() {
    location.reload();
}
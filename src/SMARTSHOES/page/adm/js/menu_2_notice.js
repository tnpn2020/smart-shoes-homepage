$(document).ready(function(){
    elem_init(); // html 태그 초기세팅
    page_init(); // 페이지 ajax 통신
    console.log(data)
})


// get방식 페이지 파라미터 값
var page_param = data;

// 태그 초기셋팅
function elem_init(){
    // 검색버튼 클릭함수 초기값 설정
    // var select_btn = document.getElementById('select_btn');
    // select_btn.onclick = function(){
    //     page_init();
    // }
    // 초기화 버튼 클릭함수 초기화 설정
    // var init_btn= document.getElementById('init_btn');
    // init_btn.onclick = function(){
    //     // 첫페이지로 이동
    //     obj.value.data.move_page = 1;
    //     // 검색 분류 초기값 - > 제목
    //     obj.elem.search_kind.value = "title";
    //     // select2 라이브러리 change 이벤트 연결
    //     $(obj.elem.search_kind).trigger("change");
    //     // 검색어 초기화
    //     obj.elem.keyword.value = "";
    //     // 입력된 초기값으로 리스트 다시 불러오기
    //     page_init();
    // }
}


function page_init(){
      // 일자별 검색창 기본값 오늘날짜 설정
    // Get references to date input fields
    const start_date = document.getElementById("start_date");
    const end_date = document.getElementById("end_date");

    // Get references to radio buttons
    const allRadio = document.getElementById("all_search");
    const todayRadio = document.getElementById("date_01");
    const oneMonthRadio = document.getElementById("date_02");
    const threeMonthsRadio = document.getElementById("date_03");
    const SixMonthsRadio = document.getElementById("date_04");
    allRadio.addEventListener("click", function () {
        const today = new Date().toISOString().split("T")[0];
        end_date.value = today;
        start_date.value = "";
    });
    todayRadio.addEventListener("click", function () {
        const today = new Date().toISOString().split("T")[0];
        end_date.value = today;
        start_date.value = today;
    });

    oneMonthRadio.addEventListener("click", function () {
        const today = new Date();
        end_date.value = today.toISOString().split("T")[0];
        today.setMonth(today.getMonth() - 1); // Subtract 3 days
        start_date.value = today.toISOString().split("T")[0];
    });
    threeMonthsRadio.addEventListener("click", function () {
        const today = new Date();
        end_date.value = today.toISOString().split("T")[0];
        today.setMonth(today.getMonth() - 3); // Subtract 3 months
        start_date.value = today.toISOString().split("T")[0];
    });
    SixMonthsRadio.addEventListener("click", function () {
        const today = new Date();
        end_date.value = today.toISOString().split("T")[0];
        today.setMonth(today.getMonth() - 6); // Subtract 3 days
        start_date.value = today.toISOString().split("T")[0];
    });
    console.log(data.date)
    if(data.date){
        obj.elem[data.date].checked=true;
        obj.elem.start_date.value = data.start_date;
        obj.elem.end_date.value = data.end_date;
    }else{
        obj.elem.all_search.click();
    }
    if(data.keyword){
        obj.elem.keyword.value = data.keyword;
    }
    if(data.search_kind){
        obj.elem.search_kind.value = data.search_kind;
    }
    if(data.category){
        obj.elem.category_filter.value = data.category;
    }

    
    // $('.loading').fadeIn();
    $(obj.elem.paging).empty();
    obj.value.data = page_param;
    // 검색시 파라미터값으로 넘김(검색어, 검색분류, 카테고리)
    obj.value.data.keyword = obj.elem.keyword.value;
    obj.value.data.search_kind = obj.elem.search_kind.value;
    obj.value.data.category = obj.elem.category_filter ? obj.elem.category_filter.value : "";
    
    
    var param = {
        ctl : "AdminMenu1",
        param1 : "notice_list",
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
    // console.log(page_param)
    // return
    
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
            console.log(result)
            // obj.elem.total_count.innerHTML = result.total_count;
            init_list(result.value);
        }
    });
}


// 행 번호 계산을 위한 변수
var current_row_index = 0;

function init_list(datas){
    // 행 번호 인덱스 초기화
    current_row_index = 0;
    
    lb.clear_wrap(lb.getElem("wrap"));
    if(datas.length == 0){//데이터가 없을경우
        lb.getElem("wrap").innerHTML = '<tr><td colspan="5"  class="align-center table-nodata"><div class="table-nodata-con">등록된 공지사항이 없습니다.</div></td></tr>';
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

// 행 번호 계산 함수
function calculate_row_number() {
    var row_number = obj.page.total_count - ((obj.page.move_page - 1) * obj.page.page_size) - current_row_index;
    current_row_index++;
    return row_number;
}

function add_list(elem, data, name, copy_elem){
    if(copy_elem.getAttribute("data-copy") != ""){
        copy_elem.setAttribute("data-copy", "");
    }

    if(name == "checkbox"){
        console.log(data)
        elem.value = data.notice_idx;
        elem.setAttribute('class','checkbox');
        elem.onchange = function(){
            select_check(this);
        }
    }else if(name == "number"){
        elem.innerHTML = calculate_row_number();
    }else if(name == "category"){
        // 카테고리 텍스트 설정
        var categoryText = getCategoryText(data.category);
        elem.innerHTML = categoryText;
        
        // 카테고리별 스타일 적용
        elem.className = "badge category-" + (data.category || 'general');
    }else if(name == "title"){
        elem.innerHTML = data.title;
        elem.onclick = function(){
            if(confirm("공지사항 수정페이지로 이동하시겠습니까?")){
                move_modify_page({target : data.notice_idx});
            }
        }
    }else if(name == "regdate"){
        if(null_exception(data.regdate)){
            elem.innerHTML = date_format(data.regdate);
        }
    }else if(name == "btn_modify"){
        elem.onclick = function(){
            if(confirm("공지사항 수정페이지로 이동하시겠습니까?")){
                move_modify_page({target : data.notice_idx});
            }
        }
    }
}

// 카테고리 텍스트 변환 함수
function getCategoryText(category) {
    const categoryMap = {
        'important': '중요',
        'service': '서비스 안내',
        'update': '업데이트',
        'event': '이벤트'
    };
    return categoryMap[category] || '일반';
}


function date_format(data){
    var year = data.substring(2,4);
    var month = data.substring(5, 7);
    var day = data.substring(8, 10);
    var date = data.substring(11, 19);

    return year + "." + month + "." + day + " " + date;
}

function register_btn(){
    
    if(confirm("공지사항 등록페이지로 이동하시겠습니까?")){
        move_register_page();
    }

}

function move_modify_page(json){
    location.href = "?ctl=move&param=adm&param1=menu2_notice_modify&idx="+json.target;
}

// function move_register_page(){
//     location.href = "?ctl=move&param=adm&param1=crm_notice_upload";
// }

function all_check_list(elem){
    var checkbox_elems = obj.elem.wrap.querySelectorAll('input[type="checkbox"]')
    for(var i = 0; i<checkbox_elems.length; i++){
        if(elem.checked == true){
            checkbox_elems[i].checked = true;
        }else{
            checkbox_elems[i].checked = false;
        }
    }
}

function select_check(elem){
    var checkbox_elems = document.getElementsByClassName('checkbox');
    // obj.elem.all_check_list => 전체 체크 박스
    if(elem.checked == false){
        obj.elem.all_check.checked = false;
    }else{
        var all_check_bool = true;
        for(var i = 0; i<checkbox_elems.length; i++){
            if(checkbox_elems[i].checked == false){
                all_check_bool = false;
                break;
            }
        }

        if(all_check_bool == true){
            obj.elem.all_check.checked = true;
        }

    }
}

function remove_btn(){
    var checkbox_elems = document.getElementsByClassName('checkbox');
    var target = [];
    for(var i = 0; i<checkbox_elems.length; i++){
        if(checkbox_elems[i].checked == true){
            target.push(checkbox_elems[i].value);
        }
    }

    if(target.length == 0){
        alert("삭제하실 공지사항을 선택해주세요.");
    }else{
        if(confirm("선택하신 공지사항을 삭제하시겠습니까?")){
            request_delete_notice(target);
        }
    }
}

// 기존 함수는 삭제됨 - request_delete_notice 사용

function reponse_notice_remove(result){
    if(result.result == "1"){
        alert(result.message || "선택된 공지사항이 삭제되었습니다.");
        page_init(); // 페이지 새로고침
    }else{
        alert(result.message || "삭제 중 오류가 발생했습니다.");
    }
}
const move_upload = () => {
    window.location.href = "/?ctl=move&param=adm&param1=menu2_notice_upload"
}

const search = () => {
    // debugger
    var url = "/?ctl=move&param=adm&param1=menu2_notice";
    var filter = "";
    if(obj.elem.start_date.value != ""){
        filter += `&start_date=${obj.elem.start_date.value}&end_date=${obj.elem.end_date.value}`;
    }
    if(obj.elem.keyword.value != ""){
        filter += `&keyword=${obj.elem.keyword.value}&search_kind=${obj.elem.search_kind.value}`;
    }
    // 카테고리 필터 추가
    if(obj.elem.category_filter && obj.elem.category_filter.value != ""){
        filter += `&category=${obj.elem.category_filter.value}`;
    }

    // console.log()
    // if(!filter){
    //     gu.alert("검색 조건을 설정해주세요.");
    //     return;
    // }else{
        var selected_option_id = document.querySelector('input[name="duration"]:checked').id;
        filter += `&date=${selected_option_id}`;
        window.location.href = url + filter
    // }
}

const enterkey = () => {
    if(window.event.keyCode == 13){
        search();
    }
}

const search_init  = () => {
    window.location.href = "/?ctl=move&param=adm&param1=menu2_notice"
}

const delete_post = () => {
    var checked_list = obj.elem.wrap.querySelectorAll('input[type="checkbox"]:checked')
    var idx_list = [];
    
    // console.log(checked_list)
    if(checked_list.length > 0){
        checked_list.forEach(data => idx_list.push(data.value));
        if(confirm("선택한 게시글을 삭제하시겠습니까?")){
            request_delete_notice({target : idx_list});
        }
    }else{
        alert("삭제할 게시글을 선택해주세요.");
        return;
    }
}

const request_delete_notice = (target) => {
    $('.loading').fadeIn();
    lb.ajax({
        type:"JsonAjaxPost",
        list : {
            ctl:"AdminMenu1",
            param1:"request_delete_notice",
            target : JSON.stringify(target.target || target),
        },
        action : lb.obj.address,
        havior : function(result){
            console.log(result);
            $('.loading').fadeOut();
            result = JSON.parse(result);
            reponse_notice_remove(result);
        }    
    });
}
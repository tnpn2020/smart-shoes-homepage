$(document).ready(function(){
    obj.value.data = page_param;
    if(null_exception(page_param.inquiry_idx)){
        request_user_1to1_index();
    }else{
        page_init();
    }
})

// 페이지네이션 갯수(10개)
var page_count = 10;
// 페이지 리스트 사이즈
var page_size = 10;
// 문의 idx 가있을때 사용
var inquiry_num;

function request_user_1to1_index(){
    // 부모창에서 들어온 값이 inquiry_idx 파라미터 값이 있을때 
    lb.ajax({
        type : "JsonAjaxPost",
        list : {
            ctl : "AdminPopup",
            param1 : "user_1to1_index",
            target : page_param.inquiry_idx,
            user_idx : page_param.user_idx,
        },
        action : "index.php",
        havior : function(result){
            console.log(result);
            result = JSON.parse(result);
            if(result.result == 1){
                // 해당 idx가 몇번째 인지 호출후 현재 페이지 설정
                var num = result.value[0].num;
                if(num*1 < 10){
                    obj.value.data.move_page = 1;
                }else{
                    var move_page = num*1/page_size*1;
                    obj.value.data.move_page = Math.ceil(move_page);
                }
                // 아래에서 값을 지우기때문에 전역변수로 둔다
                inquiry_num = obj.value.data.inquiry_idx;
                // 이후 페이지네이션에서 영향을 끼치지 않게하기위해서 객체에서 키값 삭제
                delete obj.value.data.inquiry_idx;
                page_init();
            }
        }
    })
}

function page_init(){
    $('.loading').fadeIn();
    if(typeof(obj.value.data.word)!="undefined"){
        obj.value.word = obj.value.data.word;
    }

    var param = {
        ctl : "AdminPopup",
        param1 : "user_1to1",
        user_idx : page_param.user_idx,
    }

    obj.page = {
        page_count :page_count,
        page_size : page_size,
        move_page : obj.value.data.move_page,
        move_list : JSON.stringify(obj.value.data)
    }

    for(var key in param){
        obj.page[key]=param[key];
    }

    obj.fn.page_list({
        page_num : {
            elem : obj.elem.paging,
            page_name : "move_page",
            prev_first : '<div class="page_item arrow prev">«</div>',
            prev_one : '<div class="page_item arrow prev">‹</div>',
            number_active : '<div class="page_item active"></div>',
            number : '<div class="page_item ">2</div>',
            next_one : '<div class="page_item arrow next">›</div>', 
            next_last : '<div class="page_item arrow next">»</div>'
        },
        havior : function(value){
            init_list(value);
        }
    });
}

function init_list(datas){
    $('.loading').fadeOut();
    lb.clear_wrap(lb.getElem("wrap"));
    if(datas.length == 0){//데이터가 없을경우
        lb.getElem("wrap").innerHTML = '<tr><td colspan="6"  class="align-center table-nodata"><div class="table-nodata-con">등록된 내용이 없습니다.</div></td></tr>';
    }else{
        lb.auto_view({
            wrap: "wrap",
            copy: "copy",
            attr: '["data-attr"]',
            json: datas,
            havior: add_list,
            end : function(){
                if(null_exception(inquiry_num)){
                    // 해당 값이 있으면 리스트를 선택한다 onlcick 이벤트 연결
                    var target_elem = document.getElementById('inquiry_elem_'+inquiry_num).children[3].children[0].children[0].children[0];
                    $(target_elem).trigger('click');
                }
            }
        });
    }
}

function add_list(elem, data, name, copy_elem){
    if(copy_elem.getAttribute("data-copy") != ""){
        copy_elem.setAttribute("data-copy", "");
        copy_elem.setAttribute('class','copy_elem');
        copy_elem.setAttribute('id', 'inquiry_elem_'+data.idx);
    }
    if(name == "num"){
        elem.innerHTML = obj.fn.page_calc();
    }else if(name == "kind"){
        // 분류별 데이터
        if(data.kind == "1"){
            elem.innerHTML = "구매/결제";
        }else if(data.kind == "2"){
            elem.innerHTML = "주문문의";
        }else if(data.kind == "3"){
            elem.innerHTML = "취소신청";
        }else if(data.kind == "4"){
            elem.innerHTML = "교환/반품 신청";
        }else if(data.kind == "5"){
            elem.innerHTML = "환불신청";
        }else if(data.kind == "6"){
            elem.innerHTML = "배송";
        }else if(data.kind == "7"){
            elem.innerHTML = "계정";
        }else if(data.kind == "8"){
            elem.innerHTML = "기타";
        }
        //구매/결제 배송  계정
    }else if(name == "order_number"){
        elem.innerHTML = data.order_number;
    }else if(name == "title"){
        elem.innerHTML = data.title;
        elem.onclick = function(){
            init_qna_detail(this, data);
        }
    }else if(name == "regdate"){
        var date = date_format(data.regdate);
        elem.innerHTML = date;
    }else if(name == "answer_date"){
        if(null_exception(data.answer_date)){
            var date = date_format(data.answer_date);
            elem.innerHTML = date;    
        }else{
            elem.innerHTML = "미답변";
        }
    }
}

function init_qna_detail(elem, data){
    var all_elem = document.getElementsByClassName('copy_elem');
    var parent_elem = elem.closest('tr');
    refresh_elem();
    for(var i= 0; i<all_elem.length; i++){
        if(all_elem[i].classList.contains('current')){
            all_elem[i].classList.remove('current');
        }
    }
    parent_elem.classList.add('current');
    // 상세내용란 
    // obj.elem.d_product_name.innerHTML = data.product_name;
    obj.elem.d_title.innerHTML = data.title;
    var date = date_format(data.regdate);
    obj.elem.d_regdate.innerHTML = date;
    obj.elem.d_content.value = data.content;
    if(null_exception(data.answer_date)){
        var date = date_format(data.answer_date);
        obj.elem.d_answer_date.innerHTML = date;
    }else{
        obj.elem.d_answer_date.innerHTML = "미등록";
    }
    if(null_exception(data.answer)){
        obj.elem.d_answer.value = data.answer;
    }
    // 답변등록
    obj.elem.answer_btn.onclick = function(){
        // request_qna_answer_register(data);
        gu.confirm({
            description : "Q&A 답변을 등록하시겠습니까?", //내용(string 문자열) 필수
            title : null,  //제목(string 문자열) null이면 "알림"으로 처리함
            positive_method : "request_inquiry_answer_register", //예를 눌렀을경우 호출될 메소드function 이름(string 문자열) null일 경우 메소드를 실행하지않음
            negative_method : null, //아니오를 눌렀을경우 호출될 메소드function 이름(string 문자열) null일 경우 메소드를 실행하지않음
            positive_param : {data : data}, //예를 눌렀을경우 호출될 메소드function에게 전달할 데이터(json 데이터형)
            negative_param : null, //아니오를 눌렀을경우 호출될 메소드function에게 전달할 데이터(json 데이터형)
        });
    }
    // 상품란 초기화
    obj.elem.refresh_btn.onclick = function(){
        refresh_elem();
        parent_elem.classList.remove('current');
    }



    // 첨부이미지 조회

    // <?php echo $this->project_name;?>
    request_inquiry_img(data);

}



function request_inquiry_img(data){
    $('#img_wrap').empty();
    lb.ajax({
        type:"JsonAjaxPost",
        list : {
            ctl:"AdminPopup",
            param1 : "inquiry_img",
            target : data.idx
        },
        action : lb.obj.address, //웹일경우 ajax할 주소
        response_method : "reponse_inquiry_img", //앱일경우 호출될 메소드
        havior : function(result){
            //웹일 경우 호출될 메소드
            console.log(result);
            result = JSON.parse(result);
            reponse_inquiry_img(result);
        }    
    });
}

function reponse_inquiry_img(result){
    if(result.result == "1"){
        console.log(result.value);
        var data = result.value;
        lb.auto_view({
            wrap: "img_wrap",
            copy: "img_copy",
            attr: '["data-attr"]',
            json: data,
            havior: init_img,
        });
    }else{
        gu.alert({
            description : result.message, //내용(string 문자열) 
            title : null,  //제목(string 문자열) 
            response_method : null //확인버튼을 눌럿을경우 호출될 메소드function 이름(string 문자열) 
        });
    }
}



function init_img(elem, data, name, copy_elem){
    if(copy_elem.getAttribute("data-copy") != ""){
        copy_elem.setAttribute("data-copy", "");
    }
    if(name == "img"){
        img_url = obj.link.inquiry_img_path+data.img;
        elem.src = img_url;
        elem.setAttribute('onclick', "img_popup_open('"+img_url+"', "+data.idx+")");
    }
}

function img_popup_open(img_url, target){
    var window_width = window.screen.width; //화면 사이즈 가로
    var window_height = window.screen.height; //화면 사이즈 세로
    var popupWidth= "500"; //팝업창 가로크기
    var popupHeight="400";  //팝업창 세로크기
    var popupX = (window_width / 2) - (popupWidth / 2);
    var popupY= (window_height / 2) - (popupHeight / 2);
    window.open("?ctl=move&param=adm&param1=popup_img&img="+img_url+"", 'inquiry_img_popup_'+target, 'status=no, height=' + popupHeight  + ', width=' + popupWidth  + ', left='+ popupX + ', top='+ popupY);
}


function refresh_elem(){
    $('#img_wrap').empty();
    // obj.elem.d_product_name.innerHTML = "";
    obj.elem.d_title.innerHTML = "";
    obj.elem.d_regdate.innerHTML = "";
    obj.elem.d_content.value = "";
    obj.elem.d_answer_date.innerHTML = "";
    obj.elem.d_answer.value = "";
    obj.elem.answer_btn.onclick = function(){};
    obj.elem.refresh_btn.onclick = function(){};
}

function request_inquiry_answer_register(json){
    var data = json.data;
    console.log(data);
    if(obj.flag.double_click == true){
        obj.flag.double_click = false;
        if(obj.elem.d_answer.value == ""){
            page_alert("답변내용을 입력해주세요.", null, null);
            obj.flag.double_click = true;
        }else{
            $('.loading').fadeIn();
            lb.ajax({
                type : "JsonAjaxPost",
                list : {
                    ctl : "AdminPopup",
                    param1 : "inquiry_answer_register",
                    user_idx : data.user_idx, //추가 : 조경민 ( 해당 user의 이메일 or 문자 수신동의 여부를 체크하기 위해 추가 )
                    target : data.idx,
                    answer : obj.elem.d_answer.value,
                },
                action : "index.php",
                havior : function(result){
                    obj.flag.double_click = true;
                    console.log(result);
                    result = JSON.parse(result);
                    if(result.result == 1){
                        page_alert("답변이 등록되었습니다.", null, "page_reload");  
                        $('.loading').fadeOut();
                    }
                }
            })
        }
    }else{
        page_alert("답변 입력중입니다.", null, null);
    }
}


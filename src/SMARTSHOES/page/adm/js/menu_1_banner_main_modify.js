obj.flag.double_click = true;
var img_ext_arr = ["jpg","jpeg"];
// url 데이터
var page_param = data;

var del_thumbnail_flag = 0;
var del_s_thumbnail_flag = 0;
var del_m_thumbnail_flag = 0;

$(document).ready(function(){
    if(typeof(data.idx) == "undefined"){ //product_idx가 없으면 상품조회로 이동
        alert("수정할 제품이 없습니다.");
        move_page();
        return;
    }

    set_data();
});

//image input id, name 값 설정 , summernote init 시켜주기
function set_data(){
    set_pc_banneer_input();
    // set_mob_banner_input();
    request_banner_detail(data.idx);
}

//썸네일 등록 이미지 관련 id 값 세팅 함수
function set_pc_banneer_input(){
    var attr = obj.elem.pc_banner_box.querySelectorAll('[data-attr]');
    for(var i = 0; i < attr.length; i++){
        //썸네일 관련 
        if(attr[i].getAttribute('data-attr') == "thumnail_file"){ //썸네일 file 엘리먼트 name 설정
            attr[i].name = "pc_img_file" + "[]";
            attr[i].id = "pc_img_file";
            input_file_check(attr[i], img_ext_arr);
        }else if(attr[i].getAttribute('data-attr') == "thumnail_file_label"){ //썸네일 파일 라벨 객체(+ 버튼)
            attr[i].setAttribute("for", "pc_img_file");
            attr[i].id = "pc_img_label";
        }else if(attr[i].getAttribute('data-attr') == "thumnail_wrap"){ //썸네일 업로드 이미지 나타날 wrap
            attr[i].id = "pc_img_wrap";
            attr[i].setAttribute("data-wrap","pc_img_wrap");
        }
    }
}

//썸네일 등록 이미지 관련 id 값 세팅 함수
// function set_mob_banner_input(){
//     var attr = obj.elem.mob_banner_box.querySelectorAll('[data-attr]');
//     for(var i = 0; i < attr.length; i++){
//         //썸네일 관련 
//         if(attr[i].getAttribute('data-attr') == "thumnail_file"){ //썸네일 file 엘리먼트 name 설정
//             attr[i].name = "m_img_file" + "[]";
//             attr[i].id = "m_img_file";
//             m_input_file_check(attr[i], img_ext_arr);
//         }else if(attr[i].getAttribute('data-attr') == "thumnail_file_label"){ //썸네일 파일 라벨 객체(+ 버튼)
//             attr[i].setAttribute("for", "m_img_file");
//             attr[i].id = "m_img_label";
//         }else if(attr[i].getAttribute('data-attr') == "thumnail_wrap"){ //썸네일 업로드 이미지 나타날 wrap
//             attr[i].id = "m_img_wrap";
//             attr[i].setAttribute("data-wrap","m_img_wrap");
//         }
//     }
// }

//이미지 관련 
//thumnail(썸네일) 파일 관련!!!!!!!!!!!!!!!!!!!!!!!!!
//파일 업로드 체크
function input_file_check(elem, ext_array){
    $(elem).change(function(e){
        del_thumbnail_flag = 0;
        if($(this).val() != ""){
            var ext =  $(this).val().split(".").pop().toLowerCase();
            // 매개변수로 받을 값
            if($.inArray(ext, ext_array) == -1){
                var text = "";
                for(var i = 0; i<ext_array.length; i++){
                    if(i == 0){
                        text = ext_array[i];
                    }else{
                        text = text + ", " + ext_array[i];
                    }
                }
                alert(text + " 형식의 파일확장자만 업로드 가능합니다.");
                $(this).val("");
                return;
            }
            var fileSize = this.files[0].size;
            //매개변수로 받을 값
            var maxSize = 4*1024*1024;
            if(fileSize > maxSize){
                alert("파일용량 4MB를 초과했습니다.");
                $(this).val("");
                return;
            }


            var file = this.files[0];
            var _URL = window.URL || window.webkitURL;
            var img = new Image();
            img.src = _URL.createObjectURL(file);
            img.onload = function(){
                // if(img.width > 1000){
                //     alert('가로와 세로 길이는 1000px을 넘을 수 없습니다');
                //     $(elem).val("");
                //     return;
                // }else{
                var reader = new FileReader();
                reader.onload = function(e){
                    add_img_elem(elem,e.target.result, elem.value);
                }
                reader.readAsDataURL(elem.files[0]);
                // }
            }
        }else if(elem.value.length == 0){
            var file_elem_label = document.getElementById('pc_img_label');
            $('#img_elem').remove();
            $(elem).val("");
            elem.removeAttribute('disabled');
            file_elem_label.onclick = function(){};
        }
    });
}

// 이미등록된 컨펌창
function already_img_change(json){
    var target_elem = document.getElementById('pc_img_file');
    var target_label = document.getElementById('pc_img_label');
    target_label.removeAttribute('for');
    target_elem.removeAttribute('disabled');
    target_elem.click();
    target_label.setAttribute('for','pc_img_file');
}

function add_img_elem(elem, target_src, elem_value){
    var img_wrap = "pc_img_wrap";
    var img_wrap_elem = document.getElementById(img_wrap);
    var data_arr = [];
    var data = {};
    data.idx = 1;
    data.src = target_src;
    data_arr.push(data);
    var file_elem_label = document.getElementById('pc_img_label');

    if(img_wrap_elem.children.length != 0){
        alert("썸네일 이미지는 한장만 등록하실 수 있습니다.");
        var target_elem = document.getElementById('pc_img_file');
        target_elem.value = "";
        return;
    }

    // file_elem_label.onclick = function(){
    //     gu.confirm({
    //         description : "이미 등록된 이미지가 있습니다. 이미지를 바꾸시겠습니까?", //내용(string 문자열) 필수
    //         title : null,  //제목(string 문자열) null이면 "알림"으로 처리함
    //         positive_method : "already_img_change", //예를 눌렀을경우 호출될 메소드function 이름(string 문자열) null일 경우 메소드를 실행하지않음
    //         negative_method : null, //아니오를 눌렀을경우 호출될 메소드function 이름(string 문자열) null일 경우 메소드를 실행하지않음
    //         positive_param : null, //예를 눌렀을경우 호출될 메소드function에게 전달할 데이터(json 데이터형)
    //         negative_param : null, //아니오를 눌렀을경우 호출될 메소드function에게 전달할 데이터(json 데이터형)
    //     });
    // }
    elem.setAttribute('disabled', true);
    if(img_wrap_elem.children.length == 0){
        lb.auto_view({
            wrap: img_wrap,
            copy: "img_copy",
            attr: '["data-attr"]',
            json: data_arr,
            havior : function(elem, data, name, copy_elem){
                if(copy_elem.getAttribute("data-copy") != ""){
                    copy_elem.setAttribute("data-copy", "");
                    copy_elem.setAttribute("id","img_elem");
                }
                if(name == "img"){
                    elem.src = data.src;
                    elem.setAttribute('id','img');
                }else if(name == "prev_img_url"){
                    elem.setAttribute('id','prev_elem');
                    elem.value = elem_value;
                }else if(name == "del_btn"){
                    // if(null_exception(page_param.idx)){
                    //     elem.style.display= "none";
                    // }else{
                        
                    // }
                    elem.onclick = function(){
                        var coupon_file_elem = document.getElementById('pc_img_file');
                        $('#img_elem').remove();
                        $(coupon_file_elem).val("");
                        coupon_file_elem.removeAttribute('disabled');
                        file_elem_label.onclick = function(){};

                        del_thumbnail_flag = 1;
                    }
                }
            },
        });
    }else{
        $("#img_elem").remove();
        lb.auto_view({
            wrap: img_wrap,
            copy: "img_copy",
            attr: '["data-attr"]',
            json: data_arr,
            havior : function(elem, data, name, copy_elem){
                if(copy_elem.getAttribute("data-copy") != ""){
                    copy_elem.setAttribute("data-copy", "");
                    copy_elem.setAttribute("id","img_elem");
                }
                if(name == "img"){
                    elem.src = data.src;
                    elem.setAttribute('id','img');
                }else if(name == "prev_img_url"){
                    elem.setAttribute('id','prev_elem');
                    elem.value = elem_value;
                }else if(name == "del_btn"){
                    elem.onclick = function(){
                        var coupon_file_elem = document.getElementById('pc_img_file');
                        $('#img_elem').remove();
                        $(coupon_file_elem).val("");
                        coupon_file_elem.removeAttribute('disabled');
                        file_elem_label.onclick = function(){};

                        del_thumbnail_flag = 1;
                    }
                }
            },
        });
    }
}
//thumnail(썸네일) 파일 관련 끝!!!!!!!!!!!!!!!!!!!!!!!!!

//s banner img 파일 관련!!!!!!!!!!!!!!!!!!!!!!!!!
//파일 업로드 체크

//m banner img 파일 관련!!!!!!!!!!!!!!!!!!!!!!!!!
//파일 업로드 체크
function m_input_file_check(elem, ext_array){
    $(elem).change(function(e){
        del_m_thumbnail_flag = 0;
        if($(this).val() != ""){
            var ext =  $(this).val().split(".").pop().toLowerCase();
            // 매개변수로 받을 값
            if($.inArray(ext, ext_array) == -1){
                var text = "";
                for(var i = 0; i<ext_array.length; i++){
                    if(i == 0){
                        text = ext_array[i];
                    }else{
                        text = text + ", " + ext_array[i];
                    }
                }
                alert(text + " 형식의 파일확장자만 업로드 가능합니다.");
                $(this).val("");
                return;
            }
            var fileSize = this.files[0].size;
            //매개변수로 받을 값
            var maxSize = 4*1024*1024;
            if(fileSize > maxSize){
                alert("파일용량 4MB를 초과했습니다.");
                $(this).val("");
                return;
            }


            var file = this.files[0];
            var _URL = window.URL || window.webkitURL;
            var img = new Image();
            img.src = _URL.createObjectURL(file);
            img.onload = function(){
                // if(img.width > 1000){
                //     alert('가로와 세로 길이는 1000px을 넘을 수 없습니다');
                //     $(elem).val("");
                //     return;
                // }else{
                var reader = new FileReader();
                reader.onload = function(e){
                    add_m_img_elem(elem,e.target.result, elem.value);
                }
                reader.readAsDataURL(elem.files[0]);
                // }
            }
        }else if(elem.value.length == 0){
            var file_elem_label = document.getElementById('m_img_label');
            $('#m_img_elem').remove();
            $(elem).val("");
            elem.removeAttribute('disabled');
            file_elem_label.onclick = function(){};
        }
    });
}

// 이미등록된 컨펌창
// function already_m_img_change(json){
//     var target_elem = document.getElementById('m_img_file');
//     var target_label = document.getElementById('m_img_label');
//     target_label.removeAttribute('for');
//     target_elem.removeAttribute('disabled');
//     target_elem.click();
//     target_label.setAttribute('for','m_img_file');
// }

function add_m_img_elem(elem, target_src, elem_value){
    var img_wrap = "m_img_wrap";
    var img_wrap_elem = document.getElementById(img_wrap);
    var data_arr = [];
    var data = {};
    data.idx = 1;
    data.src = target_src;
    data_arr.push(data);
    var file_elem_label = document.getElementById('m_img_label');

    if(img_wrap_elem.children.length != 0){
        alert("썸네일 이미지는 한장만 등록하실 수 있습니다.");
        var target_elem = document.getElementById('pc_img_file');
        target_elem.value = "";
        return;
    }

    // file_elem_label.onclick = function(){
    //     gu.confirm({
    //         description : "이미 등록된 이미지가 있습니다. 이미지를 바꾸시겠습니까?", //내용(string 문자열) 필수
    //         title : null,  //제목(string 문자열) null이면 "알림"으로 처리함
    //         positive_method : "already_m_img_change", //예를 눌렀을경우 호출될 메소드function 이름(string 문자열) null일 경우 메소드를 실행하지않음
    //         negative_method : null, //아니오를 눌렀을경우 호출될 메소드function 이름(string 문자열) null일 경우 메소드를 실행하지않음
    //         positive_param : null, //예를 눌렀을경우 호출될 메소드function에게 전달할 데이터(json 데이터형)
    //         negative_param : null, //아니오를 눌렀을경우 호출될 메소드function에게 전달할 데이터(json 데이터형)
    //     });
    // }
    elem.setAttribute('disabled', true);
    if(img_wrap_elem.children.length == 0){
        lb.auto_view({
            wrap: img_wrap,
            copy: "img_copy",
            attr: '["data-attr"]',
            json: data_arr,
            havior : function(elem, data, name, copy_elem){
                if(copy_elem.getAttribute("data-copy") != ""){
                    copy_elem.setAttribute("data-copy", "");
                    copy_elem.setAttribute("id","m_img_elem");
                }
                if(name == "img"){
                    elem.src = data.src;
                    elem.setAttribute('id','m_img');
                }else if(name == "prev_img_url"){
                    elem.setAttribute('id','m_prev_elem');
                    elem.value = elem_value;
                }else if(name == "del_btn"){
                    // if(null_exception(page_param.idx)){
                    //     elem.style.display= "none";
                    // }else{
                        
                    // }
                    elem.onclick = function(){
                        var coupon_file_elem = document.getElementById('m_img_file');
                        $('#s_img_elem').remove();
                        $(coupon_file_elem).val("");
                        coupon_file_elem.removeAttribute('disabled');
                        file_elem_label.onclick = function(){};

                        del_m_thumbnail_flag = 1;
                    }
                }
            },
        });
    }else{
        $("#m_img_elem").remove();
        lb.auto_view({
            wrap: img_wrap,
            copy: "img_copy",
            attr: '["data-attr"]',
            json: data_arr,
            havior : function(elem, data, name, copy_elem){
                if(copy_elem.getAttribute("data-copy") != ""){
                    copy_elem.setAttribute("data-copy", "");
                    copy_elem.setAttribute("id","m_img_elem");
                }
                if(name == "img"){
                    elem.src = data.src;
                    elem.setAttribute('id','m_img');
                }else if(name == "prev_img_url"){
                    elem.setAttribute('id','m_prev_elem');
                    elem.value = elem_value;
                }else if(name == "del_btn"){
                    elem.onclick = function(){
                        var coupon_file_elem = document.getElementById('m_img_file');
                        $('#m_img_elem').remove();
                        $(coupon_file_elem).val("");
                        coupon_file_elem.removeAttribute('disabled');
                        file_elem_label.onclick = function(){};

                        del_m_thumbnail_flag = 1;
                    }
                }
            },
        });
    }
}
//m 배너 이미지(썸네일) 파일 관련 끝!!!!!!!!!!!!!!!!!!!!!!!!!

//제품이미지(product_image) 파일 관련 끝!!!!!!!!!!!!!!!!!!!!!!!!!

//등록 여부 확인 함수
function request_save(){
    if(confirm("배너를 수정하시겠습니까?")){
        request_modify();
    }
}

//카테고리 등록시 필수값 유효성 검사 함수
function register_data_check(){
    //썸네일 file  엘리먼트 첨부파일 있는지 확인
    if(del_thumbnail_flag == 1){
        var elem_thumnail_file = lb.getElem("pc_img_file");        
        if(elem_thumnail_file.value == ""){ //첨부파일이 없다면
            alert("PC 이미지를 등록해주세요");
            obj.flag.double_click = true;
            return;
        }
    }

    //썸네일 file  엘리먼트 첨부파일 있는지 확인
    // if(del_m_thumbnail_flag == 1){
    //     var elem_thumnail_file = lb.getElem("m_img_file");        
    //     if(elem_thumnail_file.value == ""){ //첨부파일이 없다면
    //         gu.alert({
    //             description : "모바일 이미지를 등록해주세요", //내용(string 문자열) 
    //             title : null,  //제목(string 문자열)  null이면 "알림"으로 처리함
    //             response_method : null, //확인버튼을 눌럿을경우 호출될 메소드function 이름(string 문자열)  null일 경우 메소드를 실행하지않음
    //             response_param : null //확인버튼을 눌렀을 경우 호출될 메소드function에게 전달할 데이터(json 데이터형)
    //         });
    //         obj.flag.double_click = true;
    //         return;
    //     }
    // }

    // //언어별 썸네일 file 엘리먼트 disable 풀기
    var elem_thumnail_file = lb.getElem("pc_img_file");
    elem_thumnail_file.removeAttribute('disabled');

    // var elem_thumnail_file = lb.getElem("m_img_file");
    // elem_thumnail_file.removeAttribute('disabled');

    return true;
}

//카테고리 정보를 DB에 저장하는 함수
function request_modify(){
    if(obj.flag.double_click){
        obj.flag.double_click = false;
        var data_flag = register_data_check();
        if(data_flag){
            var is_use = 0;
            if(obj.elem.use.checked == true){
                is_use = 1;
            }else{
                is_use = 0;
            }

            //유효성 검사에 통과하면 DB에 insert
            $(".loading").fadeIn();
            // 통신
            lb.ajax({
                type:"AjaxFormPost",
                list : {
                    ctl:"AdminMenu1",
                    param1:"modify_banner",
                    idx:data.idx,
                    del_thumbnail_flag : del_thumbnail_flag,
                    // del_m_thumbnail_flag : del_m_thumbnail_flag,
                    is_use : is_use,
                },
                elem : lb.getElem('form'),
                action : lb.obj.address, //웹일경우 ajax할 주소
                response_method : "response_register_banner", //앱일경우 호출될 메소드
                havior : function(result){
                    $(".loading").fadeOut();
                    //웹일 경우 호출될 메소드
                    console.log(result);
                    result = JSON.parse(result);
                    console.log(result);
                    response_register_banner(result);
                }    
            });
        }
    }else{
        alert("잠시만 기다려주세요.");
    }
}

//카테고리 등록 함수의 응답 함수
function response_register_banner(result){
    obj.flag.double_click = true;
    if(result.result == "1"){
        alert("배너가 수정되었습니다.");
        move_page();
    }else{
        admin_inquiry_alert();
    }
}

function move_page(){
    location.href = "?ctl=move&param=adm&param1=menu1_banner";
}

//제품 조회
function request_banner_detail(idx){
    lb.ajax({
        type: "AjaxFormPost",
        list: {  //SendMailModel.php까지 가는 경로(App.php에서 설정)
                ctl: "AdminMenu1", 
                param1: "request_banner_detail",
                banner_idx : idx
            },
        elem: obj.elem.form,
        action: "index.php",
        havior: function(result){
            console.log(result);
            result = JSON.parse(result);
            console.log(result);
            if(result.result == "1"){
                init_banner_detail(result["value"][0]);
            }else{
                alert(result.message);
            }
        }
    });
}

//조회한 제품 내용 ui에 적용하기
function init_banner_detail(data){
    console.log(data);

    if(data.is_use == 1){
        obj.elem.use.checked = true;
    }else{
        obj.elem.not_use.checked = true;
    }

    //관리자 구분명
    if(data.name != null){
        var elem_name = lb.getElem("name");
        elem_name.value = data.name;
    }

    if(data.title1 != null){
        var elem_title1 = lb.getElem("title1");
        elem_title1.value = data.title1;
    }

    if(data.title2 != null){
        var elem_title2 = lb.getElem("title2");
        elem_title2.value = data.title2;
    }

    if(data.content != null){
        var elem_content = lb.getElem("content");
        elem_content.value = data.content;
    }

    if(data.link != null){
        var elem_link = lb.getElem("link");
        elem_link.value = data.link;
    }

    //썸네일 이미지 설정
    var img_wrap = "pc_img_wrap";
    var data_arr = [{"src":data.pc_file_name}];

    if(data_arr[0].src != null){
        lb.auto_view({
            wrap: img_wrap,
            copy: "img_copy",
            attr: '["data-attr"]',
            json: data_arr,
            havior : function(elem, data, name, copy_elem){
                if(copy_elem.getAttribute("data-copy") != ""){
                    copy_elem.setAttribute("data-copy", "");
                    copy_elem.setAttribute("id","reg_img_elem");
                }
                if(name == "img"){
                    if(data.src == null){
                        del_thumbnail_flag = 1;
                        elem.src = no_image;
                    }else{
                        elem.src = obj.link.pc_banner_img_origin_path + data.src;
                    }
    
                    elem.id= "reg_thumnail_img"; //이미 등록된 이미지의 id지정
                }else if(name == "del_btn"){ //삭제 버튼
                    if(data.src != null){
                        elem.onclick = function(){
                            document.querySelector('#pc_img_file').value = "";
                            var wrap = document.querySelector('#pc_img_wrap');
                            var del_elem = document.querySelector('#reg_img_elem');
                            wrap.removeChild(del_elem);
    
                            del_thumbnail_flag = 1;
                        }
                    }else{
                        elem.style.display = "none";
                    }
                }
            },
        });
    }

    //썸네일 이미지 설정
    // var img_wrap = "m_img_wrap";
    // var data_arr = [{"src":data.m_file_name}];

    // if(data_arr[0].src != null){
    //     lb.auto_view({
    //         wrap: img_wrap,
    //         copy: "img_copy",
    //         attr: '["data-attr"]',
    //         json: data_arr,
    //         havior : function(elem, data, name, copy_elem){
    //             if(copy_elem.getAttribute("data-copy") != ""){
    //                 copy_elem.setAttribute("data-copy", "");
    //                 copy_elem.setAttribute("id","m_reg_img_elem");
    //             }
    //             if(name == "img"){
    //                 if(data.src == null){
    //                     del_m_thumbnail_flag = 1;
    //                     elem.src = no_image;
    //                 }else{
    //                     elem.src = obj.link.m_banner_img_origin_path + data.src;
    //                 }
    
    //                 elem.id= "m_reg_thumnail_img"; //이미 등록된 이미지의 id지정
    //             }else if(name == "del_btn"){ //삭제 버튼
    //                 if(data.src != null){
    //                     elem.onclick = function(){
    //                         document.querySelector('#m_img_file').value = "";
    //                         var wrap = document.querySelector('#m_img_wrap');
    //                         var del_elem = document.querySelector('#m_reg_img_elem');
    //                         wrap.removeChild(del_elem);
    
    //                         del_m_thumbnail_flag = 1;
    //                     }
    //                 }else{
    //                     elem.style.display = "none";
    //                 }
    //             }
    //         },
    //     });
    // }
}
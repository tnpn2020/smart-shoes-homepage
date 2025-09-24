obj.flag.double_click = true;
$(document).ready(function () {
    request_project_setting();
    page_init();
});

function page_init(){
    lb.ajax({
        type : "JsonAjaxPost",
        list : {
            ctl : "ProjectSetting",
            param1 : "select_lang",
        },
        action : "index.php",
        havior : function(result){
            // console.log(result);
            result = JSON.parse(result);
            if(result.result == 1){
                init_board(result.value);
            }
        }
    });
}

function init_board(data){
    if(data.length != 0){
        obj.elem.no_board.style.display = "none";
    }
    lb.auto_view({
        wrap : "wrap",
        copy : "copy",
        attr : '["data-attr"]',
        json : data,
        havior : function(elem, data, name, copy_elem){
            if (copy_elem.getAttribute('data-copy') == "copy") {
                copy_elem.setAttribute('data-copy', '');
            }
            if(name=="lang_name"){
                elem.innerHTML = data.name;
            }else if(name == "icon"){
                elem.setAttribute('src', project_name + "/page/adm/images/" + data.icon_file_name);
            }else if(name == "del_btn"){
                elem.setAttribute('onclick', 'delete_lang(' + data.idx + ')');
            }
        }
    });
}

//setting page에서 테이블 생성 Btn을 클릭하면 쇼핑몰 Base Table을 생성해주는 함수
function create_table(){
    if(obj.flag.double_click){
        obj.flag.double_click = false;
        lb.ajax({
            type : "JsonAjaxPost",
            list : {
                ctl : "ProjectSetting",
                param1 : "create_project_table",
            },
            action : "index.php",
            havior : function(result){
                obj.flag.double_click = true;
                alert("테이블이 생성되었습니다");
            }    
        });
    }else{
        alert("테이블 생성중입니다");
    }
}

var pro_setting_flag = 0;
//쇼핑몰 분류 ( 대분류, 중분류, 소분류, 세분류)가 설정되어있는지 확인하기 위한 함수
function request_project_setting(){
    lb.ajax({
        type : "JsonAjaxPost",
        list : {
            ctl : "ProjectSetting",
            param1 : "request_project_setting",
        },
        action : "index.php",
        havior : function(result){
            // console.log(result);
            result = JSON.parse(result);
            if(result.result == 1){
                //프로젝트 세팅 테이블에 값이 없을때 ( 프로젝트 세팅이 안되어 있을때 )
                if(result.value.length == 0){
                    obj.elem.no_set_message.style.display = "block";
                }else{
                    pro_setting_flag = 1;
                }
            }
        }    
    });
}

//프로젝트 세팅값을 등록 or 수정하는 함수
function project_setting(){
    if(obj.flag.double_click){
        obj.flag.double_click = false;
        if(obj.elem.category_option.value == "no_select"){
            alert("카테고리 분류를 선택해주세요");
            obj.flag.double_click = true;
        }else{
            lb.ajax({
                type : "JsonAjaxPost",
                list : {
                    ctl : "ProjectSetting",
                    param1 : "project_setting",
                    flag : pro_setting_flag, //flag가 0이면 insert , 1이면 update문 실행
                    value : obj.elem.category_option.value, //대분류 ~ 세분류까지의 value 값
                },
                action : "index.php",
                havior : function(result){
                    obj.flag.double_click = true;
                    // console.log(result);
                    result = JSON.parse(result);
                    if(result.result == 1){
                        obj.flag.double_click = true;
                        alert("카테고리 분류가 설정되었습니다");
                        location.reload();
                    }else{
                        obj.flag.double_click = true;
                        alert("관리자에게 문의해주세요");
                    }
                }    
            });
        }
    }else{
        alert("카테고리 분류 설정중입니다");
    }
}

//언어 등록 함수
function register_lang(){
    if(obj.flag.double_click){
        obj.flag.double_click = false;
        if(obj.elem.lang_name.value == ""){
            alert("추가하실 언어명을 입력해주세요");
            obj.elem.lang_name.focus();
            obj.flag.double_click = true;
        }else{
            lb.ajax({
                type : "JsonAjaxPost",
                list : {
                    ctl : "ProjectSetting",
                    param1 : "register_lang",
                    lang_name : obj.elem.lang_name.value,
                    icon : obj.elem.lang_option.value,
                },
                action : "index.php",
                havior : function(result){
                    obj.flag.double_click = true;
                    // console.log(result);
                    result = JSON.parse(result);
                    if(result.result == 1){
                        obj.flag.double_click = true;
                        location.reload();
                    }else{
                        obj.flag.double_click = true;
                        alert("관리자에게 문의해주세요");
                    }
                }    
            });
        }
    }else{
        alert("언어 설정중입니다");
    }
}

//언어 등록 함수
function delete_lang(idx){
    if(obj.flag.double_click){
        obj.flag.double_click = false;
        if(confirm("해당 언어를 삭제하시겠습니까?")){
            lb.ajax({
                type : "JsonAjaxPost",
                list : {
                    ctl : "ProjectSetting",
                    param1 : "delete_lang",
                    idx : idx,
                },
                action : "index.php",
                havior : function(result){
                    obj.flag.double_click = true;
                    // console.log(result);
                    result = JSON.parse(result);
                    if(result.result == 1){
                        obj.flag.double_click = true;
                        location.reload();
                    }else{
                        obj.flag.double_click = true;
                        alert("관리자에게 문의해주세요");
                    }
                }    
            });
        }else{
            obj.flag.double_click = true;
        }
    }else{
        alert("언어 삭제중입니다");
    }
}

function change_national_flag(value){
    lb.getElem("national_flag").src = project_name + "/page/adm/images/" + value;
}
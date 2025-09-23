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
var type = 1; //배너 종류
var img_ext_arr = ["jpg","png","gif"];

function webviewReady(){
    request_lang_list();
    init_file();
}

function init_file(){
    input_pc_file_check(lb.getElem("pc_img_file"), img_ext_arr);
    input_m_file_check(lb.getElem("m_img_file"), img_ext_arr);
    input_s_file_check(lb.getElem("s_img_file"), img_ext_arr);
}

//배너 목록으로 이동
function move_banner_list(){
    location.href= "?ctl=move&param=adm&param1=menu1_banner";
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
        init_view(result.value);
    }else{
        alert(result.message);
    }
}

//언어별 화면 셋팅
function init_view(datas){
    obj.value.lang = datas;
    if(datas.length == 0){ //언어 등록된것이 없음
        alert("언어 설정이 되어있지 않습니다. \n개발자에게 문의 주세요.");
    }else{
        console.log(datas);
        //언어별 라디오 버튼 추가
        lb.auto_view({
            wrap: "lang_radio",
            copy: "copy_radio",
            attr: '["data-attr"]',
            json: datas,
            havior: add_lang_radio,
        });
    }
}

function add_lang_radio(elem, data, name, copy_elem){
    if(copy_elem.getAttribute("data-copy") != ""){
        copy_elem.setAttribute("data-copy", "");
    }
    if(name == "label"){
        var radio_id = "radio_" + data.idx;
        elem.setAttribute("for",radio_id);
        var checked = "";
        if(data.idx == 1){ //첫번째면 checked
            checked = "checked";
        }
        elem.innerHTML = data.name + '<input type="radio" id="' + radio_id +'" value="' + data.idx +'" name="lang" ' + checked +'/><span class="checkmark radio"></span>';
    }
}

//PC용 이미지 등록 로직
function input_pc_file_check(elem, ext_array){
    $(elem).change(function(e){
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
                var pc_file_view = document.getElementById('pc_file_view');
                if(pc_file_view != null){
                    $('#pc_file_view').remove();
                }
                alert(text + " 형식의 파일확장자만 업로드 가능합니다.");
                $(this).val("");
                return;
            }
            var fileSize = this.files[0].size;
            //매개변수로 받을 값
            var maxSize = 4*1024*1024;
            if(fileSize > maxSize){
                //추가된 파일이 있는지 확인
                var pc_file_view = document.getElementById('pc_file_view');
                if(pc_file_view != null){
                    $('#pc_file_view').remove();
                }
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
                    add_pc_img_elem(elem,e.target.result, elem.value);
                }
                reader.readAsDataURL(elem.files[0]);
                // }
            }
        }else if(elem.value.length == 0){
            var target_id = elem.getAttribute('id').split('_')[2];
            $('#pc_img_file').remove();
            $(elem).val("");
            elem.removeAttribute('disabled');
        }
    });
}


function add_pc_img_elem(elem, target_src, elem_value){
    console.log(elem.value);

    //추가된 파일이 있는지 확인
    var pc_file_view = document.getElementById('pc_file_view');
    if(pc_file_view != null){
        $('#pc_file_view').remove();
    }

    var data_arr = [];
    var data = {};
    data.src = target_src;
    data_arr.push(data);
    lb.auto_view({
        wrap: "pc_img_wrap",
        copy: "img_copy",
        attr: '["data-attr"]',
        json: data_arr,
        havior : function(elem, data, name, copy_elem){
            if(copy_elem.getAttribute("data-copy") != ""){
                copy_elem.setAttribute("data-copy", "");
                copy_elem.setAttribute("id","pc_file_view");
            }
            if(name == "img"){
                elem.src = data.src;
                elem.setAttribute('id','img_'+data.idx);
            }else if(name == "prev_img_url"){
                elem.setAttribute('id','prev_elem_'+data.idx);
                elem.value = elem_value;
            }else if(name == "del_btn"){
                elem.onclick = function(){
                    var file_elem = document.getElementById('pc_img_file');
                    $('#pc_file_view').remove();
                    $(file_elem).val("");
                }
            }
        },
    });
}




//모바일용 이미지 등록 로직
function input_m_file_check(elem, ext_array){
    $(elem).change(function(e){
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
                var pc_file_view = document.getElementById('m_file_view');
                if(pc_file_view != null){
                    $('#m_file_view').remove();
                }
                alert(text + " 형식의 파일확장자만 업로드 가능합니다.");
                $(this).val("");
                return;
            }
            var fileSize = this.files[0].size;
            //매개변수로 받을 값
            var maxSize = 4*1024*1024;
            if(fileSize > maxSize){
                //추가된 파일이 있는지 확인
                var pc_file_view = document.getElementById('m_file_view');
                if(pc_file_view != null){
                    $('#m_file_view').remove();
                }
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
            var target_id = elem.getAttribute('id').split('_')[2];
            $('#m_img_file').remove();
            $(elem).val("");
            elem.removeAttribute('disabled');
        }
    });
}


function add_m_img_elem(elem, target_src, elem_value){
    console.log(elem.value);

    //추가된 파일이 있는지 확인
    var file_view = document.getElementById('m_file_view');
    if(file_view != null){
        $('#m_file_view').remove();
    }

    var data_arr = [];
    var data = {};
    data.src = target_src;
    data_arr.push(data);
    lb.auto_view({
        wrap: "m_img_wrap",
        copy: "img_copy",
        attr: '["data-attr"]',
        json: data_arr,
        havior : function(elem, data, name, copy_elem){
            if(copy_elem.getAttribute("data-copy") != ""){
                copy_elem.setAttribute("data-copy", "");
                copy_elem.setAttribute("id","m_file_view");
            }
            if(name == "img"){
                elem.src = data.src;
                elem.setAttribute('id','img_'+data.idx);
            }else if(name == "prev_img_url"){
                elem.setAttribute('id','prev_elem_'+data.idx);
                elem.value = elem_value;
            }else if(name == "del_btn"){
                elem.onclick = function(){
                    var file_elem = document.getElementById('m_img_file');
                    $('#m_file_view').remove();
                    $(file_elem).val("");
                }
            }
        },
    });
}


//모바일용 이미지 등록 로직
function input_s_file_check(elem, ext_array){
    $(elem).change(function(e){
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
                var pc_file_view = document.getElementById('s_file_view');
                if(pc_file_view != null){
                    $('#s_file_view').remove();
                }
                alert(text + " 형식의 파일확장자만 업로드 가능합니다.");
                $(this).val("");
                return;
            }
            var fileSize = this.files[0].size;
            //매개변수로 받을 값
            var maxSize = 4*1024*1024;
            if(fileSize > maxSize){
                //추가된 파일이 있는지 확인
                var pc_file_view = document.getElementById('s_file_view');
                if(pc_file_view != null){
                    $('#s_file_view').remove();
                }
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
                    add_s_img_elem(elem,e.target.result, elem.value);
                }
                reader.readAsDataURL(elem.files[0]);
                // }
            }
        }else if(elem.value.length == 0){
            var target_id = elem.getAttribute('id').split('_')[2];
            $('#s_img_file').remove();
            $(elem).val("");
            elem.removeAttribute('disabled');
        }
    });
}


function add_s_img_elem(elem, target_src, elem_value){
    console.log(elem.value);

    //추가된 파일이 있는지 확인
    var file_view = document.getElementById('s_file_view');
    if(file_view != null){
        $('#s_file_view').remove();
    }

    var data_arr = [];
    var data = {};
    data.src = target_src;
    data_arr.push(data);
    lb.auto_view({
        wrap: "s_img_wrap",
        copy: "img_copy",
        attr: '["data-attr"]',
        json: data_arr,
        havior : function(elem, data, name, copy_elem){
            if(copy_elem.getAttribute("data-copy") != ""){
                copy_elem.setAttribute("data-copy", "");
                copy_elem.setAttribute("id","s_file_view");
                console.log(123);
            }
            if(name == "img"){
                elem.src = data.src;
                elem.setAttribute('id','img_'+data.idx);
            }else if(name == "prev_img_url"){
                elem.setAttribute('id','prev_elem_'+data.idx);
                elem.value = elem_value;
            }else if(name == "del_btn"){
                elem.onclick = function(){
                    var file_elem = document.getElementById('s_img_file');
                    $('#s_file_view').remove();
                    $(file_elem).val("");
                }
            }
        },
    });
}

function upload(){
    console.log(obj.elem.form);

    //파일 추가되어있는지 확인
    var elem_pc_file = lb.getElem("pc_img_file");
    // var elem_m_file = lb.getElem("m_img_file");
    // var elem_s_file = lb.getElem("s_img_file");


    if(elem_pc_file.value == ""){
        alert("PC 이미지를 등록해주세요");
        return;
    }

    // if(elem_m_file.value == ""){
    //     gu.alert({
    //         description : "모바일 이미지를 등록해주세요", //내용(string 문자열) 
    //         title : null,  //제목(string 문자열) 
    //         response_method : null //확인버튼을 눌럿을경우 호출될 메소드function 이름(string 문자열) 
    //     });
    //     return;
    // }

    // if(elem_s_file.value == ""){
    //     gu.alert({
    //         description : "슬라이드용 이미지를 등록해주세요", //내용(string 문자열) 
    //         title : null,  //제목(string 문자열) 
    //         response_method : null //확인버튼을 눌럿을경우 호출될 메소드function 이름(string 문자열) 
    //     });
    //     return;
    // }

    // $('.loading').fadeIn();
    lb.ajax({
        type : "AjaxFormPost",
        list : {
            ctl:"AdminMenu1",
            param1:"request_banner_reg",
            type: type
        },
        elem : obj.elem.form,
        action : lb.obj.address, //웹일경우 ajax할 주소
        response_method : "reponse_upload", //앱일경우 호출될 메소드
        havior : function(result){
            //웹일 경우 호출될 메소드
            console.log(result);
            result = JSON.parse(result);
            reponse_upload(result);
        }
    });    
}

function reponse_upload(result){
    console.log(result);
    $('.loading').fadeOut();
    if(result.result == "1"){
        console.log("!!!!!!!!");
        alert("등록되었습니다.");
        move_banner_list();
    }else{
        console.log("!!!!!!!!");
        alert(result.message);
    }
}



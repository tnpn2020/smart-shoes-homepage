obj.value.upload_img = [];
obj.value.max_image = 1;
var image_index = 2;

$(document).ready(function(){
    request_lang();
    request_detail_data();
});

//DB에 등록된 언어 목록을 가져오고
//언어수만큼 등록 폼을 생성
function request_lang(){
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
    console.log(result);
    lb.auto_view({
        wrap : "lang_radio",
        copy : "copy_radio",
        attr : '["data-attr"]',
        json : result.value,
        havior : add_lang_list,
    });
}

function add_lang_list(elem, data, name, copy_elem){
    if(copy_elem.getAttribute("data-copy") != ""){
        copy_elem.setAttribute("data-copy", "");
    }
    
    if(name == "label"){
        elem.innerHTML = data.name;
    }else if(name == "value"){
        elem.value = data.idx;
        elem.id = data.id;
    }
}

//팝업 수정에서 DB에 저장되어 있는 데이터를 가져오는 함수
function request_detail_data(){
    lb.ajax({
        type:"JsonAjaxPost",
        list : {
            ctl:"AdminMenu1",
            param1:"request_popup_detail",
            idx : data.idx,
        },
        action : lb.obj.address, //웹일경우 ajax할 주소
        response_method : "reponse_popup_detail", //앱일경우 호출될 메소드
        havior : function(result){
            //웹일 경우 호출될 메소드
            console.log(result);
            result = JSON.parse(result);
            reponse_popup_detail(result);
        }    
    });
}

var file_upload_flag = 0;
function reponse_popup_detail(result){
    var data = result.value[0];
    console.log(data);

    obj.elem.popup_name.value = data.name; //팝업 이름
    if(data.link != null){
        obj.elem.link.value = data.link; //팝업 링크
    }

    //사용 상ㅇ태
    if(data.is_use == 1){ //사용
        obj.elem.use.checked = true;
    }else{ //미사용
        obj.elem.not_use.checked = true;
    }

    //이미지
    if(data.pc_file_name != null){
        var copy = obj.elem.img_copy.cloneNode(true);
        var img = copy.querySelector('img');
        img.setAttribute('data-img', data["pc_file_name"]);
        img.src = obj.link.pc_popup_img_origin_path + data["pc_file_name"];
        obj.elem.pc_img_wrap.appendChild(copy);

        obj.value.upload_img.push(data["pc_file_name"]);

        file_upload_flag = 1;
    }
}

//팝업 수정 함수
function upload(){
    if(obj.elem.popup_name.value == ""){
        alert("팝업명을 입력해주세요.");
    }else if(file_upload_flag == 0){
        alert("팝업 이미지를 첨부해주세요.");
    }else{
        $('.loading').fadeIn();
        lb.ajax({
            type:"AjaxFormPost",
            list : {
                ctl:"AdminMenu1",
                param1:"modify_popup",
                idx : data.idx,
                upload_img : JSON.stringify(obj.value.upload_img),
            },
            elem : obj.elem.form,
            action : lb.obj.address, //웹일경우 ajax할 주소
            havior : function(result){
                //웹일 경우 호출될 메소드
                console.log(result);
                result = JSON.parse(result);
                alert("팝업이 수정되었습니다.");
                reload();

                $('.loading').fadeOut();
            }    
        });
    }
}

function reload(){
    location.reload();
}

//이미지 추가 함수
function add_image_file(elem){
    //image tag
    var image_wrap = document.querySelector('#pc_img_wrap');
    var img_tag = image_wrap.querySelectorAll('.img-upload');

    if(img_tag.length == obj.value.max_image){
        alert("이미지는 최대 " + obj.value.max_image + "개까지 등록하실 수 있습니다.");
        elem.value = "";
        return;
    }

    for(var k = 0; k < elem.files.length; k++){
        //파일 용량 체크
        if(elem.files[k] != null){
            var max_size = 4 * 1024 * 1024;
            var file_size = elem.files[k].size;
            if(file_size > max_size){
                alert("4MB 이하의 이미지 첨부 가능\n\n현재파일 : " + Math.round(file_size / 1024 / 1024 * 100) / 100 + "MB");
                elem.value = "";
                return;
            }
        }
        //파일 확장자 체크
        var file_name = elem.files[k].name;
        var last_dot = file_name.lastIndexOf('.');
        var ext = file_name.slice(last_dot + 1, (file_name.length));
        var allowed_extentions = ["jpg", "png", "gif", "jpeg"];
        if(allowed_extentions.indexOf(ext.toLowerCase()) == - 1){ 
            alert("JPG, PNG, GIF, JPEG 형식의 파일확장자만 등록하실 수 있습니다.");
            elem.value = "";
            return;
        }
    }

    var files = elem.files;
    
    // 이미지 미리 보여주기
    for(var j = 0; j < files.length; j++){
        var reader = new FileReader();
        reader.onload = function(e){
            var clone = document.querySelector('#img_copy').cloneNode(true);
            clone.querySelector('img').setAttribute('src', e.target.result);
            clone.querySelector('button').id = "delete_btn";
            image_wrap.appendChild(clone);
            
        }
        reader.readAsDataURL(files[j]);
    }

    file_upload_flag = 1;
}

//추가한 이미지 삭제 함수
function delete_image(elem){
    //수정 페이지에서 사용
    if(elem.parentNode.querySelector('img').getAttribute('data-img') != null){
        var del_img = elem.parentNode.querySelector('img').getAttribute('data-img');

        //이미지 삭제시 obj.value.upload_img에서 삭제한 이미지 이름 삭제
        var upload_img_array = obj.value.upload_img;
        if(upload_img_array.indexOf(del_img) != -1){
            var del_index = upload_img_array.indexOf(del_img);
            upload_img_array.splice(del_index, 1);
        }
    }

    //파일 삭제시 해당 파일 value값 초기화
    var file_input_wrap = document.querySelector('#file_input_wrap');
    if(file_input_wrap.querySelector('#pc_img_file') != null){
        var file_input = file_input_wrap.querySelector('#pc_img_file');
        file_input.value = "";
    }

    //삭제한 img tag image_wrap에서 제거
    var image_wrap = document.querySelector('#pc_img_wrap');
    image_wrap.removeChild(elem.parentNode);

    file_upload_flag = 0;
}
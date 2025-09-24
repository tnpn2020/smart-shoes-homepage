$(document).ready(function(){
    obj.elem["lang_views"] = []; // 언어별 view들을 담을 배열
    obj.elem["lang_btns"] = []; // 언어별 버튼을 담을 배열
    // $('.select-custom').select2({
    //     minimumResultsForSearch: -1
    // });
    //언어 세팅 가져오기
    request_lang_list();
    if(null_exception(page_param.idx)){
        obj.elem.page_title.innerHTML = "공지사항 수정";
    }else{
        obj.elem.page_title.innerHTML = "공지사항 등록";
    }

})

var page_param = data;
var file_index = 1;
var add_sumnote_img_array = []; //sumnote img중 저장되는 이미지를 담는 배열


//View init을 위해 등록된 언어 목록 가져오기
function request_lang_list(){
    $(".loading").fadeIn();
    lb.ajax({
        type:"JsonAjaxPost",
        list : {
            ctl:"Lang",
            param1:"request_lang_list",
        },
        action : lb.obj.address, //웹일경우 ajax할 주소
        response_method : "response_lang_list", //앱일경우 호출될 메소드
        havior : function(result){
            $(".loading").fadeOut();
            //웹일 경우 호출될 메소드
            result = JSON.parse(result);
            response_lang_list(result);
        }    
    });
}

function response_lang_list(result){
    if(result.result == "1"){
        init_view(result.value);
        obj.value.lang_idx = [];
        for(var i = 0; i<result.value.length; i++){
            obj.value.lang_idx.push(result.value[i].idx);
        }
    }else{
        alert(result.message);
    }
}

//언어별 화면 셋팅
function init_view(datas){
    obj.value.lang = datas;
    if(datas.length == 0){ //언어 등록된것이 없음
        alert("언어 설정이 되어있지 않습니다. 개발자에게 문의 주세요.");
    }else{
        // console.log(datas);
        //언어별 데이터 셋팅 해야함
        datas[0]["is_active"] = "1"; //첫번째 언어의 view는 활성화시켜야함으로 추가
        init_lang_btn(datas);
        init_lang_view(datas);
    }
}

//언어 버튼 init
function init_lang_btn(datas){
    // lb.clear_wrap(lb.getElem("lang_btn_wrap"));
    lb.auto_view({
        wrap: "lang_btn_wrap",
        copy: "lang_btn_copy",
        attr: '["data-attr"]',
        json: datas,
        havior: add_lang_btn,
    });
}

function add_lang_btn(elem, data, name, copy_elem){
    if(copy_elem.getAttribute("data-copy") != ""){
        copy_elem.setAttribute("data-copy", "");
        if(data.is_active == "1"){
            copy_elem.classList.add("current");
        }
        copy_elem.onclick = function(){ //버튼을 눌렀을경우 
            change_lang_view(data["idx"]);
        }
        obj.elem.lang_btns.push(copy_elem);
    }
    if(name == "lang"){
        elem.innerHTML = data["name"];
    }
}
//sumnote 등록시 용량 문제로 제대로 저장되지 않는 경우가 많아
//이미지를 첨부할때마다 아마존 서버에 이미지를 업로드하고
//업로드한 이미지 url을 db에 그대로 저장하기 위해
//image url을 가져오는 함수
function request_image_url(file, elem){
    $(".loading").fadeIn();
    lb.ajax({
        type:"AjaxFormPost",
        list : {
            ctl:"Common",
            param1:"request_image_url",
            "file[]" : file,
            path : "notice_content_path", //업로드 경로
        },
        elem : lb.getElem('form'),
        action : "index.php",
        response_method : "reponse_image_url", //앱일경우 호출될 메소드
        havior : function(result){
            //웹일 경우 호출될 메소드
            console.log(result);
            result = JSON.parse(result);
            reponse_image_url(result, elem);
        }    
    });
}

function reponse_image_url(result, elem){
    $(".loading").fadeOut();
    // console.log(result);
    for(var i = 0; i < result.value.length; i++){
        var image_name = obj.link[result.path] + result.value[i];
        add_sumnote_img_array.push(result.value[i]); //게시글 등록시 사용       
        $(elem).summernote('editor.insertImage', image_name);
    }
}

// 새로운 이미지 업로드 함수 (inquiry 방식)
function request_image_url2(file, elem){
    console.log('📤 새로운 이미지 업로드 시작 (inquiry 방식)');
    $(".loading").fadeIn();
    
    lb.ajax({
        type:"AjaxFormPost",
        list : {
            ctl:"Common",
            param1:"request_image_url2",
            "file[]" : file,
            path : "notice_content_path",
        },
        elem : lb.getElem('form'),
        action : lb.obj.address,
        response_method : "response_image_url2",
        havior : function(result){
            console.log('📥 새로운 업로드 응답:', result);
            try {
                result = JSON.parse(result);
                console.log('📋 파싱된 응답:', result);
                response_image_url2(result, elem);
            } catch(e) {
                console.error('❌ JSON 파싱 오류:', e);
                console.error('❌ 원본 응답:', result);
                $(".loading").fadeOut();
            }
        }    
    });
}

function response_image_url2(result, elem){
    $(".loading").fadeOut();
    console.log('📋 새로운 업로드 결과 처리:', result);
    
    if(result.result == "1") {
        console.log('✅ 업로드 성공');
        console.log('📁 업로드 경로:', result.upload_path);
        console.log('📄 업로드된 파일들:', result.value);
        
        for(var i = 0; i < result.value.length; i++){
            var image_name = obj.link[result.path] + result.value[i];
            console.log('🖼️ 이미지 URL:', image_name);
            
            add_sumnote_img_array.push(result.value[i]);
            $(elem).summernote('editor.insertImage', image_name);
        }
    } else {
        console.log('❌ 업로드 실패:', result.message);
        alert('이미지 업로드 실패: ' + result.message);
    }
}


//언어별 input view init
function init_lang_view(datas){
    // lb.clear_wrap(lb.getElem("lang_input_wrap"));
    lb.auto_view({
        wrap: "lang_input_wrap",
        copy: "lang_input_copy",
        attr: '["data-attr"]',
        json: datas,
        havior: add_lang_view,
        end : function(){
            $('.summernote').summernote({
                placeholder: '',
                tabsize:2,
                height: 600,
                minHeight:null,
                maxHeight:null,
                lang:'ko-KR',
                disableResizeEditor: true,
                callbacks: {
                    onImageUpload: function(files) {
                        console.log('🖼️ 서머노트 이미지 업로드 시작 - 새로운 방식 사용');
                        for(var i = 0; i < files.length; i++){
                            request_image_url2(files[i], this); // 새로운 함수 사용
                        }
                    },

                    //파일 삭제시 add_sumnote_img_array에서 제거하여 저장할 파일만 배열에 남겨두기
                    //파일 삭제시 del_sumnote_img_array에 저장하여 파일 수정시 삭제된 파일들을 DB에서 제거하고 서버에서도 삭제
                    onMediaDelete : function(target){
                        var src = target[0].src;
                        var src_array = src.split('/');
                        var del_src = src_array[src_array.length - 1]; //가장 마지막 데이터가 파일이름
                        del_sumnote_img_array.push(del_src); //삭제시킨 이미지 이름 저장
                        if(add_sumnote_img_array.indexOf(del_src) != -1){
                            var index = add_sumnote_img_array.indexOf(del_src);
                            add_sumnote_img_array.splice(index, 1);
                        }
                    }
                }
            });
            // debugger
            // console.log($('.select-custom'))
            console.log(document.querySelector('.select-custom'))
            // document.querySelector('.select-custom').select2({
            //     minimumResultsForSearch: -1
            // });
            
            if(null_exception(page_param.idx)){
                request_notice_detail();
                
            }else{
                obj.elem.save_btn.onclick = function(){
                    request_save();
                }
            }

          
        }
    });
}

//언어별 input view 붙이기
function add_lang_view(elem, data, name, copy_elem){
    if(copy_elem.getAttribute("data-copy") != ""){
        copy_elem.setAttribute("data-copy", "");
        if(data.is_active == "1"){
            copy_elem.style.display = "block";
        }else{
            copy_elem.style.display = "none";
        }
        obj.elem.lang_views.push(copy_elem);
        copy_elem.id = "lang_view_" + data["idx"];
    }
    //썸노트(디스크립션 description)
    if(name == "sum_note"){
        var id = "sum_note_" + data["idx"];
        elem.id = id
        elem.setAttribute("name",id);
        // elem.src = lb.obj.address + "?ctl=move&param=sumnote&id=" + id;
    }else if(name == "title"){ //상품명
        elem.name = "title_" + data["idx"];
        elem.id = "title_" + data["idx"];
    }else if(name == "category"){ //카테고리
        elem.setAttribute('id',"category_"+data.idx)
    }
}

//언어 뷰 바꾸기
function change_lang_view(index){
    if(null_exception(index.index)){
        var num = index.index;
    }else{
        var num = index;
    }
    // console.log("change_lang_view : " + num);
    // console.log(obj.elem.lang_btns);
    for(var i=0; i<obj.elem.lang_views.length; i++){
        var lang_view = obj.elem.lang_views[i];
        var lang_btn = obj.elem.lang_btns[i]; 
        var change_view_id = "lang_view_" + num;
        if(lang_view.id == change_view_id){ //나타나야할 view라면 display:block
            lang_view.style.display = "block";
            lang_btn.classList.add("current");
        }else{
            lang_view.style.display = "none";
            lang_btn.classList.remove("current");
        }
    }

    if(null_exception(index.mode)){
        if(index.mode == "save_title_fail"){
            var title_elem =document.getElementById("title_"+num);
            title_elem.focus();
        }else{
            var edit_elem = document.getElementById("sum_note_"+num);
            $(edit_elem).summernote('focus');
        }
    }
}

function request_save(){
    if(confirm("공지사항을 저장하시겠습니까?")){
        request_notice_register();
    }
}


function request_notice_register(){
    var id = "sum_note_1";
    var sum_obj = lb.getElem(id).nextSibling.children[2].children[2].innerHTML;

    var edit_elem = document.getElementById("sum_note_1");
    var note_edit_elem = edit_elem.nextSibling.children[2].children[2];
    widthPercent(note_edit_elem);
    var note_content = note_edit_elem.innerHTML;

    // 유효성 검사
    if(obj.elem.title.value.length == 0){
        alert("제목을 입력하지 않았습니다.");
        return;
    }

    if(note_content.trim() == ""){
        alert("내용을 입력하지 않았습니다.");
        return;
    }

    // 카테고리 선택 확인
    if(obj.elem.main_category.value == "" || obj.elem.main_category.value == null){
        alert("카테고리를 선택하지 않았습니다.");
        return;
    }
 
    // 중요도 체크박스 값 확인
    var isImportant = document.getElementById('is_important').checked ? 1 : 0;
    
    $(".loading").fadeIn();
    
    // 기존 방식으로 등록 (파일은 기존 시스템 사용)
    lb.ajax({
        type:"AjaxFormPost",
        list : {
            ctl:"AdminMenu1",
            param1:"notice_register",
            add_img_array : JSON.stringify(add_sumnote_img_array),
            sumnote: sum_obj,
            category: obj.elem.main_category.value,
            kind: isImportant
        },
        elem:obj.elem.form,
        action : lb.obj.address,
        response_method : "reponse_notice_register",
        havior : function(result){
            $(".loading").fadeOut();
            console.log(result);
            result = JSON.parse(result);
            reponse_notice_register(result);
        }    
    });
}

function reponse_notice_register(result){
    if(result.result == "1"){
        alert("공지사항이 등록되었습니다.");
        move_notice_page();
    }else{
        alert(result.message);
    }
}

function move_notice_page(){
    location.href = "?ctl=move&param=adm&param1=menu2_notice";
}

function widthPercent(data) {
    lb.traverse(data, function (elem) {
        if (elem.nodeName == "IMG") {
            var split = elem.style.cssText.split("width");
            if (split.length > 1) {
                elem.style.height = "auto";
            }
            var style = elem.style.cssText;
            elem.removeAttribute('style');
            elem.setAttribute('style',style);
        }
    });
}

function request_notice_detail(){
    lb.ajax({
        type:"JsonAjaxPost",
        list : {
            ctl:"AdminCrm",
            param1:"notice_detail",
            target : page_param.idx,
        },
        action : lb.obj.address, //웹일경우 ajax할 주소
        response_method : "reponse_notice_detail", //앱일경우 호출될 메소드
        havior : function(result){
            //웹일 경우 호출될 메소드
            // console.log(result);
            result = JSON.parse(result);
            reponse_notice_detail(result);
        }    
    })
}

function reponse_notice_detail(result){
    if(result.result == "1"){
        obj.elem.save_btn.onclick = function(){
            if(confirm("공지사항을 수정하시겠습니까?")){
                request_notice_modify();
            }
        }

        // obj.elem.select_kind.value = result.value[0].kind;
        // $(obj.elem.select_kind).trigger('change');
        // obj.elem.select_kind.value = 4;


        var lang_idx_arr = obj.value.lang_idx;
        for(var i = 0; i<lang_idx_arr.length; i++){
            var edit_elem = document.getElementById("sum_note_"+lang_idx_arr[i]);
            var title_elem = document.getElementById('title_'+lang_idx_arr[i]);
            if(lang_idx_arr[i] == result.value[i].lang_idx){
                title_elem.value = result.value[i].title;
                $(edit_elem).summernote('code',result.value[i].content);
            }
        }
    }else{
        alert(result.message);
    }
}

function request_notice_modify(){

    // 언어배열
    var lang_idx_arr = obj.value.lang_idx;
    // 제목 배열
    var title_arr = [];
    // 내용배열
    var description_arr = [];
    for(var i= 0; i<lang_idx_arr.length; i++){
        // 제목
        var title_elem = document.getElementById("title_"+lang_idx_arr[i]);
        title_arr.push(title_elem.value);
        // 내용
        // summernote api elem
        var edit_elem = document.getElementById("sum_note_"+lang_idx_arr[i]);
        // 에디터 elem
        var note_edit_elem = edit_elem.nextSibling.children[2].children[2];
        widthPercent(note_edit_elem);
        var note_content = note_edit_elem.innerHTML;
        description_arr.push(note_content);
    }

    // 언어별 제목, 내용 체크
    for(var i= 0; i<lang_idx_arr.length; i++){
        if(title_arr[i].trim() == ""){
            alert("제목을 모두 입력하지 않았습니다.");
            return;
        }
        if(description_arr[i].trim() == ""){
                gu.alert({
                description : "언어별 내용을 모두 입력하지 않았습니다.", //내용(string 문자열) 필수
                title : null,  //제목(string 문자열)  null이면 "알림"으로 처리함
                response_method : null, //확인버튼을 눌럿을경우 호출될 메소드function 이름(string 문자열)  null일 경우 메소드를 실행하지않음
                response_param : null //확인버튼을 눌렀을 경우 호출될 메소드function에게 전달할 데이터(json 데이터형)
            });
            return;
        }
    }

    $(".loading").fadeIn();
    // obj.elem.select_kind.value = 4;

    lb.ajax({
        type:"JsonAjaxPost",
        list : {
            ctl:"AdminCrm",
            param1:"notice_modify",
            target : page_param.idx,
            title : JSON.stringify(title_arr),
            content : JSON.stringify(description_arr),
            kind : obj.elem.select_kind.value,
        },
        action : lb.obj.address, //웹일경우 ajax할 주소
        response_method : "reponse_notice_modify", //앱일경우 호출될 메소드
        havior : function(result){
            //웹일 경우 호출될 메소드
            console.log(result);
            result = JSON.parse(result);
            reponse_notice_modify(result);
        }    
    });
}

function reponse_notice_modify(result){
    if(result.result == "1"){
        $(".loading").fadeOut();
        alert("공지사항이 수정되었습니다.");
        move_notice_page();
    }else{
        alert(result.message);
    }
}
function add_file_input(elem){
    var file_div = elem.parentNode.parentNode //파일 input을 추가할 wrap
    var file_input_length = file_div.querySelectorAll('input[type="file"]').length;
    // if(file_input_length == obj.value.max_file){
        //     open_alert("첨부파일은 " + obj.value.max_file + "개까지 등록하실 수 있습니다.", null);
        //     return;
        // }
        
        //현재 파일 input의 수가 obj.value.max_file의 수와 같으면 더이상 추가하지 못하게 막기
        
    var file_input = obj.elem.file_upload_copy.cloneNode(true);
    file_input.removeAttribute('id');
    

    //file_input id값 설정
    var attr = file_input.querySelectorAll('[data-attr]');
    for(var i = 0; i < attr.length; i++){
        if(attr[i].getAttribute('data-attr') == "file_name"){ //파일 이름 id값 설정
            attr[i].id = "file_name_" + file_index;
        }else if(attr[i].getAttribute('data-attr') == "file"){
            attr[i].id = "file_" + file_index; //파일 input id값 설정
            attr[i].setAttribute('name', 'add_file[]'); //파일 input name 설정
            // attr[i].setAttribute('data-file-lang-' + obj.value.current_lang, obj.value.current_lang); //파일등록 여부 체크시 사용
        }else if(attr[i].getAttribute('data-attr') == "file_label"){
            attr[i].id = "file_label_" + file_index; //파일 라벨 id값 설정
            attr[i].setAttribute('for', "file_" + file_index); //파일 라벨 for 설정
        }else if(attr[i].getAttribute('data-attr') == "del_file_btn"){
            attr[i].setAttribute('onclick', 'del_file_input(this)'); //파일 input 삭제 이벤트
        }
    }
    // console.log(file_input);
    // return

    file_index++;

    file_div.appendChild(file_input);
}
//파일 추가 함수
function add_file(elem){
    for(var k = 0; k < elem.files.length; k++){
        //파일 용량 체크
        if(elem.files[k] != null){
            var max_size = 10 * 1024 * 1024;
            var file_size = elem.files[k].size;
            if(file_size > max_size){
                open_alert("10MB 이하의 파일 첨부 가능" + "<br/><br/>" + "현재파일 : " + Math.round(file_size / 1024 / 1024 * 100) / 100 + "MB");
                elem.value = "";
                return;
            }
        }
    }

    var file_name = elem.value.split('\\').pop(); // 역슬래쉬로 나눈 배열의 마지막값이 파일 이름
    console.log(elem);
    elem.parentNode.querySelector('input[type="text"]').value = file_name;
}

const del_file_input = (elem) => {
    elem.parentNode.remove()
}

// 새로운 파일 업로드 함수
function uploadNoticeFiles() {
    const fileInputs = document.querySelectorAll('input[name="add_file[]"]');
    let hasFiles = false;
    let fileList = [];
    
    fileInputs.forEach((input, index) => {
        if (input.files && input.files[0]) {
            fileList.push(input.files[0]);
            hasFiles = true;
        }
    });
    
    if (!hasFiles) {
        return Promise.resolve({ files: [] });
    }
    
    return new Promise((resolve, reject) => {
        console.log('📤 공지사항 파일 업로드 시작');
        $(".loading").fadeIn();
        
        // 파일을 하나씩 업로드하는 방식으로 변경
        if (fileList.length > 0) {
            uploadSingleFile(fileList[0], resolve, reject);
        } else {
            reject(new Error('선택된 파일이 없습니다.'));
        }
    });
}

// 단일 파일 업로드 함수
function uploadSingleFile(file, resolve, reject) {
    console.log('📤 단일 파일 업로드:', file.name);
    
    lb.ajax({
        type:"AjaxFormPost",
        list : {
            ctl:"Common",
            param1:"request_notice_file_upload",
            "notice_file" : file
        },
        elem : lb.getElem('form'),
        action : lb.obj.address,
        response_method : "response_notice_file_upload",
        havior : function(result){
                $(".loading").fadeOut();
                console.log('📥 파일 업로드 응답:', result);
                
                try {
                    const data = JSON.parse(result);
                    console.log('📡 파싱된 데이터:', data);
                    
                    if (data.result === "1") {
                        console.log('✅ 파일 업로드 성공:', data);
                        resolve(data);
                    } else {
                        console.error('❌ 파일 업로드 실패:', data.message);
                        reject(new Error(data.message));
                    }
                } catch (e) {
                    console.error('❌ JSON 파싱 에러:', e);
                    console.error('❌ 원본 응답:', result);
                    reject(new Error('서버 응답이 올바르지 않습니다: ' + result.substring(0, 100)));
                }
            }    
        });
}

const cancle = () => {
    window.location.href = "/?ctl=move&param=adm&param1=menu2_notice"
}
const del_default_file = () =>{
    obj.elem.add_file_input_0.value = "";
    obj.elem.add_file_name_0.value = "";

}
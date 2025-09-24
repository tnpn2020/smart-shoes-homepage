obj.flag.double_click = true;
$(document).ready(function () {
    request_lang_list();
});


//개인정보방침 or 이용약관 page에서 현재 setting된 언어 list를 가져오는 함수 (주석 달기)
function request_lang_list(){
    lb.ajax({
        type : "JsonAjaxPost",
        list : {
            ctl : "AdminMenu1",
            param1 : "request_lang_list",
            terms_idx : data.terms_idx,
        },
        action : "index.php",
        havior : function(result){
            // console.log(result);
            result = JSON.parse(result);
            // console.log(result);
            if(result.result == 1){
                for(var i = 0; i < result.value.length; i++){
                    //setting된 언어의 개수만큼 textarea 생성
                    var textarea = document.createElement('textarea');
                    if(result.terms.length > 0){
                        if(result.value[i].idx == result.terms[i].lang_idx){
                            textarea.innerHTML = result.terms[i].content.replace(/(<br>|<br\/>|<br \/>)/g, '\r\n'); //br태그를 \n으로 바꾸고 뿌려주기
                        }
                    }
                    
                    textarea.style.display = "none";
                    textarea.setAttribute('rows', '16');
                    textarea.id = "textarea_" + result.value[i].idx;
                    
                    //setting된 언어의 개수만큼 언어 list 생성
                    var li = document.createElement('li');
                    var a = document.createElement('a');
                    a.innerHTML = result.value[i].name;
                    if(i == 0){ //처음 페이지에 들어오면 첫번째 li에 current = 추가
                        li.classList.add('current');
                        textarea.style.display = "";
                    }
                    li.id = result.value[i].idx;
                    li.appendChild(a);
                    li.setAttribute('onclick', 'change_lang(this)');

                    obj.elem.lang_list.appendChild(li);
                    obj.elem.content.appendChild(textarea);
                }
                
            }else{
                alert("관리자에게 문의해주세요");
            }
        }
    });
}


//개인정보 방침 or 이용약관에서 생성된 언어 list를 클릭하면
//클릭한 list에 current class를 추가하고 클릭한 list에 해당하는 textarea block
function change_lang(elem){
    //모든 list에 current class를 제거하고 textarea display none
    var li = obj.elem.lang_list.querySelectorAll('li');
    for(var i = 0 ; i < li.length; i++){
        li[i].classList.remove('current');
        document.querySelector('#textarea_' + (i + 1)).style.display = "none";
    }
    //클릭한 list에 current 추가, 클릭한 list에 해당하는 textarea display block
    elem.classList.add('current');
    document.querySelector('#textarea_' + elem.id).style.display = "";
}

//개인정보 방침 or 이용약관 page에서 선택된 언어에 맞게 terms 테이블에 입력한 내용 저장 (주석달기)
function register_terms(){
    if(obj.flag.double_click){
        obj.flag.double_click = false;
        var li = obj.elem.lang_list.querySelectorAll('li');
        var textarea = obj.elem.content.querySelectorAll('textarea');
        var terms_data = []; //content와 lang_idx을 가지는 배열
        var flag = false; //content 내용 입력 체크 변수
        for(var i = 0; i < textarea.length; i++){
            var content = [];
            //언어중 입력안한 textarea가 있으면 flag를 true로 바꿔줌
            if(textarea[i].value == ""){
                flag = true;
                content = [];
            }else{ //입력한 경우 현재 textarea에 해당하는 lang_idx와 content를 넣어줌
                var text = textarea[i].value.replace(/(?:\r\n|\r|\n)/gi, '<br />');
                text = text.replace(/'/g, "\\'");
                content.push(text);
                content.push(li[i].id);
            }
            terms_data.push(content);
        }
        if(flag){
            gu.alert({
                description : "모든 언어의 내용을 입력해주세요.",
                title : null,
                response_method : null,
            });
            obj.flag.double_click = true;
        }else{
            lb.ajax({
                type : "JsonAjaxPost",
                list : {
                    ctl : "AdminMenu1",
                    param1 : "register_terms",
                    terms_data : JSON.stringify(terms_data),
                    terms_idx : data.terms_idx,
                },
                action : "index.php",
                havior : function(result){
                    // console.log(result);
                    result = JSON.parse(result);
                    // console.log(result);
                    if(result.result == 1){
                        gu.alert({
                            description : "등록되었습니다.",
                            title : null,
                            response_method : "location.reload",
                        });
                    }else{
                        obj.flag.double_click = true;
                    }
                }    
            });
        }
    }
}
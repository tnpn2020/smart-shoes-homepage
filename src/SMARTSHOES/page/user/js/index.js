$(document).ready(function() {
    loadMainPromotion();
    init_popup();
});

// 메인 페이지 promotion 데이터 로드
function loadMainPromotion() {
    lb.ajax({
        type: "JsonAjaxPost",
        list: {
            ctl: "Userpage",
            param1: "get_first_promotion"
        },
        havior: function(result) {
            result = JSON.parse(result);
            if(result.result == "1") {
                response_main_promotion(result);
            }
        }
    });
}

// 메인 페이지 promotion 데이터 응답 처리
function response_main_promotion(result) {
    var promotion = result.value;
    
    if(promotion) {
        // 행사명 설정
        $('#promo-title').text(promotion.event_name);
        
        // 기간 설정
        $('#promo-period').text(promotion.event_period);
        
        // 위치 설정
        $('#promo-location').text(promotion.event_location);
        
        // 메인 이미지 설정
        if(promotion.main_image_path) {
            $('#promo-main-img').attr('src', promotion.main_image_path);
            $('#promo-main-img').attr('alt', promotion.event_name);
        }
        
        // 자세히 보기 링크 설정
        $('#promo-detail-link').attr('href', '?param=promotion');
    }
}

const init_popup = () =>{
    // privacy_content
    lb.ajax({
    type: "JsonAjaxPost",
    list: {
        ctl: "Userpage",
        param1: "init_popup",
    },
    action: "index.php",
    havior: function(result) {
        result = JSON.parse(result);
        if(result.result==1){

            var arr = [];
            result.value.forEach((data)=>{
                var cookie_exists = false;
                var cookie_value = null;
                
                // 쿠키 존재 여부 확인
                cookie_exists = lb.cookie({
                    type:'get',
                    name: 'smartshoes_popup'+data.idx,
                    havior: function(value) {
                        cookie_value = value;
                    }
                });
                
                //console.log('팝업 ' + data.idx + ' 쿠키 존재:', cookie_exists, '값:', cookie_value);
                
                // 쿠키가 없거나 값이 1이 아닌 경우에만 표시
                if(!cookie_exists || (cookie_value != 1 && cookie_value != '1')){     
                    //console.log('팝업 ' + data.idx + ' 표시 대상에 추가');
                    arr.push(data);
                } else {
                    //console.log('팝업 ' + data.idx + ' 일주일간 숨김 상태');
                }
            })
            if(arr.length>0){
                var popup_modal = document.getElementById('popup_modal');
                popup_modal.style.display = 'block';
            // }else{
                //TODO:팝업추가 작업 + 클릭이벤트
                lb.auto_view({
                    wrap: "popup_wrap",
                    copy: "popup_copy",
                    attr: '["data-attr"]',
                    json: arr,
                    havior: function(elem, data, name, copy_elem) {
                        if(copy_elem.getAttribute("data-copy") != "") {
                            copy_elem.setAttribute("data-copy", "");
                        } 
                        if(name == "popup_img") {
                            elem.src = obj.link.pc_popup_img_path +  data.pc_file_name;
                            elem.onclick = () => {
                                window.location.href = data.link
                            }
                            elem.style.cursor ="pointer";
                        }
                        if(name == "checkbox") {
                            elem.value = data.idx;
                            elem.id = "check_" + data.idx;
                        }
                        if(name == "label") {
                            elem.setAttribute('for', "check_" + data.idx);
                            elem.textContent = "일주일간 보지 않기";
                        }
                        if(name == "close") {
                            // 팝업 컨테이너에 data_id 설정
                            copy_elem.setAttribute("data_id", data.idx);
                            elem.onclick = ()=>{
                                // 일주일간 보지 않기 체크되었는지 확인
                                if(document.getElementById('check_' + data.idx).checked == true) {
                                    lb.cookie({
                                        type: "set",
                                        name: "smartshoes_popup" + data.idx,
                                        value: 1,
                                        day : 7,
                                    });
                                    //console.log('팝업 ' + data.idx + ' 일주일간 숨김 설정');
                                }
                                
                                // 현재 팝업 제거
                                var remove_elem = document.querySelector('[data_id="'+data.idx+'"]');
                                if(remove_elem) {
                                    remove_elem.remove();
                                }
                                
                                // 남은 팝업이 있는지 확인
                                var popup_modal = document.getElementById('popup_modal');
                                var remainingPopups = popup_modal.querySelectorAll('.popup_con[data_id]');
                                
                                if(remainingPopups.length == 0){
                                    popup_modal.style.display = 'none';
                                }
                            }
                        }
                    },

                });
                }
            }
            
        }
    });
}
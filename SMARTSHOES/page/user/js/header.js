$(document).ready(function(){

    $('.header').hover(function(){
        // $('.depth').stop().slideDown(200);
        $('.depth').css('display','block');
		//$('.depth').slideDown( 200 );
        //$('.depth_bg').slideDown( 400 );
		$(this).addClass('hover');
        
    },function(){
        // $('.depth').stop().slideUp();
        $('.depth').css('display','none');
        //$('.depth_bg').css('display','none');
		$(this).removeClass('hover');
    });

    //toTop 클릭시 부드럽게 스크롤
    $(".scroll").click(function(event){        
        $('html,body').animate({scrollTop:$(this.hash).offset().top}, 500);
    });

    //toTop 일정 이상 스크롤했을때 나타남
    $('html, body').scrollTop(0);
    $(window).scroll(function(){
        var scroll = $(this).scrollTop();
        var scrollBottom = $(document).height() - $(window).height() - $(window).scrollTop();
        if(scroll >= 300){
            $('.toTop').fadeIn();
        }else{
            $('.toTop').fadeOut();
        }

        if(scrollBottom <= 280){
            $("#sideBtn").addClass("bottom");
        }else{
            $("#sideBtn").removeClass("bottom");
        }
        /* if(scrollBottom <= 20){
            $("#sideBtn").css({
                "top" : "71%",
                });
        }else{
            $("#sideBtn").css({
                "top" : "79%",
                });
        } */

    });
    /* $('.toTop').click(function(e){
        $('html, body'),stop().animate({scrollTop:0},500)
    }); */

    // 모바일 메뉴 초기화는 별도로 실행
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', mobile_menu_init);
    } else {
        mobile_menu_init();
    }
    // request_lang_icon();
});

var cur_lang = 1;

function mobile_menu_init(){
    /* -----모바일 메뉴 toggle----- */
    //console.log('mobile_menu_init 함수 실행됨');
    
    const toggleBtn = document.getElementById("toggleBtn");
    const mobileMenu = document.getElementById("mobileMenu");
    const blank = document.getElementById("blank");
    const header = document.querySelector(".header");
    
    //console.log('toggleBtn:', toggleBtn);
    //console.log('mobileMenu:', mobileMenu);
    //console.log('blank:', blank);
    //console.log('header:', header);

    function handleToggleClick() {
        
        // 햄버거 버튼 애니메이션
        toggleBtn.classList.toggle("active");
        
        // 모바일 메뉴 토글
        mobileMenu.classList.toggle("active");
        
        // 오버레이 토글
        blank.classList.toggle("active");
        
        // 헤더에 클래스 추가 (필요시)
        header.classList.toggle("menu-open");
        
        // body 스크롤 방지/허용
        document.body.classList.toggle("menu-open");

    }

    function handleOverlayClick() {
        // 오버레이 클릭시 메뉴 닫기
        toggleBtn.classList.remove("active");
        mobileMenu.classList.remove("active");
        blank.classList.remove("active");
        header.classList.remove("menu-open");
        document.body.classList.remove("menu-open");
    }

    // 이벤트 리스너 추가
    if (toggleBtn) {
        toggleBtn.addEventListener("click", handleToggleClick);
    } else {
        console.error('toggleBtn 요소를 찾을 수 없음');
    }
    
    if (blank) {
        blank.addEventListener("click", handleOverlayClick);
    } else {
        console.error('blank 요소를 찾을 수 없음');
    }
    
    // ESC 키로 메뉴 닫기
    document.addEventListener("keydown", function(e) {
        if (e.key === "Escape" && mobileMenu.classList.contains("active")) {
            handleOverlayClick();
        }
    });
}


    // 모바일 메뉴 접었다 펼쳐지게
    $(document).ready(function(){

        $(".depth_open>a").click(function(){
			$(this).toggleClass('active');

            var moDepth = $(this).next("ul");

            if( moDepth.is(":visible") ){
                moDepth.slideUp();
            }else{
                moDepth.slideDown();
            }
        });
    });

//header fix
// $('html, body').scrollTop(0);
//     //일정 높이 이상 스크롤하면 "fix" class 추가
//     $(window).scroll(function(){
//         var scroll = $(this).scrollTop();
//         if(scroll >= 100){
//             $(header).addClass("fix");
//         }else{
//             $(header).removeClass("fix");
//         }
// });


//페이지 로딩시 lang table에서 icon 이미지를 가져와
//언어변경 Btn icon에 적용 
function request_lang_icon(){
    lb.ajax({
        type : "JsonAjaxPost",
        list : {
            ctl : "Userpage",
            param1 : "request_lang_icon",
        },
        action : "index.php",
        havior : function(result){
            result = JSON.parse(result);
            if(result.result == 1){
                init_pc_lang_btn(result.value);
                // init_mob_lang_btn(result.value);
            }
        }    
    });
}

//pc버전 언어 세팅
// function init_pc_lang_btn(value){
//     var lest_lang = lb.getElem("current_lang");
//     var lest_lang_li = lest_lang.querySelectorAll('img');
//     for(var i = 0; i < value.length; i++){
//         lest_lang_li[i].src = "https://lbcontents.s3.ap-northeast-2.amazonaws.com/images/IPIACOSMETIC/" + value[i].icon_file_name;
//         lest_lang_li[i].alt = value[i].name;
//         lest_lang_li[i].setAttribute('onclick', 'lang_change(' + (i + 1) + ')');
//     }
// }

// //모바일버전 언어 세팅
// function init_mob_lang_btn(value){
//     var lang_li = obj.elem.mob_lang_btn.querySelectorAll('img');
//     for(var i = 0; i < lang_li.length; i++){
//         lang_li[i].src = "https://lbcontents.s3.ap-northeast-2.amazonaws.com/images/IPIACOSMETIC/" + value[i].icon_file_name;
//         lang_li[i].alt = value[i].name;
//         lang_li[i].setAttribute('onclick', 'lang_change(' + (i + 1) + ')');
//     }
// }

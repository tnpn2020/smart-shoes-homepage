<!-- 모달 : 약관 -->
<div class="modal_wrap" style="display: none" id ="terms_modal">
    <div class="modal_container">
        <div class="modal_box terms">
            <p class="title" id="terms_title">개인정보처리방침</p>

            <div class="md_content">
                <div class="text_box" id = 'terms_content'>약관 내용이 들어옵니다.</div>
            </div>

            <div class="modal_btn_box">
                <button class="main" onclick="close_terms_modal()">닫기</button>
            </div>
        </div>
    </div>
</div>


<!-- 모달 : alert -->
<div class="modal_wrap alert" style="display: none" id="alert_modal">
    <div class="modal_container">
        <div class="modal_box alert">
            <p class="title">알림</p>

            <div class="md_content">
                <div class="text_box" id="alert_modal_content"></div>
            </div>

            <div class="modal_btn_box">
                <!-- 버튼 필요하면 button 추가 / 하나만 쓸 때 class="main" -->
                <!-- <button class="sub">취소</button> -->
                <button class="main" id="close_modal" >확인</button>
            </div>
        </div>
    </div>
</div>



<!-- 모달 : 팝업 -->
<div class="modal_wrap popup" style="display: none" id="popup_modal" >
    <div class="modal_popup" data-wrap="popup_wrap">
        <!-- 추가될때는 .popup_con 전체 추가 -->
        <!-- <div class="popup_con">
            <div class="popup_img">
                <img data-attr= "popup_img" id="popup_img" src="" alt="">
            </div>
            <div class="pop_btn">
                <div class="chk_con">
                    <input type="checkbox" name="popup" id="today" data-attr="checkbox">
                    <label for="today" data-attr="label">오늘보지않기</label>
                </div>

                <button data-attr="close">닫기</button>
            </div>
        </div> -->
    </div>
</div>

<script>

</script>
<div class="modal user-modal d-none" id="confirm">
    <div class="modal-wrap modal-sm">
        <div class="modal-container">
            <!-- 모달 본문 -->
            <div class="modal-container-inner">
                <div class="modal-head bold" id="confirm_title">웹페이지 메시지</div>
                <div class="modal-body" id="confirm_content">
                    개인정보보호법에 의거 보호자의 서명이 포함된 개인정보 동의서를 안전하게 보관하고 회원이 탈퇴한 후에는 완전히 파기해야합니다.
                </div>
                <ul class="btn-container align-right">
                    <li><button type="button" class="btn btn-primary" onclick="gu.confirm_positive()">확인</button></li>
                    <li><button type="button" class="btn btn-ghost" onclick ="gu.confirm_negative()">취소</button></li>
                </ul>
            </div>
            <!-- 모달 본문 // -->
        </div>
    </div>
</div>
<!-- 버튼 2개 모달 // -->
<div class="modal user-modal d-none" id="alert">
    <div class="modal-wrap modal-sm">
        <div class="modal-container">
            <span class="modal-close" onclick ="gu.alert_close()">&#10005;</span><!-- 모달 닫기 // -->
            <!-- 모달 본문 -->
            <div class="modal-container-inner">
                <div class="modal-head bold" id="alert_title">알림</div>
                <div class="modal-body" id="alert_content">
                    개인정보보호법에 의거 보호자의 서명이 포함된 개인정보 동의서를 안전하게 보관하고 회원이 탈퇴한 후에는 완전히 파기해야합니다.
                </div>
                <ul class="btn-container align-right">
                    <li><button type="button" class="btn btn-primary" onclick="gu.alert_close()">닫기</button></li>
                </ul>
            </div>
            <!-- 모달 본문 // -->
        </div>
    </div>
</div>
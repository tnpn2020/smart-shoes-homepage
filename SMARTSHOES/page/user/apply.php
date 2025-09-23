<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no">

	<title>스마트신발</title>

    <!-- META -->
    <?php include_once $this->dir."page/user/inc/meta.php"; ?>

    
    <!-- FONT -->
    <?php echo $this->utillLang->font_link(); ?>
    
    <!-- CSS -->
    <?php include_once $this->project_path."inc/common_css.php"; ?>
    <link rel="stylesheet" type="text/css" href="<?php echo $this->project_path;?>css/apply.css<?php echo $this->version;?>"/>

    
	<!-- SCRIPT -->
    <?php include_once $this->project_path."inc/common_js.php"; ?>
    <script src="<?php echo $this->project_path;?>js/apply.js<?php echo $this->version;?>"></script>

</head>
<body>
    <?php include_once $this->project_path."include/header.php"; ?>

    <!-- 서브페이지 상단 타이틀 -->
    <section class="sub-banner sub-apply">
        <div class="container">
            <div class="sub-banner-con">
                <h1>신청하기</h1>
                <p>스마트신발 서비스 이용을 위한 신청서를 작성해주세요.</p>
            </div>
        </div>
        <figure><img src="<?php echo $this->project_path;?>img/sub-apply.png"></figure>
    </section>


    <!-- 신청서 섹션 -->
    <section class="application-section">
        <div class="container bd-sm">
            <div class="application-content">

                <form class="application-form">
                    <!-- 신청 유형 -->
                    <div class="form-section form-section1">
                        <h3>신청 유형</h3>
                        <div class="radio-group">
                            <label class="radio-option">
                                <input type="radio" name="applicant-type" value="user" required>
                                <span class="radio-custom"></span>
                                <span class="radio-label">사용자 본인</span>
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="applicant-type" value="guardian" required>
                                <span class="radio-custom"></span>
                                <span class="radio-label">보호자</span>
                            </label>
                        </div>
                    </div>

                    <!-- 기본 정보 -->
                    <div class="form-section">
                        <h3>기본 정보</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="name">이름</label>
                                <input type="text" id="name" name="name" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="birthdate">생년월일</label>
                                <input type="date" id="birthdate" name="birthdate" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="phone">연락처</label>
                                <input type="tel" id="phone" name="phone" placeholder="010-0000-0000" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">이메일</label>
                                <input type="email" id="email" name="email" required>
                            </div>
                        </div>
                    </div>

                    <!-- 주소 -->
                    <div class="form-section">
                        <h3>주소</h3>
                        <div class="form-group">
                            <label for="address">주소</label>
                            <input type="text" id="address" name="address" placeholder="주소를 입력해주세요" required>
                        </div>
                    </div>

                    <!-- 유입 경로 (숨김 처리) -->
                    <div class="form-section" style="display: none;">
                        <h3>유입 경로</h3>
                        <div class="checkbox-group">
                            <label class="checkbox-option">
                                <input type="checkbox" name="traffic-sources" value="search-engine">
                                <span class="checkbox-custom"></span>
                                <span class="checkbox-label">검색엔진</span>
                            </label>
                            <label class="checkbox-option">
                                <input type="checkbox" name="traffic-sources" value="youtube">
                                <span class="checkbox-custom"></span>
                                <span class="checkbox-label">유튜브</span>
                            </label>
                            <label class="checkbox-option">
                                <input type="checkbox" name="traffic-sources" value="sns">
                                <span class="checkbox-custom"></span>
                                <span class="checkbox-label">SNS</span>
                            </label>
                            <label class="checkbox-option">
                                <input type="checkbox" name="traffic-sources" value="other">
                                <span class="checkbox-custom"></span>
                                <span class="checkbox-label">기타</span>
                            </label>
                        </div>
                    </div>

                    <!-- 신청 사유 -->
                    <div class="form-section">
                        <h3>신청 사유</h3>
                        <div class="form-group">
                            <label for="reason">신청 사유 선택</label>
                            <select id="reason" name="reason" required>
                                <option value="">신청 사유를 선택해주세요</option>
                                <option value="elderly-care">노인 케어</option>
                                <option value="disability-support">장애인 지원</option>
                                <option value="health-monitoring">건강 모니터링</option>
                                <option value="safety-protection">안전 보호</option>
                                <option value="family-care">가족 케어</option>
                                <option value="other">기타</option>
                            </select>
                        </div>
                    </div>

                    <!-- 추가 요청사항 -->
                    <div class="form-section">
                        <h3>추가 요청사항</h3>
                        <div class="form-group">
                            <label for="additional-requests">추가 요청사항</label>
                            <textarea id="additional-requests" name="additional-requests" rows="4" placeholder="추가로 요청하시는 사항이 있으시면 작성해주세요"></textarea>
                        </div>
                    </div>

                    <!-- 동의 사항 -->
                    <div class="form-section">
                        <h3>동의 사항</h3>
                        <div class="checkbox-group">
                            <label class="checkbox-option required">
                                <input type="checkbox" name="terms-agreement" required>
                                <span class="checkbox-custom"></span>
                                <span class="checkbox-label">이용약관에 동의합니다</span>
                                <button type="button" class="terms-detail-btn" onclick="showTermsModal(2)">자세히</button>
                            </label>
                            
                            <label class="checkbox-option required">
                                <input type="checkbox" name="privacy-agreement" required>
                                <span class="checkbox-custom"></span>
                                <span class="checkbox-label">개인정보 처리방침에 동의합니다</span>
                                <button type="button" class="terms-detail-btn" onclick="showTermsModal(1)">자세히</button>
                            </label>
                            
                            <label class="checkbox-option">
                                <input type="checkbox" name="marketing-agreement">
                                <span class="checkbox-custom"></span>
                                <span class="checkbox-label">마케팅 정보 수신에 동의합니다 (선택)</span>
                            </label>
                        </div>
                    </div>

                    <!-- 제출 버튼 -->
                    <div class="form-section">
                        <button type="submit" class="submit-btn">신청하기</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- 성공 모달 -->
    <div id="successModal" class="modal" style="display: none;">
        <div class="modal-content success-modal">
            <div class="success-content">
                <div class="success-icon-wrapper">
                    <div class="success-icon"></div>
                </div>
                <h2>신청이 완료되었습니다</h2>
                <p>담당자가 확인 후 연락드리겠습니다.</p>
                <button class="btn btn-primary btn-confirm" onclick="goToIndex()">확인</button>
            </div>
        </div>
    </div>

    <!-- 약관 보기 모달 -->
    <div id="termsModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="termsTitle">약관 내용</h2>
                <span class="modal-close" onclick="closeTermsModal()">&times;</span>
            </div>
            <div class="modal-body">
                <div class="terms-content" id="termsContent">
                    <!-- 약관 내용이 여기에 로드됩니다 -->
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-ghost" onclick="closeTermsModal()">닫기</button>
                <button class="btn btn-primary" id="agreeBtn" onclick="agreeToTerms()">동의</button>
            </div>
        </div>
    </div>

    <style>
    .checkbox-option {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        position: relative;
    }

    .terms-detail-btn {
        margin-left: 10px;
        padding: 4px 8px;
        background: transparent;
        color: #666;
        border: 1px solid #ddd;
        border-radius: 3px;
        font-size: 11px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .terms-detail-btn:hover {
        background: #f8f9fa;
        color: #333;
        border-color: #999;
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
    }

    .modal-content {
        background-color: white;
        margin: 5% auto;
        padding: 0;
        border-radius: 8px;
        width: 90%;
        max-width: 600px;
        max-height: 80vh;
        display: flex;
        flex-direction: column;
    }

    .modal-header {
        padding: 20px;
        border-bottom: 1px solid #dee2e6;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h2 {
        margin: 0;
        font-size: 18px;
    }

    .modal-close {
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
        color: #999;
    }

    .modal-close:hover {
        color: #333;
    }

    .modal-body {
        padding: 20px;
        overflow-y: auto;
        flex: 1;
    }

    .modal-footer {
        padding: 15px 20px;
        border-top: 1px solid #dee2e6;
        text-align: right;
    }

    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        margin-left: 10px;
    }

    .btn-primary {
        background: #007bff;
        color: white;
    }

    .btn-ghost {
        background: #6c757d;
        color: white;
    }

    .btn:hover {
        opacity: 0.9;
    }

    .success-modal {
        max-width: 400px;
        margin: 15% auto;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }

    .success-content {
        text-align: center;
        padding: 40px 30px;
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    }

    .success-icon-wrapper {
        margin-bottom: 25px;
    }

    .success-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: #28a745;
        margin: 0 auto;
        position: relative;
        animation: checkmark 0.6s ease-in-out;
    }

    .success-icon::after {
        content: '';
        position: absolute;
        left: 22px;
        top: 18px;
        width: 8px;
        height: 16px;
        border: solid white;
        border-width: 0 3px 3px 0;
        transform: rotate(45deg);
    }

    @keyframes checkmark {
        0% {
            transform: scale(0);
            opacity: 0;
        }
        50% {
            transform: scale(1.1);
        }
        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    .success-content h2 {
        color: #333;
        font-size: 22px;
        font-weight: 600;
        margin: 0 0 15px 0;
        line-height: 1.3;
    }

    .success-content p {
        color: #666;
        font-size: 15px;
        margin: 0 0 30px 0;
        line-height: 1.5;
    }

    .btn-confirm {
        width: 100%;
        padding: 12px 0;
        font-size: 16px;
        font-weight: 500;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .btn-confirm:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,123,255,0.3);
    }

    .terms-content {
        line-height: 1.6;
        white-space: pre-wrap;
    }
    </style>

    <?php include_once $this->project_path."include/footer.php"; ?>
</body>
</html>

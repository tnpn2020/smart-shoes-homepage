$(document).ready(function() {
    initializeForm();
});

// 폼 초기화
function initializeForm() {
    // 폰 번호 자동 포맷팅
    $('#phone').on('input', function() {
        let value = $(this).val().replace(/[^0-9]/g, '');
        if (value.length >= 4 && value.length <= 7) {
            value = value.replace(/(\d{3})(\d+)/, '$1-$2');
        } else if (value.length >= 8) {
            value = value.replace(/(\d{3})(\d{4})(\d+)/, '$1-$2-$3');
        }
        $(this).val(value);
    });

    // 폼 제출 처리
    $('.application-form').on('submit', function(e) {
        e.preventDefault();
        submitApplication();
    });
}

// 더블클릭 방지 변수
let isSubmitting = false;

// 신청서 제출
function submitApplication() {
    // 더블클릭 방지
    if (isSubmitting) {
        return;
    }
    
    // 유효성 검사
    if (!validateForm()) {
        return;
    }
    
    isSubmitting = true;
    
    // 제출 버튼 비활성화
    const submitBtn = $('.submit-btn');
    const originalText = submitBtn.text();
    submitBtn.prop('disabled', true).text('신청 처리 중...');
    
    // 폼 데이터 수집
    const formData = collectFormData();
    
    // AJAX 요청
    lb.ajax({
        type: "JsonAjaxPost",
        list: {
            ctl: "Userpage",
            param1: "submit_application",
            ...formData
        },
        action: lb.obj.address,
        havior: function(result) {
            //console.log(result);
            try {
                result = JSON.parse(result);
                handleSubmissionResponse(result);
            } catch(e) {
                console.error('JSON 파싱 오류:', e);
                showMessage('서버 응답 처리 중 오류가 발생했습니다.', 'error');
            } finally {
                // 제출 상태 초기화
                isSubmitting = false;
                submitBtn.prop('disabled', false).text(originalText);
            }
        }
    });
}

// 폼 유효성 검사
function validateForm() {
    // 필수 필드 체크 (유입경로와 추가요청사항 제외)
    const requiredFields = [
        { name: 'name', label: '이름' },
        { name: 'birthdate', label: '생년월일' },
        { name: 'phone', label: '연락처' },
        { name: 'email', label: '이메일' },
        { name: 'address', label: '주소' },
        { name: 'reason', label: '신청사유' }
    ];
    
    // 신청자 유형 체크
    const applicantType = $('input[name="applicant-type"]:checked').val();
    if (!applicantType) {
        showMessage('신청자 유형을 선택해주세요.', 'error');
        return false;
    }
    
    // 필수 필드 체크
    for (let field of requiredFields) {
        const value = $(`[name="${field.name}"]`).val().trim();
        if (!value) {
            showMessage(`${field.label}을(를) 입력해주세요.`, 'error');
            $(`[name="${field.name}"]`).focus();
            return false;
        }
    }
    
    // 필수 체크박스 확인
    const termsChecked = $('input[name="terms-agreement"]').is(':checked');
    const privacyChecked = $('input[name="privacy-agreement"]').is(':checked');
    
    if (!termsChecked || !privacyChecked) {
        showMessage('필수 동의사항을 확인해주세요.', 'error');
        return false;
    }
    
    // 이메일 형식 검사
    const email = $('#email').val();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        showMessage('올바른 이메일 주소를 입력해주세요.', 'error');
        $('#email').focus();
        return false;
    }
    
    // 전화번호 형식 검사
    const phone = $('#phone').val();
    const phoneRegex = /^010-\d{4}-\d{4}$/;
    if (!phoneRegex.test(phone)) {
        showMessage('올바른 전화번호 형식을 입력해주세요. (010-0000-0000)', 'error');
        $('#phone').focus();
        return false;
    }
    
    return true;
}

// 폼 데이터 수집
function collectFormData() {
    const formData = {};
    
    // 기본 입력 필드들
    const fields = [
        'name', 'birthdate', 'phone', 'email', 
        'address', 'reason', 'additional-requests'
    ];
    
    fields.forEach(field => {
        const element = $(`[name="${field}"]`);
        formData[field.replace('-', '_')] = element.val() || '';
    });
    
    // 라디오 버튼 (신청자 유형)
    formData.applicant_type = $('input[name="applicant-type"]:checked').val() || '';
    
    // 유입경로 체크박스들
    const trafficSources = [];
    $('input[name="traffic-sources"]:checked').each(function() {
        trafficSources.push($(this).val());
    });
    formData.traffic_sources = JSON.stringify(trafficSources);
    
    // 동의사항 체크박스들
    formData.terms_agreement = $('input[name="terms-agreement"]').is(':checked') ? 1 : 0;
    formData.privacy_agreement = $('input[name="privacy-agreement"]').is(':checked') ? 1 : 0;
    formData.marketing_agreement = $('input[name="marketing-agreement"]').is(':checked') ? 1 : 0;
    
    return formData;
}

// 제출 응답 처리
function handleSubmissionResponse(result) {
    if (result.result == "1") {
        // 성공 모달 표시
        showSuccessModal();
    } else {
        showMessage('신청 처리 중 오류가 발생했습니다: ' + (result.message || '알 수 없는 오류'), 'error');
    }
}

// 메시지 표시 함수
function showMessage(message, type = 'info') {
    // 기존 메시지 제거
    $('.form-message').remove();
    
    const messageClass = type === 'error' ? 'error' : (type === 'success' ? 'success' : 'info');
    const messageHtml = `
        <div class="form-message ${messageClass}" style="
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            text-align: center;
            ${type === 'error' ? 'background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;' : ''}
            ${type === 'success' ? 'background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb;' : ''}
            ${type === 'info' ? 'background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb;' : ''}
        ">
            ${message}
        </div>
    `;
    
    $('.application-form').prepend(messageHtml);
    
    // 메시지 위치로 스크롤
    $('html, body').animate({
        scrollTop: $('.form-message').offset().top - 100
    }, 500);
    
    // 자동 제거 (에러가 아닌 경우)
    if (type !== 'error') {
        setTimeout(function() {
            $('.form-message').fadeOut();
        }, 5000);
    }
}



// 성공 모달 표시
function showSuccessModal() {
    $('#successModal').show();
}

// 인덱스 페이지로 이동
function goToIndex() {
    window.location.href = '?param=index';
}

// 약관 보기 모달 표시
let currentTermsIdx = null;

function showTermsModal(termsIdx) {
    currentTermsIdx = termsIdx;
    
    // 제목 설정
    let title = '';
    switch(termsIdx) {
        case 1: title = '개인정보 처리방침'; break;
        case 2: title = '이용약관'; break;
        case 4: title = '마케팅 정보 수신동의'; break;
        default: title = '약관'; break;
    }
    
    $('#termsTitle').text(title);
    $('#termsContent').html('<div style="text-align: center; padding: 20px;">약관을 불러오는 중...</div>');
    
    // 약관 내용 로드
    lb.ajax({
        type: "JsonAjaxPost",
        list: {
            ctl: "Userpage",
            param1: "get_terms",
            terms_idx: termsIdx
        },
        action: lb.obj.address,
        havior: function(result) {
            //console.log(result);
            try {
                result = JSON.parse(result);
                handleTermsResponse(result);
            } catch(e) {
                console.error('JSON 파싱 오류:', e);
                $('#termsContent').html('<div style="text-align: center; padding: 20px; color: red;">약관을 불러오는데 실패했습니다.</div>');
            }
        }
    });
    
    $('#termsModal').show();
}

// 약관 조회 응답 처리
function handleTermsResponse(result) {
    if (result.result == "1") {
        $('#termsContent').html(result.value.content || '약관 내용이 없습니다.');
    } else {
        $('#termsContent').html('<div style="text-align: center; padding: 20px; color: red;">약관을 불러오는데 실패했습니다.</div>');
    }
}

// 약관 모달 닫기
function closeTermsModal() {
    $('#termsModal').hide();
    currentTermsIdx = null;
}

// 약관에 동의
function agreeToTerms() {
    if (currentTermsIdx) {
        // 해당 체크박스 체크
        switch(currentTermsIdx) {
            case 1: // 개인정보 처리방침
                $('input[name="privacy-agreement"]').prop('checked', true);
                break;
            case 2: // 이용약관
                $('input[name="terms-agreement"]').prop('checked', true);
                break;
            case 4: // 마케팅 정보 수신동의
                $('input[name="marketing-agreement"]').prop('checked', true);
                break;
        }
    }
    
    closeTermsModal();
}

// 모달 배경 클릭 시 닫기
$(document).on('click', '.modal', function(e) {
    if (e.target === this) {
        if (this.id === 'termsModal') {
            closeTermsModal();
        }
        // 성공 모달은 배경 클릭으로 닫지 않음
    }
});

// ESC 키로 모달 닫기
$(document).on('keydown', function(e) {
    if (e.key === 'Escape') {
        if ($('#termsModal').is(':visible')) {
            closeTermsModal();
        }
        // 성공 모달은 ESC로 닫지 않음
    }
});
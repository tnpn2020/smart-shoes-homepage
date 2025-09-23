$(document).ready(function() {
    // URL 파라미터 확인
    const urlParams = new URLSearchParams(window.location.search);
    const type = urlParams.get('type');
    
    // 페이지 타이틀과 설명 설정
    setPageTitle(type);
    
    // 약관 내용 로드
    loadPrivacyContent();
});

// 페이지 타이틀 설정
function setPageTitle(type) {
    let title = '';
    
    switch(type) {
        case 'terms':
            title = '이용약관';
            break;
        case 'privacy':
            title = '개인정보처리방침';
            break;
        case 'email':
            title = '이메일무단수집거부';
            break;
        default:
            title = '약관 및 정책';
            break;
    }
    
    $('#pageTitle').text(title);
    document.title = title + ' - 스마트신발';
}

// 약관 내용 로드
function loadPrivacyContent() {
    const urlParams = new URLSearchParams(window.location.search);
    const type = urlParams.get('type');
    
    // 타입별 terms_idx 매핑
    let termsIdx = 0;
    switch(type) {
        case 'terms': termsIdx = 2; break;      // 이용약관
        case 'privacy': termsIdx = 1; break;    // 개인정보처리방침
        case 'email': termsIdx = 3; break;      // 이메일무단수집거부
        default:
            showError('잘못된 접근입니다.');
            return;
    }
    
    // 로딩 표시
    showLoading();
    
    // AJAX 요청
    lb.ajax({
        type: "JsonAjaxPost",
        list: {
            ctl: "Userpage",
            param1: "get_terms",
            terms_idx: termsIdx
        },
        action: lb.obj.address,
        havior: function(result) {
            try {
                result = JSON.parse(result);
                handlePrivacyResponse(result);
            } catch(e) {
                console.error('JSON 파싱 오류:', e);
                showError('서버 응답 처리 중 오류가 발생했습니다.');
            }
        }
    });
}

// 약관 조회 응답 처리
function handlePrivacyResponse(result) {
    if (result.result == "1") {
        showContent(result.value.content || '약관 내용이 없습니다.');
    } else {
        showError(result.message || '약관을 불러오는데 실패했습니다.');
    }
}

// 로딩 표시
function showLoading() {
    $('#loading').show();
    $('#privacyContent').hide();
    $('#errorMessage').hide();
}

// 내용 표시
function showContent(content) {
    $('#loading').hide();
    $('#errorMessage').hide();
    $('#privacyContent').html(content).show();
    
    // 스크롤을 내용 영역으로 이동
    $('html, body').animate({
        scrollTop: $('#privacyContent').offset().top - 100
    }, 500);
}

// 에러 표시
function showError(message) {
    $('#loading').hide();
    $('#privacyContent').hide();
    $('#errorMessage').find('p').text(message);
    $('#errorMessage').show();
}

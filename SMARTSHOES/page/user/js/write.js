// 글쓰기 페이지 JavaScript

$(document).ready(function() {
    // 페이지 로드 시 초기화
    initWritePage();
    
    // 이벤트 리스너 등록
    bindEvents();
});

// 페이지 초기화
function initWritePage() {
    // 파일 업로드 미리보기
    initFileUpload();
    
    // 폼 유효성 검사 초기화
    initFormValidation();
    
    // 개발용 테스트 데이터 자동 입력 (개발 환경에서만)
    fillTestData();
}

// 테스트 데이터 자동 입력 함수
function fillTestData() {
    const testData = {
        title: '테스트 문의글 제목 - ' + new Date().toLocaleString(),
        author: '홍길동',
        password: '1234',
        category: 'technical',
        email: 'test@test.com',
        content: `테스트 내용입니다. 이것저것 테스트 용입니다.`,
        privacy: true
    };
    
    // 각 필드에 테스트 데이터 입력
    $('#title').val(testData.title);
    $('#author').val(testData.author);
    $('#password').val(testData.password);
    $('#category').val(testData.category);
    $('#email').val(testData.email);
    $('#content').val(testData.content);
    $('#privacy').prop('checked', testData.privacy);
    
    //console.log('테스트 데이터가 입력되었습니다.');
}

// 테스트 데이터 초기화 함수
function clearTestData() {
    $('#title').val('');
    $('#author').val('');
    $('#password').val('');
    $('#category').val('');
    $('#email').val('');
    $('#content').val('');
    $('#privacy').prop('checked', false);
    $('#file').val('');
    
    //console.log('폼이 초기화되었습니다.');
}

// 파일 업로드 초기화
function initFileUpload() {
    $('#file').on('change', function() {
        const files = this.files;
        const maxFiles = 5;
        const maxSize = 10 * 1024 * 1024; // 10MB
        
        if (files.length > maxFiles) {
            showMessage(`최대 ${maxFiles}개 파일만 업로드 가능합니다.`, 'error');
            this.value = '';
            return;
        }
        
        for (let i = 0; i < files.length; i++) {
            if (files[i].size > maxSize) {
                showMessage(`파일 크기는 10MB 이하여야 합니다. (${files[i].name})`, 'error');
                this.value = '';
                return;
            }
        }
        
        // 파일 미리보기 표시
        showFilePreview(files);
    });
}

// 파일 미리보기 표시
function showFilePreview(files) {
    const previewContainer = $('#file-preview');
    if (previewContainer.length === 0) {
        $('#file').after('<div id="file-preview" class="file-preview"></div>');
    }
    
    let previewHtml = '<h4>첨부된 파일:</h4><ul>';
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        const size = formatFileSize(file.size);
        previewHtml += `<li>${file.name} (${size})</li>`;
    }
    previewHtml += '</ul>';
    
    $('#file-preview').html(previewHtml);
}

// 파일 크기 포맷팅
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// 폼 유효성 검사 초기화
function initFormValidation() {
    // 실시간 유효성 검사
    $('#title').on('input', function() {
        validateTitle($(this));
    });
    
    $('#email').on('input', function() {
        validateEmail($(this));
    });
    
    $('#password').on('input', function() {
        validatePassword($(this));
    });
}

// 이벤트 리스너 바인딩
function bindEvents() {
    // 취소 버튼 이벤트
    $('.btn-secondary').on('click', function() {
        if (confirm('작성 중인 내용이 있습니다. 정말 취소하시겠습니까?')) {
            history.back();
        }
    });
    
    // 카테고리 변경 이벤트
    $('#category').on('change', function() {
        updateCategoryHelp($(this).val());
    });
}

// 폼 제출 처리
function handleFormSubmit() {
    if (!validateForm()) {
        //console.log("validateForm");
        return false;
    }
    
    // 서버로 전송
    showLoading('글을 등록하고 있습니다...');
    
    lb.ajax({
        type: "AjaxFormPost",
        list: {
            ctl: "Userpage",
            param1: "reg_inquiry"
        },
        action: lb.obj.address,
        elem: document.getElementById("form"),
        response_method: "response_write_inquiry",
        havior: function(result) {
            hideLoading();
            result = JSON.parse(result);
            response_write_inquiry(result);
        }
    });
}

// 폼 유효성 검사
function validateForm() {
    const category = $('#category').val();
    const title = $('#title').val().trim();
    const author = $('#author').val().trim();
    const email = $('#email').val().trim();
    const password = $('#password').val();
    const content = $('#content').val().trim();
    const privacy = $('#privacy').is(':checked');

    // 분류 검사
    if (!category) {
        showMessage('분류를 선택해주세요.', 'error');
        $('#category').focus();
        return false;
    }

    // 제목 검사
    if (!title) {
        showMessage('제목을 입력해주세요.', 'error');
        $('#title').focus();
        return false;
    }

    if (title.length < 2) {
        showMessage('제목은 2자 이상 입력해주세요.', 'error');
        $('#title').focus();
        return false;
    }

    // 작성자 검사
    if (!author) {
        showMessage('작성자를 입력해주세요.', 'error');
        $('#author').focus();
        return false;
    }

    // 이메일 검사
    if (!email) {
        showMessage('이메일을 입력해주세요.', 'error');
        $('#email').focus();
        return false;
    }

    if (!isValidEmail(email)) {
        showMessage('올바른 이메일 형식을 입력해주세요.', 'error');
        $('#email').focus();
        return false;
    }

    // 비밀번호 검사
    if (!password) {
        showMessage('비밀번호를 입력해주세요.', 'error');
        $('#password').focus();
        return false;
    }

    if (password.length < 4) {
        showMessage('비밀번호는 4자 이상 입력해주세요.', 'error');
        $('#password').focus();
        return false;
    }

    // 내용 검사
    if (content.length < 10) {
        showMessage('내용을 10자 이상 입력해주세요.', 'error');
        $('#content').focus();
        return false;
    }

    // 개인정보 동의 검사
    if (!privacy) {
        showMessage('개인정보 수집 및 이용에 동의해주세요.', 'error');
        $('#privacy').focus();
        return false;
    }
    //console.log("validateForm22");
    return true;
}

// 개별 필드 유효성 검사
function validateTitle(element) {
    const value = element.val().trim();
    if (value.length > 0 && value.length < 2) {
        element.addClass('error');
        showFieldError(element, '제목은 2자 이상 입력해주세요.');
    } else {
        element.removeClass('error');
        hideFieldError(element);
    }
}

function validateEmail(element) {
    const value = element.val().trim();
    if (value.length > 0 && !isValidEmail(value)) {
        element.addClass('error');
        showFieldError(element, '올바른 이메일 형식을 입력해주세요.');
    } else {
        element.removeClass('error');
        hideFieldError(element);
    }
}

function validatePassword(element) {
    const value = element.val();
    if (value.length > 0 && value.length < 4) {
        element.addClass('error');
        showFieldError(element, '비밀번호는 4자 이상 입력해주세요.');
    } else {
        element.removeClass('error');
        hideFieldError(element);
    }
}

// 이메일 유효성 검사
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// 카테고리 도움말 업데이트
function updateCategoryHelp(category) {
    const helpTexts = {
        'product': '제품 관련 문의사항을 작성해주세요.',
        'service': '서비스 관련 문의사항을 작성해주세요.',
        'technical': '기술 관련 문의사항을 작성해주세요.',
        'general': '기타 문의사항을 작성해주세요.'
    };
    
    if (helpTexts[category]) {
        $('#category').next('.form-help').text(helpTexts[category]);
    }
}

// 필드 오류 표시
function showFieldError(element, message) {
    hideFieldError(element);
    element.after(`<div class="field-error">${message}</div>`);
}

// 필드 오류 숨김
function hideFieldError(element) {
    element.siblings('.field-error').remove();
}

// 메시지 표시
function showMessage(message, type = 'info') {
    const alertClass = type === 'error' ? 'alert-danger' : 
                      type === 'success' ? 'alert-success' : 'alert-info';
    
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible" role="alert" style="
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 9999;
            min-width: 300px;
            max-width: 500px;
            padding: 15px 20px;
            margin: 0;
            border-radius: 5px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            background: ${type === 'error' ? '#f8d7da' : type === 'success' ? '#d1edff' : '#e2e3e5'};
            color: ${type === 'error' ? '#721c24' : type === 'success' ? '#0c5460' : '#383d41'};
            border: 1px solid ${type === 'error' ? '#f5c6cb' : type === 'success' ? '#bee5eb' : '#d1ecf1'};
        ">
            <strong>${type === 'error' ? '오류!' : type === 'success' ? '성공!' : '알림'}</strong> ${message}
            <button type="button" class="close" onclick="$(this).parent().remove()" style="
                float: right;
                font-size: 18px;
                font-weight: bold;
                line-height: 1;
                color: inherit;
                background: transparent;
                border: 0;
                cursor: pointer;
                padding: 0;
                margin-left: 10px;
            ">
                <span>&times;</span>
            </button>
        </div>
    `;
    
    // 기존 알림 제거
    $('.alert').remove();
    
    // body에 직접 추가
    $('body').append(alertHtml);
    
    // 5초 후 자동 제거 (성공/정보 메시지만)
    if (type !== 'error') {
        setTimeout(function() {
            $('.alert').fadeOut(function() {
                $(this).remove();
            });
        }, 5000);
    }
}

// 로딩 표시
function showLoading(message = '처리 중입니다...') {
    const loadingHtml = `
        <div id="loading-overlay">
            <div class="loading-content">
                <div class="spinner"></div>
                <p>${message}</p>
            </div>
        </div>
    `;
    
    $('body').append(loadingHtml);
}

// 로딩 숨김
function hideLoading() {
    $('#loading-overlay').remove();
}



// 글쓰기 응답 처리
function response_write_inquiry(result) {
    if (result.result == "1") {
        showMessage('글이 성공적으로 등록되었습니다.', 'success');
        setTimeout(function() {
            window.location.href = '?param=contact';
        }, 1500);
    } else {
        showMessage('글 등록에 실패했습니다: ' + (result.message || '알 수 없는 오류가 발생했습니다.'), 'error');
    }
}

// 문의 수정 페이지 JavaScript

$(document).ready(function() {
    // 페이지 로드 시 초기화
    initEditPage();
    
    // 이벤트 리스너 등록
    bindEvents();
    
    // URL에서 문의 ID 가져오기
    const urlParams = new URLSearchParams(window.location.search);
    const inquiryId = urlParams.get('id');
    if (inquiryId) {
        $('#inquiryId').val(inquiryId);
    } else {
        showMessage('올바르지 않은 접근입니다.', 'error');
        setTimeout(() => history.back(), 2000);
    }
});

// 페이지 초기화
function initEditPage() {
    // 파일 업로드 초기화
    initFileUpload();
    
    // 폼 유효성 검사 초기화
    initFormValidation();
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
    
    let previewHtml = '<h4>새로 첨부할 파일:</h4><ul>';
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
    // Enter 키로 비밀번호 확인
    $('#checkPassword').on('keypress', function(e) {
        if (e.which === 13) {
            checkPassword();
        }
    });
}

// 비밀번호 확인
function checkPassword() {
    const password = $('#checkPassword').val().trim();
    const inquiryId = $('#inquiryId').val();
    
    if (!password) {
        showMessage('비밀번호를 입력해주세요.', 'error');
        return;
    }
    
    if (!inquiryId) {
        showMessage('문의 ID가 없습니다.', 'error');
        return;
    }
    
    showLoading('비밀번호를 확인하고 있습니다...');
    
    lb.ajax({
        type: "JsonAjaxPost",
        list: {
            ctl: "Userpage",
            param1: "check_inquiry_password",
            inquiry_id: inquiryId,
            password: password
        },
        action: lb.obj.address,
        response_method: "response_check_password",
        havior: function(result) {
            hideLoading();
            result = JSON.parse(result);
            response_check_password(result);
        }
    });
}

// 비밀번호 확인 응답 처리
function response_check_password(result) {
    if (result.result == "1") {
        // 비밀번호 확인 성공 - 문의 데이터 로드
        loadInquiryData(result.inquiry_data);
        $('#passwordCheckSection').hide();
        $('#editSection').show();
    } else {
        showMessage('비밀번호가 일치하지 않습니다.', 'error');
        $('#checkPassword').focus();
    }
}

// 문의 데이터 로드
function loadInquiryData(data) {
    $('#title').val(data.title);
    $('#author').val(data.author);
    $('#password').val(data.password);
    $('#category').val(data.category);
    $('#email').val(data.email);
    $('#content').val(data.content);
    
    // 기존 파일 목록 표시
    if (data.files && data.files.length > 0) {
        displayExistingFiles(data.files);
    }
}

// 기존 파일 목록 표시
function displayExistingFiles(files) {
    let filesHtml = '<div class="existing-files">';
    
    files.forEach((file, index) => {
        filesHtml += `
            <div class="file-item" data-file-index="${index}">
                <span class="file-name">${file.original_name}</span>
                <span class="file-size">(${formatFileSize(file.file_size)})</span>
                <button type="button" class="btn-delete-file" onclick="removeExistingFile(${index})">
                    <i class="xi-close"></i>
                </button>
            </div>
        `;
    });
    
    filesHtml += '</div>';
    
    $('#existingFilesList').html(filesHtml);
    $('#existingFilesGroup').show();
}

// 기존 파일 삭제
function removeExistingFile(index) {
    if (confirm('이 파일을 삭제하시겠습니까?')) {
        $(`.file-item[data-file-index="${index}"]`).remove();
    }
}

// 문의 수정
function updateInquiry() {
    if (!validateForm()) {
        return false;
    }
    
    showLoading('문의를 수정하고 있습니다...');
    
    lb.ajax({
        type: "AjaxFormPost",
        list: {
            ctl: "Userpage",
            param1: "update_inquiry"
        },
        action: lb.obj.address,
        elem: document.getElementById("editForm"),
        response_method: "response_update_inquiry",
        havior: function(result) {
            hideLoading();
            result = JSON.parse(result);
            response_update_inquiry(result);
        }
    });
}

// 문의 수정 응답 처리
function response_update_inquiry(result) {
    if (result.result == "1") {
        showMessage('문의가 성공적으로 수정되었습니다.', 'success');
        setTimeout(function() {
            window.location.href = '?param=contact';
        }, 1500);
    } else {
        showMessage('문의 수정에 실패했습니다: ' + (result.message || '알 수 없는 오류가 발생했습니다.'), 'error');
    }
}

// 문의 삭제
function deleteInquiry() {
    if (!confirm('정말로 이 문의를 삭제하시겠습니까?\n삭제된 문의는 복구할 수 없습니다.')) {
        return;
    }
    
    const inquiryId = $('#inquiryId').val();
    const password = $('#password').val();
    
    if (!password) {
        showMessage('비밀번호를 입력해주세요.', 'error');
        return;
    }
    
    showLoading('문의를 삭제하고 있습니다...');
    
    lb.ajax({
        type: "JsonAjaxPost",
        list: {
            ctl: "Userpage",
            param1: "delete_inquiry",
            inquiry_id: inquiryId,
            password: password
        },
        action: lb.obj.address,
        response_method: "response_delete_inquiry",
        havior: function(result) {
            hideLoading();
            result = JSON.parse(result);
            response_delete_inquiry(result);
        }
    });
}

// 문의 삭제 응답 처리
function response_delete_inquiry(result) {
    if (result.result == "1") {
        showMessage('문의가 성공적으로 삭제되었습니다.', 'success');
        setTimeout(function() {
            window.location.href = '?param=contact';
        }, 1500);
    } else {
        showMessage('문의 삭제에 실패했습니다: ' + (result.message || '알 수 없는 오류가 발생했습니다.'), 'error');
    }
}

// 폼 유효성 검사
function validateForm() {
    const category = $('#category').val();
    const title = $('#title').val().trim();
    const author = $('#author').val().trim();
    const email = $('#email').val().trim();
    const password = $('#password').val();
    const content = $('#content').val().trim();

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
        <div id="loading-overlay" style="
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 10000;
            display: flex;
            justify-content: center;
            align-items: center;
        ">
            <div class="loading-content" style="
                background: white;
                padding: 30px;
                border-radius: 10px;
                text-align: center;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            ">
                <div class="spinner" style="
                    border: 4px solid #f3f3f3;
                    border-top: 4px solid #3498db;
                    border-radius: 50%;
                    width: 30px;
                    height: 30px;
                    animation: spin 1s linear infinite;
                    margin: 0 auto 15px;
                "></div>
                <p style="margin: 0; color: #333;">${message}</p>
            </div>
        </div>
        <style>
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        </style>
    `;
    
    $('body').append(loadingHtml);
}

// 로딩 숨김
function hideLoading() {
    $('#loading-overlay').remove();
}

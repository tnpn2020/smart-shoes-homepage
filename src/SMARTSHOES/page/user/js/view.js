// 문의글 상세보기 페이지 JavaScript

$(document).ready(function() {
    // 페이지 로드 시 초기화
    initViewPage();
    
    // 이벤트 리스너 등록
    bindEvents();
});

// 페이지 초기화
function initViewPage() {
    // 글 조회수 증가
    incrementViewCount();
    
    // 첨부파일 다운로드 기능 초기화
    initFileDownloads();
    
    // 답변 상태에 따른 UI 업데이트
    updateAnswerStatus();
}

// 조회수 증가
function incrementViewCount() {
    const postId = new URLSearchParams(window.location.search).get('id');
    if (postId) {
        $.ajax({
            url: 'increment_view.php',
            type: 'POST',
            data: { post_id: postId },
            success: function(response) {
                //console.log('조회수가 증가되었습니다.');
            }
        });
    }
}

// 첨부파일 다운로드 초기화
function initFileDownloads() {
    $('.file-link').on('click', function(e) {
        e.preventDefault();
        const fileName = $(this).text();
        const postId = new URLSearchParams(window.location.search).get('id');
        
        // 파일 다운로드 요청
        downloadFile(postId, fileName);
    });
}

// 파일 다운로드
function downloadFile(postId, fileName) {
    $.ajax({
        url: 'download_file.php',
        type: 'POST',
        data: {
            post_id: postId,
            file_name: fileName
        },
        success: function(response) {
            if (response.success) {
                // 파일 다운로드 링크 생성
                const link = document.createElement('a');
                link.href = response.download_url;
                link.download = fileName;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            } else {
                alert('파일 다운로드에 실패했습니다: ' + response.message);
            }
        },
        error: function() {
            alert('파일 다운로드 중 오류가 발생했습니다.');
        }
    });
}

// 답변 상태에 따른 UI 업데이트
function updateAnswerStatus() {
    const hasAnswer = $('.admin-answer').length > 0;
    
    if (!hasAnswer) {
        // 답변이 없는 경우 대기 상태 표시
        showWaitingAnswer();
    }
}

// 답변 대기 상태 표시
function showWaitingAnswer() {
    const waitingHtml = `
        <div class="admin-answer waiting">
            <div class="answer-header">
                <h3>관리자 답변</h3>
                <div class="answer-meta">
                    <span class="answer-status waiting">답변대기</span>
                </div>
            </div>
            <div class="answer-content">
                <p>관리자의 답변을 기다리고 있습니다.</p>
                <p>답변은 보통 1-2일 내에 등록됩니다.</p>
            </div>
        </div>
    `;
    
    $('.post-content').after(waitingHtml);
}

// 이벤트 리스너 바인딩
function bindEvents() {
    // 목록으로 버튼
    $('.btn-secondary').on('click', function() {
        window.location.href = '?param=contact';
    });
    
    // 새 글쓰기 버튼
    $('.btn-primary').on('click', function() {
        window.location.href = '?param=write';
    });
    
    // 수정 버튼
    $('.btn-outline').on('click', function() {
        showPasswordModal('edit');
    });
    
    // 삭제 버튼
    $('.btn-outline-danger').on('click', function() {
        showPasswordModal('delete');
    });
    
    // 모달 닫기
    $('.close').on('click', closePasswordModal);
    $(window).on('click', function(e) {
        if (e.target == document.getElementById('passwordModal')) {
            closePasswordModal();
        }
    });
    
    // 비밀번호 폼 제출
    $('#passwordForm').on('submit', function(e) {
        e.preventDefault();
        handlePasswordSubmit();
    });
    
    // ESC 키로 모달 닫기
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape') {
            closePasswordModal();
        }
    });
}

// 비밀번호 모달 표시
function showPasswordModal(action) {
    currentAction = action;
    const title = action === 'edit' ? '글 수정' : '글 삭제';
    $('#modalTitle').text(title + ' - 비밀번호 확인');
    $('#passwordModal').show();
    $('#password').focus();
    
    // 모달 표시 시 스크롤 방지
    $('body').addClass('modal-open');
}

// 비밀번호 모달 닫기
function closePasswordModal() {
    $('#passwordModal').hide();
    $('#passwordForm')[0].reset();
    currentAction = '';
    
    // 모달 닫을 때 스크롤 복원
    $('body').removeClass('modal-open');
}

// 비밀번호 확인 처리
function handlePasswordSubmit() {
    const password = $('#password').val();
    const postId = new URLSearchParams(window.location.search).get('id');
    
    if (!password) {
        showMessage('비밀번호를 입력해주세요.', 'error');
        $('#password').focus();
        return;
    }

    // 로딩 표시
    showLoading('비밀번호를 확인하고 있습니다...');

    // AJAX로 비밀번호 확인
    $.ajax({
        url: 'verify_password.php',
        type: 'POST',
        data: {
            post_id: postId,
            password: password
        },
        success: function(response) {
            hideLoading();
            if (response.success) {
                closePasswordModal();
                if (currentAction === 'edit') {
                    window.location.href = `?param=edit&id=${postId}`;
                } else if (currentAction === 'delete') {
                    if (confirm('정말로 이 글을 삭제하시겠습니까?\n삭제된 글은 복구할 수 없습니다.')) {
                        deletePost();
                    }
                }
            } else {
                showMessage('비밀번호가 일치하지 않습니다.', 'error');
                $('#password').focus();
            }
        },
        error: function() {
            hideLoading();
            showMessage('서버 오류가 발생했습니다.', 'error');
        }
    });
}

// 글 삭제
function deletePost() {
    const postId = new URLSearchParams(window.location.search).get('id');
    
    showLoading('글을 삭제하고 있습니다...');

    $.ajax({
        url: 'delete_post.php',
        type: 'POST',
        data: { post_id: postId },
        success: function(response) {
            hideLoading();
            if (response.success) {
                showMessage('글이 삭제되었습니다.', 'success');
                setTimeout(function() {
                    window.location.href = '?param=contact';
                }, 1500);
            } else {
                showMessage('글 삭제에 실패했습니다: ' + response.message, 'error');
            }
        },
        error: function() {
            hideLoading();
            showMessage('서버 오류가 발생했습니다.', 'error');
        }
    });
}

// 메시지 표시
function showMessage(message, type = 'info') {
    const alertClass = type === 'error' ? 'alert-danger' : 
                      type === 'success' ? 'alert-success' : 'alert-info';
    
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    `;
    
    // 기존 알림 제거
    $('.alert').remove();
    
    // 새 알림 추가
    $('.post-header').before(alertHtml);
    
    // 5초 후 자동 제거 (성공/정보 메시지만)
    if (type !== 'error') {
        setTimeout(function() {
            $('.alert').fadeOut();
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

// 키보드 단축키
$(document).on('keydown', function(e) {
    // Ctrl + L: 목록으로
    if (e.ctrlKey && e.key === 'l') {
        e.preventDefault();
        window.location.href = '?param=contact';
    }
    
    // Ctrl + N: 새 글쓰기
    if (e.ctrlKey && e.key === 'n') {
        e.preventDefault();
        window.location.href = '?param=write';
    }
    
    // Ctrl + E: 글 수정
    if (e.ctrlKey && e.key === 'e') {
        e.preventDefault();
        showPasswordModal('edit');
    }
    
    // Ctrl + D: 글 삭제
    if (e.ctrlKey && e.key === 'd') {
        e.preventDefault();
        showPasswordModal('delete');
    }
});

// 페이지 새로고침 시 중복 조회수 증가 방지
let viewCountIncremented = false;

// 페이지 가시성 변경 감지
document.addEventListener('visibilitychange', function() {
    if (!document.hidden && !viewCountIncremented) {
        viewCountIncremented = true;
        incrementViewCount();
    }
});

// 답변 알림 기능 (선택적)
function checkForNewAnswer() {
    const postId = new URLSearchParams(window.location.search).get('id');
    const lastAnswerDate = $('.answer-date').text().replace('답변일: ', '');
    
    $.ajax({
        url: 'check_answer.php',
        type: 'POST',
        data: {
            post_id: postId,
            last_answer_date: lastAnswerDate
        },
        success: function(response) {
            if (response.has_new_answer) {
                showNewAnswerNotification();
            }
        }
    });
}

// 새 답변 알림
function showNewAnswerNotification() {
    const notificationHtml = `
        <div class="notification">
            <div class="notification-content">
                <p>새로운 답변이 등록되었습니다!</p>
                <button onclick="location.reload()" class="btn btn-primary">확인하기</button>
            </div>
        </div>
    `;
    
    $('body').append(notificationHtml);
    
    // 10초 후 자동 제거
    setTimeout(function() {
        $('.notification').fadeOut();
    }, 10000);
}

// 답변이 있는 경우 주기적으로 새 답변 확인
if ($('.admin-answer').length > 0) {
    setInterval(checkForNewAnswer, 30000); // 30초마다 확인
}

// 모달 열린 상태에서 스크롤 방지 CSS
const modalCSS = `
<style>
body.modal-open {
    overflow: hidden;
}

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.loading-content {
    background: white;
    padding: 30px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #25A69B;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 15px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    background: #25A69B;
    color: white;
    padding: 15px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    z-index: 1000;
    animation: slideIn 0.3s ease;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}
</style>
`;

$('head').append(modalCSS);

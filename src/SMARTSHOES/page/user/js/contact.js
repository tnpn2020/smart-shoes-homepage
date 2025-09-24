// 문의하기 리스트 페이지 JavaScript

let currentPage = 1;
let currentLimit = 10;
let currentFilters = {};
let selectedInquiryId = null;

$(document).ready(function() {
    // 페이지 로드 시 초기화
    initContactPage();
    
    // 이벤트 리스너 등록
    bindEvents();
    
    // 문의글 리스트 로드
    loadInquiryList();
});

// 페이지 초기화
function initContactPage() {
    // 검색 이벤트
    $('.search-btn').on('click', function() {
        searchInquiries();
    });
    
    $('.search-input').on('keypress', function(e) {
        if (e.which === 13) {
            searchInquiries();
        }
    });
    
    // 필터 변경 시 자동 검색
    $('#categoryFilter, #statusFilter, #sortType').on('change', function() {
        searchInquiries();
    });
}

// 이벤트 리스너 바인딩
function bindEvents() {
    // 비밀번호 모달 관련
    $(document).on('click', '.close-modal', function() {
        closePasswordModal();
    });
    
    $(document).on('click', '.password-modal-overlay', function(e) {
        if (e.target === this) {
            closePasswordModal();
        }
    });
    
    // 비밀번호 입력 시 엔터키
    $(document).on('keypress', '#inquiryPassword', function(e) {
        if (e.which === 13) {
            checkInquiryPassword();
        }
    });
}

// 문의글 리스트 로드
function loadInquiryList(page = 1) {
    currentPage = page;
    
    showLoading();
    
    const params = {
        ctl: "Userpage",
        param1: "get_inquiry_list",
        page: currentPage,
        limit: currentLimit,
        ...currentFilters
    };
    
    lb.ajax({
        type: "JsonAjaxPost",
        list: params,
        action: lb.obj.address,
        response_method: "response_inquiry_list",
        havior: function(result) {
            hideLoading();
            
            try {
                result = JSON.parse(result);
                response_inquiry_list(result);
            } catch(e) {
                console.error('JSON 파싱 오류:', e);
                showMessage('서버 응답 처리 중 오류가 발생했습니다.', 'error');
            }
        }
    });
}

// 문의글 리스트 응답 처리
function response_inquiry_list(result) {
    if (result.result == "1") {
        // 데이터 타입 확실히 변환
        const totalCount = parseInt(result.total_count) || 0;
        const currentPageNum = parseInt(result.current_page) || 1;
        const totalPages = parseInt(result.total_pages) || 1;
        
        displayInquiryList(result.list || [], totalCount, currentPageNum);
        displayPagination(currentPageNum, totalPages, totalCount);
        updateTotalCount(totalCount);
        
        // 현재 페이지 정보 업데이트
        currentPage = currentPageNum;
        
        if ((result.list || []).length === 0) {
            showEmptyState();
        } else {
            hideEmptyState();
        }
    } else {
        showMessage('문의글 리스트를 불러오는데 실패했습니다: ' + (result.message || '알 수 없는 오류'), 'error');
        showEmptyState();
    }
}

// 문의글 리스트 표시
function displayInquiryList(inquiries, totalCount, pageNum) {
    const tbody = $('.board-table tbody');
    tbody.empty();
    
    if (inquiries.length === 0) {
        tbody.append('<tr><td colspan="6" class="text-center">등록된 문의글이 없습니다.</td></tr>');
        return;
    }
    
    inquiries.forEach(function(inquiry, index) {
        const categoryText = getCategoryText(inquiry.category);
        const statusText = getStatusText(inquiry.status);
        const statusClass = getStatusClass(inquiry.status);
        const regDate = formatDate(inquiry.reg_date);
        const hasFiles = inquiry.has_files == '1';
        
        // 역순 연번 계산 (최신 글이 가장 큰 번호)
        const rowNumber = totalCount - ((pageNum - 1) * currentLimit) - index;
        
        const row = `
            <tr data-inquiry-id="${inquiry.id}">
                <td class="col-number">${rowNumber}</td>
                <td class="col-category">
                    <span class="category-badge ${inquiry.category}">${categoryText}</span>
                </td>
                <td class="col-title">
                    <a href="javascript:void(0)" onclick="showPasswordModal(${inquiry.id})" class="title-link">
                        ${inquiry.title}
                        ${hasFiles ? '<i class="xi-clip"></i>' : ''}
                    </a>
                </td>
                <td class="col-author">${inquiry.author}</td>
                <td class="col-date">${regDate}</td>
                <td class="col-status">
                    <span class="status-badge ${statusClass}">${statusText}</span>
                </td>
            </tr>
        `;
        
        tbody.append(row);
    });
}

// 페이징 표시
function displayPagination(currentPage, totalPages, totalCount) {
    const pagingWrap = $('.paging-wrap');
    
    if (pagingWrap.length === 0) {
        return;
    }
    
    pagingWrap.empty();
    
    if (totalPages <= 1) {
        pagingWrap.hide();
        return;
    }
    
    pagingWrap.show();
    
    let paginationHtml = '';
    
    // 이전 페이지 버튼
    if (currentPage > 1) {
        paginationHtml += `
            <a href="javascript:void(0)" class="paging-btn paging-prev" onclick="loadInquiryList(${currentPage - 1})">
                <span><i class="xi-angle-left"></i></span>
            </a>
        `;
    } else {
        paginationHtml += `
            <a href="javascript:void(0)" class="paging-btn paging-prev disabled">
                <span><i class="xi-angle-left"></i></span>
            </a>
        `;
    }
    
    // 페이지 번호 박스
    paginationHtml += '<div class="paging-num-box">';
    
    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, currentPage + 2);
    
    for (let i = startPage; i <= endPage; i++) {
        const activeClass = i === currentPage ? ' active' : '';
        paginationHtml += `<a href="javascript:void(0)" class="paging-num${activeClass}" onclick="loadInquiryList(${i})">${i}</a>`;
    }
    
    paginationHtml += '</div>';
    
    // 다음 페이지 버튼
    if (currentPage < totalPages) {
        paginationHtml += `
            <a href="javascript:void(0)" class="paging-btn paging-next" onclick="loadInquiryList(${currentPage + 1})">
                <span><i class="xi-angle-right"></i></span>
            </a>
        `;
    } else {
        paginationHtml += `
            <a href="javascript:void(0)" class="paging-btn paging-next disabled">
                <span><i class="xi-angle-right"></i></span>
            </a>
        `;
    }
    
    pagingWrap.html(paginationHtml);
}

// 비밀번호 모달 표시
function showPasswordModal(inquiryId) {
    selectedInquiryId = inquiryId;
    
    const modalHtml = `
        <div class="password-modal-overlay" id="passwordModal">
            <div class="password-modal">
                <div class="modal-header">
                    <h3>비밀번호 확인</h3>
                    <button type="button" class="close-modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>비밀번호를 입력해주세요.</p>
                    <div class="password-input-group">
                        <input type="password" id="inquiryPassword" placeholder="비밀번호를 입력하세요" maxlength="20">
                        <button type="button" class="btn btn-primary" onclick="checkInquiryPassword()">확인</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('body').append(modalHtml);
    $('#inquiryPassword').focus();
}

// 비밀번호 모달 닫기
function closePasswordModal() {
    $('#passwordModal').remove();
    selectedInquiryId = null;
}

// 문의글 비밀번호 확인
function checkInquiryPassword() {
    const password = $('#inquiryPassword').val().trim();
    
    // 기존 오류 메시지 제거
    $('.password-error').remove();
    
    if (!password) {
        const errorHtml = `
            <div class="password-error" style="color: #dc3545; font-size: 14px; margin-top: 10px; text-align: center;">
                비밀번호를 입력해주세요.
            </div>
        `;
        $('.password-input-group').after(errorHtml);
        $('#inquiryPassword').focus();
        return;
    }
    
    if (!selectedInquiryId) {
        const errorHtml = `
            <div class="password-error" style="color: #dc3545; font-size: 14px; margin-top: 10px; text-align: center;">
                문의글 정보가 없습니다.
            </div>
        `;
        $('.password-input-group').after(errorHtml);
        return;
    }
    
    // 버튼 비활성화 및 로딩 표시
    const submitBtn = $('.password-input-group .btn');
    submitBtn.prop('disabled', true).text('확인 중...');
    
    // 배경 깜빡임 방지를 위해 모달에 로딩 표시
    showModalLoading();
    
    lb.ajax({
        type: "JsonAjaxPost",
        list: {
            ctl: "Userpage",
            param1: "check_inquiry_password",
            inquiry_id: selectedInquiryId,
            password: password
        },
        action: lb.obj.address,
        response_method: "response_check_password",
        havior: function(result) {
            hideModalLoading();
            
            // 버튼 상태 복원
            const submitBtn = $('.password-input-group .btn');
            submitBtn.prop('disabled', false).text('확인');
            
            try {
                result = JSON.parse(result);
                response_check_password(result);
            } catch(e) {
                // 오류 메시지 표시
                const errorHtml = `
                    <div class="password-error" style="color: #dc3545; font-size: 14px; margin-top: 10px; text-align: center;">
                        서버 응답 처리 중 오류가 발생했습니다.
                    </div>
                `;
                $('.password-error').remove();
                $('.password-input-group').after(errorHtml);
            }
        }
    });
}

// 비밀번호 확인 응답 처리
function response_check_password(result) {
    if (result.result == "1") {
        // URL 이동 전에 selectedInquiryId를 미리 저장
        const inquiryId = selectedInquiryId;
        
        // 비밀번호 확인 성공 - 세션이 서버에 저장되었으므로 바로 상세페이지로 이동
        closePasswordModal();
        
        const targetUrl = `?param=view&id=${inquiryId}`;
        window.location.href = targetUrl;
    } else {
        // 모달 내에 오류 메시지 표시
        const errorHtml = `
            <div class="password-error" style="color: #dc3545; font-size: 14px; margin-top: 10px; text-align: center;">
                비밀번호가 일치하지 않습니다. 다시 입력해주세요.
            </div>
        `;
        
        // 기존 오류 메시지 제거
        $('.password-error').remove();
        
        // 새 오류 메시지 추가
        $('.password-input-group').after(errorHtml);
        
        $('#inquiryPassword').focus().select();
        
        // 3초 후 오류 메시지 제거
        setTimeout(function() {
            $('.password-error').fadeOut();
        }, 3000);
    }
}

// 검색 실행
function searchInquiries() {
    currentFilters = {
        search_type: $('.search-select').val() || 'all',
        search_keyword: $('.search-input').val().trim(),
        category: $('#categoryFilter').val(),
        status: $('#statusFilter').val(),
        sort: $('#sortType').val()
    };
    
    // 빈 값 제거
    Object.keys(currentFilters).forEach(key => {
        if (!currentFilters[key]) {
            delete currentFilters[key];
        }
    });
    
    // 검색 시 항상 첫 페이지로
    loadInquiryList(1);
}

// 유틸리티 함수들
function getCategoryText(category) {
    const categories = {
        'product': '제품문의',
        'service': '서비스문의',
        'technical': '기술문의',
        'general': '일반문의'
    };
    return categories[category] || category;
}

function getStatusText(status) {
    const statuses = {
        'pending': '대기',
        'processing': '처리중',
        'completed': '완료'
    };
    return statuses[status] || status;
}

function getStatusClass(status) {
    return `status-${status}`;
}

function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('ko-KR');
}

function updateTotalCount(count) {
    $('.total-count').text(`전체 ${count}건`);
}

function showLoading() {
    $('.board-table').addClass('loading');
}

function hideLoading() {
    $('.board-table').removeClass('loading');
}

function showEmptyState() {
    const tbody = $('.board-table tbody');
    tbody.html('<tr><td colspan="6" class="text-center empty-message">등록된 문의글이 없습니다.</td></tr>');
}

function hideEmptyState() {
    // 빈 상태 숨김 처리는 displayInquiryList에서 처리
}

// 메시지 표시
function showMessage(message, type = 'info') {
    const alertClass = type === 'error' ? 'alert-danger' : 
                      type === 'success' ? 'alert-success' : 'alert-info';
    
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="close" onclick="$(this).parent().remove()">
                <span>&times;</span>
            </button>
        </div>
    `;
    
    // 기존 알림 제거
    $('.alert').remove();
    
    // 새 알림 추가
    $('.board-header').after(alertHtml);
    
    // 5초 후 자동 제거 (성공/정보 메시지만)
    if (type !== 'error') {
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    }
}

// 모달 전용 로딩 표시 (배경 깜빡임 방지)
function showModalLoading() {
    const loadingHtml = `
        <div class="modal-loading" style="
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
            border-radius: 10px;
        ">
            <div style="text-align: center;">
                <div style="
                    border: 3px solid #f3f3f3;
                    border-top: 3px solid #25A69B;
                    border-radius: 50%;
                    width: 30px;
                    height: 30px;
                    animation: spin 1s linear infinite;
                    margin: 0 auto 10px;
                "></div>
                <div style="color: #666; font-size: 14px;">확인 중...</div>
            </div>
        </div>
        <style>
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        </style>
    `;
    
    // 배경 스크롤 고정 (깜빡임 방지)
    $('body').css('overflow', 'hidden');
    
    // 모달 내에 로딩 오버레이 추가
    $('.password-modal').css('position', 'relative').append(loadingHtml);
}

// 모달 로딩 숨김
function hideModalLoading() {
    $('.modal-loading').remove();
    // 배경 스크롤 복원
    $('body').css('overflow', 'auto');
}
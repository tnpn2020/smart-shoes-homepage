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
            result = JSON.parse(result);
            response_inquiry_list(result);
        }
    });
}

// 문의글 리스트 응답 처리
function response_inquiry_list(result) {
    if (result.result == "1") {
        displayInquiryList(result.list);
        displayPagination(result.current_page, result.total_pages, result.total_count);
        updateTotalCount(result.total_count);
        
        if (result.list.length === 0) {
            showEmptyState();
        } else {
            hideEmptyState();
        }
    } else {
        showMessage('문의글 리스트를 불러오는데 실패했습니다: ' + result.message, 'error');
        showEmptyState();
    }
}

// 문의글 리스트 표시
function displayInquiryList(inquiries) {
    const tbody = $('.board-table tbody');
    tbody.empty();
    
    if (inquiries.length === 0) {
        tbody.append('<tr><td colspan="6" class="text-center">등록된 문의글이 없습니다.</td></tr>');
        return;
    }
    
    inquiries.forEach(function(inquiry) {
        const categoryText = getCategoryText(inquiry.category);
        const statusText = getStatusText(inquiry.status);
        const statusClass = getStatusClass(inquiry.status);
        const regDate = formatDate(inquiry.reg_date);
        const hasFiles = inquiry.has_files == '1';
        
        const row = `
            <tr data-inquiry-id="${inquiry.id}">
                <td class="col-number">${inquiry.id}</td>
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
    const pagination = $('.pagination');
    pagination.empty();
    
    if (totalPages <= 1) return;
    
    let paginationHtml = '';
    
    // 이전 페이지
    if (currentPage > 1) {
        paginationHtml += `<a href="javascript:void(0)" class="page-btn" onclick="loadInquiryList(1)">처음</a>`;
        paginationHtml += `<a href="javascript:void(0)" class="page-btn" onclick="loadInquiryList(${currentPage - 1})">이전</a>`;
    }
    
    // 페이지 번호
    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, currentPage + 2);
    
    for (let i = startPage; i <= endPage; i++) {
        const activeClass = i === currentPage ? 'active' : '';
        paginationHtml += `<a href="javascript:void(0)" class="page-btn ${activeClass}" onclick="loadInquiryList(${i})">${i}</a>`;
    }
    
    // 다음 페이지
    if (currentPage < totalPages) {
        paginationHtml += `<a href="javascript:void(0)" class="page-btn" onclick="loadInquiryList(${currentPage + 1})">다음</a>`;
        paginationHtml += `<a href="javascript:void(0)" class="page-btn" onclick="loadInquiryList(${totalPages})">마지막</a>`;
    }
    
    pagination.html(paginationHtml);
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
                    <p>문의글을 보려면 비밀번호를 입력해주세요.</p>
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
    
    if (!password) {
        showMessage('비밀번호를 입력해주세요.', 'error');
        return;
    }
    
    if (!selectedInquiryId) {
        showMessage('문의글 ID가 없습니다.', 'error');
        return;
    }
    
    showLoading('비밀번호를 확인하고 있습니다...');
    
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
            hideLoading();
            result = JSON.parse(result);
            response_check_password(result);
        }
    });
}

// 비밀번호 확인 응답 처리
function response_check_password(result) {
    if (result.result == "1") {
        // 비밀번호 확인 성공 - 세션에 저장하고 상세페이지로 이동
        const password = $('#inquiryPassword').val();
        sessionStorage.setItem('inquiry_password', password);
        closePasswordModal();
        window.location.href = `?param=view&id=${selectedInquiryId}`;
    } else {
        showMessage('비밀번호가 일치하지 않습니다.', 'error');
        $('#inquiryPassword').focus().select();
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

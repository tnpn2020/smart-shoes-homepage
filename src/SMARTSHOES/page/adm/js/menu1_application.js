$(document).ready(function() {
    // 초기 로드
    init_application_list();
    
    // 검색 입력 시 엔터키 이벤트
    $('#search_keyword').on('keypress', function(e) {
        if (e.which === 13) {
            searchApplications();
        }
    });
    
    // 필터 변경 시 자동 검색
    $('#status_filter, #applicant_type_filter, #date_from, #date_to').on('change', function() {
        searchApplications();
    });
});

// 전역 변수
let currentPage = 1;
let currentLimit = 10; // 페이지당 표시 개수 (고정)
let currentFilters = {};
let applicationData = [];
let totalCount = 0; // 전체 개수 저장용

// 신청서 리스트 초기화
function init_application_list() {
    // 전체 탭을 기본 활성화
    $('#total_li').addClass('active');
    loadApplicationList(1);
}

// 신청서 리스트 로드
function loadApplicationList(page = 1) {
    currentPage = page;
    
    showLoading();
    
    lb.ajax({
        type:"JsonAjaxPost",
        list : {
            ctl: "AdminMenu1",
            param1: "get_application_list",
            page: currentPage,
            limit: currentLimit,
            ...currentFilters
        },
        action : lb.obj.address,
        havior : function(result){
            result = JSON.parse(result);
            hideLoading();
            response_application_list(result);
        }
    });
}

// 신청서 리스트 응답 처리
function response_application_list(result) {
    try {
        if (result.result == "1") {
            applicationData = result.list || [];
            totalCount = result.total_count || 0; // 전체 개수 저장
            displayApplicationList(applicationData);
            displayPagination(result.current_page, result.total_pages, result.total_count);
            updateSummary(result.status_counts || {}, result.total_count);
            updateTotalCount(result.total_count);
            
            if (applicationData.length === 0) {
                showEmptyState();
            } else {
                hideEmptyState();
            }
        } else {
            alert('신청서 리스트를 불러오는데 실패했습니다: ' + (result.message || '알 수 없는 오류'));
            showEmptyState();
        }
    } catch(e) {
        console.error('응답 처리 오류:', e);
        alert('서버 응답 처리 중 오류가 발생했습니다.');
        hideLoading();
    }
}

// 신청서 리스트 표시
function displayApplicationList(applications) {
    const wrap = $('#wrap');
    wrap.empty();
    
    if (applications.length === 0) {
        return;
    }
    
    applications.forEach((app, index) => {
        // 역순 번호 계산: 전체개수 - ((현재페이지-1) * 페이지당개수) - 인덱스
        const rowNum = totalCount - ((currentPage - 1) * currentLimit) - index;
        
        // 상태 배지 클래스
        let statusClass = '';
        let statusText = '';
        switch(app.status) {
            case 'pending':
                statusClass = 'pending';
                statusText = '대기';
                break;
            case 'processed':
                statusClass = 'processed';
                statusText = '처리완료';
                break;
            case 'rejected':
                statusClass = 'rejected';
                statusText = '거절';
                break;
            default:
                statusClass = '';
                statusText = app.status;
        }
        
        // 신청자 유형
        let applicantTypeText = '';
        switch(app.applicant_type) {
            case 'user':
                applicantTypeText = '사용자 본인';
                break;
            case 'guardian':
                applicantTypeText = '보호자';
                break;
            default:
                applicantTypeText = app.applicant_type;
        }
        
        // 신청 사유
        let reasonText = '';
        switch(app.reason) {
            case 'elderly-care':
                reasonText = '노인 케어';
                break;
            case 'disability-support':
                reasonText = '장애인 지원';
                break;
            case 'health-monitoring':
                reasonText = '건강 모니터링';
                break;
            case 'safety-protection':
                reasonText = '안전 보호';
                break;
            case 'family-care':
                reasonText = '가족 케어';
                break;
            case 'other':
                reasonText = '기타';
                break;
            default:
                reasonText = app.reason;
        }
        
        // 템플릿 복사
        const template = $('[data-copy="copy"]').first();
        const copy_elem = template.clone();
        copy_elem.removeAttr('data-copy');
        $('#wrap').append(copy_elem);
        
        // 데이터 설정
        copy_elem.find('[data-attr="number"]').text(rowNum);
        copy_elem.find('[data-attr="applicant_type"]').text(applicantTypeText);
        copy_elem.find('[data-attr="name"]').text(app.name);
        copy_elem.find('[data-attr="phone"]').text(app.phone);
        copy_elem.find('[data-attr="email"]').text(app.email);
        copy_elem.find('[data-attr="reason"]').text(reasonText);
        copy_elem.find('[data-attr="regdate"]').text(formatDateTime(app.regdate));
        
        // 상태 배지
        const statusBadge = copy_elem.find('[data-attr="status_badge"]');
        statusBadge.text(statusText).addClass(statusClass);
        
        // 상세보기 버튼 이벤트
        copy_elem.find('[data-attr="btn_detail"]').on('click', function() {
            viewApplicationDetail(app.idx);
        });
    });
}

// 페이징 표시 (기존 스타일 사용)
function displayPagination(currentPage, totalPages, totalCount) {
    const pagination = $('#pagination');
    pagination.empty();
    
    if (totalPages <= 1) {
        pagination.html('<div class="page_item active">1</div>');
        return;
    }
    
    let paginationHtml = '';
    
    // 처음/이전 버튼
    if (currentPage > 1) {
        paginationHtml += `<div class="page_item arrow" data-page="1">«</div>`;
        paginationHtml += `<div class="page_item arrow" data-page="${currentPage - 1}">‹</div>`;
    } else {
        paginationHtml += `<div class="page_item arrow disabled">«</div>`;
        paginationHtml += `<div class="page_item arrow disabled">‹</div>`;
    }
    
    // 페이지 번호들 (현재 페이지 기준으로 앞뒤 2개씩)
    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, currentPage + 2);
    
    for (let i = startPage; i <= endPage; i++) {
        const activeClass = i === currentPage ? 'active' : '';
        paginationHtml += `<div class="page_item ${activeClass}" data-page="${i}">${i}</div>`;
    }
    
    // 다음/마지막 버튼
    if (currentPage < totalPages) {
        paginationHtml += `<div class="page_item arrow" data-page="${currentPage + 1}">›</div>`;
        paginationHtml += `<div class="page_item arrow" data-page="${totalPages}">»</div>`;
    } else {
        paginationHtml += `<div class="page_item arrow disabled">›</div>`;
        paginationHtml += `<div class="page_item arrow disabled">»</div>`;
    }
    
    pagination.html(paginationHtml);
    
    // 페이지 클릭 이벤트
    pagination.find('.page_item').on('click', function() {
        const $this = $(this);
        
        // disabled 상태이거나 active 상태면 무시
        if ($this.hasClass('disabled') || $this.hasClass('active')) {
            return;
        }
        
        const page = parseInt($this.data('page'));
        if (page && page !== currentPage) {
            loadApplicationList(page);
        }
    });
}

// 요약 정보 업데이트
function updateSummary(statusCounts, totalCount) {
    $('#total_count').text(`전체 (${totalCount || 0})`);
    $('#pending_count').text(`대기 (${statusCounts.pending || 0})`);
    $('#processed_count').text(`처리완료 (${statusCounts.processed || 0})`);
    $('#rejected_count').text(`거절 (${statusCounts.rejected || 0})`);
}

// 전체 카운트 업데이트
function updateTotalCount(count) {
    $('.total-info .total-count').text(count || 0);
}

// 검색 실행
function searchApplications() {
    currentFilters = {
        search_type: $('#search_type').val() || 'all',
        search_keyword: $('#search_keyword').val().trim(),
        status_filter: $('#status_filter').val(),
        applicant_type_filter: $('#applicant_type_filter').val(),
        date_from: $('#date_from').val(),
        date_to: $('#date_to').val()
    };
    
    // 빈 값 제거
    Object.keys(currentFilters).forEach(key => {
        if (!currentFilters[key]) {
            delete currentFilters[key];
        }
    });
    
    // 검색 시 항상 첫 페이지로
    loadApplicationList(1);
}

// 필터 초기화
function resetFilters() {
    $('#search_type').val('all');
    $('#search_keyword').val('');
    $('#status_filter').val('');
    $('#applicant_type_filter').val('');
    $('#date_from').val('');
    $('#date_to').val('');
    
    currentFilters = {};
    loadApplicationList(1);
}

// 페이지당 표시 개수는 10개로 고정
// currentLimit 변수를 파일 상단에서 수정하여 조절 가능

// 신청서 상세 보기
function viewApplicationDetail(idx) {
    lb.ajax({
        type:"JsonAjaxPost",
        list : {
            ctl: "AdminMenu1",
            param1: "get_application_detail",
            idx: idx
        },
        action : lb.obj.address,
        havior : function(result){
            result = JSON.parse(result);
            response_application_detail(result);
        }
    });
}

// 신청서 상세 응답 처리
function response_application_detail(result) {
    if (result.result == "1") {
        showApplicationDetail(result.value);
    } else {
        alert('신청서 정보를 불러오는데 실패했습니다: ' + (result.message || '알 수 없는 오류'));
    }
}

// 전역 변수 - 현재 선택된 신청서 정보
let currentApplication = null;

// 신청서 상세 표시
function showApplicationDetail(application) {
    currentApplication = application; // 상태 변경을 위해 저장
    
    const detailHtml = createDetailHTML(application);
    $('#detail_content').html(detailHtml);
    openDetailModal();
}

// 상세 정보 HTML 생성
function createDetailHTML(app) {
    const trafficSources = getTrafficSourcesText(app.traffic_sources_array);
    const statusText = getStatusText(app.status);
    const applicantTypeText = getApplicantTypeText(app.applicant_type);
    const reasonText = getReasonText(app.reason);
    
    return `
        <div class="detail-grid">
            <div class="detail-item">
                <div class="detail-label">신청자 유형</div>
                <div class="detail-value">${applicantTypeText}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">이름</div>
                <div class="detail-value">${app.name}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">생년월일</div>
                <div class="detail-value">${app.birthdate}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">연락처</div>
                <div class="detail-value">${app.phone}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">이메일</div>
                <div class="detail-value">${app.email}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">신청 사유</div>
                <div class="detail-value">${reasonText}</div>
            </div>
            <div class="detail-item full-width">
                <div class="detail-label">주소</div>
                <div class="detail-value">${app.address}</div>
            </div>
            <div class="detail-item full-width">
                <div class="detail-label">유입 경로</div>
                <div class="detail-value">${trafficSources}</div>
            </div>
            <div class="detail-item full-width">
                <div class="detail-label">추가 요청사항</div>
                <div class="detail-value">${app.additional_requests || '없음'}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">이용약관 동의</div>
                <div class="detail-value">${app.terms_agreement ? 'Y' : 'N'}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">개인정보 동의</div>
                <div class="detail-value">${app.privacy_agreement ? 'Y' : 'N'}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">마케팅 동의</div>
                <div class="detail-value">${app.marketing_agreement ? 'Y' : 'N'}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">상태</div>
                <div class="detail-value">${statusText}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">신청일시</div>
                <div class="detail-value">${formatDateTime(app.regdate)}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">처리일시</div>
                <div class="detail-value">${app.processed_at ? formatDateTime(app.processed_at) : '미처리'}</div>
            </div>
            <div class="detail-item full-width">
                <div class="detail-label">관리자 메모</div>
                <div class="detail-value">${app.admin_memo || '없음'}</div>
            </div>
        </div>
    `;
}

// 유틸리티 함수들
function getTrafficSourcesText(sources) {
    if (!sources || sources.length === 0) return '없음';
    
    const sourceLabels = sources.map(source => {
        switch(source) {
            case 'search-engine': return '검색엔진';
            case 'youtube': return '유튜브';
            case 'sns': return 'SNS';
            case 'other': return '기타';
            default: return source;
        }
    });
    return sourceLabels.join(', ');
}

function getStatusText(status) {
    switch(status) {
        case 'pending': return '대기';
        case 'processed': return '처리완료';
        case 'rejected': return '거절';
        default: return status;
    }
}

function getApplicantTypeText(type) {
    switch(type) {
        case 'user': return '사용자 본인';
        case 'guardian': return '보호자';
        default: return type;
    }
}

function getReasonText(reason) {
    switch(reason) {
        case 'elderly-care': return '노인 케어';
        case 'disability-support': return '장애인 지원';
        case 'health-monitoring': return '건강 모니터링';
        case 'safety-protection': return '안전 보호';
        case 'family-care': return '가족 케어';
        case 'other': return '기타';
        default: return reason;
    }
}

// 모달 관리 함수들
function openDetailModal() {
    $('#detail_modal').css('display', 'flex');
}

function closeDetailModal() {
    $('#detail_modal').css('display', 'none');
    currentApplication = null;
}

function openStatusModal() {
    if (!currentApplication) {
        alert('신청서 정보가 없습니다.');
        return;
    }
    
    $('#status_application_idx').val(currentApplication.idx);
    $('#status_select').val(currentApplication.status);
    $('#admin_memo').val('');
    $('#status_modal').css('display', 'flex');
}

function closeStatusModal() {
    $('#status_modal').css('display', 'none');
}

// 상태 변경 요청
function updateApplicationStatus() {
    const idx = $('#status_application_idx').val();
    const status = $('#status_select').val();
    const adminMemo = $('#admin_memo').val().trim();
    
    if (!idx || !status) {
        alert('필수 정보가 누락되었습니다.');
        return;
    }
    
    lb.ajax({
        type:"JsonAjaxPost",
        list : {
            ctl: "AdminMenu1",
            param1: "update_application_status",
            idx: idx,
            status: status,
            admin_memo: adminMemo
        },
        action : lb.obj.address,
        havior : function(result){
            console.log('update_application_status result:', result);
            result = JSON.parse(result);
            handleStatusUpdateResponse(result);
        }
    });
}

// 상태 변경 응답 처리
function handleStatusUpdateResponse(result) {
    if (result.result == "1") {
        alert('상태가 변경되었습니다.');
        closeStatusModal();
        closeDetailModal();
        loadApplicationList(currentPage); // 현재 페이지 새로고침
    } else {
        alert('상태 변경에 실패했습니다: ' + (result.message || '알 수 없는 오류'));
    }
}

// 탭 상태 필터링
function filterByStatus(status) {
    // 모든 탭에서 active 클래스 제거
    $('.out-tab-container li').removeClass('active');
    
    // 클릭된 탭에 active 클래스 추가
    if (status === '') {
        $('#total_li').addClass('active');
    } else if (status === 'pending') {
        $('#pending_li').addClass('active');
    } else if (status === 'processed') {
        $('#processed_li').addClass('active');
    } else if (status === 'rejected') {
        $('#rejected_li').addClass('active');
    }
    
    // 상태 필터 설정
    $('#status_filter').val(status);
    
    // 검색 실행
    searchApplications();
}

// 엑셀 다운로드
function downloadExcel() {
    // 현재 필터 조건으로 엑셀 다운로드
    const urlParams = new URLSearchParams({
        ctl: "AdminMenu1",
        param1: "download_applications_excel",
        ...currentFilters
    });
    
    const url = lb.obj.address + '?' + urlParams.toString();
    
    // 새 창에서 다운로드
    window.open(url, '_blank');
}

// 유틸리티 함수들
function formatDateTime(dateString) {
    if (!dateString) return '';
    
    const date = new Date(dateString);
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    
    return `${year}-${month}-${day} ${hours}:${minutes}`;
}

function showLoading() {
    $('#loading').show();
    $('#wrap').hide();
    $('#empty_state').hide();
}

function hideLoading() {
    $('#loading').hide();
    $('#wrap').show();
}

function showEmptyState() {
    $('#empty_state').show();
    $('#wrap').hide();
}

function hideEmptyState() {
    $('#empty_state').hide();
    $('#wrap').show();
}
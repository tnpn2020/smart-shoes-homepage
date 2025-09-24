// 공지사항 리스트 페이지 JavaScript

// ====== 설정 변수 ======
var NOTICE_ITEMS_PER_PAGE = 6; // 한 페이지에 표시할 공지사항 개수

$(document).ready(function() {
    // 페이지 로드 시 초기화
    initNoticePage();
    
    // 이벤트 리스너 등록
    bindNoticeEvents();
});

// 공지사항 객체
var notice = {
    config: {
        page: 1,
        limit: NOTICE_ITEMS_PER_PAGE,
        total_pages: 1,
        search_keyword: '',
        category_filter: '',
        sort: 'reg_date_desc'
    },
    
    elem: {
        notice_list: null,
        search_input: null,
        search_btn: null,
        category_filter: null,
        paging_wrap: null
    }
};

// 페이지 초기화
function initNoticePage() {

    
    // 엘리먼트 참조 설정
    notice.elem.notice_list = $('.notice-list');
    notice.elem.search_input = $('.search-input');
    notice.elem.search_btn = $('.search-btn');
    notice.elem.category_filter = $('.filter-select');
    notice.elem.paging_wrap = $('.paging-wrap');
    
    // URL 파라미터 읽기
    readUrlParams();
    
    // 초기 데이터 로드
    loadNoticeList();
}

// 이벤트 리스너 바인딩
function bindNoticeEvents() {
    // 검색 버튼 클릭
    $(document).on('click', '.search-btn', function(e) {
        e.preventDefault();
        searchNotice();
    });
    
    // 검색 입력 엔터키
    $(document).on('keypress', '.search-input', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            searchNotice();
        }
    });
    
    // 카테고리 필터 변경
    $(document).on('change', '.filter-select', function() {
        filterByCategory();
    });
    
    // 공지사항 아이템 클릭 (동적 생성된 요소를 위한 이벤트 위임)
    $(document).on('click', '.notice-item', function() {
        var noticeIdx = $(this).data('notice-idx');
        if (noticeIdx) {
            viewNoticeDetail(noticeIdx);
        }
    });
}

// URL 파라미터 읽기
function readUrlParams() {
    const urlParams = new URLSearchParams(window.location.search);
    
    notice.config.page = parseInt(urlParams.get('page')) || 1;
    notice.config.search_keyword = urlParams.get('search') || '';
    notice.config.category_filter = urlParams.get('category') || '';
    
    // 입력 필드에 값 설정
    if (notice.elem.search_input && notice.config.search_keyword) {
        notice.elem.search_input.val(notice.config.search_keyword);
    }
    if (notice.elem.category_filter && notice.config.category_filter) {
        notice.elem.category_filter.val(notice.config.category_filter);
    }
}

// 공지사항 목록 로드
function loadNoticeList() {

    
    // 로딩 표시
    showNoticeLoading();
    
    lb.ajax({
        type: "AjaxFormPost",
        list: {
            ctl: "Userpage",
            param1: "get_notice_list",
            page: notice.config.page,
            limit: notice.config.limit,
            search_keyword: notice.config.search_keyword,
            category: notice.config.category_filter,
            sort: notice.config.sort
        },
        action: lb.obj.address,
        response_method: "response_notice_list",
        havior: function(result) {

            result = JSON.parse(result);
            response_notice_list(result);
        }
    });
}

// 공지사항 목록 응답 처리
function response_notice_list(result) {
    hideNoticeLoading();
    
    if (result.result === "1") {

        renderNoticeList(result.list || []);
        renderPagination(result.current_page, result.total_pages);
        notice.config.total_pages = result.total_pages;
    } else {
        console.error('❌ 공지사항 목록 로드 실패:', result.message);
        renderEmptyList();
        showMessage('공지사항을 불러오는데 실패했습니다.', 'error');
    }
}

// 공지사항 목록 렌더링
function renderNoticeList(notices) {
    if (!notice.elem.notice_list.length) {
        console.error('❌ notice_list 엘리먼트를 찾을 수 없습니다.');
        return;
    }
    
    if (!notices || notices.length === 0) {
        renderEmptyList();
        return;
    }
    
    var html = '';
    notices.forEach(function(noticeItem) {
        var categoryClass = noticeItem.category || 'general';
        var categoryText = noticeItem.category_text || '일반';
        var formattedDate = noticeItem.formatted_date || noticeItem.regdate;
        
        // content에서 HTML 태그 제거하고 일정 길이로 자르기
        var content = noticeItem.content || '';
        var cleanContent = content.replace(/<[^>]*>/g, '').trim(); // HTML 태그 제거
        var shortContent = cleanContent.length > 100 ? cleanContent.substring(0, 100) + '...' : cleanContent;
        
        // 중요도 표시 여부 확인 (kind 컬럼 사용)
        var isImportant = noticeItem.kind == 1;
        var displayCategory = isImportant ? categoryText : categoryText; // 중요한 경우에도 카테고리 표시
        
        html += `
            <div class="notice-item ${categoryClass}" style="cursor: pointer;" data-notice-idx="${noticeItem.idx}">
                <div class="notice-header">
                    <div class="notice-badges">
                        ${isImportant ? '<span class="badge-important">중요</span>' : ''}
                        ${displayCategory ? `<span class="badge-category">${displayCategory}</span>` : ''}
                    </div>
                    <div class="notice-date">
                        ${formattedDate}
                    </div>
                </div>
                <div class="notice-content">
                    <h3 class="notice-title">
                        <span class="title-text">${noticeItem.title}</span>
                    </h3>
                    <p class="notice-preview">${shortContent}</p>
                </div>
            </div>
        `;
    });
    
    notice.elem.notice_list.html(html);
}

// 빈 목록 렌더링
function renderEmptyList() {
    if (!notice.elem.notice_list.length) return;
    
    notice.elem.notice_list.html(`
        <div class="empty-notice">
            <p>등록된 공지사항이 없습니다.</p>
        </div>
    `);
}

// 페이지네이션 렌더링
function renderPagination(currentPage, totalPages) {
    if (!notice.elem.paging_wrap.length || totalPages <= 1) {
        if (notice.elem.paging_wrap.length) {
            notice.elem.paging_wrap.hide();
        }
        return;
    }
    
    notice.elem.paging_wrap.show();
    
    var html = '';
    
    // 이전 페이지 버튼
    if (currentPage > 1) {
        html += `<a href="#" class="paging-btn paging-prev" onclick="goToPage(${currentPage - 1})">
                    <span><i class="xi-angle-left"></i></span>
                 </a>`;
    } else {
        html += `<span class="paging-btn paging-prev disabled">
                    <span><i class="xi-angle-left"></i></span>
                 </span>`;
    }
    
    // 페이지 번호들
    html += '<div class="paging-num-box">';
    
    var startPage = Math.max(1, currentPage - 2);
    var endPage = Math.min(totalPages, currentPage + 2);
    
    for (var i = startPage; i <= endPage; i++) {
        var activeClass = i === currentPage ? 'active' : '';
        html += `<a href="#" class="paging-num ${activeClass}" onclick="goToPage(${i})">${i}</a>`;
    }
    
    html += '</div>';
    
    // 다음 페이지 버튼
    if (currentPage < totalPages) {
        html += `<a href="#" class="paging-btn paging-next" onclick="goToPage(${currentPage + 1})">
                    <span><i class="xi-angle-right"></i></span>
                 </a>`;
    } else {
        html += `<span class="paging-btn paging-next disabled">
                    <span><i class="xi-angle-right"></i></span>
                 </span>`;
    }
    
    notice.elem.paging_wrap.html(html);
}

// 페이지 이동
function goToPage(page) {
    if (page < 1 || page > notice.config.total_pages) return;
    
    notice.config.page = page;
    updateUrl();
    loadNoticeList();
}

// 검색
function searchNotice() {
    if (!notice.elem.search_input.length) return;
    
    notice.config.search_keyword = notice.elem.search_input.val().trim();
    notice.config.page = 1; // 검색 시 첫 페이지로
    
    updateUrl();
    loadNoticeList();
}

// 카테고리 필터링
function filterByCategory() {
    if (!notice.elem.category_filter.length) return;
    
    notice.config.category_filter = notice.elem.category_filter.val();
    notice.config.page = 1; // 필터링 시 첫 페이지로
    
    updateUrl();
    loadNoticeList();
}

// URL 업데이트
function updateUrl() {
    var params = new URLSearchParams();
    params.set('param', 'notice');
    
    if (notice.config.page > 1) {
        params.set('page', notice.config.page);
    }
    if (notice.config.search_keyword) {
        params.set('search', notice.config.search_keyword);
    }
    if (notice.config.category_filter) {
        params.set('category', notice.config.category_filter);
    }
    
    var newUrl = window.location.pathname + '?' + params.toString();
    window.history.pushState({}, '', newUrl);
}

// 공지사항 상세보기
function viewNoticeDetail(noticeIdx) {
    if (!noticeIdx) {
        console.error('❌ 공지사항 ID가 없습니다.');
        return;
    }
    

    window.location.href = `?param=notice-detail&idx=${noticeIdx}`;
}

// 로딩 표시
function showNoticeLoading() {
    if (notice.elem.notice_list.length) {
        notice.elem.notice_list.html(`
            <div class="loading-notice">
                <p>공지사항을 불러오는 중...</p>
            </div>
        `);
    }
}

// 로딩 숨김
function hideNoticeLoading() {
    // 로딩은 데이터 렌더링으로 자동 교체됨
}

// 메시지 표시 (write.js와 동일한 스타일)
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

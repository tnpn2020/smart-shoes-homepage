// 문의글 관리 JavaScript

let currentPage = 1;
let currentLimit = 10;
let currentFilters = {};
let selectedInquiryId = null;
let selectedInquiryData = null;
let currentTab = 0;

$(document).ready(function() {
    initPage();
    loadInquiryList();
});

// 페이지 초기화
function initPage() {
    // 검색 이벤트
    $('#search_text').on('keypress', function(e) {
        if (e.which === 13) searchInquiries();
    });
    
    // 필터 변경 시 자동 검색
    $('#category_filter, #status_filter').on('change', function() {
        searchInquiries();
    });
    
    // 탭 초기화
    move_tab(0);
}

// 탭 이동
function move_tab(tab_index) {
    currentTab = tab_index;
    
    // 탭 활성화
    $('.out-tab-container li').removeClass('active');
    
    let statusFilter = '';
    switch(tab_index) {
        case 0: // 전체
            $('#total_li').addClass('active');
            statusFilter = '';
            break;
        case 1: // 대기
            $('#pending_li').addClass('active');
            statusFilter = 'pending';
            break;
        case 2: // 처리중
            $('#processing_li').addClass('active');
            statusFilter = 'processing';
            break;
        case 3: // 완료
            $('#completed_li').addClass('active');
            statusFilter = 'completed';
            break;
    }
    
    $('#status_filter').val(statusFilter);
    searchInquiries();
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
            handleInquiryListResponse(result);
        }
    });
}

// 리스트 응답 처리
function handleInquiryListResponse(result) {
    if (result.result == "1") {
        displayInquiryList(result.list, result.total_count);
        displayPagination(result.current_page, result.total_pages);
        updateTabCounts(result.status_counts || {});
        
        if (result.list.length === 0) {
            showEmptyState();
        } else {
            hideEmptyState();
        }
    } else {
        showEmptyState();
    }
}

// 문의글 리스트 표시
function displayInquiryList(inquiries, totalCount = 0) {
    const tbody = $('#inquiry_list');
    tbody.empty();
    
    inquiries.forEach(function(inquiry, index) {
        // 역순 연번 계산: 전체건수 - ((현재페이지-1) * 페이지당개수 + 현재인덱스)
        const rowNumber = totalCount - ((currentPage - 1) * currentLimit + index);
        
        const categoryText = getCategoryText(inquiry.category);
        const statusText = getStatusText(inquiry.status);
        const regDate = formatDate(inquiry.reg_date);
        const hasFiles = inquiry.has_files == '1';
        
        const row = `
            <tr>
                <td class="col-num">${rowNumber}</td>
                <td class="col-category">
                    <span class="badge badge-${inquiry.category}">${categoryText}</span>
                </td>
                <td class="col-title">
                    <div class="table-title">
                        <p class="title">
                            <span onclick="viewDetail(${inquiry.id})" style="cursor: pointer; color: #007bff;">${inquiry.title}</span>
                        </p>
                        <p class="sub-info">${inquiry.author} | ${inquiry.email}</p>
                    </div>
                </td>
                <td class="col-author">${inquiry.author}</td>
                <td class="col-email">${inquiry.email}</td>
                <td class="col-date">
                    <div class="table-date">${regDate}</div>
                </td>
                <td class="col-status">
                    <span class="badge badge-${inquiry.status}">${statusText}</span>
                </td>
                <td class="col-files">
                    ${hasFiles ? '📎' : '-'}
                </td>
                <td class="col-actions">
                    <div class="btn-group">
                        <input class="btn-sm btn-primary" type="button" value="보기" onclick="viewDetail(${inquiry.id})"/>
                        <input class="btn-sm btn-danger" type="button" value="삭제" onclick="quickDelete(${inquiry.id})"/>
                    </div>
                </td>
            </tr>
        `;
        tbody.append(row);
    });
}

// 페이징 표시
function displayPagination(currentPage, totalPages) {
    const container = $('#paging');
    container.empty();
    
    if (totalPages <= 1) return;
    
    let html = '<div class="pagination-wrap">';
    
    // 이전 페이지
    if (currentPage > 1) {
        html += `<a href="javascript:void(0);" onclick="loadInquiryList(${currentPage - 1})" class="page-btn">이전</a>`;
    }
    
    // 페이지 번호
    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, currentPage + 2);
    
    for (let i = startPage; i <= endPage; i++) {
        const activeClass = i === currentPage ? 'current' : '';
        html += `<a href="javascript:void(0);" onclick="loadInquiryList(${i})" class="page-num ${activeClass}">${i}</a>`;
    }
    
    // 다음 페이지
    if (currentPage < totalPages) {
        html += `<a href="javascript:void(0);" onclick="loadInquiryList(${currentPage + 1})" class="page-btn">다음</a>`;
    }
    
    html += '</div>';
    container.html(html);
}

// 탭 카운트 업데이트
function updateTabCounts(counts) {
    // 숫자로 변환하여 더하기 (문자열 연결 방지)
    const pending = parseInt(counts.pending || 0);
    const processing = parseInt(counts.processing || 0);
    const completed = parseInt(counts.completed || 0);
    const total = pending + processing + completed;
    
    $('#total_count').text(`전체 (${total})`);
    $('#pending_count').text(`대기 (${pending})`);
    $('#processing_count').text(`처리중 (${processing})`);
    $('#completed_count').text(`완료 (${completed})`);
}

// 상세보기
function viewDetail(inquiryId) {
    selectedInquiryId = inquiryId;
    
    lb.ajax({
        type: "JsonAjaxPost",
        list: {
            ctl: "Userpage",
            param1: "admin_get_inquiry_detail",
            inquiry_id: inquiryId
        },
        action: lb.obj.address,
        response_method: "response_inquiry_detail",
        havior: function(result) {
            console.log('admin_get_inquiry_detail result:', result);
            result = JSON.parse(result);
            console.log('admin_get_inquiry_detail parsed:', result);
            handleDetailResponse(result);
        }
    });
}

// 상세보기 응답 처리
function handleDetailResponse(result) {
    if (result.result == "1") {
        selectedInquiryData = result.inquiry;
        displayDetailModal(result.inquiry);
        showModal('inquiry_detail_modal');
    } else {
        alert('문의글을 불러오는데 실패했습니다.');
    }
}

// 상세보기 모달 표시
function displayDetailModal(inquiry) {
    const categoryText = getCategoryText(inquiry.category);
    const statusText = getStatusText(inquiry.status);
    
    let filesHtml = '';
    if (inquiry.files && inquiry.files.length > 0) {
        filesHtml = `
            <div class="detail-section">
                <div class="detail-label">첨부파일</div>
                <div class="detail-value">
        `;
        inquiry.files.forEach(function(file) {
            filesHtml += `
                <div class="file-item">
                    <span class="file-name">📎 ${file.original_name}</span>
                    <button type="button" class="btn-download" onclick="downloadFile('${file.saved_name}', '${file.original_name}')">
                        💾 다운로드
                    </button>
                </div>
            `;
        });
        filesHtml += `
                </div>
            </div>
        `;
    }
    
    let replyHtml = '';
    if (inquiry.admin_reply) {
        let adminFilesHtml = '';
        if (inquiry.admin_files && inquiry.admin_files.length > 0) {
            adminFilesHtml = '<div class="admin-files-section">';
            inquiry.admin_files.forEach(function(file) {
                adminFilesHtml += `
                    <div class="file-item admin-file-item">
                        <span class="file-name">📎 ${file.original_name}</span>
                        <button type="button" class="btn-download" onclick="downloadFile('${file.saved_name}', '${file.original_name}')">
                            💾 다운로드
                        </button>
                    </div>
                `;
            });
            adminFilesHtml += '</div>';
        }
        
        replyHtml = `
            <div class="admin-reply-section">
                <div class="reply-header">
                    <strong>📝 관리자 답변</strong>
                    <span class="reply-date">${formatDateTime(inquiry.reply_date)}</span>
                </div>
                <div class="reply-content">${inquiry.admin_reply.replace(/\n/g, '<br>')}</div>
                ${adminFilesHtml}
            </div>
        `;
    }
    
    const html = `
        <div class="inquiry-detail-wrapper">
            <!-- 기본 정보 섹션 -->
            <div class="detail-info-grid">
                <div class="detail-section">
                    <div class="detail-label">제목</div>
                    <div class="detail-value">${inquiry.title}</div>
                </div>
                
                <div class="detail-section">
                    <div class="detail-label">분류</div>
                    <div class="detail-value">
                        <span class="badge badge-${inquiry.category}">${categoryText}</span>
                    </div>
                </div>
                
                <div class="detail-section">
                    <div class="detail-label">상태</div>
                    <div class="detail-value">
                        <span class="badge badge-${inquiry.status}">${statusText}</span>
                    </div>
                </div>
                
                <div class="detail-section">
                    <div class="detail-label">작성자</div>
                    <div class="detail-value">${inquiry.author}</div>
                </div>
                
                <div class="detail-section">
                    <div class="detail-label">이메일</div>
                    <div class="detail-value">${inquiry.email}</div>
                </div>
                
                <div class="detail-section">
                    <div class="detail-label">등록일</div>
                    <div class="detail-value">${formatDateTime(inquiry.reg_date)}</div>
                </div>
            </div>
            
            <!-- 문의 내용 섹션 -->
            <div class="detail-section content-section">
                <div class="detail-label">문의 내용</div>
                <div class="content-box">
                    ${inquiry.content.replace(/\n/g, '<br>')}
                </div>
            </div>
            
            ${filesHtml}
            ${replyHtml}
        </div>
    `;
    
    $('#inquiry_detail_content').html(html);
}

// 모달 표시/숨김 함수들
function showModal(modalId) {
    $('#' + modalId).addClass('show');
}

function hideModal(modalId) {
    $('#' + modalId).removeClass('show');
}

// 빠른 삭제
function quickDelete(inquiryId) {
    selectedInquiryId = inquiryId;
    showDeleteModal();
}

// 답변 모달 표시
function showReplyModal() {
    if (!selectedInquiryData) {
        alert('문의글 정보를 먼저 불러와주세요.');
        return;
    }
    
    $('#reply_title').val(selectedInquiryData.title);
    $('#reply_content').val(selectedInquiryData.admin_reply || '');
    $('#reply_status').val(selectedInquiryData.status);
    $('#reply_inquiry_id').val(selectedInquiryId);
    $('#reply_files').val(''); // 파일 입력 초기화
    $('#reply_file_list').empty(); // 파일 목록 초기화
    showModal('reply_modal');
}

// 선택된 파일 표시
function displaySelectedFiles() {
    const fileInput = document.getElementById('reply_files');
    const fileList = document.getElementById('reply_file_list');
    
    fileList.innerHTML = '';
    
    if (fileInput.files.length > 0) {
        const listHtml = Array.from(fileInput.files).map(file => 
            `<div class="selected-file-item">
                📎 ${file.name} (${formatFileSize(file.size)})
            </div>`
        ).join('');
        
        fileList.innerHTML = listHtml;
    }
}

// 파일 크기 포맷팅
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// 답변 저장
function saveReply() {
    const content = $('#reply_content').val().trim();
    const status = $('#reply_status').val();
    
    if (!content) {
        alert('답변 내용을 입력해주세요.');
        return;
    }
    
    // 파일이 있는지 확인
    const fileInput = document.getElementById('reply_files');
    const hasFiles = fileInput.files.length > 0;
    
    if (hasFiles) {
        // 파일이 있으면 AjaxFormPost 사용
        lb.ajax({
            type: "AjaxFormPost",
            list: {
                ctl: "Userpage",
                param1: "save_reply_with_files",
                inquiry_id: selectedInquiryId
            },
            elem: document.getElementById("reply_form"),
            action: lb.obj.address,
            response_method: "response_save_reply",
            havior: function(result) {
                console.log('save_reply_with_files result:', result);
                result = JSON.parse(result);
                console.log('save_reply_with_files parsed:', result);
                if (result.result == "1") {
                    alert('답변이 저장되었습니다.');
                    closeReplyModal();
                    loadInquiryList(currentPage);
                } else {
                    alert('답변 저장에 실패했습니다: ' + (result.message || ''));
                }
            }
        });
    } else {
        // 파일이 없으면 기존 방식 사용
        lb.ajax({
            type: "JsonAjaxPost",
            list: {
                ctl: "Userpage",
                param1: "save_reply",
                inquiry_id: selectedInquiryId,
                reply_content: content,
                status: status
            },
            action: lb.obj.address,
            response_method: "response_save_reply",
            havior: function(result) {
                console.log('save_reply result:', result);
                result = JSON.parse(result);
                console.log('save_reply parsed:', result);
                if (result.result == "1") {
                    alert('답변이 저장되었습니다.');
                    closeReplyModal();
                    loadInquiryList(currentPage);
                } else {
                    alert('답변 저장에 실패했습니다: ' + (result.message || ''));
                }
            }
        });
    }
}



// 상태 업데이트
function updateStatus() {
    const newStatus = $('#new_status').val();
    
    lb.ajax({
        type: "JsonAjaxPost",
        list: {
            ctl: "Userpage",
            param1: "update_status",
            inquiry_id: selectedInquiryId,
            status: newStatus
        },
        action: lb.obj.address,
        response_method: "response_update_status",
        havior: function(result) {
            console.log('update_status result:', result);
            result = JSON.parse(result);
            console.log('update_status parsed:', result);
            if (result.result == "1") {
                alert('상태가 변경되었습니다.');
                closeStatusModal();
                loadInquiryList(currentPage);
            } else {
                alert('상태 변경에 실패했습니다.');
            }
        }
    });
}

// 삭제 모달 표시
function showDeleteModal() {
    showModal('delete_modal');
}

// 삭제 확인
function confirmDelete() {
    lb.ajax({
        type: "JsonAjaxPost",
        list: {
            ctl: "Userpage",
            param1: "admin_delete_inquiry",
            inquiry_id: selectedInquiryId
        },
        action: lb.obj.address,
        response_method: "response_delete_inquiry",
        havior: function(result) {
            console.log('admin_delete_inquiry result:', result);
            result = JSON.parse(result);
            console.log('admin_delete_inquiry parsed:', result);
            if (result.result == "1") {
                alert('문의글이 삭제되었습니다.');
                closeDeleteModal();
                loadInquiryList(currentPage);
            } else {
                alert('삭제에 실패했습니다.');
            }
        }
    });
}

// 검색 실행
function searchInquiries() {
    currentFilters = {
        search_type: $('#search_type').val(),
        search_keyword: $('#search_text').val().trim(),
        category: $('#category_filter').val(),
        status: $('#status_filter').val(),
        sort: 'reg_date_desc'
    };
    
    // 빈 값 제거
    Object.keys(currentFilters).forEach(key => {
        if (!currentFilters[key]) {
            delete currentFilters[key];
        }
    });
    
    loadInquiryList(1);
}

// 검색 초기화
function resetSearch() {
    $('#search_type').val('all');
    $('#search_text').val('');
    $('#category_filter').val('');
    $('#status_filter').val('');

    
    currentFilters = {};
    move_tab(0); // 전체 탭으로 이동
}



// 모달 닫기 함수들
function closeModal() {
    hideModal('inquiry_detail_modal');
    selectedInquiryId = null;
    selectedInquiryData = null;
}

function closeReplyModal() {
    hideModal('reply_modal');
    $('#reply_content').val('');
}

function closeStatusModal() {
    hideModal('status_modal');
}

function closeDeleteModal() {
    hideModal('delete_modal');
    selectedInquiryId = null;
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

function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('ko-KR').replace(/\./g, '.').replace(/\s/g, '');
}

function formatDateTime(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleString('ko-KR');
}

function showLoading() {
    $('#loading_state').show();
    $('#table_container').hide();
    $('#paging').hide();
}

function hideLoading() {
    $('#loading_state').hide();
    $('#table_container').show();
    $('#paging').show();
}

function showEmptyState() {
    $('#empty_state').show();
    $('#table_container').hide();
    $('#paging').hide();
}

function hideEmptyState() {
    $('#empty_state').hide();
    $('#table_container').show();
    $('#paging').show();
}

// 파일 다운로드
function downloadFile(savedName, originalName) {
    console.log('다운로드 요청:', savedName, originalName);
    
    const downloadUrl = `${lb.obj.address}?ctl=Userpage&param1=download_inquiry_file&file_name=${encodeURIComponent(savedName)}&original_name=${encodeURIComponent(originalName)}`;
    
    // 새 창에서 다운로드
    const link = document.createElement('a');
    link.href = downloadUrl;
    link.download = originalName;
    link.target = '_blank';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

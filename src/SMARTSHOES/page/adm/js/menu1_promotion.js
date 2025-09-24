// 홍보/행사 관리 목록 페이지 JavaScript

$(document).ready(function() {
    // 페이지 로드 시 초기화
    initPromotionPage();
    
    // 검색 엔터키 이벤트
    $('#search_keyword').on('keypress', function(e) {
        if (e.which === 13) {
            searchPromotions();
        }
    });
});

// 홍보/행사 객체
var promotion = {
    config: {
        page: 1,
        limit: 10,
        total_pages: 1,
        search_keyword: ''
    },
    
    elem: {}
};

var promotion_relation_array = []; // 순서를 위한 배열
var select_promotion_idx = null;
var elem_select_tr = null;
var now_datas = null; // 현재 데이터(초기화 버튼을 위해 필요)

// 페이지 초기화
function initPromotionPage() {
    // elem 객체 초기화
    promotion.elem = {
        list_container: $('#promotion_list'),
        pagination_container: $('#pagination'),
        loading_spinner: $('#loading_spinner'),
        empty_message: $('#empty_message'),
        search_keyword: $('#search_keyword')
    };
    
    loadPromotionList();
}

// 홍보/행사 목록 로드
function loadPromotionList(page = 1) {
    promotion.config.page = page;
    
    showLoading();
    
    lb.ajax({
        type: "JsonAjaxPost",
        list: {
            ctl: "AdminMenu1",
            param1: "get_promotion_list",
            page: promotion.config.page,
            limit: promotion.config.limit,
            search_keyword: promotion.config.search_keyword
        },
        action: lb.obj.address,
        response_method: "response_promotion_list",
        havior: function(result) {
            hideLoading();
            result = JSON.parse(result);
            response_promotion_list(result);
        }
    });
}

// 홍보/행사 목록 응답 처리
function response_promotion_list(result) {
    if (result.result === '1') {
        now_datas = result.value; // 현재 데이터 저장
        renderPromotionList(result.value);
        renderPagination(result.total_pages, result.current_page);
        promotion.config.total_pages = result.total_pages;
    } else {
        alert('목록을 불러오는데 실패했습니다.');
        showEmptyMessage();
    }
}

// 홍보/행사 목록 렌더링
function renderPromotionList(list) {
    if (list.length === 0) {
        showEmptyMessage();
        return;
    }
    
    hideEmptyMessage();
    
    var html = '';
    
    list.forEach(function(item, index) {
        var rowNumber = (promotion.config.page - 1) * promotion.config.limit + index + 1;
        var statusBadge = item.is_active == 1 ? 
            '<span class="status-active">활성</span>' : 
            '<span class="status-inactive">비활성</span>';
        
        var awardBadge = item.award_badge ? 
            '<span class="award-badge">' + item.award_badge + '</span>' : 
            '<span class="no-data">-</span>';
        
        html += `
            <tr id="promotion_${item.idx}" promotion_idx="${item.idx}">
                <td>
                    <div class="insert insert-chk">
                        <label class="check_label">
                            <input type="checkbox" name="checkbox[]" value="${item.idx}"/>
                            <span class="checkmark"></span>
                        </label>
                    </div>
                </td>
                <td class="col-num">${rowNumber}</td>
                <td class="col-tit">
                    <div class="table-tit">
                        <p class="tit">
                            <span onclick="goToModify(${item.idx})" style="cursor: pointer;">${item.event_name}</span>
                        </p>
                    </div>
                </td>
                <td class="col-long-num">
                    <div class="table-date">${item.event_period}</div>
                </td>
                <td class="col-tit">
                    <div class="table-tit">
                        <p>${item.event_location}</p>
                    </div>
                </td>
                <td class="col-short-num">${awardBadge}</td>
                <td class="col-short-num">${statusBadge}</td>
                <td class="col-long-num">
                    <div class="table-date">${item.formatted_regdate}</div>
                </td>
                <td class="col-short-num">
                    <div class="btn-container">
                        <button type="button" class="btn btn-xs btn-outline-primary" onclick="goToModify(${item.idx})">수정</button>
                        <button type="button" class="btn btn-xs btn-outline-danger" onclick="showDeleteModal(${item.idx})">삭제</button>
                    </div>
                </td>
            </tr>
        `;
        
        promotion_relation_array.push(item.idx);
    });
    
    promotion.elem.list_container.html(html);
    
    // 클릭 이벤트 초기화
    init_select();
}

// 선택 이벤트 초기화 (체크박스 방식으로 변경)
function init_select() {
    // 체크박스 클릭 시 선택 처리
    $('input[name="checkbox[]"]').off('change').on('change', function() {
        if (this.checked) {
            select_promotion(this.value);
        } else {
            // 체크 해제 시 선택 해제
            if (select_promotion_idx == this.value) {
                if (elem_select_tr) {
                    elem_select_tr.style.backgroundColor = "white";
                }
                select_promotion_idx = null;
                elem_select_tr = null;
            }
        }
    });
}




// 페이징 렌더링
function renderPagination(totalPages, currentPage) {
    if (totalPages <= 1) {
        promotion.elem.pagination_container.empty();
        return;
    }
    
    var html = '';
    var startPage = Math.max(1, currentPage - 2);
    var endPage = Math.min(totalPages, currentPage + 2);
    
    html += '<div class="pagination-wrap">';
    
    // 이전 페이지
    if (currentPage > 1) {
        html += `<a href="javascript:void(0);" onclick="loadPromotionList(${currentPage - 1})" class="page-btn">이전</a>`;
    }
    
    // 페이지 번호
    for (var i = startPage; i <= endPage; i++) {
        var activeClass = i === currentPage ? 'current' : '';
        html += `<a href="javascript:void(0);" onclick="loadPromotionList(${i})" class="page-num ${activeClass}">${i}</a>`;
    }
    
    // 다음 페이지
    if (currentPage < totalPages) {
        html += `<a href="javascript:void(0);" onclick="loadPromotionList(${currentPage + 1})" class="page-btn">다음</a>`;
    }
    
    html += '</div>';
    
    promotion.elem.pagination_container.html(html);
}

// 검색 실행
function searchPromotions() {
    promotion.config.search_keyword = promotion.elem.search_keyword.val().trim();
    promotion.config.page = 1;
    loadPromotionList();
}

// 등록 페이지로 이동
function goToRegister() {
    if(confirm("홍보/행사 등록페이지로 이동하시겠습니까?")){
        window.location.href = "/?ctl=move&param=adm&param1=menu1_promotion_upload";
    }
}

// 수정 페이지로 이동
function goToModify(idx) {
    if(confirm("홍보/행사 수정페이지로 이동하시겠습니까?")){
        window.location.href = "?ctl=move&param=adm&param1=menu1_promotion_modify&idx=" + idx;
    }
}

// 홍보/행사 선택
function select_promotion(promotion_idx) {
    var elem_tr = document.getElementById("promotion_" + promotion_idx);
    if (elem_tr != null) {
        if (elem_select_tr != null) {
            elem_select_tr.style.backgroundColor = "white";
        }
        select_promotion_idx = promotion_idx;
        elem_select_tr = elem_tr;
        elem_tr.style.backgroundColor = "#d8dce0";
    }
}

// 선택된 행이 있는지 확인 (체크박스 방식)
function check_select_tr() {
    var checkedBoxes = document.querySelectorAll('input[name="checkbox[]"]:checked');
    if (checkedBoxes.length === 0) {
        alert("순서를 변경할 홍보/행사를 선택해주세요");
        return false;
    } else if (checkedBoxes.length > 1) {
        alert("순서 변경은 하나의 항목만 선택해주세요");
        return false;
    } else {
        // 체크된 항목으로 선택 상태 업데이트
        select_promotion(checkedBoxes[0].value);
        return true;
    }
}

// 위로 이동
function btn_up() {
    var move_count = document.getElementById("move_count").value;
    if (move_count == "") {
        move_count = 1;
        document.getElementById("move_count").value = 1;
    }
    
    if (check_select_tr()) {
        for (var i = 0; i < move_count; i++) {
            var $tr = $(elem_select_tr);
            $tr.prev().before($tr);
        }
    }
}

// 아래로 이동
function btn_down() {
    var move_count = document.getElementById("move_count").value;
    if (move_count == "") {
        move_count = 1;
        document.getElementById("move_count").value = 1;
    }

    if (check_select_tr()) {
        for (var i = 0; i < move_count; i++) {
            var $tr = $(elem_select_tr);
            $tr.next().after($tr);
        }
    }
}

// 맨 위로 이동
function btn_top() {
    if (check_select_tr()) {
        var $tr = $(elem_select_tr);
        $tr.closest('tbody').find('tr:first').before($tr);
    }
}

// 맨 아래로 이동
function btn_end() {
    if (check_select_tr()) {
        var $tr = $(elem_select_tr);
        $tr.closest('tbody').append($tr);
    }
}

// 초기화
function btn_init() {
    if (now_datas != null) {
        renderPromotionList(now_datas);
    }
}

// 순서 적용하기
function btn_save() {
    var elem_tbody = document.getElementById("promotion_list");
    var childs = elem_tbody.children;
    
    if (childs.length == 0) {
        alert("적용할 홍보/행사가 없습니다.");
        return;
    } else {
        var relation_array = [];
        // tr에 있는 promotion_idx 값을 가져와서 배열에 담음
        for (var i = 0; i < childs.length; i++) {
            relation_array.push(childs[i].getAttribute("promotion_idx"));
        }
        
        // 원래 배열과 비교해서 변경된게 있는지 확인
        if (JSON.stringify(promotion_relation_array) == JSON.stringify(relation_array)) {
            alert("순서 변동이 없습니다.");
        } else {
            // 변경 API 전송
            request_promotion_relation_change(relation_array);
        }
    }
}

// 순서 변경 API 호출
function request_promotion_relation_change(relation_array) {
    lb.ajax({
        type: "JsonAjaxPost",
        list: {
            ctl: "AdminMenu1",
            param1: "request_promotion_relation_change",
            relation_array: JSON.stringify(relation_array)
        },
        action: lb.obj.address,
        response_method: "response_promotion_relation_change",
        havior: function(result) {
            result = JSON.parse(result);
            response_promotion_relation_change(result);
        }
    });
}

// 순서 변경 응답 처리
function response_promotion_relation_change(result) {
    if (result.result == "1") {
        alert("적용되었습니다");
        loadPromotionList(promotion.config.page);
    } else {
        alert(result.message || "순서 변경 중 오류가 발생했습니다.");
    }
}

// 삭제 확인 및 실행
function showDeleteModal(idx) {
    if (confirm('정말로 삭제하시겠습니까?\n삭제된 데이터는 복구할 수 없습니다.')) {
        deletePromotion(idx);
    }
}

// 홍보/행사 삭제 실행
function deletePromotion(idx) {
    lb.ajax({
        type: "JsonAjaxPost",
        list: {
            ctl: "AdminMenu1",
            param1: "promotion_delete",
            idx: idx
        },
        action: lb.obj.address,
        response_method: "response_promotion_delete",
        havior: function(result) {
            result = JSON.parse(result);
            response_promotion_delete(result);
        }
    });
}

// 홍보/행사 삭제 응답 처리
function response_promotion_delete(result) {
    if (result.result === '1') {
        alert('삭제가 완료되었습니다.');
        // 현재 페이지 새로고침
        window.location.reload();
    } else {
        alert(result.message || '삭제에 실패했습니다.');
    }
}

// 로딩 표시
function showLoading() {
    promotion.elem.list_container.hide();
    promotion.elem.empty_message.hide();
    promotion.elem.loading_spinner.show();
}

// 로딩 숨김
function hideLoading() {
    promotion.elem.loading_spinner.hide();
    promotion.elem.list_container.show();
}

// 빈 목록 메시지 표시
function showEmptyMessage() {
    promotion.elem.list_container.hide();
    promotion.elem.loading_spinner.hide();
    promotion.elem.empty_message.show();
}

// 빈 목록 메시지 숨김
function hideEmptyMessage() {
    promotion.elem.empty_message.hide();
}

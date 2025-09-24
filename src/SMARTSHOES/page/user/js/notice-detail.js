// 공지사항 상세 페이지 JavaScript

$(document).ready(function () {
  // 페이지 로드 시 초기화
  initNoticeDetailPage();
});

// 공지사항 상세 객체
var noticeDetail = {
  config: {
    notice_idx: null,
  },

  elem: {
    post_wrap: null,
    loading_container: null,
  },
};

// 페이지 초기화
function initNoticeDetailPage() {
  // URL에서 공지사항 ID 추출
  getNoticeIdFromUrl();

  // 공지사항 상세 데이터 로드
  if (noticeDetail.config.notice_idx) {
    loadNoticeDetail();
  } else {
    showDetailError("공지사항 ID가 없습니다.");
  }
}

// URL에서 공지사항 ID 추출
function getNoticeIdFromUrl() {
  const urlParams = new URLSearchParams(window.location.search);
  noticeDetail.config.notice_idx = urlParams.get("idx");
}

// 공지사항 상세 로드
function loadNoticeDetail() {
  // 로딩 표시
  showDetailLoading();

  lb.ajax({
    type: "AjaxFormPost",
    list: {
      ctl: "Userpage",
      param1: "get_notice_detail",
      idx: noticeDetail.config.notice_idx,
    },
    action: lb.obj.address,
    response_method: "response_notice_detail",
    havior: function (result) {
      result = JSON.parse(result);
      response_notice_detail(result);
    },
  });
}

// 공지사항 상세 응답 처리
function response_notice_detail(result) {
  hideDetailLoading();

  if (result.result === "1" && result.notice) {
    renderNoticeDetail(result.notice);
  } else {
    showDetailError(result.message || "공지사항을 찾을 수 없습니다.");
  }
}

// 공지사항 상세 렌더링
function renderNoticeDetail(notice) {
  // 로딩 숨김
  $("#loading-detail").hide();

  // 카테고리 정보 설정
  var categoryText = getCategoryText(notice.category);
  var categoryClass = notice.category || "general";
  var formattedDate = notice.formatted_date || notice.regdate;
  var isImportant = notice.is_important == 1 || notice.is_important == "1";

  // 중요 게시물 배지 표시/숨김
  if (isImportant) {
    $("#notice-important-badge").show();
  } else {
    $("#notice-important-badge").hide();
  }

  // 각 요소에 값 설정
  $("#notice-category").text(categoryText);
  $("#notice-badge")
    .removeClass()
    .addClass("notice-badge " + categoryClass);
  $("#notice-title").text(notice.title);
  $("#notice-date").text("작성일: " + formattedDate);
  $("#notice-content").html(notice.content);

  // 첨부파일 처리
  if (notice.files && notice.files.length > 0) {
    var filesHtml = "";
    notice.files.forEach(function (file) {
      var fileSize = file.file_size ? formatFileSize(file.file_size) : "";
      filesHtml += `
                <li>
                    <a href="javascript:void(0)" onclick="downloadNoticeFile('${
                      file.original_name
                    }', '${file.saved_name}')" 
                       class="file-link">${file.saved_name}</a>
                    ${
                      fileSize
                        ? `<span class="file-size">(${fileSize})</span>`
                        : ""
                    }
                </li>
            `;
    });
    $("#notice-files").html(filesHtml);
    $("#notice-attachments").show();
  } else {
    $("#notice-attachments").hide();
  }

  // 템플릿 표시
  $("#notice-detail-template").show();
}

// 카테고리명 변환
function getCategoryText(category) {
  var categoryMap = {
    important: "중요",
    service: "서비스 안내",
    update: "업데이트",
    event: "이벤트",
    general: "일반",
  };
  return categoryMap[category] || "일반";
}

// 파일 크기 포맷팅
function formatFileSize(bytes) {
  if (!bytes) return "";

  var sizes = ["Bytes", "KB", "MB", "GB"];
  var i = Math.floor(Math.log(bytes) / Math.log(1024));
  return Math.round((bytes / Math.pow(1024, i)) * 100) / 100 + " " + sizes[i];
}

// 로딩 표시
function showDetailLoading() {
  $("#loading-detail").show();
  $("#error-detail").hide();
  $("#notice-detail-template").hide();
}

// 로딩 숨김
function hideDetailLoading() {
  $("#loading-detail").hide();
}

// 에러 표시
function showDetailError(message) {
  $("#loading-detail").hide();
  $("#notice-detail-template").hide();
  $("#error-message").text("❌ " + message);
  $("#error-detail").show();
}

// 목록으로 돌아가기
function goToNoticeList() {
  window.location.href = "?param=notice";
}

// 파일 다운로드
function downloadNoticeFile(fileName, originalName) {
  // 로딩 메시지 표시
  showMessage("파일을 다운로드하는 중...", "info");

  // AJAX로 파일 데이터 요청
  lb.ajax({
    type: "AjaxFormPost",
    list: {
      ctl: "Userpage",
      param1: "download_notice_file",
      file_name: fileName,
      original_name: originalName,
    },
    action: lb.obj.address,
    havior: function (result) {
      result = JSON.parse(result);

      if (result.result === "1") {
        // Base64 데이터를 Blob으로 변환
        const byteCharacters = atob(result.file_data);
        const byteNumbers = new Array(byteCharacters.length);
        for (let i = 0; i < byteCharacters.length; i++) {
          byteNumbers[i] = byteCharacters.charCodeAt(i);
        }
        const byteArray = new Uint8Array(byteNumbers);
        const blob = new Blob([byteArray], { type: result.mime_type });

        // 다운로드 링크 생성
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement("a");
        link.href = url;
        link.download = result.file_name;
        link.style.display = "none";

        // 다운로드 실행
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        // URL 객체 해제
        window.URL.revokeObjectURL(url);

        showMessage("파일 다운로드가 완료되었습니다.", "success");
      } else {
        showMessage("파일 다운로드에 실패했습니다: " + result.message, "error");
      }
    },
  });
}

// 메시지 표시 (write.js와 동일한 스타일)
function showMessage(message, type = "info") {
  const alertClass =
    type === "error"
      ? "alert-danger"
      : type === "success"
      ? "alert-success"
      : "alert-info";

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
            background: ${
              type === "error"
                ? "#f8d7da"
                : type === "success"
                ? "#d1edff"
                : "#e2e3e5"
            };
            color: ${
              type === "error"
                ? "#721c24"
                : type === "success"
                ? "#0c5460"
                : "#383d41"
            };
            border: 1px solid ${
              type === "error"
                ? "#f5c6cb"
                : type === "success"
                ? "#bee5eb"
                : "#d1ecf1"
            };
        ">
            <strong>${
              type === "error" ? "오류!" : type === "success" ? "성공!" : "알림"
            }</strong> ${message}
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
  $(".alert").remove();

  // body에 직접 추가
  $("body").append(alertHtml);

  // 5초 후 자동 제거 (성공/정보 메시지만)
  if (type !== "error") {
    setTimeout(function () {
      $(".alert").fadeOut(function () {
        $(this).remove();
      });
    }, 5000);
  }
}

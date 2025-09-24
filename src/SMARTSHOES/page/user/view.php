<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no">

	<title>스마트신발</title>

    <!-- META -->
    <?php include_once $this->dir."page/user/inc/meta.php"; ?>

    
    <!-- FONT -->
    <?php echo $this->utillLang->font_link(); ?>
    
    <!-- CSS -->
    <?php include_once $this->project_path."inc/common_css.php"; ?>
    <link rel="stylesheet" type="text/css" href="<?php echo $this->project_path;?>css/view.css<?php echo $this->version;?>"/>

    
	<!-- SCRIPT -->
    <?php include_once $this->project_path."inc/common_js.php"; ?>


</head>
<body>
    <?php include_once $this->project_path."include/header.php"; ?>

    <!-- 서브페이지 상단 타이틀 -->
    <section class="sub-banner sub-contact">
        <div class="container">
            <div class="sub-banner-con">
                <h1>문의 상세보기</h1>
                <p>문의사항의 상세 내용과 답변을 확인할 수 있습니다.</p>
            </div>
        </div>
        <figure><img src="<?php echo $this->project_path;?>img/sub-contact.png"></figure>
    </section>

    <!-- 문의글 상세 섹션 -->
    <section class="view-section">
        <div class="bd-sm">
            <div class="view-content">
                <!-- 로딩 상태 -->
                <div class="loading-state" id="loadingState">
                    <p>문의글을 불러오는 중...</p>
                </div>

                <!-- 비밀번호 입력 폼 -->
                <div class="password-form" id="passwordForm" style="display: none;">
                    <h3>비밀번호 확인</h3>
                    <p>문의글을 보기 위해 비밀번호를 입력해주세요.</p>
                    <div class="password-input-group">
                        <input type="password" id="viewPassword" placeholder="비밀번호를 입력하세요" maxlength="20">
                        <button type="button" class="btn btn-primary" onclick="verifyPassword()">확인</button>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="goToList()">목록으로</button>
                    </div>
                </div>

                <!-- 문의글 내용 -->
                <div class="post-wrap" id="inquiryDetail" style="display: none;">
                    <!-- 문의글 헤더 -->
                    <div class="post-header">
                        <div class="post-title">
                            <h2 id="inquiryTitle"></h2>
                            <div class="post-category">
                                <span class="category-badge" id="inquiryCategory"></span>
                            </div>
                        </div>
                        <div class="post-meta">
                            <div class="post-info">
                                <span><i class="xi-user-o"></i> <span id="inquiryAuthor"></span></span>
                                <span><i class="xi-mail-o"></i> <span id="inquiryEmail"></span></span>
                                <span><i class="xi-time-o"></i> <span id="inquiryRegDate"></span></span>
                            </div>
                        </div>
                    </div>

                    <!-- 문의글 내용 -->
                    <div class="post-content">
                        <div class="content-body" id="inquiryContent">
                            <!-- 동적으로 생성 -->
                        </div>

                        <!-- 첨부파일 -->
                        <div class="attachments" id="inquiryFiles" style="display: none;">
                            <h4><i class="xi-clip"></i> 첨부파일</h4>
                            <ul class="file-list" id="fileList">
                                <!-- 동적으로 생성 -->
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- 관리자 답변 -->
                <div class="admin-answer" id="adminReply" style="display: none;">
                    <div class="answer-header">
                        <h3><i class="xi-comment"></i> 관리자 답변</h3>
                        <span id="replyDate"></span>
                    </div>
                    <div class="answer-content" id="replyContent">
                        <!-- 동적으로 생성 -->
                    </div>
                    <!-- 관리자 첨부파일 -->
                    <div class="attachments" id="adminFiles" style="display: none;">
                        <h4><i class="xi-clip"></i> 첨부파일</h4>
                        <ul class="file-list" id="adminFileList">
                            <!-- 동적으로 생성 -->
                        </ul>
                    </div>
                </div>

                <!-- 액션 버튼 -->
                <div class="post-actions" id="viewActions" style="display: none;">
                    <div class="action-buttons">
                        <a href="?param=contact" class="btn btn-outline">목록으로</a>
                    </div>
                    <div class="edit-buttons">
                        <!-- <button type="button" class="btn btn-primary" onclick="goToEdit()">수정하기</button> -->
                        <button type="button" class="btn btn-outline-danger" onclick="deleteInquiry()">삭제하기</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 삭제 확인 모달 -->
    <div class="modal" id="deleteModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>문의글 삭제</h3>
                <span class="close" onclick="closeDeleteModal()">&times;</span>
            </div>
            <div class="modal-body">
                <p>정말로 이 문의글을 삭제하시겠습니까?</p>
                <p style="color: #dc3545; font-weight: 600;">삭제된 문의글은 복구할 수 없습니다.</p>
                
                <div class="form-group">
                    <label for="deletePassword">비밀번호 확인</label>
                    <input type="password" id="deletePassword" placeholder="비밀번호를 입력하세요" maxlength="20">
                    <small class="form-help">글 작성 시 입력한 비밀번호를 입력하세요</small>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">삭제하기</button>
                    <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">취소</button>
                </div>
            </div>
        </div>
    </div>

    <?php include_once $this->project_path."include/footer.php"; ?>

    <script>
        // 페이지 로드 시 초기화
        $(document).ready(function() {
            // URL에서 문의 ID 가져오기
            const urlParams = new URLSearchParams(window.location.search);
            const inquiryId = urlParams.get('id');
            
            if (inquiryId) {
                loadInquiryDetail(inquiryId);
            } else {
                showMessage('올바르지 않은 접근입니다.', 'error');
                setTimeout(() => goToList(), 2000);
            }
        });

        // 문의글 상세 로드 (세션 기반)
        function loadInquiryDetail(inquiryId) {
            console.log('📞 loadInquiryDetail 호출됨 - inquiryId:', inquiryId);
            lb.ajax({
                type: "JsonAjaxPost",
                list: {
                    ctl: "Userpage",
                    param1: "get_inquiry_for_view",
                    inquiry_id: inquiryId
                },
                action: lb.obj.address,
                response_method: "response_inquiry_detail",
                havior: function(result) {
                    console.log('📥 get_inquiry_for_view 원본 result:', result);
                    try {
                        result = JSON.parse(result);
                        console.log('📋 get_inquiry_for_view 파싱된 result:', result);
                        console.log('📋 result.result:', result.result);
                        console.log('📋 result.need_password:', result.need_password);
                        console.log('📋 result.message:', result.message);
                        response_inquiry_detail(result);
                    } catch(e) {
                        console.error('❌ JSON 파싱 오류:', e);
                        console.error('❌ 원본 데이터:', result);
                    }
                }
            });
        }

        // 상세 정보 응답 처리
        function response_inquiry_detail(result) {
            console.log('📋 response_inquiry_detail 호출됨:', result);
            $('#loadingState').hide();
            
            if (result.result == "1") {
                console.log('✅ 성공 - 문의글 표시');
                displayInquiryDetail(result.inquiry);
                $('#inquiryDetail').show();
                $('#viewActions').show();
            } else if (result.need_password) {
                console.log('🔑 비밀번호 입력 필요');
                // 비밀번호 입력 필요
                $('#passwordForm').show();
                $('#viewPassword').focus();
            } else {
                console.log('❌ 오류:', result.message);
                showMessage('문의글을 불러오는데 실패했습니다: ' + result.message, 'error');
                setTimeout(() => goToList(), 2000);
            }
        }

        // 비밀번호 확인
        function verifyPassword() {
            const password = $('#viewPassword').val().trim();
            const urlParams = new URLSearchParams(window.location.search);
            const inquiryId = urlParams.get('id');
            
            if (!password) {
                showMessage('비밀번호를 입력해주세요.', 'error');
                return;
            }
            
            console.log('🔄 비밀번호 확인 시작');
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
                response_method: "response_verify_password",
                havior: function(result) {
                    hideLoading();
                    console.log('check_inquiry_password result:', result);
                    try {
                        result = JSON.parse(result);
                        console.log('check_inquiry_password parsed:', result);
                        response_verify_password(result);
                    } catch(e) {
                        console.error('❌ JSON 파싱 오류:', e);
                        hideLoading();
                        showMessage('서버 응답 처리 중 오류가 발생했습니다.', 'error');
                    }
                }
            });
        }

        // 비밀번호 확인 응답 처리
        function response_verify_password(result) {
            console.log('🔑 response_verify_password 호출됨:', result);
            if (result.result == "1") {
                console.log('✅ 비밀번호 확인 성공 - 세션 저장됨');
                // 비밀번호 확인 성공 - 다시 문의글 로드
                $('#passwordForm').hide();
                const inquiryId = new URLSearchParams(window.location.search).get('id');
                console.log('🔄 문의글 다시 로드:', inquiryId);
                loadInquiryDetail(inquiryId);
            } else {
                console.log('❌ 비밀번호 불일치');
                showMessage('비밀번호가 일치하지 않습니다.', 'error');
                $('#viewPassword').focus().select();
            }
        }

        // 문의글 상세 표시
        function displayInquiryDetail(inquiry) {
            const categoryText = getCategoryText(inquiry.category);
            const statusText = getStatusText(inquiry.status);
            const statusClass = getStatusClass(inquiry.status);
            
            $('#inquiryTitle').text(inquiry.title);
            $('#inquiryCategory').text(categoryText).addClass(inquiry.category);
            $('#inquiryStatus').text(statusText).addClass(statusClass);
            $('#inquiryAuthor').text(inquiry.author);
            $('#inquiryEmail').text(inquiry.email);
            $('#inquiryRegDate').text(formatDateTime(inquiry.reg_date));
            $('#inquiryUpdateDate').text(inquiry.update_date ? formatDateTime(inquiry.update_date) : '-');
            $('#inquiryContent').html(inquiry.content.replace(/\n/g, '<br>'));
            
            // 첨부파일 표시
            if (inquiry.files && inquiry.files.length > 0) {
                displayFiles(inquiry.files);
                $('#inquiryFiles').show();
            }
            
            // 관리자 답변 표시 (있는 경우)
            if (inquiry.admin_reply) {
                $('#replyContent').html(inquiry.admin_reply.replace(/\n/g, '<br>'));
                $('#replyDate').text(formatDateTime(inquiry.reply_date));
                $('#adminReply').show();
                
                // 관리자 첨부파일 표시
                if (inquiry.admin_files && inquiry.admin_files.length > 0) {
                    displayAdminFiles(inquiry.admin_files);
                    $('#adminFiles').show();
                }
            } else {
                // 답변 대기 상태
                $('#adminReply').addClass('waiting');
                $('#replyContent').html('관리자 답변을 기다리고 있습니다.');
                $('#replyDate').text('');
                $('#adminReply').show();
            }
        }

        // 첨부파일 표시
        function displayFiles(files) {
            let filesHtml = '';
            files.forEach(function(file) {
                filesHtml += `
                    <li>
                        <i class="xi-clip"></i>
                        <a href="javascript:void(0)" onclick="downloadFile(${file.id})" class="file-link">
                            ${file.original_name}
                        </a>
                        <span class="file-size">${formatFileSize(file.file_size)}</span>
                    </li>
                `;
            });
            $('#fileList').html(filesHtml);
        }

        // 관리자 첨부파일 표시
        function displayAdminFiles(files) {
            let filesHtml = '';
            files.forEach(function(file) {
                filesHtml += `
                    <li>
                        <i class="xi-clip"></i>
                        <a href="javascript:void(0)" onclick="downloadFile(${file.id})" class="file-link">
                            ${file.original_name}
                        </a>
                        <span class="file-size">${formatFileSize(file.file_size)}</span>
                    </li>
                `;
            });
            $('#adminFileList').html(filesHtml);
        }

        // 파일 다운로드
        function downloadFile(fileId) {
            window.open(`?ctl=Userpage&param1=download_inquiry_file&file_id=${fileId}`, '_blank');
        }

        // 수정 페이지로 이동
        function goToEdit() {
            const urlParams = new URLSearchParams(window.location.search);
            const inquiryId = urlParams.get('id');
            window.location.href = `?param=edit&id=${inquiryId}`;
        }

        // 목록으로 이동
        function goToList() {
            window.location.href = '?param=contact';
        }

        // 삭제 모달 표시
        function deleteInquiry() {
            $('#deleteModal').css('display', 'flex');
            $('#deletePassword').focus();
        }

        // 삭제 모달 닫기
        function closeDeleteModal() {
            $('#deleteModal').hide();
            $('#deletePassword').val('');
        }

        // 삭제 확인
        function confirmDelete() {
            const password = $('#deletePassword').val().trim();
            const urlParams = new URLSearchParams(window.location.search);
            const inquiryId = urlParams.get('id');
            
            if (!password) {
                showMessage('비밀번호를 입력해주세요.', 'error');
                return;
            }
            
            showLoading('문의글을 삭제하고 있습니다...');
            
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

        // 삭제 응답 처리
        function response_delete_inquiry(result) {
            if (result.result == "1") {
                showMessage('문의글이 성공적으로 삭제되었습니다.', 'success');
                setTimeout(function() {
                    goToList();
                }, 1500);
            } else {
                showMessage('문의글 삭제에 실패했습니다: ' + result.message, 'error');
                closeDeleteModal();
            }
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

        function formatDateTime(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleString('ko-KR');
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
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
            $('.view-content').prepend(alertHtml);
            
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
                <div id="loading-overlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center; z-index: 9999;">
                    <div class="loading-content" style="background: white; padding: 20px; border-radius: 8px; text-align: center;">
                        <div class="spinner" style="border: 4px solid #f3f3f3; border-top: 4px solid #3498db; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; margin: 0 auto 10px;"></div>
                        <p>${message}</p>
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

        // 모달 외부 클릭 시 닫기
        $(window).on('click', function(e) {
            if (e.target.id === 'deleteModal') {
                closeDeleteModal();
            }
        });

        // ESC 키로 모달 닫기
        $(document).on('keydown', function(e) {
            if (e.keyCode === 27) {
                closeDeleteModal();
            }
        });

        // 비밀번호 입력 시 엔터키
        $(document).on('keypress', '#deletePassword', function(e) {
            if (e.which === 13) {
                confirmDelete();
            }
        });

        // view 비밀번호 입력 시 엔터키
        $(document).on('keypress', '#viewPassword', function(e) {
            if (e.which === 13) {
                verifyPassword();
            }
        });
    </script>
</body>
</html>
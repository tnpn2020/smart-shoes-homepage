<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title>문의글 관리</title>
	<!-- font -->
	<link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet">
	<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-kr.css' rel='stylesheet' type='text/css'>

	<!-- css -->
	<?php include_once $this->dir."page/adm/inc/common_css.php"; ?>
	
	<style>
	/* 페이징 스타일 */
	.pagination-wrap {
		display: flex;
		justify-content: center;
		align-items: center;
		gap: 8px;
		margin-top: 20px;
	}
	
	.page-btn, .page-num {
		display: inline-block;
		padding: 8px 12px;
		border: 1px solid #ddd;
		border-radius: 4px;
		color: #337ab7;
		text-decoration: none;
		transition: all 0.3s ease;
		cursor: pointer;
		min-width: 40px;
		text-align: center;
	}
	
	.page-btn:hover, .page-num:hover {
		background-color: #eee;
		border-color: #ddd;
		text-decoration: none;
	}
	
	.page-num.current {
		background-color: #337ab7;
		border-color: #337ab7;
		color: white;
	}
	
	.page-btn {
		font-weight: bold;
	}
	</style>

	<!-- script -->
	<?php include_once $this->dir."page/adm/inc/common_js.php"; ?>
	<script type="text/javascript" src="<?php echo $this->project_admin_path;?>js/adm_menu1_inquiry.js<?php echo $this->version;?>"></script>
	
</head>
<body>
	<div class="wrap">
        <?php include_once $this->dir."page/adm/include/adm_menu1_aside.php";?>
        <?php include_once $this->dir."page/adm/include/adm_header.php";?>

		<div class="bd">
			<article class="body-container">
				<div class="body-head mb-1">
					<h2 style="display: inline-block;">문의글 관리</h2>
				</div>
				<form class="form">
					<div class="body-box mb-3">
						<div class="box-search-container">
							<div class="insert-wrap">
								<div class="insert insert-select">
									<select class="select-custom" type="text" id="search_type">
										<option value="all">전체</option>
										<option value="title">제목</option>
										<option value="author">작성자</option>
										<option value="email">이메일</option>
										<option value="content">내용</option>
									</select>
								</div>
								<div class="insert insert-input">
									<input class="input-lg" type="text" id="search_text" placeholder="검색어를 입력하세요"/>
									<input type="text" style="display:none;"/>
								</div>
								<div class="insert insert-select">
									<select class="select-custom" type="text" id="category_filter">
										<option value="">전체 분류</option>
										<option value="product">제품문의</option>
										<option value="service">서비스문의</option>
										<option value="technical">기술문의</option>
										<option value="general">일반문의</option>
									</select>
								</div>
								<div class="insert insert-select">
									<select class="select-custom" type="text" id="status_filter">
										<option value="">전체 상태</option>
										<option value="pending">대기</option>
										<option value="processing">처리중</option>
										<option value="completed">완료</option>
									</select>
								</div>
							</div>
						</div>
						<div class="insert-wrap align-center mt-3">
							<div class="insert insert-input-btn">
								<input class="btn-primary" type="button" value="검색" onclick="searchInquiries()"/>
							</div>
							<div class="insert insert-input-btn">
								<input class="btn-default" type="button" value="초기화" onclick="resetSearch()"/>
							</div>
						</div>
					</div>
					
					<div class="body-out mb-3">
						<div class="out-tab-container">
							<ul>
								<li id="total_li" onclick="move_tab(0)">
									<a href="#" id="total_count">전체 (0)</a>
								</li>
								<li id="pending_li" onclick="move_tab(1)">
									<a href="#" id="pending_count">대기 (0)</a>
								</li>
								<li id="processing_li" onclick="move_tab(2)">
									<a href="#" id="processing_count">처리중 (0)</a>
								</li>
								<li id="completed_li" onclick="move_tab(3)">
									<a href="#" id="completed_count">완료 (0)</a>
								</li>
							</ul>
						</div>
						


					</div>

					<!-- 테이블 영역을 별도 body-box로 분리 -->
					<div class="body-box">
						<!-- 로딩 상태 -->
						<div id="loading_state" style="display: none; text-align: center; padding: 50px;">
							<p>문의글을 불러오는 중...</p>
						</div>

						<!-- 빈 상태 -->
						<div id="empty_state" style="display: none; text-align: center; padding: 50px;">
							<p>등록된 문의글이 없습니다.</p>
						</div>

						<div class="table-container">
							<table class="table1">
								<thead>
									<tr>
										<th style="width: 60px;">번호</th>
										<th style="width: 100px;">분류</th>
										<th>제목</th>
										<th style="width: 120px;">작성자</th>
										<th style="width: 150px;">이메일</th>
										<th style="width: 120px;">등록일</th>
										<th style="width: 100px;">상태</th>
										<th style="width: 80px;">첨부</th>
										<th style="width: 120px;">관리</th>
									</tr>
								</thead>
								<tbody id="inquiry_list" data-wrap="wrap">
									<!-- 동적으로 생성 -->
								</tbody>
							</table>
						</div>
						
						<div class="pagination_container mt-3" id="paging">
							<!-- 동적으로 생성 -->
						</div>
					</div>
				</form>
			</article>
		</div>
		
		<!-- <?php include_once $this->dir."page/adm/include/adm_footer.php";?> -->
	</div>

	<!-- 문의글 상세보기 모달 -->
	<div class="modal fade" id="inquiry_detail_modal" tabindex="-1" role="dialog">
		<div class="modal-dialog" style="width: 80%; max-width: 900px;">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">문의글 상세보기</h4>
					<button type="button" class="close" onclick="closeModal()">&times;</button>
				</div>
				<div class="modal-body" id="inquiry_detail_content">
					<!-- 동적으로 생성 -->
				</div>
				<div class="modal-footer">
					<input class="btn-primary btn-large" type="button" value="답변하기" onclick="showReplyModal()"/>
					<input class="btn-danger btn-large" type="button" value="삭제" onclick="showDeleteModal()"/>
					<input class="btn-default btn-large" type="button" value="닫기" onclick="closeModal()"/>
				</div>
			</div>
		</div>
	</div>

	<!-- 답변 작성 모달 -->
	<div class="modal fade" id="reply_modal" tabindex="-1" role="dialog">
		<div class="modal-dialog" style="width: 80%; max-width: 800px;">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">답변 작성</h4>
					<button type="button" class="close" onclick="closeReplyModal()">&times;</button>
				</div>
				<form id="reply_form" enctype="multipart/form-data">
					<div class="modal-body">
						<div style="margin-bottom: 15px;">
							<label style="display: block; margin-bottom: 5px; font-weight: bold;">문의 제목</label>
							<input type="text" id="reply_title" readonly style="width: 100%; padding: 8px; border: 1px solid #ddd; background: #f9f9f9;">
						</div>
						<div style="margin-bottom: 15px;">
							<label style="display: block; margin-bottom: 5px; font-weight: bold;">답변 내용</label>
							<textarea id="reply_content" name="reply_content" rows="10" placeholder="답변 내용을 입력하세요..." style="width: 100%; padding: 8px; border: 1px solid #ddd; resize: vertical;"></textarea>
						</div>
						<div style="margin-bottom: 15px;">
							<label style="display: block; margin-bottom: 5px; font-weight: bold;">첨부파일</label>
							<input type="file" id="reply_files" name="reply_files[]" multiple style="width: 100%; padding: 8px; border: 1px solid #ddd;" onchange="displaySelectedFiles()">
							<small style="color: #666; display: block; margin-top: 5px;">여러 파일을 선택할 수 있습니다. (최대 10MB)</small>
							<div id="reply_file_list" style="margin-top: 10px;"></div>
						</div>
						<div style="margin-bottom: 15px;">
							<label style="display: block; margin-bottom: 5px; font-weight: bold;">상태 변경</label>
							<select id="reply_status" name="status" style="width: 200px; padding: 8px; border: 1px solid #ddd;">
								<option value="processing">처리중</option>
								<option value="completed">완료</option>
							</select>
						</div>
						<input type="hidden" name="inquiry_id" id="reply_inquiry_id">
					</div>
				</form>
				<div class="modal-footer">
					<input class="btn-primary" type="button" value="답변 저장" onclick="saveReply()"/>
					<input class="btn-default" type="button" value="취소" onclick="closeReplyModal()"/>
				</div>
			</div>
		</div>
	</div>

	<!-- 상태 변경 모달 -->
	<div class="modal fade" id="status_modal" tabindex="-1" role="dialog">
		<div class="modal-dialog" style="width: 400px;">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">상태 변경</h4>
					<button type="button" class="close" onclick="closeStatusModal()">&times;</button>
				</div>
				<div class="modal-body">
					<div style="margin-bottom: 15px;">
						<label style="display: block; margin-bottom: 5px; font-weight: bold;">새로운 상태</label>
						<select id="new_status" style="width: 100%; padding: 8px; border: 1px solid #ddd;">
							<option value="pending">대기</option>
							<option value="processing">처리중</option>
							<option value="completed">완료</option>
						</select>
					</div>
				</div>
				<div class="modal-footer">
					<input class="btn-primary" type="button" value="변경" onclick="updateStatus()"/>
					<input class="btn-default" type="button" value="취소" onclick="closeStatusModal()"/>
				</div>
			</div>
		</div>
	</div>

	<!-- 삭제 확인 모달 -->
	<div class="modal fade" id="delete_modal" tabindex="-1" role="dialog">
		<div class="modal-dialog" style="width: 400px;">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">문의글 삭제</h4>
					<button type="button" class="close" onclick="closeDeleteModal()">&times;</button>
				</div>
				<div class="modal-body">
					<p>정말로 이 문의글을 삭제하시겠습니까?</p>
					<p style="color: #e74c3c; font-weight: bold;">삭제된 문의글은 복구할 수 없습니다.</p>
				</div>
				<div class="modal-footer">
					<input class="btn-danger" type="button" value="삭제" onclick="confirmDelete()"/>
					<input class="btn-default" type="button" value="취소" onclick="closeDeleteModal()"/>
				</div>
			</div>
		</div>
	</div>

</body>
</html>
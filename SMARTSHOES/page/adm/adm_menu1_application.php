<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title>신청서 관리</title>
	<!-- font -->
	<link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet">
	<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-kr.css' rel='stylesheet' type='text/css'>

	<!-- css -->
	<?php include_once $this->dir."page/adm/inc/common_css.php"; ?>

	<!-- script -->
	<?php include_once $this->dir."page/adm/inc/common_js.php"; ?>
	<script type="text/javascript" src="<?php echo $this->project_admin_path;?>js/menu1_application.js<?php echo $this->version;?>"></script>
	
</head>
<body>
	<!-- 상세보기 모달 -->
	<div class="application-modal" id="detail_modal">
		<div class="modal-overlay" onclick="closeDetailModal()"></div>
		<div class="modal-content">
			<div class="modal-header">
				<h3>신청서 상세보기</h3>
				<button class="modal-close" onclick="closeDetailModal()">&times;</button>
			</div>
			<div class="modal-body" id="detail_content">
				<!-- 상세 내용이 여기에 로드됩니다 -->
			</div>
			<div class="modal-footer">
				<button type="button" class="btn-secondary" onclick="closeDetailModal()">닫기</button>
				<button type="button" class="btn-primary" onclick="openStatusModal()">상태 변경</button>
			</div>
		</div>
	</div>

	<!-- 상태변경 모달 -->
	<div class="application-modal" id="status_modal">
		<div class="modal-overlay" onclick="closeStatusModal()"></div>
		<div class="modal-content status-modal">
			<div class="modal-header">
				<h3>상태 변경</h3>
				<button class="modal-close" onclick="closeStatusModal()">&times;</button>
			</div>
			<div class="modal-body">
				<input type="hidden" id="status_application_idx">
				<div class="form-group">
					<label for="status_select">상태 선택</label>
					<select id="status_select" class="form-control">
						<option value="pending">대기</option>
						<option value="processed">처리완료</option>
						<option value="rejected">거절</option>
					</select>
				</div>
				<div class="form-group">
					<label for="admin_memo">관리자 메모</label>
					<textarea id="admin_memo" class="form-control" rows="4" placeholder="처리 내용을 입력하세요"></textarea>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn-secondary" onclick="closeStatusModal()">취소</button>
				<button type="button" class="btn-primary" onclick="updateApplicationStatus()">상태 변경</button>
			</div>
		</div>
	</div>

	<div class="wrap">
        <?php include_once $this->dir."page/adm/include/adm_menu1_aside.php";?>
        <?php include_once $this->dir."page/adm/include/adm_header.php";?>

		<div class="bd">
			<article class="body-container">
				<div class="body-head mb-1">
					<h2 style="display: inline-block;">신청서 관리</h2>
				</div>
				<form class="form">
					<div class="body-box mb-3">
						<div class="box-search-container">
							<div class="insert-wrap">
								<div class="insert insert-select">
									<select class="select-custom" type="text" id="search_type">
										<option value="all">전체</option>
										<option value="name">이름</option>
										<option value="phone">연락처</option>
										<option value="email">이메일</option>
									</select>
								</div>
								<div class="insert insert-input">
									<input class="input-lg" type="text" id="search_keyword" placeholder="검색어를 입력하세요"/>
									<input type="text" style="display:none;"/>
								</div>
								<div class="insert insert-select">
									<select class="select-custom" type="text" id="status_filter">
										<option value="">전체 상태</option>
										<option value="pending">대기</option>
										<option value="processed">처리완료</option>
										<option value="rejected">거절</option>
									</select>
								</div>
								<div class="insert insert-select">
									<select class="select-custom" type="text" id="applicant_type_filter">
										<option value="">전체 유형</option>
										<option value="user">사용자 본인</option>
										<option value="guardian">보호자</option>
									</select>
								</div>
							</div>
						</div>
						<div class="insert-wrap align-center mt-3">
							<div class="insert insert-input-btn">
								<input class="btn-primary" type="button" value="검색" onclick="searchApplications()"/>
							</div>
							<div class="insert insert-input-btn">
								<input class="btn-default" type="button" value="초기화" onclick="resetFilters()"/>
							</div>
							<div class="insert insert-input-btn">
								<input class="btn-success" type="button" value="엑셀 다운로드" onclick="downloadExcel()"/>
							</div>
						</div>
					</div>
					
					<div class="body-out mb-3">
						<div class="out-tab-container">
							<ul>
								<li id="total_li" onclick="filterByStatus('')">
									<a href="#" id="total_count">전체 (0)</a>
								</li>
								<li id="pending_li" onclick="filterByStatus('pending')">
									<a href="#" id="pending_count">대기 (0)</a>
								</li>
								<li id="processed_li" onclick="filterByStatus('processed')">
									<a href="#" id="processed_count">처리완료 (0)</a>
								</li>
								<li id="rejected_li" onclick="filterByStatus('rejected')">
									<a href="#" id="rejected_count">거절 (0)</a>
								</li>
							</ul>
						</div>
					</div>

					<!-- 테이블 영역 -->
					<div class="body-box">
						<!-- 로딩 상태 -->
						<div id="loading" style="display: none; text-align: center; padding: 50px;">
							<p>신청서를 불러오는 중...</p>
						</div>

						<!-- 빈 상태 -->
						<div id="empty_state" style="display: none; text-align: center; padding: 50px;">
							<p>등록된 신청서가 없습니다.</p>
						</div>

						<div class="table-container">
							<table class="table1">
								<thead>
									<tr>
										<th style="width: 60px;">번호</th>
										<th style="width: 100px;">유형</th>
										<th style="width: 100px;">이름</th>
										<th style="width: 120px;">연락처</th>
										<th style="width: 150px;">이메일</th>
										<th style="width: 120px;">신청사유</th>
										<th style="width: 120px;">등록일</th>
										<th style="width: 100px;">상태</th>
										<th style="width: 120px;">관리</th>
									</tr>
								</thead>
								<tbody id="wrap" data-wrap="wrap">
									<!-- 동적으로 생성 -->
								</tbody>
							</table>
						</div>
						
						<div class="pagination_container mt-3" id="pagination">
							<!-- 동적으로 생성 -->
						</div>
					</div>
				</form>
			</article>
		</div>
	</div>
	
	<div style="display:none;">
		<table>
			<tr data-copy="copy">
				<td class="col-num" data-attr="number">1</td>
				<td data-attr="applicant_type">사용자 본인</td>
				<td data-attr="name">홍길동</td>
				<td data-attr="phone">010-0000-0000</td>
				<td data-attr="email">test@test.com</td>
				<td data-attr="reason">노인 케어</td>
				<td data-attr="regdate">2024-01-01 10:00</td>
				<td>
					<span class="status-badge" data-attr="status_badge">대기</span>
				</td>
				<td>
					<button type="button" class="btn btn-sm btn-primary" data-attr="btn_detail">상세</button>
				</td>
			</tr>
		</table>
	</div>

	<link rel="stylesheet" href="<?php echo $this->project_admin_path;?>css/application_modal.css<?php echo $this->version;?>">

</body>
</html>
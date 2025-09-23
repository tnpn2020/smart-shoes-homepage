<!DOCTYPE html>
<html lang="ko">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>홍보/행사 관리</title>
	<!-- font -->
	<link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet">
	<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-kr.css' rel='stylesheet' type='text/css'>

		<!-- css -->
	<?php include_once $this->dir."page/adm/inc/common_css.php"; ?>
	
	<style>
	/* 버튼 크기 및 아웃라인 스타일 */
	.btn-xs {
		padding: 2px 8px;
		font-size: 11px;
		line-height: 1.3;
		border-radius: 3px;
		border-width: 1px;
		border-style: solid;
		cursor: pointer;
		text-decoration: none;
		display: inline-block;
		text-align: center;
		transition: all 0.2s ease;
		margin: 0 2px;
	}
	
	/* 파란색 테두리 버튼 (수정) */
	.btn-outline-primary {
		color: #007bff;
		background-color: transparent;
		border-color: #007bff;
	}
	
	.btn-outline-primary:hover {
		color: #fff;
		background-color: #007bff;
		border-color: #007bff;
	}
	
	/* 빨간색 테두리 버튼 (삭제) */
	.btn-outline-danger {
		color: #dc3545;
		background-color: transparent;
		border-color: #dc3545;
	}
	
	.btn-outline-danger:hover {
		color: #fff;
		background-color: #dc3545;
		border-color: #dc3545;
	}
	
	/* 버튼 컨테이너 */
	.btn-container {
		display: flex;
		gap: 4px;
		justify-content: center;
		align-items: center;
	}
	</style>
 
	<!-- script -->
	<?php include_once $this->dir."page/adm/inc/common_js.php"; ?>
	<script type="text/javascript"src="<?php echo $this->project_admin_path;?>js/menu1_promotion.js<?php echo $version;?>"></script>
</head>

<body>
	<div class="wrap">
		<?php include_once $dir."page/adm/include/adm_menu1_aside.php";?>
		<?php include_once $dir."page/adm/include/adm_header.php";?>
		<div class="bd">
			<article class="body-container">
				<div class="body-head">
					<h2>홍보/행사 관리</h2>
				</div>
				<form class="form" id="form">
																			<div class="row p-relative mt-2">
								<div class="col-md-11">
									<div class="body-box">
										<!-- 검색 및 등록 버튼 영역 -->
										<div class="search-container mb-3">
											<div class="row align-items-center">
												<div class="col-md-6">
													<div class="insert insert-input">
														<input type="text" class="input-sm" id="search_keyword" placeholder="행사명, 장소로 검색">
													</div>
												</div>
												<div class="col-md-6 text-right">
													<button type="button" class="btn btn-primary" onclick="searchPromotions()">검색</button>
												</div>
											</div>
										</div>

										<!-- 테이블 컨테이너 -->
										<div class="table-container">
											<table class="table1">
												<thead>
													<tr>
														<th>선택</th>
														<th width="80">번호</th>
														<th width="200">행사명</th>
														<th width="150">기간</th>
														<th width="150">장소</th>
														<th width="100">수상</th>
														<th width="80">상태</th>
														<th width="120">등록일</th>
														<th width="150">관리</th>
													</tr>
												</thead>
												<tbody id="promotion_list">
													<!-- 목록이 여기에 동적으로 추가됩니다 -->
												</tbody>
											</table>
										</div>

										<!-- 로딩 스피너 -->
										<div id="loading_spinner" class="text-center py-4" style="display: none;">
											<div class="loading">로딩 중...</div>
										</div>

										<!-- 빈 목록 메시지 -->
										<div id="empty_message" class="text-center py-4" style="display: none;">
											<p>등록된 홍보/행사가 없습니다.</p>
										</div>

										<!-- 페이징 -->
										<div class="pagination-container mt-3">
											<div class="text-center">
												<div id="pagination">
													<!-- 페이징이 여기에 동적으로 추가됩니다 -->
												</div>
											</div>
										</div>
									</div>

									<div class="btn-container align-right mt-3">
										<button type="button" class="btn btn-primary" onclick="goToRegister()">행사등록</button>
									</div>
								</div>

								<div class="col-md-1">
									<div class="sort-control-container">
										<div class="body-box">
											<div class="mt-2">
												<div class="insert mb-1"><input type="text" class="input-32" id="move_count" value="1" onkeyup="lb.validate_number(this)"/></div>
												<div class="insert"><span class="controller-btn"><img src="<?php echo $this->project_admin_image_path;?>control-top-max.png" onclick="btn_top()"/>최상</span></div>
												<div class="insert"><span class="controller-btn"><img src="<?php echo $this->project_admin_image_path;?>control-top.png" onclick="btn_up()"/>위</span></div>
												<div class="insert"><span class="controller-btn"><img src="<?php echo $this->project_admin_image_path;?>control-bt.png" onclick="btn_down()"/>아래</span></div>
												<div class="insert"><span class="controller-btn"><img src="<?php echo $this->project_admin_image_path;?>control-bt-max.png" onclick="btn_end()"/>최하</span></div>
											</div>
										</div>
										<div class="sort-control-insert-wrap">
											<div class="insert insert-input-btn mt-1"><input class="btn-primary" type="button" value="적용하기" onclick="btn_save()"></div>
											<div class="insert insert-input-btn mt-1"><input class="btn-default" type="button" value="초기화" onclick="btn_init()"></div>
										</div>
									</div>
								</div>
							</div>
					</div>
				</form>
			</article>
		</div>
	</div>

</body>

</html>

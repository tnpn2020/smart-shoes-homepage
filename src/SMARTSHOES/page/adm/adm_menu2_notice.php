<!DOCTYPE html>
<html lang="ko">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>공지사항 관리</title>
	<!-- font -->
	<link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet">
	<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-kr.css' rel='stylesheet' type='text/css'>

	<!-- css -->
	<?php include_once $this->dir."page/adm/inc/common_css.php"; ?>

	<!-- script -->
	<?php include_once $this->dir."page/adm/inc/common_js.php"; ?>
	<script type="text/javascript"
		src="<?php echo $this->project_admin_path;?>/js/menu_2_notice.js<?php echo $version;?>"></script>
</head>

<body>
	<div class="modal user-modal" id="modify_modal" style="display:none;">
		<div class="modal-wrap modal-sm">
			<div class="modal-container">
				<!-- 모달 본문 -->
				<div class="modal-container-inner">
					<div class="modal-head bold">연혁 관리</div>
					<div class="modal-body">
						<form class="form" id="modify_form">
							<input type="hidden" class="input-lg" id="modify_banner_idx">
							<input type="hidden" class="input-lg" id="modify_banner_kind">
							<div class="box-table-container">
								<div class="mt-2">
									<div class="insert insert-input">
										<p class="mb-1 bold">배너명</p>
										<input type="text" class="input-lg" id="modify_banner_name">
									</div>
								</div>
								<div class="mt-2">
									<div class="insert insert-input">
										<p class="mb-1 bold">링크</p>
										<input type="text" class="input-lg" id="modify_banner_link">
									</div>
								</div>
								<div class="mt-2">
									<div class="insert insert-input">
										<p class="mb-1 bold">배너 타이틀</p>
										<input type="text" class="input-lg" id="modify_banner_title"
											placeholder="쇼핑몰에서 보여지는 타이틀 입력">
									</div>
								</div>
								<div class="mt-2">
									<div class="insert insert-input">
										<p class="mb-1 bold">배너 내용</p>
										<input type="text" class="input-lg" id="modify_banner_content"
											placeholder="쇼핑몰에서 보여지는 내용 입력">
									</div>
								</div>
							</div>
					</div>
					</form>
					<ul class="btn-container align-right">
						<li><button type="button" class="btn btn-ghost" onclick="modify_modal_hide()">취소</button></li>
						<li><button type="button" class="btn btn-primary" onclick="request_modify_banner()">수정</button>
						</li>
					</ul>
				</div>
				<!-- 모달 본문 // -->
			</div>
		</div>
	</div>
	<div class="wrap">
		<?php include_once $dir."page/adm/include/adm_menu1_aside.php";?>
		<?php include_once $dir."page/adm/include/adm_header.php";?>
		<div class="bd">
			<article class="body-container">
				<div class="body-head">
					<h2>공지사항 관리</h2>
				</div>
				<form class="form" id="form">
				<div class="body-box mb-3 mt-3">
						<div class="insert-wrap">
							<div class="insert insert-select">
								<select class="form-select" id="category_filter">
									<option value="" selected>전체 카테고리</option>
									<option value="important">중요</option>
									<option value="service">서비스 안내</option>
									<option value="update">업데이트</option>
									<option value="event">이벤트</option>
								</select>
							</div>
							<div class="insert insert-select">
								<select class="form-select" id="search_kind">
									<option value="all" selected>전체</option>
									<option value="title">제목</option>
									<option value="content">내용</option>
									<option value="file">첨부파일</option>
								</select>
							</div>
							<div class="insert insert-input" style="width: 30%; max-width: 640px"><input
									onkeyup="enterkey()" id="keyword" style="width: 100%" type="text" /></div>
							<div class="insert insert-chk">
								<!-- <label class="check_label" style="margin-right: 15px">상세검색
									<input type="checkbox" id="detail_search_check">
									<span class="checkmark"></span>
								</label> -->
								<div class="insert insert-input-btn"><input class="btn-primary" id="select_btn"
										type="button" value="검색" onclick="search()" /></div>
								<div class="insert insert-input-btn"><input class="btn-default" id="init_btn"
										onclick="search_init()" type="button" value="초기화" /></div>
							</div>
						</div>
						<div class="box-table-container mt-2" id="detail_search_content">
							<dl class="box-tbody">
								<dt class="box-th box-head">
									<p>기간</p>
								</dt>
								<dd class="box-td">
									<ul class="insert-wrap">
										<li class="insert insert-chk">
											<label class="check_label" for="all_search">전체
												<input type="radio" id="all_search" value="all_search" name="duration">
												<span class="checkmark radio"></span>
											</label>
										</li>
										<li class="insert insert-chk">
											<label class="check_label" for="date_01">오늘
												<input type="radio" id="date_01" value="date_01" name="duration">
												<span class="checkmark radio"></span>
											</label>
										</li>
										<li class="insert insert-chk">
											<label class="check_label" for="date_02">1개월
												<input type="radio" id="date_02" value="date_02" name="duration">
												<span class="checkmark radio"></span>
											</label>
										</li>
										<li class="insert insert-chk">
											<label class="check_label" for="date_03">3개월
												<input type="radio" id="date_03" value="date_03" name="duration">
												<span class="checkmark radio"></span>
											</label>
										</li>
										<li class="insert insert-chk">
											<label class="check_label" for="date_04">6개월
												<input type="radio" id="date_04" value="date_04" name="duration">
												<span class="checkmark radio"></span>
											</label>
										</li>
										<li class="insert insert-input datepick-wirte" style="z-index: 5;">
											<input id="start_date" type="date" id="start_date"
												style="padding: 8px; border: 1px solid #ddd; background-color: #fff">
										</li>
										<li class="insert">~</li>
										<li class="insert insert-input datepick-wirte" style="z-index: 5;">
											<input id="end_date" type="date" id="end_date"
												style="padding: 8px; border: 1px solid #ddd; background-color: #fff">
										</li>
									</ul>
								</dd>
							</dl>
						</div>
					</div>
					<div class="out-tab-container st2 mt-2" style="display: none">
						<ul id="lang_list" data-wrap="lang_wrap">
							<!-- <li class="current"><a href="">한국어</a></li>
									<li><a href="">영어</a></li>
									<li><a href="">중국어</a></li> -->
						</ul>
					</div>
					<div class="row p-relative mt-2">
						<div class="col-md-12">
							<div class="body-box">
								<div class="table-container">
									<table class="table1">
										<thead>
											<tr>
												<th>
													<div class="insert insert-chk">
														<label class="check_label">
															<input type="checkbox" id="all_check"
																onclick="all_check_list(this)" />
															<span class="checkmark"></span>
														</label>
													</div>
												</th>
												<th>번호</th>
												<th>카테고리</th>
												<th class="col-tit">제목</th>
												<th>등록일</th>
												<!-- <th>사용여부</th> -->
												<th>수정</th>
											</tr>
										</thead>
										<tbody data-wrap="wrap" id="wrap">
										</tbody>
									</table>
								</div>
								<div class="insert-wrap mt-1">
									<div class="insert insert-input-btn"><input class="btn-default btn-32" type="button"
											value="선택삭제" onclick="delete_post()"></div>
								</div>
							</div>
							<div class="pagination_container mt-3" id = "paging">
								<div class="page_item arrow prev">«</div>
								<div class="page_item active">1</div>
								<!-- <div class="page_item ">2</div> -->
								<div class="page_item arrow next">»</div>
                     		</div>
							<div class="btn-container align-right mt-3">
								<button type="button" class="btn btn-primary" onclick="move_upload()">신규등록</button>
							</div>
						</div>
						<!-- <div class="col-md-1">
							<div class="sort-control-container">
								<div class="body-box">
									<div class="mt-2">
										<div class="insert mb-1"><input type="text" class="input-32" id="move_count"
												value="1" onkeyup="lb.validate_number(this)" /></div>
										<div class="insert"><span class="controller-btn"><img
													src="<?php echo $this->project_admin_image_path;?>control-top-max.png"
													onclick="btn_top()" />최상</span></div>
										<div class="insert"><span class="controller-btn"><img
													src="<?php echo $this->project_admin_image_path;?>control-top.png"
													onclick="btn_up()" />위</span></div>
										<div class="insert"><span class="controller-btn"><img
													src="<?php echo $this->project_admin_image_path;?>control-bt.png"
													onclick="btn_down()" />아래</span></div>
										<div class="insert"><span class="controller-btn"><img
													src="<?php echo $this->project_admin_image_path;?>control-bt-max.png"
													onclick="btn_end()" />최하</span></div>
									</div>
								</div>
								<div class="sort-control-insert-wrap">
									<div class="insert insert-input-btn mt-1"><input class="btn-primary" type="button"
											value="적용하기" onclick="btn_save()"></div>
									<div class="insert insert-input-btn mt-1"><input class="btn-default" type="button"
											value="초기화" onclick="btn_init()"></div>
								</div>
							</div>
						</div> -->
					</div>
				</form>


			</article>
		</div>
	</div>
	<div style="display:none;">
		<table>
			<tr data-copy="copy">
				<td>
					<div class="insert insert-chk">
						<label class="check_label">
							<input type="checkbox" data-attr="checkbox" />
							<span class="checkmark"></span>
						</label>
					</div>
				</td>
				<td class="col-num" data-attr="number">1</td>
				<td class="col-short-num">
					<span class="badge" data-attr="category">중요</span>
				</td>
				<td class="col-tit">
					<div class="table-tit">
						<p class="tit"><span onclick="" data-attr="title">연혁의 내용이 나옵니다.</span></p>
					</div>
				</td>
				<td class="col-long-num">
					<div class="table-date" data-attr="regdate">20.03.18</div>
				</td>
				<!-- <td class="col-short-num">
					<div>
						<select class="" data-attr="is_use">
							<option value="1">사용</option>
							<option value="0">미사용</option>
						</select>
					</div>
				</td> -->
				<td class="col-long-num"><input class="btn-default btn-32" type="button" value="수정하기"
						data-attr="btn_modify"></td>
			</tr>
		</table>

		<li class="current" onclick="" data-copy="lang"><a data-attr="name">KOR</a></li>
	</div>
</body>
<!-- Select2 라이브러리 제거 - 일반 select 사용 -->

<style>
/* 카테고리 배지 스타일 */
.badge {
    display: inline-block;
    padding: 4px 8px;
    font-size: 12px;
    font-weight: bold;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: 4px;
    color: #fff;
}

.category-important {
    background-color: #dc3545; /* 빨강 - 중요 */
}

.category-service {
    background-color: #007bff; /* 파랑 - 서비스 안내 */
}

.category-update {
    background-color: #28a745; /* 초록 - 업데이트 */
}

.category-event {
    background-color: #ffc107; /* 노랑 - 이벤트 */
    color: #212529;
}

.category-general {
    background-color: #6c757d; /* 회색 - 일반 */
}
</style>

<style>
/* 검색 영역 셀렉트박스 스타일 수정 */
.insert-wrap {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.insert.insert-select {
    min-width: 150px;
}

/* 일반 Select 박스 스타일 */
.form-select {
    width: 100%;
    min-width: 150px;
    height: 38px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background-color: #fff;
    padding: 0 12px;
    font-size: 14px;
    color: #333;
    cursor: pointer;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23666' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 8px center;
    background-size: 16px 12px;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
}

.form-select:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
}

.form-select:hover {
    border-color: #999;
}

/* 검색 입력창 스타일 */
.insert.insert-input input {
    height: 38px;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 0 12px;
    font-size: 14px;
}

/* 버튼 스타일 정렬 */
.insert.insert-input-btn input {
    height: 38px;
    padding: 0 16px;
    border-radius: 4px;
    font-size: 14px;
    border: none;
    cursor: pointer;
}

.btn-primary {
    background-color: #007bff !important;
    color: white !important;
}

.btn-primary:hover {
    background-color: #0056b3 !important;
}

.btn-default {
    background-color: #6c757d !important;
    color: white !important;
}

.btn-default:hover {
    background-color: #545b62 !important;
}

/* 반응형 디자인 */
@media (max-width: 768px) {
    .insert-wrap {
        flex-direction: column;
        align-items: stretch;
    }
    
    .insert.insert-select,
    .insert.insert-input {
        width: 100% !important;
        max-width: none !important;
    }
}
</style>

<script type="text/javascript">
	$(document).ready(function () {
		// 일반 select 박스 사용 - 추가 초기화 불필요
		console.log('Form select boxes initialized');
	});
</script>

</html>
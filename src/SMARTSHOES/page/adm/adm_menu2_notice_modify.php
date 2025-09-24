<!DOCTYPE html>
<html lang="ko">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title>공지사항 등록</title>
	<!-- font -->
	<link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet">
	<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-kr.css' rel='stylesheet' type='text/css'>

	<!-- css -->
	<?php include_once $this->dir."page/adm/inc/common_css.php"; ?>
	<!-- script -->
	<?php include_once $this->dir."page/adm/inc/common_js.php"; ?>
	<?php include_once $this->dir."page/adm/inc/summernote.php"; ?>
	<!-- include summernote css/js-->
	<script type="text/javascript"
		src="<?php echo $this->project_admin_path;?>js/menu_2_notice_modify.js<?php echo $version;?>"></script>
</head>

<body>
	<div class="wrap">
		<?php include_once $dir."page/adm/include/adm_menu1_aside.php";?>
		<?php include_once $dir."page/adm/include/adm_header.php";?>
		<div class="bd">
			<article class="body-container">
				<div class="body-head">
					<h2 id="page_title"></h2>
				</div>
				<form class="form" id="form" id="form" onsubmit='return false;'>
					<div class="out-tab-container st2 mt-3" style="display: none">
						<ul data-wrap="lang_btn_wrap" id="lang_btn_wrap">
							<!-- <li class="current"><a>한국어</a></li>
							<li><a>영어</a></li>
							<li><a>중국어</a></li> -->
						</ul>
					</div>
					<!-- 정보 // -->
					<div class="row" data-wrap="lang_input_wrap" id="lang_input_wrap">
						<div class="col-md-12">
							<div class="body-box mt-3">
															<div class="box-table-container">
								<dl class="box-tbody">
									<dt class="box-th box-head">
										<p>카테고리</p>
									</dt>
									<dd class="box-td">
										<div class="insert insert-select">
											<select class="select-custom" type="text" id="main_category"
												name="main_category" style="width: 220px" data-attr="category">
												<option value="" disabled selected>카테고리를 선택해주세요.</option>
												<option value="service">서비스 안내</option>
												<option value="update">업데이트</option>
												<option value="event">이벤트</option>
											</select>
										</div>
									</dd>
								</dl>
								<dl class="box-tbody">
									<dt class="box-th box-head">
										<p>중요 공지</p>
									</dt>
									<dd class="box-td">
										<div class="insert insert-checkbox" style="display: block !important; visibility: visible !important;">
											<label style="display: flex !important; align-items: center; visibility: visible !important;">
												<input type="checkbox" id="is_important" name="is_important" value="1" data-attr="kind" 
													   style="display: inline-block !important; visibility: visible !important; opacity: 1 !important; width: 18px; height: 18px; margin-right: 10px;" />
												<span style="display: inline-block !important; visibility: visible !important;">중요 공지사항으로 설정</span>
											</label>
										</div>
									</dd>
								</dl>
								<dl class="box-tbody">
										<dt class="box-th box-head">
											<p>제목</p>
										</dt>
										<dd class="box-td">
											<div class="insert insert-input">
												<input type="text" class="input-sm" id="title" name="title"
													data-attr="title" />
											</div>
										</dd>
									</dl>
									<dl class="box-tbody">
										<dt class="box-th box-head">
											<p>첨부파일</p>
										</dt>
										<dd class="box-td">
											<div class="insert insert-input">
												<input class="input-sm" type="file" id="add_file_input_0"
													name="add_file[]" style="display: none;" onchange="add_file(this)">
												<input class="input-sm" readonly="true" type="text" id="add_file_name_0"
													style="margin-right: 10px;">
												<label for="add_file_input_0" class="file-upload btn-primary"
													style="margin-right: 5px; cursor: pointer;">파일첨부</label>
												<!-- <label class="file-upload btn-default" style="cursor: pointer;" onclick="file_remove()">삭제</label> -->
												<label class="file-upload btn-ghost" style="cursor: pointer;"
													onclick="add_file_input(this)">추가</label>
												<label class="file-upload btn-default" data-attr="del_file_btn" style="cursor: pointer;"
												onclick="del_default_file()">삭제</label>
											</div>

										</dd>
									</dl>
								</div>
							</div>
						</div>	
					<!-- <div class ="summernote">

						</div> -->
						<!-- <div class="body-box mt-3">
							<div class="box-table-container">
								<dl class="box-tbody">
									<dt class="box-th box-head">
										<p>제목</p>
									</dt>
									<dd class="box-td">
										<div class="insert insert-input">
											<input type="text" class="input-sm" data-attr="product_name"/>
										</div>
									</dd>
								</dl>
							</div>
						</div>
						추가정보 //
						<div class="row">
							<div class="col-md-12">
								<div class="body-box mt-3">
									<div class="box-tit mb-2"><h3>내용</h3></div>
									<div class="insert insert-textarea mt-2">
										<iframe scrolling="no" frameborder="0" style="width:100%; height:654px;" src="<?php echo $this->project_path;?>/lib/summernote-0.8.9-dist/dist/index.php" id="" data-attr="sum_note"></iframe>
									</div>
								</div>
							</div>
							상품정보 //
						</div> -->
					</div>
				</form>
				<div class="btn-container align-right mt-3">
					<button type="button" class="btn btn-ghost" onclick="cancle()">취소</button>
					<button type="button" class="btn btn-primary ml-1" id="save_btn"
						onclick="request_save()">저장하기</button>
				</div>
			</article>
		</div>
	</div>
	<div style="display:none">
		<li class="" data-copy="lang_btn_copy"><a data-attr="lang">한국어</a></li> <!-- 언어 버튼, 활성화면 li class = current-->
		<!-- 언어별 데이터 input  -->
		<div data-copy="lang_input_copy" style="display:none;">
			
			<!-- 추가정보 // -->
			<div class="row">
				<div class="col-md-12">
					<div class="body-box mt-3">
						<div class="box-tit mb-2">
							<h3>공지사항 내용</h3>
						</div>
						<div class="insert insert-textarea mt-2">
							<div class="summernote" style="width:100%; height:654px;" data-attr="sum_note">

							</div>
							<!-- <iframe scrolling="no" frameborder="0" style="width:100%; height:654px;" src="<?php echo $this->project_path;?>/lib/summernote-0.8.9-dist/dist/index.php" id="" data-attr="sum_note"></iframe> -->
						</div>
					</div>
				</div>
				<!-- 상품정보 // -->
			</div>
		</div>
		<!-- 추가 클릭하면 아래 div 전체 추가 -->
		<div class="insert insert-input" id="file_upload_copy">
			<input class="input-sm" type="file" data-attr="file" name="add_file[]" style="display: none;"
				onchange="add_file(this)">
			<input class="input-sm" readonly="true" type="text" data-attr="file_name" style="margin-right: 10px;">
			<label for="add_file_input_0" data-attr="file_label" class="file-upload btn-primary"
				style="margin-right: 5px; cursor: pointer;">파일첨부</label>
			<label class="file-upload btn-default" data-attr="del_file_btn" style="cursor: pointer;"
				onclick="file_remove()">삭제</label>
		</div>
	</div>
</body>
<!-- select2 -->
<link rel="stylesheet" type="text/css"
	href="<?php echo $this->project_admin_path;?>layout/select2/css/select2.min.css" />
<link rel="stylesheet" type="text/css" href="common_css/adm/adm_select.css?<?php echo $version;?>" />
<script type="text/javascript" src="<?php echo $this->project_admin_path;?>layout/select2/js/select2.full.min.js">
</script>
<!-- yd custom -->
<script type="text/javascript" src="<?php echo $this->project_admin_path;?>js/custom.js"></script>
<script type="text/javascript">
	$(document).ready(function () {
		$('.select-custom').select2({
			minimumResultsForSearch: -1
		});
	});
</script>
</html>
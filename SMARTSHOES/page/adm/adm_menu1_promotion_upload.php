<!DOCTYPE html>
<html lang="ko">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title>홍보/행사 등록</title>
	<!-- font -->
	<link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet">
	<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-kr.css' rel='stylesheet' type='text/css'>

	<!-- css -->
	<?php include_once $this->dir."page/adm/inc/common_css.php"; ?>
	<!-- script -->
	<?php include_once $this->dir."page/adm/inc/common_js.php"; ?>
	<?php include_once $this->dir."page/adm/inc/summernote.php"; ?>
	<!-- include summernote css/js-->
	<script type="text/javascript" src="<?php echo $this->project_admin_path;?>js/menu1_promotion_upload.js<?php echo $version;?>"></script>
</head>

<body>
	<div class="wrap">
		<?php include_once $dir."page/adm/include/adm_menu1_aside.php";?>
		<?php include_once $dir."page/adm/include/adm_header.php";?>
		<div class="bd">
			<article class="body-container">
				<div class="body-head">
					<h2 id="page_title">홍보/행사 등록</h2>
				</div>
				<form class="form" id="promotion_form" onsubmit='return false;' enctype="multipart/form-data">
					<!-- 기본 정보 -->
					<div class="row">
						<div class="col-md-12">
							<div class="body-box mt-3">
								<div class="box-table-container">
									<dl class="box-tbody">
										<dt class="box-th box-head">
											<p>행사명 <span class="text-danger">*</span></p>
										</dt>
										<dd class="box-td">
											<div class="insert insert-input">
												<input type="text" class="input-sm" id="event_name" name="event_name"
													placeholder="행사명을 입력해주세요" required />
											</div>
										</dd>
									</dl>
									<dl class="box-tbody">
										<dt class="box-th box-head">
											<p>행사기간 <span class="text-danger">*</span></p>
										</dt>
										<dd class="box-td">
											<div class="insert insert-input">
												<input type="text" class="input-sm" id="event_period" name="event_period"
													placeholder="예: 2024년 10월 15일 - 10월 18일" required />
											</div>
										</dd>
									</dl>
									<dl class="box-tbody">
										<dt class="box-th box-head">
											<p>행사장소 <span class="text-danger">*</span></p>
										</dt>
										<dd class="box-td">
											<div class="insert insert-input">
												<input type="text" class="input-sm" id="event_location" name="event_location"
													placeholder="예: 서울 코엑스" required />
											</div>
										</dd>
									</dl>
									<dl class="box-tbody">
										<dt class="box-th box-head">
											<p>수상 내역</p>
										</dt>
										<dd class="box-td">
											<div class="insert insert-input">
												<input type="text" class="input-sm" id="award_badge" name="award_badge"
													placeholder="예: 혁신상 수상 (선택사항)" />
											</div>
										</dd>
									</dl>
									<dl class="box-tbody">
										<dt class="box-th box-head">
											<p>활성화 상태</p>
										</dt>
										<dd class="box-td">
											<div class="insert insert-checkbox">
												<label>
													<input type="checkbox" id="is_active" name="is_active" value="1" checked />
													<span>활성화</span>
												</label>
											</div>
										</dd>
									</dl>
								</div>
							</div>
						</div>
					</div>

					<!-- 이미지 업로드 -->
					<div class="row mt-3">
						<div class="col-md-12">
							<div class="body-box">
								<div class="box-table-container">
									<dl class="box-tbody">
										<dt class="box-th box-head">
											<p>메인 이미지 <span class="text-danger">*</span></p>
										</dt>
										<dd class="box-td">
											<div class="insert insert-input">
												<input class="input-sm" type="file" id="main_image" name="main_image" 
													accept="image/*" style="display: none;">
												<input class="input-sm" readonly="true" type="text" id="main_image_name"
													style="margin-right: 10px;" placeholder="메인 이미지를 선택해주세요">
												<label for="main_image" class="file-upload btn-primary"
													style="margin-right: 5px; cursor: pointer;">이미지 선택</label>
												<label class="file-upload btn-ghost" style="cursor: pointer;"
													onclick="clearMainImage()">삭제</label>
											</div>
											<div id="main_image_preview" class="mt-2" style="display: none;">
												<img id="main_image_preview_img" src="" alt="미리보기" 
													style="max-width: 300px; max-height: 200px; border: 1px solid #ddd; border-radius: 4px;">
											</div>
										</dd>
									</dl>
									<dl class="box-tbody">
										<dt class="box-th box-head">
											<p>서브 이미지</p>
											<small class="text-muted d-block">최대 20개까지 업로드 가능</small>
										</dt>
										<dd class="box-td">
											<div class="insert insert-input">
												<input class="input-sm" type="file" id="sub_images" name="sub_images[]" 
													accept="image/*" multiple style="display: none;">
												<label for="sub_images" class="file-upload btn-primary"
													style="margin-right: 5px; cursor: pointer;">이미지 선택</label>
												<label class="file-upload btn-ghost" style="cursor: pointer;"
													onclick="clearSubImages()">전체 삭제</label>
											</div>
											<div id="sub_images_preview" class="mt-2" style="display: none;">
												<div class="row" id="sub_images_container">
													<!-- 서브 이미지 미리보기가 여기에 추가됩니다 -->
												</div>
											</div>
										</dd>
									</dl>
								</div>
							</div>
						</div>
					</div>

					<!-- 내용 입력 -->
					<div class="row mt-3">
						<div class="col-md-12">
							<div class="body-box">
								<div class="box-table-container">
									<dl class="box-tbody">
										<dt class="box-th box-head">
											<p>행사 내용 <span class="text-danger">*</span></p>
										</dt>
										<dd class="box-td">
											<div class="insert insert-input">
												<textarea id="content" name="content" class="input-sm" rows="10" placeholder="행사 내용을 입력해주세요"></textarea>
											</div>
										</dd>
									</dl>
								</div>
							</div>
						</div>
					</div>

					<!-- 버튼 영역 -->
					<div class="row mt-4">
						<div class="col-md-12 text-center">

							<button type="button" class="btn btn-secondary" onclick="goToList()" style="margin-right: 10px;">
								<i class="fas fa-list"></i> 목록
							</button>
							<button type="button" class="btn btn-primary" onclick="submitForm()">
								<i class="fas fa-save"></i> 등록
							</button>
						</div>
					</div>
				</form>
			</article>
		</div>
	</div>
</body>

</html>

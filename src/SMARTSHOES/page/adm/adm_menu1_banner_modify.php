<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>R&D 수정</title>
	<!-- font -->
	<link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet">
	<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-kr.css' rel='stylesheet' type='text/css'>

	<!-- css -->
	<?php include_once $this->dir."page/adm/inc/common_css.php"; ?>

	<!-- script -->
	<?php include_once $this->dir."page/adm/inc/common_js.php"; ?>
	<script type="text/javascript" src="<?php echo $this->project_admin_path;?>js/menu1_rnd_modify.js<?php echo $version;?>"></script>
</head>
<body>
	<div class="wrap">
        <?php include_once $dir."page/adm/include/adm_menu1_aside.php";?>
        <?php include_once $dir."page/adm/include/adm_header.php";?>
			<div class="bd">
				<article class="body-container">
					<div class="body-head"><h2>R&D 수정</h2></div>
					<form class="form" id="form" onsubmit='return false;'>
						<div class="row">
							<div class="col-md-12">
								<div class="body-box mt-1">
									<div class="box-table-container">
										<dl class="box-tbody">
											<dt class="box-th box-head">
												<p>사용여부</p>
											</dt>
											<dd class="box-td">
												<div class="insert">
													<div class="insert-wrap">
														<div class="insert insert-chk">
															<label class="check_label" for="use">사용
																<input type="radio" id="use" value="1" name="is_use" checked/>
																<span class="checkmark radio"></span>
															</label>
														</div>
														<div class="insert insert-chk">
															<label class="check_label" for="not_use">미사용
																<input type="radio" id="not_use" value="0" name="is_use"/>
																<span class="checkmark radio"></span>
															</label>
														</div>
													</div>
												</div>
											</dd>
										</dl>
										<dl class="box-tbody">
											<dt class="box-th box-head">
												<p>R&D 분류</p>
											</dt>
											<dd class="box-td">
												<div class="insert">
													<div class="insert-wrap">
														<div class="insert insert-chk">
															<label class="check_label">인증현황
																<input type="radio" value="1" name="rnd-list" checked/>
																<span class="checkmark radio"></span>
															</label>
														</div>
														<div class="insert insert-chk">
															<label class="check_label">특허현황
																<input type="radio" value="0" name="rnd-list"/>
																<span class="checkmark radio"></span>
															</label>
														</div>
													</div>
												</div>
											</dd>
										</dl>
										<dl class="box-tbody">
											<dt class="box-th box-head">
												<p>제목</p>
											</dt>
											<dd class="box-td">
												<div class="insert">
													<input type="text" id="name" name="name" class="form-control" placeholder="제목을 입력하세요">
												</div>
											</dd>
										</dl>
										<dl class="box-tbody" id="pc_rnd_box">
											<dt class="box-th box-head">
												<p>이미지 등록</p>
											</dt>
											<dd class="box-td">
                                                <!-- 이미지 등록 버튼 -->
                                                <div class="img-upload img-upload-main" style="overflow: hidden;">
                                                    <span class="btn-wrap">
                                                        <button class="btn-img-upload" href="#"><strong></strong></button>
                                                        <input type="file" id="pc_img_file" name="pc_rnd_files[]" data-attr="thumnail_file">
                                                    </span> 				
                                                    <label for="pc_img_file" id="pc_img_label" data-attr="thumnail_file_label"></label>
                                                </div>
                                                <!-- 이미지 등록 버튼 // -->
                                                <!-- 이미지가 첨부될 시 옆에 나열됩니다 -->
                                                <div data-wrap="pc_img_wrap" style = "display:inline-block" data-attr="thumnail_wrap">
													<!-- <div class="img-upload" style="overflow: hidden;">
														<img src="<?php echo $this->project_path;?>/images/sample.png" onerror="this.style.display='none'" alt="img_upload"/>
														<button class="delete-btn" type="button"></button>
													</div> -->
												</div>
												<!-- 이미지 1 // -->
												<p class="xsmall">jpg, png, gif 형식의 파일, 권장 사이즈 1920x1080px<br>4MB 이하의 이미지 1장 첨부 가능</p>
											</dd>
										</dl>
									</div>
								</div>
							</div>
						</div>
						<!-- 정보 // -->
						
                    </form>
                    <div class="btn-container align-right mt-3">
						<button type="button" class="btn btn-ghost" onclick="location.href='?ctl=move&param=adm&param1=menu1_rnd';">취소</button>
						<button type="button" class="btn btn-primary ml-1" onclick="request_save()">저장하기</button>
					</div>
				</article>
			</div>
	</div>
	<div style="display:none;">
		<!-- 언어별 라디오 버튼 -->
		<div class="insert insert-chk" data-copy="copy_radio">
			<label class="check_label" for="1" data-attr="label">KR
				<input type="radio" id="1" value="1" name="lang"/>
				<span class="checkmark radio"></span>
			</label>
		</div>

		<div class="img-upload" style="overflow: hidden;" data-copy = "img_copy">
            <img src="" data-attr="img" alt="img_upload"/>
            <button  data-attr="del_btn" class="delete-btn" type="button"></button>
        </div>
	</div>
</body>
<!-- select2 -->
<link rel="stylesheet" type="text/css" href="<?php echo $this->project_admin_path;?>layout/select2/css/select2.min.css"/>
<link rel="stylesheet" type="text/css" href="common_css/adm/adm_select.css?<?php echo $version;?>"/>
<script type="text/javascript" src="<?php echo $this->project_admin_path;?>layout/select2/js/select2.full.min.js"></script>

<!-- yd custom -->
<script type="text/javascript" src="<?php echo $this->project_admin_path;?>js/custom.js"></script>

</html>
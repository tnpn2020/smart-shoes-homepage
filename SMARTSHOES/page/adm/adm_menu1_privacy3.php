<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title>이메일무단수집거부 설정</title>
	<!-- font -->
	<link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet">
	<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-kr.css' rel='stylesheet' type='text/css'>

	<!-- css -->
	<?php include_once $this->dir."page/adm/inc/common_css.php"; ?>

	<script>
		var data = <?php echo $this->data ?>;
	</script>

	<!-- script -->
	<?php include_once $this->dir."page/adm/inc/common_js.php"; ?>
	<script type="text/javascript" src="<?php echo $this->project_admin_path;?>js/menu1_privacy3.js<?php echo $version;?>"></script>
	
</head>
<body>
	<div class="wrap">
        <?php include_once $dir."page/adm/include/adm_menu1_aside.php";?>
        <?php include_once $dir."page/adm/include/adm_header.php";?>
			<div class="bd">
				<article class="body-container">
					<div class="body-head mb-1"><h2>이메일무단수집거부 설정</h2></div>
					<form class="form" id="form">
						<div class="out-tab-container st2 mt-2">
							<ul id="lang_list">
								<!-- <li class="current"><a href="">한국어</a></li>
								<li><a href="">영어</a></li>
								<li><a href="">중국어</a></li> -->
							</ul>
						</div>
						<!-- <div class="body-box">
							<div class="insert-wrap">
								<div class="insert insert-chk">
									<label class="check_label" for="use_form">Form을 이용하여 등록
										<input type="radio" name="use" id="use_form" value="use_form" onchange="change_ui()" checked/>
										<span class="checkmark radio"></span>
									</label>
								</div>
								<div class="insert insert-chk">
									<label class="check_label" for="use_description">직접입력하여 등록
										<input type="radio" name="use" id="use_description" value="use_description"  onchange="change_ui()" />
										<span class="checkmark radio"></span>
									</label>
								</div>
							</div>
						</div> -->
						<div class="body-box mt-3">
                            <div class="insert insert-textarea" id="content">
								<textarea rows="16" id ='content_value'></textarea>
                            </div>
                        </div>
					</form>
					
                    <div class="btn-container align-right mt-3">
						<button type="button" class="btn btn-ghost d-none" onclick="delete_terms()">삭제</button>
						<button type="button" class="btn btn-primary" onclick='update_privacy();'>저장하기</button>
					</div>
				</article>
			</div>
	</div>

</body>
<!-- select2 -->
<link rel="stylesheet" type="text/css" href="<?php echo $this->project_admin_path;?>layout/select2/css/select2.min.css"/>
<link rel="stylesheet" type="text/css" href="common_css/adm/adm_select.css?<?php echo $version;?>"/>
<script type="text/javascript" src="<?php echo $this->project_admin_path;?>layout/select2/js/select2.full.min.js"></script>
<script type="text/javascript">

	$(document).ready(function() {
		$("select[class='select-custom']").select2();
	});

</script>
</html>
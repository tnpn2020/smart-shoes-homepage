<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title>관리자 로그인</title>
	<!-- font -->
	<link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet">
	<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-kr.css' rel='stylesheet' type='text/css'>

	<!-- css -->
	<?php include_once $this->dir."page/adm/inc/common_css.php"; ?>
	<link rel="stylesheet" type="text/css" href="/SMARTSHOES/common_css/adm/adm_login.css<?php echo $version;?>"/>

	<!-- JS -->
    <?php include_once $this->dir."page/adm/inc/common_js.php"; ?>
	<script type="text/javascript" src="<?php echo $this->project_admin_path;?>js/login.js"></script>

	<!-- script -->
	
</head>
<body>
	<div class="wrap">
		<div class="img-bg login-bg">
			<div class="bd bd-md">
				<div class="login-container">
					<div class="inner d-flex justify-center align-items-center">
					<h1 class="mb-1">Login</h1>
						<p class="mb-4 medium">관리자 로그인이 필요합니다.</p>
						<div class="form-wrap">
							<form class="form">
								<dl class="form-con mb-2">
									<dt>아이디</dt>
									<dd><div class="input-insert"><input type="text" placeholder="아이디" id="id" onkeyup="enterkey()"/></div></dd>
								</dl>
								<dl class="form-con">
									<dt>비밀번호</dt>
									<dd><div class="input-insert"><input type="password" placeholder="비밀번호" id="pw" onkeyup="enterkey()"/></div></dd>
								</dl>
							</form>
							<div class="mt-2"><button type="button" class="btn btn-primary" onclick="login()">로그인</button></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php include_once $dir."page/adm/adm_alert_modal.php";?>
</body>
</html>
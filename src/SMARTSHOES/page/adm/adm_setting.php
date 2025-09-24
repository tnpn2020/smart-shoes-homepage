<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title>프로젝트 세팅</title>
	<!-- font -->
	<link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet">
	<link href='//spoqa.github.io/spoqa-han-sans/css/SpoqaHanSans-kr.css' rel='stylesheet' type='text/css'>

	<!-- css -->
	<?php include_once $this->dir."page/adm/inc/common_css.php"; ?>

	<!-- script -->
	<?php include_once $this->dir."page/adm/inc/common_js.php"; ?>
    <script type="text/javascript" src="<?php echo $this->project_admin_path;?>js/setting.js<?php echo $version;?>"></script>
    <script>
        var project_name = '<?php echo $this->project_name?>';
    </script>
</head>
<body>
	<div class="wrap">
        <div class="bd">
            <article class="body-container">
                <div class="body-head mb-1"><h2>테이블 세팅</h2></div>
                <form class="form">
                    <div class="body-box">
                        <div class="box-table-container">
                            <button type="button" style="background-color: #1f6bbb; color: white; height: 30px;" onclick="create_table();">테이블 생성하기</button>
                        </div>
                    </div>
                </form>
            </article>

            <article class="body-container">
                <div class="body-head mb-1"><h2>프로젝트 세팅</h2></div>
                <form class="form">
                    <div class="body-box">
                        <div class="box-table-container">
                            <select id="category_option" style="width: 200px; height: 30px; border: 1px solid gray;">
                                <option value="no_select">선택</option>
                                <option value="0">대분류</option>
                                <option value="1">중분류</option>
                                <option value="2">소분류</option>
                                <option value="3">세분류</option>
                            </select>
                            <button type="button" style="background-color: #1f6bbb; color: white; height: 30px; margin-left: 10px;" onclick="project_setting();">설정</button>
                            <p style="margin-top: 20px; display: none;" id="no_set_message">프로젝트 세팅이 설정되어 있지 않습니다.</p>
                        </div>
                    </div>
                </form>
            </article>

            <article class="body-container">
                <div class="body-head mb-1"><h2>언어 설정</h2></div>
                <form class="form">
                    <div class="body-box">
                        <div class="box-table-container">
                            <input type="text" style="width: 200px; height: 30px;" placeholder="추가하실 언어를 입력해주세요" id="lang_name">
                            <select id="lang_option" style="width: 200px; height: 30px; border: 1px solid gray; margin-left: 5px;" onchange="change_national_flag(this.value)">
                                <option value="i-kor.png" checked>KR</option>
                                <option value="i-eng.png">EN</option>
                                <option value="i-chn.png">CH</option>
                            </select>
                            <img src="<?php echo $this->project_name?>/page/adm/images/i-kor.png" style="width: 25px; height: 25px; vertical-align: middle;" id="national_flag">
                            <button type="button" style="background-color: #1f6bbb; color: white; height: 30px; margin-left: 10px;" onclick="register_lang();">추가</button>
                            <table class="adm_table adm_fixed_table" style="width: 50%;">
                                <thead>
                                    <tr>
                                        <th style="width: 100px;">언어</th>
                                        <th style="width: 100px;">아이콘</th>
                                        <th style="width: 100px;">삭제</th>
                                    </tr>
                                </thead>
                                <tbody data-wrap="wrap">
                                    <tr id="no_board">
                                        <td colspan="3" style="width:100%; text-align: center;">등록된 언어가 없습니다.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </article>
        </div>
    </div>
    <!-- 등록 언어 게시판 auto_view -->
    <div style="display: none;">
        <table>
            <tr data-copy="copy">
                <th data-attr="lang_name"></th>
                <th><img src="" data-attr="icon" style="width: 30px; height: 30px;"></th>
                <th><button type="button" data-attr="del_btn" style="background-color: #1f6bbb; color: white;">삭제</button></th>
            </tr>
        </table>
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
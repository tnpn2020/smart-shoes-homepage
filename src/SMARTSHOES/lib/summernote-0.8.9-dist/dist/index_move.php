<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>bootstrap4</title>
    <script src="MOMO_B2C/lib/summernote-0.8.9-dist/dist/jquery-3.2.1.slim.min.js"></script>
    <link href="MOMO_B2C/lib/summernote-0.8.9-dist/dist/summernote-lite.css" rel="stylesheet"/>
    <script src="MOMO_B2C/lib/summernote-0.8.9-dist/dist/summernote-lite.js"></script>
	<script>
    var id = '<?php echo $id ?>';
		function getNote(){
			return document.getElementsByClassName("note-editable")[0];
		}

		function getToolBar(){
			return document.getElementsByClassName("note-toolbar")[0];
    }

		window.onload = function(){ //id 추가
      if(typeof(parent.iframeLoaded)=="function"){
        parent.iframeLoaded(document.getElementsByClassName("note-editable")[0],id);
      }
		}
	</script>
  </head>
  <body style="margin:0;">
    <div id="summernote"></div>
    <script>
      $('#summernote').summernote({
        placeholder: '',
        tabsize:2,
        height: 600,
        minHeight:null,
        maxHeight:null,
        lang:'ko-KR',
        disableResizeEditor: true
      });
    </script>
  </body>
</html>
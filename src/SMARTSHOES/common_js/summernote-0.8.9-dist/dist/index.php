<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>bootstrap4</title>
    <script src="jquery-3.2.1.slim.min.js"></script>
    <link href="summernote-lite.css" rel="stylesheet"/>
    <script src="summernote-lite.js"></script>
	<script>
		function getNote(){
			return document.getElementsByClassName("note-editable")[0];
		}

		function getToolBar(){
			return document.getElementsByClassName("note-toolbar")[0];
    }

		window.onload = function(){
      if(typeof(parent.iframeLoaded)=="function"){
        // console.log(document.getElementsByClassName("note-editable"));
        parent.iframeLoaded(document.getElementsByClassName("note-editable")[0]);
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
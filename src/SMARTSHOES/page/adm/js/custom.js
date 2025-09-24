$(document).ready(function() {
    $('.select-custom').select2({
        minimumResultsForSearch: -1
    });

    $('.tog-btn').click(function(){
        $('.tog-btn').toggleClass('open')
        $('.tog-container').toggle()
    });
});

$(document).ready(function() {
    if(lb.getElem("iframeeditor") != null){
        $('#iframeeditor').summernote({
            height: 300,                 // set editor height
            minHeight: null,             // set minimum height of editor
            maxHeight: null,             // set maximum height of editor
            focus: true                  // set focus to editable area after initializing summernote
        });
    }
});

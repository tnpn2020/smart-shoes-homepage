// 홍보/행사 등록 페이지 JavaScript

$(document).ready(function() {
    // 페이지 로드 시 초기화
    initUploadPage();
});

// 홍보/행사 등록 객체
var promotionUpload = {
    elem: {
        form: $('#promotion_form'),
        content: $('#content'),
        main_image: $('#main_image'),
        main_image_name: $('#main_image_name'),
        main_image_preview: $('#main_image_preview'),
        main_image_preview_img: $('#main_image_preview_img'),
        sub_images: $('#sub_images'),
        sub_images_preview: $('#sub_images_preview'),
        sub_images_container: $('#sub_images_container')
    },
    
    data: {
        sub_images_files: []
    }
};

// 페이지 초기화
function initUploadPage() {
    // TEXTAREA 사용 (Summernote 제거)
    // initSummernote();
    
    // 이미지 업로드 이벤트 연결
    bindImageEvents();
}

// 이미지 업로드 이벤트 연결
function bindImageEvents() {
    // 메인 이미지 파일 선택 이벤트
    $('#main_image').on('change', function() {
        previewMainImage(this);
    });
    
    // 서브 이미지 파일 선택 이벤트
    $('#sub_images').on('change', function() {
        previewSubImages(this);
    });
}

// Summernote 초기화
function initSummernote() {
    promotionUpload.elem.content.summernote({
        height: 400,
        minHeight: null,
        maxHeight: null,
        focus: false,
        lang: 'ko-KR',
        placeholder: '행사 내용을 입력해주세요.',
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ],
        callbacks: {
            onImageUpload: function(files) {
                for (let i = 0; i < files.length; i++) {
                    uploadSummernoteImage(files[i], this);
                }
            }
        }
    });
}

// Summernote 이미지 업로드
function uploadSummernoteImage(file, editor) {
    // 서머노트 이미지 업로드는 기존 방식 유지 (파일 업로드이므로)
    var formData = new FormData();
    formData.append('action', 'AdminMenu1Model');
    formData.append('function', 'upload_summernote_image');
    formData.append('image', file);
    
    $.ajax({
        url: 'action.php',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            if (response.result === '1') {
                $(editor).summernote('insertImage', response.image_url);
            } else {
                alert('이미지 업로드에 실패했습니다.');
            }
        },
        error: function() {
            alert('이미지 업로드 중 오류가 발생했습니다.');
        }
    });
}

// 메인 이미지 미리보기
function previewMainImage(input) {
    if (input.files && input.files[0]) {
        var file = input.files[0];
        
        // 파일 크기 체크 (5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('파일 크기는 5MB를 초과할 수 없습니다.');
            clearMainImage();
            return;
        }
        
        // 파일명 표시
        $('#main_image_name').val(file.name);
        
        // 미리보기 생성
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#main_image_preview_img').attr('src', e.target.result);
            $('#main_image_preview').show();
        };
        reader.readAsDataURL(file);
    }
}

// 메인 이미지 삭제
function clearMainImage() {
    $('#main_image').val('');
    $('#main_image_name').val('');
    $('#main_image_preview').hide();
    $('#main_image_preview_img').attr('src', '');
}

// 서브 이미지 미리보기
function previewSubImages(input) {
    if (input.files && input.files.length > 0) {
        var newFiles = Array.from(input.files);
        
        // 기존 파일들과 새 파일들 합치기
        var allFiles = promotionUpload.data.sub_images_files.concat(newFiles);
        
        // 최대 20개 제한
        if (allFiles.length > 20) {
            alert('서브 이미지는 최대 20개까지 업로드 가능합니다. 현재 ' + promotionUpload.data.sub_images_files.length + '개가 이미 선택되어 있습니다.');
            // input 초기화
            $('#sub_images').val('');
            return;
        }
        
        // 파일 크기 체크 (새 파일들만)
        var oversizedFiles = newFiles.filter(file => file.size > 5 * 1024 * 1024);
        if (oversizedFiles.length > 0) {
            alert('파일 크기는 5MB를 초과할 수 없습니다.');
            // input 초기화
            $('#sub_images').val('');
            return;
        }
        
        // 기존 파일들과 새 파일들 합치기
        promotionUpload.data.sub_images_files = allFiles;
        
        // 전체 미리보기 재생성
        $('#sub_images_container').empty();
        
        // 미리보기 생성
        allFiles.forEach(function(file, index) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var html = `
                    <div class="col-md-3 mb-3" data-index="${index}">
                        <div class="card">
                            <img src="${e.target.result}" class="card-img-top" 
                                 style="height: 150px; object-fit: cover;" alt="서브 이미지 ${index + 1}">
                            <div class="card-body p-2">
                                <small class="text-muted text-truncate d-block">${file.name}</small>
                                <button type="button" class="btn btn-sm btn-danger mt-1" 
                                        onclick="removeSubImage(${index})">삭제</button>
                            </div>
                        </div>
                    </div>
                `;
                $('#sub_images_container').append(html);
            };
            reader.readAsDataURL(file);
        });
        
        // input 파일 업데이트 (모든 파일로)
        updateSubImagesInput();
        
        $('#sub_images_preview').show();
    }
}

// 서브 이미지 input 파일 업데이트
function updateSubImagesInput() {
    var dt = new DataTransfer();
    promotionUpload.data.sub_images_files.forEach(function(file) {
        dt.items.add(file);
    });
    $('#sub_images')[0].files = dt.files;
}

// 서브 이미지 개별 삭제
function removeSubImage(index) {
    // 배열에서 해당 파일 제거
    promotionUpload.data.sub_images_files.splice(index, 1);
    
    // 파일 input 재설정
    updateSubImagesInput();
    
    // 미리보기 재생성
    if (promotionUpload.data.sub_images_files.length > 0) {
        previewSubImages({ files: promotionUpload.data.sub_images_files });
    } else {
        clearSubImages();
    }
}

// 서브 이미지 전체 삭제
function clearSubImages() {
    $('#sub_images').val('');
    $('#sub_images_container').empty();
    $('#sub_images_preview').hide();
    promotionUpload.data.sub_images_files = [];
}

// 폼 유효성 검사
function validateForm() {
    var event_name = $('#event_name').val().trim();
    var event_period = $('#event_period').val().trim();
    var event_location = $('#event_location').val().trim();
    var content = $('#content').val().trim();
    
    if (!event_name) {
        alert('행사명을 입력해주세요.');
        $('#event_name').focus();
        return false;
    }
    
    if (!event_period) {
        alert('행사기간을 입력해주세요.');
        $('#event_period').focus();
        return false;
    }
    
    if (!event_location) {
        alert('행사장소를 입력해주세요.');
        $('#event_location').focus();
        return false;
    }
    
    if (!content) {
        alert('행사 내용을 입력해주세요.');
        $('#content').focus();
        return false;
    }
    
    // 메인 이미지 체크는 필수가 아니므로 제거
    // if (!promotionUpload.elem.main_image[0].files.length) {
    //     alert('메인 이미지를 선택해주세요.');
    //     return false;
    // }
    
    return true;
}

// 폼 제출
function submitForm() {
    if (!validateForm()) {
        return;
    }
    
    if (!confirm('등록하시겠습니까?')) {
        return;
    }
    
    // 버튼 비활성화
    $('button').prop('disabled', true);
    
    // lb.ajax AjaxFormPost 방식 사용 (공지사항과 동일)
    lb.ajax({
        type: "AjaxFormPost",
        list: {
            ctl: "AdminMenu1",
            param1: "promotion_register",
            event_name: $('#event_name').val().trim(),
            event_period: $('#event_period').val().trim(),
            event_location: $('#event_location').val().trim(),
            award_badge: $('#award_badge').val().trim(),
            content: $('#content').val().trim(),
            is_active: $('#is_active').is(':checked') ? 1 : 0
        },
        elem: $('#promotion_form')[0],
        action: lb.obj.address,
        response_method: "response_promotion_register",
                 havior: function(result) {
             $('button').prop('disabled', false);
            result = JSON.parse(result);
            response_promotion_register(result);
        }
    });
}

// 홍보/행사 등록 응답 처리
function response_promotion_register(result) {
    if (result.result === '1') {
        alert('등록이 완료되었습니다.');
        window.location.href = "/?ctl=move&param=adm&param1=menu1_promotion";
    } else {
        alert(result.message || '등록에 실패했습니다.');
    }
}



// 목록으로 이동
function goToList() {
    if (confirm('목록으로 이동하시겠습니까? 작성 중인 내용은 저장되지 않습니다.')) {
        window.location.href = "/?ctl=move&param=adm&param1=menu1_promotion";
    }
}

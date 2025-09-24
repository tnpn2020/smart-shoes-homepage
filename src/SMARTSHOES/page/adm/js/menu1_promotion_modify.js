// 홍보/행사 수정 페이지 JavaScript

$(document).ready(function() {
    // 페이지 로드 시 초기화
    initModifyPage();
});

// 페이지 변수
var promotionModify = {
    data: {
        sub_images_files: [],
        existing_sub_images: [],
        deleted_main_image: false,
        deleted_sub_images: []
    }
};

// 페이지 초기화
function initModifyPage() {
    // URL에서 idx 파라미터 가져오기
    var urlParams = new URLSearchParams(window.location.search);
    var idx = urlParams.get('idx');
    
    if (!idx) {
        alert('잘못된 접근입니다.');
        goToList();
        return;
    }
    
    $('#idx').val(idx);
    
    // 이미지 업로드 이벤트 연결
    bindImageEvents();
    
    // 기존 데이터 로드
    loadPromotionData(idx);
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

// 기존 데이터 로드
function loadPromotionData(idx) {
    lb.ajax({
        type: "JsonAjaxPost",
        list: {
            ctl: "AdminMenu1",
            param1: "get_promotion_detail",
            idx: idx
        },
        action: lb.obj.address,
        response_method: "response_promotion_detail",
        havior: function(result) {
            result = JSON.parse(result);
            response_promotion_detail(result);
        }
    });
}

// 상세 데이터 응답 처리
function response_promotion_detail(result) {
    if (result.result === '1') {
        var data = result.value[0]; // 배열의 첫 번째 요소 가져오기
        
        // 기본 정보 설정
        $('#event_name').val(data.event_name);
        $('#event_period').val(data.event_period);
        $('#event_location').val(data.event_location);
        $('#award_badge').val(data.award_badge);
        $('#content').val(data.content);
        $('#is_active').prop('checked', data.is_active == 1);
        
        // 기존 메인 이미지 표시
        if (data.main_image) {
            showExistingMainImage(data.main_image);
        }
        
        // 기존 서브 이미지들 표시
        if (data.sub_images && data.sub_images.length > 0) {
            promotionModify.data.existing_sub_images = data.sub_images;
            showExistingSubImages(data.sub_images);
        }
        
    } else {
        alert('데이터를 불러오는데 실패했습니다.');
        goToList();
    }
}

// 기존 메인 이미지 표시
function showExistingMainImage(imagePath) {
    $('#existing_main_image_img').attr('src', imagePath);
    $('#existing_main_image').show();
}

// 기존 메인 이미지 삭제
function deleteExistingMainImage() {
    if (confirm('기존 메인 이미지를 삭제하시겠습니까?')) {
        $('#existing_main_image').hide();
        promotionModify.data.deleted_main_image = true;
    }
}

// 기존 서브 이미지들 표시
function showExistingSubImages(images) {
    var html = '';
    images.forEach(function(image, index) {
        html += `
            <div class="col-md-3 mb-3" data-existing-index="${index}">
                <div class="card">
                    <img src="${image.image_path}" class="card-img-top" 
                         style="height: 150px; object-fit: cover;" alt="서브 이미지 ${index + 1}">
                    <div class="card-body p-2">
                        <small class="text-muted text-truncate d-block">기존 이미지 ${index + 1}</small>
                        <button type="button" class="btn btn-sm btn-danger mt-1" 
                                onclick="deleteExistingSubImage(${index})">삭제</button>
                    </div>
                </div>
            </div>
        `;
    });
    
    $('#existing_sub_images_container').html(html);
    $('#existing_sub_images').show();
}

// 기존 서브 이미지 개별 삭제
function deleteExistingSubImage(index) {
    if (confirm('이 이미지를 삭제하시겠습니까?')) {
        var imageData = promotionModify.data.existing_sub_images[index];
        promotionModify.data.deleted_sub_images.push(imageData.idx);
        
        // 화면에서 제거
        $(`[data-existing-index="${index}"]`).remove();
        
        // 배열에서 제거
        promotionModify.data.existing_sub_images.splice(index, 1);
        
        // 인덱스 재정렬
        updateExistingSubImagesIndex();
        
        // 기존 이미지가 모두 삭제되면 컨테이너 숨기기
        if (promotionModify.data.existing_sub_images.length === 0) {
            $('#existing_sub_images').hide();
        }
    }
}

// 기존 서브 이미지 인덱스 재정렬
function updateExistingSubImagesIndex() {
    $('#existing_sub_images_container .col-md-3').each(function(newIndex) {
        $(this).attr('data-existing-index', newIndex);
        $(this).find('button').attr('onclick', `deleteExistingSubImage(${newIndex})`);
        $(this).find('small').text(`기존 이미지 ${newIndex + 1}`);
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
        var allFiles = promotionModify.data.sub_images_files.concat(newFiles);
        
        // 기존 이미지와 새 이미지 합쳐서 최대 20개 제한
        var totalImages = promotionModify.data.existing_sub_images.length + allFiles.length;
        if (totalImages > 20) {
            alert('서브 이미지는 최대 20개까지 업로드 가능합니다. 현재 ' + promotionModify.data.existing_sub_images.length + '개의 기존 이미지와 ' + promotionModify.data.sub_images_files.length + '개의 새 이미지가 있습니다.');
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
        promotionModify.data.sub_images_files = allFiles;
        
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
    promotionModify.data.sub_images_files.forEach(function(file) {
        dt.items.add(file);
    });
    $('#sub_images')[0].files = dt.files;
}

// 서브 이미지 개별 삭제
function removeSubImage(index) {
    // 배열에서 해당 파일 제거
    promotionModify.data.sub_images_files.splice(index, 1);
    
    // 파일 input 재설정
    updateSubImagesInput();
    
    // 미리보기 재생성
    if (promotionModify.data.sub_images_files.length > 0) {
        previewSubImages({ files: promotionModify.data.sub_images_files });
    } else {
        clearSubImages();
    }
}

// 서브 이미지 전체 삭제
function clearSubImages() {
    $('#sub_images').val('');
    $('#sub_images_container').empty();
    $('#sub_images_preview').hide();
    promotionModify.data.sub_images_files = [];
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
    
    return true;
}

// 폼 제출
function submitForm() {
    if (!validateForm()) {
        return;
    }
    
    $('button').prop('disabled', true);
    
    // 폼에 숨겨진 필드들 추가
    addHiddenFields();
    
    lb.ajax({
        type: "AjaxFormPost",
        list: {
            ctl: "AdminMenu1",
            param1: "promotion_modify"
        },
        elem: $('#promotion_form')[0],
        action: lb.obj.address,
        response_method: "response_promotion_modify",
        havior: function(result) {
            $('button').prop('disabled', false);
            result = JSON.parse(result);
            response_promotion_modify(result);
        }
    });
}

// 폼에 숨겨진 필드들 추가
function addHiddenFields() {
    // 기존 숨겨진 필드들 제거
    $('#promotion_form .temp-hidden').remove();
    
    // 새로운 숨겨진 필드들 추가
    var hiddenFields = [
        { name: 'is_active', value: $('#is_active').is(':checked') ? 1 : 0 },
        { name: 'deleted_main_image', value: promotionModify.data.deleted_main_image ? 1 : 0 },
        { name: 'deleted_sub_images', value: JSON.stringify(promotionModify.data.deleted_sub_images) }
    ];
    
    hiddenFields.forEach(function(field) {
        $('<input>').attr({
            type: 'hidden',
            name: field.name,
            value: field.value,
            class: 'temp-hidden'
        }).appendTo('#promotion_form');
    });
}

// 수정 응답 처리
function response_promotion_modify(result) {
    if (result.result === '1') {
        alert(result.message || '수정이 완료되었습니다.');
        window.location.href = "/?ctl=move&param=adm&param1=menu1_promotion";
    } else {
        alert(result.message || '수정 중 오류가 발생했습니다.');
    }
}

// 목록으로 이동
function goToList() {
    if (confirm('목록으로 이동하시겠습니까? 입력한 내용은 저장되지 않습니다.')) {
        window.location.href = "/?ctl=move&param=adm&param1=menu1_promotion";
    }
}



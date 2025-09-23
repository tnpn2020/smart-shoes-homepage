$(document).ready(function() {
    loadPromotionData();
});

// 홍보/행사 데이터 로드
function loadPromotionData() {
    lb.ajax({
        type: "JsonAjaxPost",
        list: {
            ctl: "Userpage",
            param1: "get_promotion_list"
        },
        action: lb.obj.address,
        response_method: "response_promotion_list",
        havior: function(result) {
            result = JSON.parse(result);
            response_promotion_list(result);
        }
    });
}

// 홍보/행사 데이터 응답 처리
function response_promotion_list(result) {
    if (result.result === '1' && result.value.length > 0) {
        renderPromotionList(result.value);
    } else {
        showEmptyState();
    }
}

// 홍보/행사 목록 렌더링
function renderPromotionList(promotions) {
    var html = '';
    
    promotions.forEach(function(promotion, index) {
        var isLeftImage = (index + 1) % 2 === 1; // 홀수번째는 이미지 좌측
        
        html += `
            <div class="promo-item ${isLeftImage ? 'image-left' : 'image-right'}">
                <div class="promo-header">
                    <div class="promo-title">
                        <h2>${promotion.event_name}</h2>
                        ${promotion.award_badge ? `<div class="award-badge">${promotion.award_badge}</div>` : ''}
                    </div>
                    <div class="promo-meta">
                        <span class="date"><i class="xi-calendar-check"></i>${promotion.event_period}</span>
                        <span class="location"><i class="xi-maker"></i>${promotion.event_location}</span>
                    </div>
                </div>
                <div class="promo-content">
                    ${renderImageSection(promotion, isLeftImage)}
                    ${renderTextSection(promotion)}
                </div>
                ${renderGalleryNavigation(promotion)}
            </div>
        `;
    });
    
    $('.promos .container').html(html);
    
    // 슬라이더 초기화
    initializeSliders();
}

// 이미지 섹션 렌더링
function renderImageSection(promotion, isLeftImage) {
    var mainImageSrc = promotion.main_image_path ? 
        obj.link.promotion_main_image_path + promotion.main_image : 
        obj.link.project_path + '/img/expo-default.svg';
    
    var imageHtml = `
        <div class="promo-image-wrap">
            <!-- 메인 이미지 (고정, 변경되지 않음) -->
            <div class="promo-image-view">
                <div class="gallery-item active">
                    <img src="${mainImageSrc}" alt="${promotion.event_name} 메인 이미지">
                </div>
            </div>
        </div>`;
    
    return imageHtml;
}

// 텍스트 섹션 렌더링
function renderTextSection(promotion) {
    return `
        <div class="promo-text">
            ${promotion.content ? promotion.content.replace(/\n/g, '<br>') : '<p>내용이 없습니다.</p>'}
        </div>
    `;
}

// 서브 이미지 슬라이드 네비게이션 렌더링 (3개씩 보여주기)
function renderGalleryNavigation(promotion) {
    if (!promotion.sub_images || promotion.sub_images.length === 0) {
        return ''; // 서브 이미지가 없으면 네비게이션도 숨김
    }
    
    var navHtml = `
        <div class="slide-nav-wrap">
            <div class="promo-image-nav" data-promotion="${promotion.idx}">`;
    
    // 서브 이미지들만 슬라이드로 표시 (3개씩)
    promotion.sub_images.forEach(function(subImage, index) {
        navHtml += `
            <div class="gallery-item" data-index="${index}">
                <img src="${obj.link.promotion_sub_image_path}${subImage.image_file}" alt="${promotion.event_name} 서브 이미지 ${index + 1}">
            </div>`;
    });
    
    navHtml += `
            </div>
            <div class="slide_btn prevArrow" data-promotion="${promotion.idx}"><i class="xi-angle-left-thin"></i></div>
            <div class="slide_btn nextArrow" data-promotion="${promotion.idx}"><i class="xi-angle-right-thin"></i></div>
        </div>
    `;
    
    return navHtml;
}

// 슬라이더 초기화 (서브 이미지 3개씩 표시)
function initializeSliders() {
    // 각 프로모션별로 서브 이미지 슬라이더 초기화
    $('.promo-image-nav').each(function() {
        var promotionId = $(this).data('promotion');
        var $imageNav = $(this);
        var $prevBtn = $(`.prevArrow[data-promotion="${promotionId}"]`);
        var $nextBtn = $(`.nextArrow[data-promotion="${promotionId}"]`);
        
        var $galleryItems = $imageNav.find('.gallery-item');
        var totalImages = $galleryItems.length;
        
        // 반응형 itemsPerPage 설정 - 큰 이미지로 3개씩
        function getItemsPerPage() {
            if ($(window).width() <= 480) {
                return 1; // 작은 모바일: 1개씩 (큰 이미지라서)
            } else if ($(window).width() <= 768) {
                return 2; // 모바일: 2개씩
            } else {
                return 3; // 데스크톱: 3개씩 (큰 이미지로)
            }
        }
        
        var itemsPerPage = getItemsPerPage();
        var currentStartIndex = 0;
        var maxStartIndex = Math.max(0, totalImages - itemsPerPage);
        
        // //console.log('Total images:', totalImages, 'Items per page:', itemsPerPage, 'Max start index:', maxStartIndex);
        
        // 초기 표시
        showSlide(currentStartIndex);
        
        // 이전 버튼 클릭 (무한 스크롤)
        $prevBtn.click(function() {
            currentStartIndex--;
            if (currentStartIndex < 0) {
                currentStartIndex = maxStartIndex; // 마지막으로 이동
            }
            showSlide(currentStartIndex);
            updateButtonState();
        });
        
        // 다음 버튼 클릭 (무한 스크롤)
        $nextBtn.click(function() {
            currentStartIndex++;
            if (currentStartIndex > maxStartIndex) {
                currentStartIndex = 0; // 처음으로 이동
            }
            showSlide(currentStartIndex);
            updateButtonState();
        });
        
        // 슬라이드 표시 함수 (반응형 너비 계산)
        function showSlide(startIndex) {
            if ($galleryItems.length === 0) return;
            
            // 반응형 너비 계산 - 큰 이미지 크기로 조정
            function getItemWidth() {
                if ($(window).width() <= 480) {
                    return 300; // 작은 모바일 (큰 이미지)
                } else if ($(window).width() <= 768) {
                    return 300; // 모바일 (큰 이미지)
                } else {
                    return 400; // 데스크톱 (2배 큰 이미지)
                }
            }
            
            var itemWidth = getItemWidth();
            var margin = 10; // margin 5px * 2
            var gap = 15; // CSS gap
            var totalItemWidth = itemWidth + margin + gap;
            
            var translateX = -(startIndex * totalItemWidth);
            
            // //console.log('showSlide:', {
            //     startIndex: startIndex,
            //     totalImages: totalImages,
            //     itemsPerPage: itemsPerPage,
            //     maxStartIndex: maxStartIndex,
            //     itemWidth: itemWidth,
            //     totalItemWidth: totalItemWidth,
            //     translateX: translateX
            // });
            
            $imageNav.css('transform', 'translateX(' + translateX + 'px)');
        }
        
        // 버튼 상태 업데이트 (무한 스크롤에서는 항상 활성화)
        function updateButtonState() {
            // 무한 스크롤에서는 버튼이 항상 활성화 상태
            $prevBtn.removeClass('disabled');
            $nextBtn.removeClass('disabled');
        }
        
        // 초기 버튼 상태 설정
        updateButtonState();
        
        // 서브 이미지가 itemsPerPage 이하인 경우 버튼 숨김 (무한 스크롤 불필요)
        if (totalImages <= itemsPerPage) {
            $prevBtn.hide();
            $nextBtn.hide();
        } else {
            // 무한 스크롤이 가능한 경우 버튼 표시
            $prevBtn.show();
            $nextBtn.show();
        }
        
        // 윈도우 리사이즈 이벤트
        $(window).on('resize', function() {
            var newItemsPerPage = getItemsPerPage();
            if (newItemsPerPage !== itemsPerPage) {
                itemsPerPage = newItemsPerPage;
                maxStartIndex = Math.max(0, totalImages - itemsPerPage);
                currentStartIndex = Math.min(currentStartIndex, maxStartIndex);
                
                // 애니메이션 없이 즉시 이동
                $imageNav.css('transition', 'none');
                showSlide(currentStartIndex);
                
                // 애니메이션 복원 (약간의 지연 후)
                setTimeout(function() {
                    $imageNav.css('transition', 'transform 0.5s ease-in-out');
                }, 50);
                
                updateButtonState();
                
                // 버튼 표시/숨김 재설정
                if (totalImages <= itemsPerPage) {
                    $prevBtn.hide();
                    $nextBtn.hide();
                } else {
                    $prevBtn.show();
                    $nextBtn.show();
                }
            }
        });
    });
}

// 빈 상태 표시
function showEmptyState() {
    var emptyHtml = `
        <div class="empty-state">
            <h3>등록된 홍보/행사가 없습니다.</h3>
            <p>관리자가 홍보/행사를 등록하면 여기에 표시됩니다.</p>
        </div>
    `;
    $('.promos .container').html(emptyHtml);
}

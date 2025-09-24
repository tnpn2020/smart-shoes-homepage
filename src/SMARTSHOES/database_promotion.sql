-- promotion 관리를 위한 데이터베이스 스키마

-- 1. promotion 메인 테이블
CREATE TABLE IF NOT EXISTS `promotion` (
    `idx` int(11) NOT NULL AUTO_INCREMENT,
    `event_name` varchar(255) NOT NULL COMMENT '행사명',
    `event_period` varchar(100) NOT NULL COMMENT '행사기간',
    `event_location` varchar(255) NOT NULL COMMENT '행사장소',
    `award_badge` varchar(100) DEFAULT NULL COMMENT '수상 배지 (예: 혁신상 수상)',
    `main_image` varchar(255) DEFAULT NULL COMMENT '메인 이미지 파일명',
    `content` text COMMENT '행사 내용 (서머노트)',
    `is_active` tinyint(1) DEFAULT 1 COMMENT '활성화 여부 (1: 활성, 0: 비활성)',
    `regdate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '등록일',
    `moddate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일',
    PRIMARY KEY (`idx`),
    KEY `idx_active` (`is_active`),
    KEY `idx_regdate` (`regdate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='홍보/행사 관리 테이블';

-- 2. promotion 서브 이미지 테이블 (0~20개)
CREATE TABLE IF NOT EXISTS `promotion_images` (
    `idx` int(11) NOT NULL AUTO_INCREMENT,
    `promotion_idx` int(11) NOT NULL COMMENT 'promotion 테이블 참조',
    `image_file` varchar(255) NOT NULL COMMENT '이미지 파일명',
    `image_alt` varchar(255) DEFAULT NULL COMMENT '이미지 alt 텍스트',
    `sort_order` int(11) DEFAULT 0 COMMENT '정렬 순서',
    `regdate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '등록일',
    PRIMARY KEY (`idx`),
    KEY `idx_promotion` (`promotion_idx`),
    KEY `idx_sort` (`sort_order`),
    FOREIGN KEY (`promotion_idx`) REFERENCES `promotion` (`idx`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='홍보/행사 서브 이미지 테이블';

-- 샘플 데이터 삽입
INSERT INTO `promotion` (`event_name`, `event_period`, `event_location`, `award_badge`, `content`) VALUES
('2025 스마트 헬스케어 엑스포', '2023년 10월 15일 - 10월 18일', '서울 코엑스', '혁신상 수상', '<p>관리자 텍스트 영역</p><p>스마트신발이 참가한 헬스케어 엑스포에서 혁신상을 수상했습니다.</p>'),
('2024 디지털 헬스 서밋', '2024년 5월 20일 - 5월 22일', '부산 벡스코', '우수상 수상', '<p>관리자 텍스트 영역</p><p>관리자 텍스트 영역</p><p>관리자 텍스트 영역</p><p>관리자 텍스트 영역</p><p>관리자 텍스트 영역</p><p>관리자 텍스트 영역</p>');

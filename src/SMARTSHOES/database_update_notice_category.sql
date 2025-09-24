-- 공지사항 카테고리 기능 추가를 위한 데이터베이스 스키마 업데이트

-- notice 테이블에 category 컬럼 추가
ALTER TABLE `notice` ADD COLUMN `category` VARCHAR(20) DEFAULT 'general' COMMENT '카테고리 (important: 중요, service: 서비스 안내, update: 업데이트, event: 이벤트)';

-- 기존 데이터에 대한 기본값 설정 (선택사항)
-- UPDATE `notice` SET `category` = 'general' WHERE `category` IS NULL;

-- 카테고리별 인덱스 추가 (성능 향상)
ALTER TABLE `notice` ADD INDEX `idx_category` (`category`);

-- 카테고리 값 확인을 위한 체크 제약 조건 (MySQL 8.0.16 이상)
-- ALTER TABLE `notice` ADD CONSTRAINT `chk_category` CHECK (`category` IN ('important', 'service', 'update', 'event'));

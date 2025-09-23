-- 홍보/행사 순서 변경 기능을 위한 데이터베이스 수정

-- 1. promotion 테이블에 sort_order 컬럼 추가
ALTER TABLE promotion ADD COLUMN sort_order INT DEFAULT 0 AFTER is_active;

-- 2. 기존 데이터에 순서 설정 (등록일 기준으로 자동 설정)
SET @row_number = 0;
UPDATE promotion 
SET sort_order = (@row_number := @row_number + 1) 
ORDER BY regdate ASC;

-- 3. sort_order 컬럼에 인덱스 추가 (성능 향상)
ALTER TABLE promotion ADD INDEX idx_sort_order (sort_order);

-- 확인 쿼리
SELECT idx, event_name, sort_order, regdate FROM promotion ORDER BY sort_order ASC;

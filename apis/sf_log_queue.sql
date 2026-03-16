-- ============================================================================
-- 스마트공장 로그 전송 큐 테이블
-- ============================================================================
-- 실행: mysql -u root -p chammat < apis/sf_log_queue.sql

CREATE TABLE IF NOT EXISTS sf_log_queue (
  uid        INT AUTO_INCREMENT PRIMARY KEY,
  logDt      VARCHAR(23)  NOT NULL COMMENT 'YYYY-MM-DD HH:MI:SS.SSS',
  useSe      VARCHAR(200) NOT NULL COMMENT '접속구분 (DO6001~DO6999)',
  sysUser    VARCHAR(60)  NOT NULL COMMENT '사용자 ID',
  conectIp   VARCHAR(30)  DEFAULT '' COMMENT '클라이언트 IP',
  dataUsgqty INT          DEFAULT 0 COMMENT '데이터사용량 (byte)',
  sentYn     CHAR(1)      DEFAULT 'N' COMMENT '전송여부 (N:미전송/Y:성공/F:실패)',
  sentDt     DATETIME     DEFAULT NULL COMMENT '전송시각',
  resultCd   VARCHAR(20)  DEFAULT NULL COMMENT '응답코드 (AP1002 등)',
  resultMsg  TEXT         DEFAULT NULL COMMENT '응답상세',
  regDt      DATETIME     DEFAULT CURRENT_TIMESTAMP COMMENT '등록일시',
  INDEX idx_sent (sentYn, logDt)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='스마트공장 로그 전송 큐';

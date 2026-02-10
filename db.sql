-- --------------------------------------------------------
-- 호스트:                          49.247.4.198
-- 서버 버전:                        10.11.13-MariaDB-0ubuntu0.24.04.1 - Ubuntu 24.04
-- 서버 OS:                        debian-linux-gnu
-- HeidiSQL 버전:                  12.11.0.7065
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- 테이블 mbiz.adminst 구조 내보내기
CREATE TABLE IF NOT EXISTS `adminst` (
  `uid` int(11) NOT NULL,
  `adminName` varchar(50) NOT NULL DEFAULT '0',
  `adminId` varchar(50) DEFAULT NULL,
  `adminPwd` varchar(255) DEFAULT NULL,
  `adminMobile` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='관리자';

-- 테이블 데이터 mbiz.adminst:~1 rows (대략적) 내보내기
INSERT INTO `adminst` (`uid`, `adminName`, `adminId`, `adminPwd`, `adminMobile`) VALUES
	(1, '최고관리자', 'admin', '$2y$10$WvP8j0dHwHHL4ZUB9JwyEODj/JfMuyn03dmFjjkjh.U3.U23mYYda', '1074536975');

-- 테이블 mbiz.error_log 구조 내보내기
CREATE TABLE IF NOT EXISTS `error_log` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `controller` varchar(50) DEFAULT NULL COMMENT '컨트롤러',
  `method` varchar(50) DEFAULT NULL COMMENT '메서드',
  `query` text DEFAULT NULL COMMENT '쿼리',
  `registDate` datetime DEFAULT NULL COMMENT '등록일시',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



-- 테이블 mbiz.mes_account 구조 내보내기
CREATE TABLE IF NOT EXISTS `mes_account` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `classification` varchar(50) NOT NULL COMMENT '구분',
  `name` varchar(255) NOT NULL,
  `taxNumber` varchar(255) DEFAULT NULL COMMENT '사업자번호',
  `bizNumber` varchar(50) DEFAULT NULL COMMENT '법인번호',
  `owner` varchar(255) DEFAULT NULL,
  `telephone` varchar(255) DEFAULT NULL,
  `fax` varchar(255) DEFAULT NULL,
  `mobile` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='거래처';

-- 테이블 mbiz.mes_account_classification 구조 내보내기
CREATE TABLE IF NOT EXISTS `mes_account_classification` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='거래처 매입,매출구분';

-- 테이블 mbiz.mes_blueprint 구조 내보내기
CREATE TABLE IF NOT EXISTS `mes_blueprint` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `account` int(11) NOT NULL COMMENT '거래처UID',
  `accountName` varchar(50) NOT NULL COMMENT '거래처명',
  `name` varchar(50) NOT NULL COMMENT '도면명',
  `blueprint` varchar(255) NOT NULL COMMENT '도면',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='도면';


-- 테이블 mbiz.mes_bom 구조 내보내기
CREATE TABLE IF NOT EXISTS `mes_bom` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `gid` int(11) DEFAULT NULL COMMENT '그룹 UID',
  `fid` int(11) DEFAULT NULL COMMENT '상위품목 UID',
  `depth` int(11) DEFAULT NULL COMMENT '깊이',
  `classification` varchar(50) DEFAULT NULL COMMENT '해당품목 구분',
  `itemUid` int(11) DEFAULT NULL COMMENT '해당 품목 UID',
  `code` varchar(50) DEFAULT NULL COMMENT '해당품목 품번',
  `name` varchar(50) DEFAULT NULL COMMENT '해당품목 품명',
  `standard` varchar(50) DEFAULT NULL COMMENT '해당품목 규격',
  `unit` varchar(50) DEFAULT NULL COMMENT '해당품목 단위',
  `qty` int(11) DEFAULT NULL COMMENT '소요량',
  PRIMARY KEY (`uid`),
  KEY `gid` (`gid`),
  KEY `fid` (`fid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 테이블 mbiz.mes_classification 구조 내보내기
CREATE TABLE IF NOT EXISTS `mes_classification` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '구분명',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='제품구분';

-- 테이블 데이터 mbiz.mes_classification:~3 rows (대략적) 내보내기
INSERT INTO `mes_classification` (`uid`, `name`) VALUES
	(1, '원자재'),
	(2, '반제품'),
	(3, '완제품');

-- 테이블 mbiz.mes_daily_work 구조 내보내기
CREATE TABLE IF NOT EXISTS `mes_daily_work` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `workDate` varchar(50) NOT NULL COMMENT '작업일자',
  `classification` varchar(50) NOT NULL COMMENT '품목구분',
  `item` int(11) NOT NULL COMMENT '품목 UID',
  `itemName` varchar(50) NOT NULL COMMENT '품명',
  `code` varchar(50) NOT NULL COMMENT '품번',
  `process` int(11) NOT NULL COMMENT '공정 UID',
  `processName` varchar(50) NOT NULL COMMENT '공정명',
  `employee` int(11) NOT NULL COMMENT '작업자UID',
  `employeeName` varchar(50) NOT NULL COMMENT '작업자명',
  `qty` int(11) NOT NULL COMMENT '작업수량',
  `defectiveQty` int(11) NOT NULL COMMENT '불량수량',
  `defectiveReason` varchar(50) NOT NULL COMMENT '불량사유',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='작업일지';

-- 테이블 데이터 mbiz.mes_daily_work:~0 rows (대략적) 내보내기

-- 테이블 mbiz.mes_day_power 구조 내보내기
CREATE TABLE IF NOT EXISTS `mes_day_power` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `year` int(11) DEFAULT NULL,
  `month` int(11) DEFAULT NULL,
  `day1` int(11) DEFAULT NULL,
  `day2` int(11) DEFAULT NULL,
  `day3` int(11) DEFAULT NULL,
  `day4` int(11) DEFAULT NULL,
  `day5` int(11) DEFAULT NULL,
  `day6` int(11) DEFAULT NULL,
  `day7` int(11) DEFAULT NULL,
  `day8` int(11) DEFAULT NULL,
  `day9` int(11) DEFAULT NULL,
  `day10` int(11) DEFAULT NULL,
  `day11` int(11) DEFAULT NULL,
  `day12` int(11) DEFAULT NULL,
  `day13` int(11) DEFAULT NULL,
  `day14` int(11) DEFAULT NULL,
  `day15` int(11) DEFAULT NULL,
  `day16` int(11) DEFAULT NULL,
  `day17` int(11) DEFAULT NULL,
  `day18` int(11) DEFAULT NULL,
  `day19` int(11) DEFAULT NULL,
  `day20` int(11) DEFAULT NULL,
  `day21` int(11) DEFAULT NULL,
  `day22` int(11) DEFAULT NULL,
  `day23` int(11) DEFAULT NULL,
  `day24` int(11) DEFAULT NULL,
  `day25` int(11) DEFAULT NULL,
  `day26` int(11) DEFAULT NULL,
  `day27` int(11) DEFAULT NULL,
  `day28` int(11) DEFAULT NULL,
  `day29` int(11) DEFAULT NULL,
  `day30` int(11) DEFAULT NULL,
  `day31` int(11) DEFAULT NULL,
  PRIMARY KEY (`uid`),
  KEY `year` (`year`),
  KEY `month` (`month`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 테이블 mbiz.mes_defective 구조 내보내기
CREATE TABLE IF NOT EXISTS `mes_defective` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '불량사유',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='불량사유';

-- 테이블 데이터 mbiz.mes_defective:~0 rows (대략적) 내보내기

-- 테이블 mbiz.mes_defective_report 구조 내보내기
CREATE TABLE IF NOT EXISTS `mes_defective_report` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `itemName` varchar(50) NOT NULL DEFAULT '0' COMMENT '품목명',
  `itemCode` varchar(50) NOT NULL DEFAULT '0' COMMENT '품번',
  `reason` varchar(50) DEFAULT NULL COMMENT '불량사유',
  `qty` int(11) DEFAULT NULL COMMENT '불량수량',
  `registerDate` date DEFAULT NULL COMMENT '등록일',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='불량통계용';

-- 테이블 데이터 mbiz.mes_defective_report:~0 rows (대략적) 내보내기

-- 테이블 mbiz.mes_defect_reason 구조 내보내기
CREATE TABLE IF NOT EXISTS `mes_defect_reason` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '불량 사유',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='불량 사유';

-- 테이블 mbiz.mes_department 구조 내보내기
CREATE TABLE IF NOT EXISTS `mes_department` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL COMMENT '부서명',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='부서';

-- 테이블 mbiz.mes_employee 구조 내보내기
CREATE TABLE IF NOT EXISTS `mes_employee` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '이름',
  `gender` varchar(10) NOT NULL COMMENT '성별',
  `rank` varchar(50) DEFAULT NULL COMMENT '직위',
  `department` varchar(50) DEFAULT NULL COMMENT '부서',
  `mobile` varchar(50) DEFAULT NULL COMMENT '휴대전화',
  `email` varchar(50) DEFAULT NULL COMMENT '이메일',
  `address` varchar(50) DEFAULT NULL COMMENT '주소',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='사원';

-- 테이블 mbiz.mes_inspect_report 구조 내보내기
CREATE TABLE IF NOT EXISTS `mes_inspect_report` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) NOT NULL COMMENT '설비UID',
  `name` varchar(50) NOT NULL COMMENT '설비명',
  `code` varchar(50) NOT NULL COMMENT '설비관리번호',
  `inspectPart` varchar(50) NOT NULL COMMENT '점검부위',
  `inspectName` varchar(50) NOT NULL COMMENT '점검항목',
  `inspectMethod` varchar(50) NOT NULL COMMENT '점검방법',
  `inspectComment` varchar(50) NOT NULL COMMENT '점검기준',
  `inspectResult` varchar(50) NOT NULL COMMENT '점검결과',
  `employee` int(11) NOT NULL COMMENT '점검자UID',
  `employeeName` varchar(50) NOT NULL COMMENT '점검자명',
  `inspectDate` date NOT NULL COMMENT '점검일',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='설비점검내역';

-- 테이블 데이터 mbiz.mes_inspect_report:~0 rows (대략적) 내보내기

-- 테이블 mbiz.mes_items 구조 내보내기
CREATE TABLE IF NOT EXISTS `mes_items` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `classification` varchar(50) DEFAULT NULL COMMENT '제품구분',
  `code` varchar(50) DEFAULT NULL COMMENT '코드',
  `name` varchar(50) DEFAULT NULL COMMENT '제품명',
  `standard` varchar(50) DEFAULT NULL COMMENT '규격',
  `unit` varchar(50) DEFAULT NULL COMMENT '단위',
  `stockQty` int(11) DEFAULT NULL COMMENT '재고수량',
  `safetyStockQty` int(11) DEFAULT NULL COMMENT '안전재고수량',
  `barcode` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='품목';

-- 테이블 mbiz.mes_items_inout 구조 내보내기
CREATE TABLE IF NOT EXISTS `mes_items_inout` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `classification` varchar(10) NOT NULL COMMENT '입고,출고 구분',
  `itemUid` int(11) NOT NULL COMMENT '품목UID',
  `itemName` varchar(50) NOT NULL COMMENT '품목명',
  `itemCode` varchar(50) NOT NULL COMMENT '품번',
  `itemStandard` varchar(50) NOT NULL COMMENT '품목규격',
  `itemUnit` varchar(10) NOT NULL COMMENT '품목단위',
  `qty` int(11) NOT NULL COMMENT '입출고수량',
  `registerDate` date NOT NULL COMMENT '등록일',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='자재수불부';

-- 테이블 mbiz.mes_kpi 구조 내보내기
CREATE TABLE IF NOT EXISTS `mes_kpi` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `classification` varchar(50) DEFAULT NULL COMMENT '분야',
  `indicator` varchar(50) DEFAULT NULL COMMENT '핵심성과지표',
  `unit` varchar(50) DEFAULT NULL COMMENT '단위',
  `pastValue` double DEFAULT NULL COMMENT '기준',
  `targetValue` double DEFAULT NULL COMMENT '목표',
  PRIMARY KEY (`uid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='KPI';

-- 테이블 mbiz.mes_machine 구조 내보내기
CREATE TABLE IF NOT EXISTS `mes_machine` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL COMMENT '설비명',
  `code` varchar(50) DEFAULT NULL COMMENT '관리번호',
  `maker` varchar(50) DEFAULT NULL COMMENT '제조업체',
  `makerContact` varchar(20) DEFAULT NULL COMMENT '제조업체 연락처',
  `purchaseYear` varchar(20) DEFAULT NULL COMMENT '구매년도',
  `attach` varchar(255) DEFAULT NULL COMMENT '사진',
  `mainOfficer` int(11) DEFAULT NULL COMMENT '관리사원(정) UID',
  `mainOfficerName` varchar(20) DEFAULT NULL COMMENT '관리사원(정) 이름',
  `subOfficer` int(11) DEFAULT NULL COMMENT '관리사원(부) UID',
  `subOfficerName` varchar(20) DEFAULT NULL COMMENT '관리사원(부) 이름',
  `sensor` varchar(50) DEFAULT NULL COMMENT '센서연결값',
  `ratedVoltage` int(11) DEFAULT NULL COMMENT '정격전압',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='설비';

-- 테이블 mbiz.mes_machine_component 구조 내보내기
CREATE TABLE IF NOT EXISTS `mes_machine_component` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) NOT NULL,
  `name` varchar(50) NOT NULL COMMENT '부품명',
  `standard` varchar(50) NOT NULL COMMENT '부품규격',
  `purchaseCompany` varchar(50) NOT NULL COMMENT '부품구매업체',
  `companyContact` varchar(50) NOT NULL COMMENT '부품구매업체 연락처',
  `qty` int(11) NOT NULL COMMENT '재고수량',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='설비 부품';

-- 테이블 데이터 mbiz.mes_machine_component:~0 rows (대략적) 내보내기

-- 테이블 mbiz.mes_machine_inspect 구조 내보내기
CREATE TABLE IF NOT EXISTS `mes_machine_inspect` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) NOT NULL,
  `part` varchar(255) NOT NULL COMMENT '점검 부위',
  `name` varchar(255) NOT NULL COMMENT '점검 항목',
  `method` varchar(255) NOT NULL COMMENT '점검 방법',
  `inspectDate` int(11) NOT NULL COMMENT '점검 주기',
  `comment` varchar(255) NOT NULL COMMENT '점검 기준',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='설비 점검 항목';

-- 테이블 데이터 mbiz.mes_machine_inspect:~0 rows (대략적) 내보내기

-- 테이블 mbiz.mes_machine_spec 구조 내보내기
CREATE TABLE IF NOT EXISTS `mes_machine_spec` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) NOT NULL COMMENT 'mes_machine UID',
  `name` varchar(100) NOT NULL COMMENT '스팩명',
  `value` varchar(255) NOT NULL COMMENT '스팩값',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='설비 스팩(제원)';

-- 테이블 데이터 mbiz.mes_machine_spec:~0 rows (대략적) 내보내기

-- 테이블 mbiz.mes_month_power 구조 내보내기
CREATE TABLE IF NOT EXISTS `mes_month_power` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `year` int(11) DEFAULT NULL,
  `power1` int(11) DEFAULT NULL,
  `price1` int(11) DEFAULT NULL,
  `power2` int(11) DEFAULT NULL,
  `price2` int(11) DEFAULT NULL,
  `power3` int(11) DEFAULT NULL,
  `price3` int(11) DEFAULT NULL,
  `power4` int(11) DEFAULT NULL,
  `price4` int(11) DEFAULT NULL,
  `power5` int(11) DEFAULT NULL,
  `price5` int(11) DEFAULT NULL,
  `power6` int(11) DEFAULT NULL,
  `price6` int(11) DEFAULT NULL,
  `power7` int(11) DEFAULT NULL,
  `price7` int(11) DEFAULT NULL,
  `power8` int(11) DEFAULT NULL,
  `price8` int(11) DEFAULT NULL,
  `power9` int(11) DEFAULT NULL,
  `price9` int(11) DEFAULT NULL,
  `power10` int(11) DEFAULT NULL,
  `price10` int(11) DEFAULT NULL,
  `power11` int(11) DEFAULT NULL,
  `price11` int(11) DEFAULT NULL,
  `power12` int(11) DEFAULT NULL,
  `price12` int(11) DEFAULT NULL,
  PRIMARY KEY (`uid`),
  KEY `year` (`year`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='월별전력사용량 및 요금';

-- 테이블 mbiz.mes_orders 구조 내보내기
CREATE TABLE IF NOT EXISTS `mes_orders` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `accountUid` int(11) NOT NULL COMMENT '거래처UID',
  `accountName` varchar(50) NOT NULL COMMENT '거래처명',
  `itemUid` int(11) NOT NULL COMMENT '품목UID',
  `itemName` varchar(50) NOT NULL COMMENT '품명',
  `qty` int(11) NOT NULL COMMENT '수주수량',
  `orderDate` varchar(50) NOT NULL COMMENT '수주일',
  `shipmentDate` varchar(50) NOT NULL COMMENT '납기일',
  `shipmentPlace` varchar(255) NOT NULL COMMENT '납품장소',
  `memo` text NOT NULL COMMENT '메모',
  `status` varchar(50) DEFAULT '',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='수주';

-- 테이블 mbiz.mes_power 구조 내보내기
CREATE TABLE IF NOT EXISTS `mes_power` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `machineUid` varchar(50) DEFAULT NULL COMMENT '설비번호',
  `value` double DEFAULT NULL COMMENT '전력값',
  `registDate` datetime DEFAULT NULL COMMENT '측정일시',
  PRIMARY KEY (`uid`),
  KEY `machineUid` (`machineUid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='전력량';

-- 테이블 mbiz.mes_process 구조 내보내기
CREATE TABLE IF NOT EXISTS `mes_process` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `lastProcess` varchar(50) NOT NULL COMMENT '생산입고시킬 마지막 공정인자',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='작업공정';

-- 테이블 mbiz.mes_rank 구조 내보내기
CREATE TABLE IF NOT EXISTS `mes_rank` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL COMMENT '직급명',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='직급';

-- 테이블 mbiz.mes_setting 구조 내보내기
CREATE TABLE IF NOT EXISTS `mes_setting` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `enableBom` char(1) DEFAULT NULL COMMENT 'BOM사용여부',
  `minusStockCount` char(1) DEFAULT NULL COMMENT '재고수량 마이너스 표기',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='환경설정값';

-- 테이블 데이터 mbiz.mes_setting:~0 rows (대략적) 내보내기
INSERT INTO `mes_setting` (`uid`, `enableBom`, `minusStockCount`) VALUES
	(1, 'Y', 'N');

-- 테이블 mbiz.mes_shipment 구조 내보내기
CREATE TABLE IF NOT EXISTS `mes_shipment` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) NOT NULL COMMENT 'Shipment Order Uid',
  `shipmentDate` date NOT NULL COMMENT '출하일',
  `account` varchar(50) NOT NULL COMMENT '거래처UID',
  `accountName` varchar(50) NOT NULL COMMENT '거래처명',
  `item` varchar(50) NOT NULL COMMENT '품목UID',
  `itemName` varchar(50) NOT NULL COMMENT '품명',
  `code` varchar(50) NOT NULL COMMENT '품번',
  `standard` varchar(50) NOT NULL COMMENT '품목규격',
  `address` varchar(255) NOT NULL COMMENT '배송지',
  `qty` int(11) NOT NULL COMMENT '출하수량',
  `loginId` varchar(50) NOT NULL COMMENT '등록 아이디',
  `registerDate` datetime NOT NULL COMMENT '등록일시',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='출하내역';

-- 테이블 mbiz.mes_shipment_order 구조 내보내기
CREATE TABLE IF NOT EXISTS `mes_shipment_order` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `classification` varchar(20) NOT NULL COMMENT '품목구분',
  `item` int(11) NOT NULL COMMENT '품목UID',
  `itemName` varchar(50) NOT NULL COMMENT '품명',
  `code` varchar(50) NOT NULL COMMENT '품번',
  `standard` varchar(50) NOT NULL COMMENT '품목규격',
  `shipmentDate` date NOT NULL COMMENT '출하지시일',
  `account` int(11) NOT NULL COMMENT '거래처UID',
  `accountName` varchar(50) NOT NULL COMMENT '거래처명',
  `address` varchar(255) NOT NULL COMMENT '배송지',
  `qty` int(11) NOT NULL COMMENT '출하지시수량',
  `remainQty` int(11) NOT NULL COMMENT '잔여출하수량',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='출하지시';

-- 테이블 mbiz.mes_unit 구조 내보내기
CREATE TABLE IF NOT EXISTS `mes_unit` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL COMMENT '단위명',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='제품단위';

-- 테이블 데이터 mbiz.mes_unit:~5 rows (대략적) 내보내기
INSERT INTO `mes_unit` (`uid`, `name`) VALUES
	(1, 'ea'),
	(2, 'kg'),
	(3, 'g'),
	(4, 'cm'),
	(5, 'km');

-- 테이블 mbiz.mes_user 구조 내보내기
CREATE TABLE IF NOT EXISTS `mes_user` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `employee` int(11) NOT NULL COMMENT '사원 UID',
  `employeeName` varchar(50) NOT NULL COMMENT '사원명',
  `loginId` varchar(50) NOT NULL COMMENT '로그인 아이디',
  `loginPwd` varchar(255) NOT NULL COMMENT '로그인 비밀번호',
  `auth` int(11) NOT NULL COMMENT '권한',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='시스템 사용자';

-- 테이블 데이터 mbiz.mes_user:~0 rows (대략적) 내보내기

-- 테이블 mbiz.mes_user_login 구조 내보내기
CREATE TABLE IF NOT EXISTS `mes_user_login` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `loginId` varchar(50) DEFAULT NULL,
  `registerDate` datetime DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='로그인 기록정보';

-- 테이블 mbiz.mes_weekly_product 구조 내보내기
CREATE TABLE IF NOT EXISTS `mes_weekly_product` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `itemUid` int(11) NOT NULL DEFAULT 0 COMMENT '생산품목 UID',
  `name` varchar(50) NOT NULL DEFAULT '0' COMMENT '생산품목명',
  `code` varchar(50) NOT NULL DEFAULT '0' COMMENT '생산품목코드',
  `standard` varchar(50) NOT NULL DEFAULT '0' COMMENT '생산품목규격',
  `unit` varchar(50) NOT NULL DEFAULT '0' COMMENT '생산품목단위',
  `qty` int(11) DEFAULT NULL COMMENT '생산수량',
  `productDate` date DEFAULT NULL COMMENT '생산일',
  PRIMARY KEY (`uid`),
  KEY `productDate` (`productDate`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='매일 생산량 기록';

-- 테이블 mbiz.mes_work_order 구조 내보내기
CREATE TABLE IF NOT EXISTS `mes_work_order` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `classification` varchar(20) DEFAULT NULL COMMENT '품목구분',
  `itemUid` int(11) DEFAULT NULL COMMENT '품목UID',
  `name` varchar(50) DEFAULT NULL COMMENT '품목명',
  `code` varchar(50) DEFAULT NULL COMMENT '품번',
  `standard` varchar(50) DEFAULT NULL COMMENT '규격',
  `process` varchar(255) DEFAULT NULL COMMENT '배포공정',
  `orderQty` int(11) DEFAULT NULL COMMENT '작업지시수량',
  `startDate` date DEFAULT NULL COMMENT '작업시작일',
  `endDate` date DEFAULT NULL COMMENT '작업종료일',
  `memo` text DEFAULT NULL COMMENT '메모',
  `remainQty` int(11) DEFAULT NULL COMMENT '잔여작업수량',
  `status` varchar(20) DEFAULT NULL COMMENT '진행상태',
  `registerDate` date DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='작업지시서';

-- 테이블 mbiz.mes_work_report 구조 내보내기
CREATE TABLE IF NOT EXISTS `mes_work_report` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) NOT NULL COMMENT 'work_order UID',
  `itemUid` int(11) NOT NULL COMMENT '품목 UID',
  `name` varchar(50) NOT NULL COMMENT '품명',
  `code` varchar(50) NOT NULL COMMENT '품번',
  `standard` varchar(50) NOT NULL COMMENT '규격',
  `process` int(11) NOT NULL COMMENT '공정 UID',
  `processName` varchar(50) NOT NULL COMMENT '작업공정명',
  `productQty` int(11) NOT NULL COMMENT '총생산량',
  `qty` int(11) NOT NULL COMMENT '적합생산량',
  `defectQty` int(11) NOT NULL COMMENT '부적합생산량',
  `defectReason` int(11) NOT NULL COMMENT '부적합사유 UID',
  `defectReasonName` varchar(50) NOT NULL COMMENT '부적합사유',
  `employee` int(11) NOT NULL COMMENT '작업자UID',
  `employeeName` varchar(50) NOT NULL COMMENT '작업자명',
  `workDate` date NOT NULL COMMENT '작업일자',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='작업일보 (생산실적)';

-- 테이블 데이터 mbiz.mes_work_report:~0 rows (대략적) 내보내기

-- 테이블 mbiz.system_admin 구조 내보내기
CREATE TABLE IF NOT EXISTS `system_admin` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `adminId` varchar(50) DEFAULT NULL,
  `adminPwd` varchar(255) DEFAULT NULL,
  `logo` varchar(255) NOT NULL COMMENT '로고(로고텍스트)',
  PRIMARY KEY (`uid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 테이블 데이터 mbiz.system_admin:~0 rows (대략적) 내보내기
INSERT INTO `system_admin` (`uid`, `adminId`, `adminPwd`, `logo`) VALUES
	(1, 'sysadmin', '$2y$10$oJytfOqujALdZmYtCZlD9OPsANGU3eq44L5UmkAsFbfTo6sWACjrG', 'DainLab');

<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("controllers/functions.php");

class Electricity extends Functions {       
    private $param;
	private $now;
	private $nowTime;
    private $response = [];   
     
    public function __construct($param) {
        $this->param = $param;
		$this->now = date("Y-m-d");
		$this->nowTime = date("Y-m-d H:i:s");
    }

    // 년도별 월간 전력 사용량 데이터
    public function getYearlyData() {
        $year = isset($this->param['year']) ? intval($this->param['year']) : date('Y');
    
        $query = "
            SELECT 
                DATE_FORMAT(registDate, '%Y-%m') as month,
                SUM(value) as totalPower
            FROM mes_power
            WHERE YEAR(registDate) = '$year'
            GROUP BY DATE_FORMAT(registDate, '%Y-%m')
            ORDER BY DATE_FORMAT(registDate, '%Y-%m')
        ";
    
        $this->query($query);
        $results = $this->fetchAll();
    
        // 월 데이터를 초기화 (1월 ~ 12월 기본값 0)
        $monthlyData = array_fill(1, 12, 0);
        // 쿼리 결과 데이터를 월별로 매핑
        foreach ($results as $row) {
            $month = (int) substr($row['month'], 5, 2); // 'YYYY-MM'에서 MM 추출
            $monthlyData[$month] = (float) $row['totalPower'];
        }
    
        // 응답 데이터 생성
        $response = [
            'totalCount' => count($results),
            'data' => array_map(function ($month, $value) {
                return [
                    'month' => $month . '월', // '1월', '2월' 형식
                    'totalPower' => $value   // 해당 월의 전력 사용량
                ];
            }, array_keys($monthlyData), $monthlyData)
        ];
    
        header('Content-Type: application/json');
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function getYearlyComparisonData() {
        $currentYear = isset($this->param['year']) ? intval($this->param['year']) : date('Y');
        $previousYear = $currentYear - 1;
        
        // 현재 년도와 이전 년도의 데이터를 모두 가져오는 쿼리
        $query = "
            SELECT 
                YEAR(registDate) as year,
                DATE_FORMAT(registDate, '%Y-%m') as month,
                SUM(value) as totalPower
            FROM mes_power
            WHERE YEAR(registDate) IN ('$currentYear', '$previousYear')
            GROUP BY YEAR(registDate), DATE_FORMAT(registDate, '%Y-%m')
            ORDER BY DATE_FORMAT(registDate, '%Y-%m')
        ";
        
        $this->query($query);
        $results = $this->fetchAll();
        
        // 현재 년도와 이전 년도 데이터를 저장할 배열 (1-12월)
        $currentYearData = array_fill(1, 12, 0);
        $previousYearData = array_fill(1, 12, 0);
        
        // 쿼리 결과를 년도별로 분류하여 배열에 매핑
        foreach ($results as $row) {
            $month = (int) substr($row['month'], 5, 2);
            if ($row['year'] == $currentYear) {
                $currentYearData[$month] = (float) $row['totalPower'];
            } else {
                $previousYearData[$month] = (float) $row['totalPower'];
            }
        }
        
        // 응답 데이터 생성
        $response = [
            'totalCount' => count($results),
            'data' => array_map(function ($month) use ($currentYearData, $previousYearData, $currentYear) {
                $currentValue = $currentYearData[$month];
                $previousValue = $previousYearData[$month];
                
                // 증감률 계산 (이전 년도 데이터가 0일 경우 0으로 설정)
                $growthRate = 0;
                if ($previousValue > 0) {
                    $growthRate = round((($currentValue - $previousValue) / $previousValue) * 100, 2);
                }
                
                return [
                    'month' => $month . '월',
                    'currentYear' => [
                        'year' => $currentYear,
                        'totalPower' => $currentValue
                    ],
                    'previousYear' => [
                        'year' => $currentYear - 1,
                        'totalPower' => $previousValue
                    ],
                    'difference' => round($currentValue - $previousValue, 2),
                    'growthRate' => $growthRate
                ];
            }, array_keys($currentYearData))
        ];
        
        header('Content-Type: application/json');
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function getRealPowerData() {
        // mes_power 테이블에서 각 machineUid의 가장 최근 데이터를 가져오는 쿼리
        $query = "
            SELECT machineUid, value, registDate
            FROM mes_power
            WHERE (machineUid, registDate) IN (
                SELECT machineUid, MAX(registDate)
                FROM mes_power
                GROUP BY machineUid
            )
            ORDER BY machineUid
        ";
    
        $this->query($query);
        $results = $this->fetchAll();
    
        // 데이터 정리
        $response = [];
        foreach ($results as $row) {
            // M001 -> 0, M002 -> 1, ...
            $machineIndex = intval(substr($row['machineUid'], 1)) - 1; // 0부터 시작하도록 조정
            $response["myChart{$machineIndex}"] = (float) $row['value'];
        }
    
        // 남은 myChart가 있으면 0으로 초기화
        for ($i = 0; $i <= 6; $i++) {
            if (!isset($response["myChart{$i}"])) {
                $response["myChart{$i}"] = 0;
            }
        }
    
        // JSON 응답
        header('Content-Type: application/json');
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
    
    

    // 시간대별
    public function getHourlyComparisonData() {
        // 클라이언트에서 'year' 파라미터가 제공되지 않으면 현재 년도를 사용
        $currentYear = isset($this->param['year']) ? intval($this->param['year']) : date('Y');
        $previousYear = $currentYear - 1;
    
        // 현재 년도와 이전 년도의 시간대별 데이터를 가져오는 쿼리
        $query = "
            SELECT 
                YEAR(registDate) as year,
                DATE_FORMAT(registDate, '%H:00') as hour,  -- 시간만 추출
                SUM(value) as totalPower
            FROM mes_power
            WHERE YEAR(registDate) IN ('$currentYear', '$previousYear')
            GROUP BY YEAR(registDate), DATE_FORMAT(registDate, '%H:00')
            ORDER BY DATE_FORMAT(registDate, '%H:00')
        ";
    
        $this->query($query);
        $results = $this->fetchAll();
    
        // 현재 년도와 이전 년도의 데이터를 저장할 배열 (24시간)
        $currentYearData = array_fill(0, 24, 0);
        $previousYearData = array_fill(0, 24, 0);
    
        // 쿼리 결과를 년도별로 분류하여 배열에 매핑
        foreach ($results as $row) {
            $hour = (int) substr($row['hour'], 0, 2);
            if ($row['year'] == $currentYear) {
                $currentYearData[$hour] = (float) $row['totalPower'];
            } else {
                $previousYearData[$hour] = (float) $row['totalPower'];
            }
        }
    
        // 응답 데이터 생성
        $response = [
            'totalCount' => count($results),
            'data' => array_map(function ($hour) use ($currentYearData, $previousYearData, $currentYear) {
                $currentValue = $currentYearData[$hour];
                $previousValue = $previousYearData[$hour];
    
                // 증감률 계산 (이전 년도 데이터가 0일 경우 0으로 설정)
                $growthRate = 0;
                if ($previousValue > 0) {
                    $growthRate = round((($currentValue - $previousValue) / $previousValue) * 100, 2);
                }
    
                return [
                    'hour' => sprintf('%02d:00', $hour),  // 시간대 포맷 (예: 00:00, 01:00, ...)
                    'currentYear' => [
                        'year' => $currentYear,
                        'totalPower' => $currentValue
                    ],
                    'previousYear' => [
                        'year' => $currentYear - 1,
                        'totalPower' => $previousValue
                    ],
                    'difference' => round($currentValue - $previousValue, 2),
                    'growthRate' => $growthRate
                ];
            }, array_keys($currentYearData))
        ];
    
        header('Content-Type: application/json');
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
    
    public function getHourlyData() {
        // 2024년도 데이터만 가져오기
        $currentYear = date('Y'); // 현재년도 (2024년)
        
        // 2024년도 시간대별 전력 사용량 데이터를 구하는 쿼리
        $query = "
            SELECT 
                HOUR(registDate) as hour,
                SUM(value) as totalPower
            FROM mes_power
            WHERE YEAR(registDate) = '$currentYear'
            GROUP BY HOUR(registDate)
            ORDER BY HOUR(registDate)
        ";
        
        $this->query($query);
        $results = $this->fetchAll();
        
        // 결과를 시간대별로 매핑
        $hourlyData = [];
        foreach ($results as $row) {
            $hourlyData[] = [
                'hour' => str_pad($row['hour'], 2, '0', STR_PAD_LEFT) . ':00', // 시간대를 "00:00" 형식으로
                'currentYear' => [
                    'year' => $currentYear,
                    'totalPower' => (float) $row['totalPower']
                ]
            ];
        }
        
        // 응답 데이터 생성
        $response = [
            'totalCount' => count($results),
            'data' => $hourlyData
        ];
        
        // JSON 형식으로 응답
        header('Content-Type: application/json');
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }



    public function getMonthlyComparisonData() {
        $currentYear = isset($this->param['year']) ? intval($this->param['year']) : date('Y');
        $currentMonth = isset($this->param['month']) ? intval($this->param['month']) : date('m');
        $previousYear = $currentYear - 1;
    
        // 현재 연도와 전년도 동일 월의 데이터를 mes_day_power에서 가져오기
        $query = "
            SELECT * 
            FROM mes_day_power
            WHERE (year = {$currentYear} OR year = {$previousYear}) 
              AND month = {$currentMonth}
        ";
        $this->query($query);
    
        $results = $this->fetchAll();
    
        // 데이터를 년도별로 분리
        $currentYearData = [];
        $previousYearData = [];
    
        foreach ($results as $row) {
            for ($day = 1; $day <= 31; $day++) {
                $field = "day{$day}";
                if (isset($row[$field]) && $row[$field] !== null) {
                    if ($row['year'] == $currentYear) {
                        $currentYearData[$day] = (float)$row[$field];
                    } else if ($row['year'] == $previousYear) {
                        $previousYearData[$day] = (float)$row[$field];
                    }
                }
            }
        }
    
        // 해당 월의 일 수 가져오기
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear);
        $allDays = range(1, $daysInMonth);
    
        // 응답 데이터 생성
        $response = [
            'totalCount' => count($allDays),
            'data' => array_map(function ($day) use ($currentYearData, $previousYearData, $currentYear, $previousYear) {
                $currentValue = $currentYearData[$day] ?? 0;
                $previousValue = $previousYearData[$day] ?? 0;
    
                // 성장률 계산
                $growthRate = ($previousValue > 0) ? round((($currentValue - $previousValue) / $previousValue) * 100, 2) : 0;
    
                return [
                    'day' => $day,
                    'currentYear' => [
                        'year' => $currentYear,
                        'totalPower' => $currentValue
                    ],
                    'previousYear' => [
                        'year' => $previousYear,
                        'totalPower' => $previousValue
                    ],
                    'difference' => round($currentValue - $previousValue, 2),
                    'growthRate' => $growthRate
                ];
            }, $allDays)
        ];
    
        header('Content-Type: application/json');
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
    
    
    
    public function getYearlyMonthlyData() {
        $currentYear = isset($this->param['year']) ? intval($this->param['year']) : date('Y');
    
        // 특정 연도의 월별 전력 사용량 쿼리
        $query = "
            SELECT 
                MONTH(registDate) as month,
                SUM(value) as totalPower
            FROM mes_power
            WHERE YEAR(registDate) = '$currentYear'
            GROUP BY MONTH(registDate)
            ORDER BY MONTH(registDate)
        ";
    
        $this->query($query);
        $results = $this->fetchAll();
    
        // 결과를 JSON 형식으로 변환
        $response = [
            'totalCount' => count($results),
            'data' => array_map(function ($row) {
                return [
                    'month' => (int) $row['month'],
                    'totalPower' => (float) $row['totalPower']
                ];
            }, $results)
        ];
    
        header('Content-Type: application/json');
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
    
    public function getYearlyMonthlyComparisonData() {
        $currentYear = date('Y');
        $previousYear = $currentYear - 1;
    
        $query = "
            SELECT * 
            FROM mes_month_power
            WHERE year IN ('$currentYear', '$previousYear')
            ORDER BY year ASC
        ";
        $this->query($query);
        $results = $this->fetchAll();
    
        // 데이터를 연도별로 분류
        $data = [
            'currentYear' => [],
            'previousYear' => []
        ];
    
        foreach ($results as $row) {
            $year = $row['year'];
    
            for ($month = 1; $month <= 12; $month++) {
                $field = "power{$month}";
                $value = isset($row[$field]) ? (float)$row[$field] : 0;
    
                if ($year == $currentYear) {
                    $data['currentYear'][$month] = $value;
                } elseif ($year == $previousYear) {
                    $data['previousYear'][$month] = $value;
                }
            }
        }
    
        // JSON 데이터 구성
        $response = [];
        for ($month = 1; $month <= 12; $month++) {
            $response[] = [
                'month' => $month,
                'currentYear' => ['totalPower' => $data['currentYear'][$month] ?? 0],
                'previousYear' => ['totalPower' => $data['previousYear'][$month] ?? 0]
            ];
        }
    
        echo json_encode(['data' => $response], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
    
    
    //========================================================================================
    // 연도별 전력 사용량 정보
    //========================================================================================
    public function getFiveYearsMonthlyData() {
        $currentYear = isset($this->param['year']) ? intval($this->param['year']) : date('Y');
        $startYear = $currentYear - 4;
    
        // 최근 5년간 데이터를 가져오는 쿼리
        $query = "
            SELECT 
                year,
                power1, power2, power3, power4, power5, power6,
                power7, power8, power9, power10, power11, power12
            FROM mes_month_power
            WHERE year BETWEEN '$startYear' AND '$currentYear'
        ";
    
        $this->query($query);
        $results = $this->fetchAll();
    
        // 데이터 정리
        $response = [];

        // 연도별 데이터 생성
        foreach ($results as $row) {
            $year = (int) $row['year'];

            for ($month = 1; $month <= 12; $month++) {
                $field = "power{$month}";
                $response[$year][$month] = isset($row[$field]) ? (float) $row[$field] : 0;
            }
        }
    
        // 결과 출력
        header('Content-Type: application/json');
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    // 당해년도 월간 데이터
    public function getCurrentYearMonthlyData() {
        $currentYear = isset($this->param['year']) ? intval($this->param['year']) : date('Y');
    
        // 당해년도 데이터를 가져오는 쿼리
        $query = "
            SELECT 
                year,
                power1, power2, power3, power4, power5, power6,
                power7, power8, power9, power10, power11, power12
            FROM mes_month_power
            WHERE year = {$currentYear}
        ";
    
        $this->query($query);
        $result = $this->fetch();
    
        // 데이터 정리
        $response = [
            'year' => $currentYear,
            'monthlyData' => []
        ];
    
        if ($result) {
            for ($month = 1; $month <= 12; $month++) {
                $field = "power{$month}";
                $response['monthlyData'][$month] = isset($result[$field]) ? (float) $result[$field] : 0;
            }
        }
    
        // 결과 출력
        header('Content-Type: application/json');
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
    
    
    // 년도별 텍스용
    public function getFiveYearsSumData() {
        $currentYear = date('Y');
        $lastYear = $currentYear - 1;
    
        // 올해 전력 사용량 합계 쿼리
        $queryCurrentYear = "
            SELECT 
                SUM(power1 + power2 + power3 + power4 + power5 + power6 + 
                    power7 + power8 + power9 + power10 + power11 + power12) AS totalPower
            FROM mes_month_power
            WHERE year = '$currentYear'
        ";
        $this->query($queryCurrentYear);
        $currentYearResult = $this->fetch();
    
        // 작년 전력 사용량 합계 쿼리
        $queryLastYear = "
            SELECT 
                SUM(power1 + power2 + power3 + power4 + power5 + power6 + 
                    power7 + power8 + power9 + power10 + power11 + power12) AS totalPower
            FROM mes_month_power
            WHERE year = '$lastYear'
        ";
        $this->query($queryLastYear);
        $lastYearResult = $this->fetch();
    
        // 월평균 계산 (현재 연도 데이터가 없으면 0 처리)
        $currentYearTotal = (float) ($currentYearResult['totalPower'] ?? 0);
        $monthlyAverage = $currentYearTotal / 12;
    
        // 결과 데이터 구성
        $this->response = [
            'currentYearTotal' => $currentYearTotal,           // 올해 전력 사용량 합계
            'lastYearTotal' => (float) ($lastYearResult['totalPower'] ?? 0), // 작년 전력 사용량 합계
            'monthlyAverage' => round($monthlyAverage, 2)      // 월평균 사용량 (소수점 2자리)
        ];
    
        // JSON 출력
        echo json_encode($this->response);
    }
    
    public function getMonthlyPowerData() {
        // 쿼리 실행
        $query = "SELECT * FROM mes_month_power ORDER BY year ASC";
        $this->query($query);
        $results = $this->fetchAll();
        
        // 응답 데이터 구성
        $this->response = array_map(function($data) {
            // power1 ~ power12의 합계 계산
            $totalPower = (float) $data['power1'] + (float) $data['power2'] + (float) $data['power3'] +
                          (float) $data['power4'] + (float) $data['power5'] + (float) $data['power6'] +
                          (float) $data['power7'] + (float) $data['power8'] + (float) $data['power9'] +
                          (float) $data['power10'] + (float) $data['power11'] + (float) $data['power12'];
    
            // price1 ~ price12의 합계 계산
            $totalPrice = (float) $data['price1'] + (float) $data['price2'] + (float) $data['price3'] +
                          (float) $data['price4'] + (float) $data['price5'] + (float) $data['price6'] +
                          (float) $data['price7'] + (float) $data['price8'] + (float) $data['price9'] +
                          (float) $data['price10'] + (float) $data['price11'] + (float) $data['price12'];
    
            return [
                'uid' => $data['uid'],
                'year' => $data['year'],
                'power1' => $data['power1'],
                'power2' => $data['power2'],
                'power3' => $data['power3'],
                'power4' => $data['power4'],
                'power5' => $data['power5'],
                'power6' => $data['power6'],
                'power7' => $data['power7'],
                'power8' => $data['power8'],
                'power9' => $data['power9'],
                'power10' => $data['power10'],
                'power11' => $data['power11'],
                'power12' => $data['power12'],
                'totalPower' => $totalPower, // 전력 총합 추가
                'price1' => $data['price1'],
                'price2' => $data['price2'],
                'price3' => $data['price3'],
                'price4' => $data['price4'],
                'price5' => $data['price5'],
                'price6' => $data['price6'],
                'price7' => $data['price7'],
                'price8' => $data['price8'],
                'price9' => $data['price9'],
                'price10' => $data['price10'],
                'price11' => $data['price11'],
                'price12' => $data['price12'],
                'totalPrice' => $totalPrice // 가격 총합 추가
            ];
        }, $results);
        
        // JSON 응답 반환
        echo json_encode($this->response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
    
    
    

    public function getPeakPowerData() {
        if (!isset($this->param['code']) || empty($this->param['code'])) {
            echo json_encode(['error' => 'Machine UIDs are required']);
            return;
        }
    
        // 여러 설비의 machineUid 값들을 배열로 받기
        $machineUids = explode(',', $this->param['code']);
    
        $responseData = [];
        
        // 전압(V)와 역률(PF)을 설정 (필요시 동적으로 받도록 수정 가능)
        $voltage = 220; // 기본 전압 (단상 220V)
        $powerFactor = 0.8; // 기본 역률 (0.9)
    
        // 각 설비의 최신 데이터를 가져옴
        foreach ($machineUids as $machineUid) {
            $query = "
                SELECT 
                    value
                FROM mes_power
                WHERE 
                    machineUid = '{$machineUid}'
                ORDER BY registDate DESC
                LIMIT 1
            ";
    
            $this->query($query);
            $result = $this->fetch(); // 최신 데이터 1개만 반환
    
            if ($result) {
                $current = floatval($result['value']); // 암페어 값
                $powerKw = ($current * $voltage * $powerFactor) / 1000; // kW로 변환
                $responseData[] = round($powerKw, 2); // 소수점 2자리로 반올림
            } else {
                // 데이터가 없을 경우 랜덤 암페어 값을 생성하고 변환
                $current = rand(10, 100);
                $powerKw = ($current * $voltage * $powerFactor) / 1000; // kW로 변환
                $responseData[] = round($powerKw, 2); // 소수점 2자리로 반올림
            }
        }
    
        // JSON 응답
        header('Content-Type: application/json');
        echo json_encode($responseData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
    
    
    
    
    
    

    
    





    public function registerPowerData() {
        // 필수 파라미터 확인
        if (empty($this->param['year']) || empty($this->param['month']) || empty($this->param['day']) || empty($this->param['power']) || empty($this->param['machine'])) {
            $this->response = [
                'result' => 'error',
                'message' => '필수 파라미터가 누락되었습니다'
            ];
            echo json_encode($this->response);
            return;
        }
    
        // 전달받은 파라미터 할당
        $year = $this->param['year'];
        $month = $this->param['month'];
        $day = $this->param['day'];
        $machine = $this->param['machine'];
        $dailyPowerKW = $this->param['power'];
    
        // 전환에 필요한 상수 값
        $voltage = 220; // 전압 (단상 220V로 가정)
        $powerFactor = 0.8; // 역률 (기본값)
    
        // kW를 A로 변환
        $dailyPowerA = ($dailyPowerKW * 1000) / ($voltage * $powerFactor);
    
        // 하루 총 근무 시간: 9시간 - 1시간(점심시간) = 8시간
        $workingSeconds = (8 * 60 * 60); // 총 28,800초
        $totalIntervals = $workingSeconds / 10; // 10초 간격으로 나눔
        $currentPerInterval = $dailyPowerA / $totalIntervals;
    
        $successCount = 0;
    
        // 전달된 날짜가 토요일 또는 일요일인 경우, 처리하지 않고 메시지 출력
        $date = new DateTime("$year-$month-$day");
        $dayOfWeek = $date->format('N'); // 1(월요일) ~ 7(일요일)
    
        if ($dayOfWeek >= 6) { // 토요일(6) 또는 일요일(7)인 경우
            $this->response = [
                'result' => 'error',
                'message' => '공휴일에는 데이터를 등록할 수 없습니다.'
            ];
            echo json_encode($this->response);
            return;
        }
    
        // 근무 시간(9시~18시, 점심시간 제외) 데이터 생성
        for ($hour = 9; $hour < 18; $hour++) {
            if ($hour == 12) continue; // 점심시간 건너뛰기
    
            for ($minute = 0; $minute < 60; $minute++) {
                for ($second = 0; $second < 60; $second += 10) {
                    // 시간당 전력량에 ±10% 변동값 추가
                    $variation = mt_rand(-10, 10) / 100.0; // -10% ~ +10%
                    $adjustedCurrent = $currentPerInterval * (1 + $variation);
    
                    // 데이터 준비
                    $registDate = sprintf(
                        "%04d-%02d-%02d %02d:%02d:%02d",
                        $year, $month, $day, $hour, $minute, $second
                    );
    
                    $data = [
                        'machine' => $machine,
                        'registDate' => $registDate,
                        'value' => round($adjustedCurrent, 2) // 소수점 둘째 자리까지
                    ];
    
                    // 데이터 삽입
                    if ($this->insertPower($data)) {
                        $successCount++;
                    }
                }
            }
        }
    
        // 결과 반환
        if ($successCount > 0) {
            $this->response = [
                'result' => 'success',
                'message' => '등록이 완료되었습니다',
                'inserted_count' => $successCount
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => '등록에 실패하였습니다'
            ];
        }
    
        echo json_encode($this->response);
    }
    
    
    
    // 해당 월의 평일(월~금) 수 계산 함수
    public function getWorkingDays($year, $month) {
        $workingDays = 0;
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = new DateTime("$year-$month-$day");
            $dayOfWeek = $date->format('N'); // 1(월요일) ~ 7(일요일)
            
            if ($dayOfWeek <= 5) {
                $workingDays++;
            }
        }
    
        return $workingDays;
    }
    
    // 데이터 삽입 함수
    public function insertPower($data) {
        $query = array(
            'table' => 'mes_power',
            'machineUid' => $data['machine'],
            'value' => $data['value'],
            'registDate' => $data['registDate']
        );

        return $this->insert($query);
    }

    // 월별 전력량과 요금 등록
    public function registerMonthPowerData() {
        $year = $this->param['year']; // 연도
        $month = $this->param['month']; // 월
        $power = $this->param['power']; // 전력 사용량
        $price = $this->param['price']; // 전력 비용
    
        // 월별 필드명 생성
        $powerField = "power{$month}";
        $priceField = "price{$month}";
    
        // 테이블에서 해당 연도의 데이터 존재 여부 확인
        $query = "SELECT uid FROM mes_month_power WHERE year = {$year}";
        $this->query($query);
    
        if ($this->getRows() > 0) {
            // 데이터가 존재하면 해당 월 데이터만 업데이트
            $updateData = array(
                'table' => 'mes_month_power',
                $powerField => $power,
                $priceField => $price,
                'where' => "year = {$year}"
            );
            $result = $this->update($updateData);
        } else {
            // 데이터가 없으면 기본값을 포함한 행 삽입
            $insertData = array(
                'table' => 'mes_month_power',
                'year' => $year
            );
    
            // 모든 power1 ~ power12 및 price1 ~ price12를 초기값으로 설정
            for ($i = 1; $i <= 12; $i++) {
                $insertData["power{$i}"] = 0; // 기본값 0
                $insertData["price{$i}"] = 0; // 기본값 0
            }
    
            // 현재 월 데이터를 업데이트
            $insertData[$powerField] = $power;
            $insertData[$priceField] = $price;
    
            $result = $this->insert($insertData);
        }

        if($result) {
            $this->response = [
                'result' => 'success',
                'message' => '등록하였습니다'
            ];
        } else {
            $this->response = [
                'result' => 'error',
                'message' => '등록에 실패하였습니다'
            ];
        }

        echo json_encode($this->response);
    }


    // 일별데이터 생성
    public function createDayPowerData() {
        $year = $this->param['year'];
        $month = $this->param['month'];

        // 월별 데이터 가져오기
        $query = "
            SELECT power{$month} AS monthlyPower 
            FROM mes_month_power 
            WHERE year = {$year}
        ";
        $this->query($query);
        $result = $this->fetch();
    
        if (!$result || !$result['monthlyPower']) {
            echo json_encode(['result' => 'error', 'message' => '해당 월의 데이터가 없습니다.']);
            return;
        }
    
        $monthlyPower = (float) $result['monthlyPower'];

        // 해당 월의 일수 계산
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        // 기본 평균값 계산
        $averagePower = round($monthlyPower / $daysInMonth, 2);
        $dailyPower = [];
        $remainingPower = $monthlyPower;

        // 랜덤 변동값 분배
        for ($day = 1; $day <= $daysInMonth; $day++) {
            if ($day === $daysInMonth) {
                // 마지막 날에 남은 전력 모두 할당
                $dailyPower[$day] = round($remainingPower, 2);
            } else {
                // ±10% 변동값 적용
                $variation = rand(-10, 10) / 100; // -10% ~ +10%
                $dailyAllocation = $averagePower * (1 + $variation);
                $dailyAllocation = min($dailyAllocation, $remainingPower); // 남은 값보다 크지 않게
                $dailyPower[$day] = round($dailyAllocation, 2);
                $remainingPower -= $dailyAllocation;
            }
        }

        // 데이터 삽입 준비
        $insertData = [
            'table' => 'mes_day_power',
            'year' => $year,
            'month' => $month,
        ];

        // day1 ~ day31 데이터 추가
        for ($i = 1; $i <= 31; $i++) {
            $insertData["day{$i}"] = $i <= $daysInMonth ? $dailyPower[$i] : 0;
        }

        // 삽입 실행
        $insertResult = $this->insert($insertData);

        if ($insertResult) {
            echo json_encode(['result' => 'success', 'message' => '데이터 생성 완료']);
        } else {
            echo json_encode(['result' => 'error', 'message' => '데이터 삽입 실패']);
        }
    }
    
    
    // 일별
    public function getMonthlyDailyData() {
        $year = isset($this->param['year']) ? intval($this->param['year']) : date('Y');
        $month = isset($this->param['month']) ? intval($this->param['month']) : date('m');
    
        // 해당 연도와 월의 데이터 가져오기
        $query = "
            SELECT year, month, 
                day1, day2, day3, day4, day5, day6, day7, day8, day9, day10,
                day11, day12, day13, day14, day15, day16, day17, day18, day19, day20,
                day21, day22, day23, day24, day25, day26, day27, day28, day29, day30, day31
            FROM mes_day_power
            WHERE year = {$year} AND month = {$month}
            LIMIT 1
        ";
        $this->query($query);
        $result = $this->fetch();
    
        if (!$result) {
            echo json_encode(['result' => 'error', 'message' => '데이터를 찾을 수 없습니다.']);
            return;
        }
    
        // 데이터 정리
        $response = [];
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $field = "day{$day}";
            $response[] = [
                'day' => $day,
                'totalPower' => isset($result[$field]) ? (float) $result[$field] : 0
            ];
        }
    
        echo json_encode(['data' => $response], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    
    /*
    // 월간 일별 전력 사용량 데이터
    public function getMonthlyData($yearMonth = null) {
        $yearMonth = $yearMonth ?: date('Y-m');
        $query = "
            SELECT 
                DATE(registerDate) as date,
                SUM(value) as totalPower
            FROM mes_power
            WHERE DATE_FORMAT(registerDate, '%Y-%m') = '$yearMonth'
            GROUP BY DATE(registerDate)
            ORDER BY date
        ";
        
        $result = $this->db->query($query);
        $data = array();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = array(
                    'label' => $row['date'],
                    'value' => (float)$row['totalPower']
                );
            }
            echo json_encode($data);
        } else {
            echo json_encode(['error' => $this->db->error]);
        }
    }
    
    // 일별 시간대별 전력 사용량 데이터
    public function getDailyData($date = null) {
        $date = $date ?: date('Y-m-d');
        $query = "
            SELECT 
                DATE_FORMAT(registerDate, '%H:00') as hour,
                SUM(value) as totalPower
            FROM mes_power
            WHERE DATE(registerDate) = '$date'
            GROUP BY HOUR(registerDate)
            ORDER BY HOUR(registerDate)
        ";
        
        $result = $this->db->query($query);
        $data = array();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = array(
                    'label' => $row['hour'],
                    'value' => (float)$row['totalPower']
                );
            }
            echo json_encode($data);
        } else {
            echo json_encode(['error' => $this->db->error]);
        }
    }
    
    // 특정 시간대의 분단위 전력 사용량 데이터
    public function getHourlyData($date = null, $hour = null) {
        $date = $date ?: date('Y-m-d');
        $hour = $hour ?: date('H');
        $query = "
            SELECT 
                DATE_FORMAT(registerDate, '%H:%i') as minute,
                value as totalPower
            FROM mes_power
            WHERE DATE(registerDate) = '$date' 
            AND HOUR(registerDate) = '$hour'
            ORDER BY registerDate
        ";
        
        $result = $this->db->query($query);
        $data = array();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = array(
                    'label' => $row['minute'],
                    'value' => (float)$row['totalPower']
                );
            }
            echo json_encode($data);
        } else {
            echo json_encode(['error' => $this->db->error]);
        }
    }
    
    // 설비별 전력 사용량 데이터 (시계열)
    public function getMachineData($machineUid = null, $startDate = null, $endDate = null) {
        if (!$machineUid) {
            echo json_encode(['error' => 'Machine UID is required']);
            return;
        }
        
        $startDate = $startDate ?: date('Y-m-d');
        $endDate = $endDate ?: date('Y-m-d');
        
        $query = "
            SELECT 
                DATE_FORMAT(registerDate, '%Y-%m-%d %H:%i') as timestamp,
                value as powerValue
            FROM mes_power
            WHERE machineUid = '$machineUid'
            AND DATE(registerDate) BETWEEN '$startDate' AND '$endDate'
            ORDER BY registerDate
        ";
        
        $result = $this->db->query($query);
        $data = array();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = array(
                    'label' => $row['timestamp'],
                    'value' => (float)$row['powerValue']
                );
            }
            echo json_encode($data);
        } else {
            echo json_encode(['error' => $this->db->error]);
        }
    }
    
    public function __destruct() {
        $this->db->close();
    }
    */
}
?>
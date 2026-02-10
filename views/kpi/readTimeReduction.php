<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>납기 단축 성과 지표 대시보드</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .main-container {
            max-width: 100%;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        /* 요약 통계 카드 스타일 (4개 카드) */
        .summary-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
            gap: 20px;
            margin-bottom: 30px;
        }

        .summary-card {
            background-color: #007bff; 
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-left: 5px solid #28a745; 
            transition: transform 0.3s, box-shadow 0.3s;
        }

        /* 카드 색상 */
        .summary-card.total-card { background-color: #007bff; border-left-color: #ffc107; }
        .summary-card.target-card { background-color: #6c757d; border-left-color: #007bff; }
        .summary-card.avg-card { background-color: #007bff; border-left-color: #28a745; }
        .summary-card.kpi-card { background-color: #28a745; border-left-color: #ffc107; }
        
        /* 글자 색상: 흰색 고정 */
        .summary-card h4, .summary-card .number, .summary-card .unit, .summary-card .label {
            color: white !important;
        }
        
        .summary-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }

        .summary-card h4 {
            font-size: 16px;
            margin-bottom: 15px;
            font-weight: 600;
        }
        
        .summary-card .number {
            font-size: 36px;
            font-weight: 700;
            display: inline-block;
        }

        .summary-card .unit {
            display: inline-block;
            margin-left: 8px;
            font-size: 18px;
        }
        
        /* 차트 그리드 스타일 */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .chart-card {
            background-color: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .chart-card h3 {
            font-size: 20px;
            color: #333;
            margin-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
        }

        .chart-container {
            height: 350px; 
        }
        
        /* 테이블 스타일 */
        .data-table-container { margin-top: 30px; }

        .list { width: 100%; border-collapse: collapse; font-size: 14px; text-align: left; }
        .list thead { background-color: #007bff; color: #fff; }
        .list th, .list td { padding: 12px 15px; border: 1px solid #ddd; }
        .list tbody tr:nth-child(even) { background-color: #f9f9f9; }
        .list tbody tr:hover { background-color: #f1f1f1; }
        
        .list th:nth-child(5), .list td:nth-child(5),
        .list th:nth-child(7), .list td:nth-child(7) {
            text-align: right; 
        }

        .status-ontime { color: #28a745; font-weight: bold; }
        .status-delayed { color: #dc3545; font-weight: bold; }
        .mt20 { margin-top: 20px; }
    </style>
</head>
<body>
    <?php
    // =======================================================
    // PHP 더미 데이터 생성 및 계산 로직 (시간 단위 적용)
    // =======================================================

    // 💡 조건부 포맷팅 함수: 소수점 이하가 0이면 정수만, 아니면 2자리 표시
    function format_time_value($value) {
        if (floor($value) == $value) {
            return number_format($value, 0);
        } else {
            // 소수점 2자리까지만 표시 (콤마 미사용)
            return number_format($value, 2, '.', '');
        }
    }

    // KPI 목표 설정 (단위를 시간으로 변경: 기준 56시간, 목표 52시간)
    $kpi_base_hours = 56.0;     // 기준 납기 기간: 56시간
    $kpi_target_hours = 52.0;   // 목표 납기 기간: 52시간
    $data_count = 50;         // 최근 납기 건수 50개

    $today = new DateTime();
    $shipment_data = [];
    $total_shipments = 0;
    $total_lead_time_hours = 0; // 시간 단위로 누적
    $monthly_summary = [];

    // 거래처 및 제품 정보
    $customers = ['(주)대한테크', '세종물산', '미래금속', '신화ENG'];
    $products = ['A-3000', 'B-4050', 'C-1002'];

    for ($i = 0; $i < $data_count; $i++) {
        $shipment_date = (clone $today)->modify("-$i days")->format('Y-m-d');
        $order_date = (clone $today)->modify("-$i days -" . mt_rand(3, 5) . " days")->format('Y-m-d'); 
        $month = (clone $today)->modify("-$i days")->format('Y-m');
        
        $customer = $customers[array_rand($customers)];
        $product = $products[array_rand($products)];
        
        // 실제 소요 기간: 50.5시간 ~ 51.5시간 사이로 랜덤 생성 (평균 51시간 목표)
        $actual_lead_time_hours = round(mt_rand(5050, 5150) / 100, 2); 
        
        // 납기 상태 판별: 실제 소요 기간이 목표 기간(52시간)보다 짧으면 '단축 성공' 
        $status = ($actual_lead_time_hours <= $kpi_target_hours) ? '단축 성공' : '목표 초과';
        $status_class = ($actual_lead_time_hours <= $kpi_target_hours) ? 'status-ontime' : 'status-delayed';

        $shipment_data[] = [
            'shipment_no' => 1000 + $i,
            'customer' => $customer,
            'product' => $product,
            'order_date' => $order_date,
            'shipment_date' => $shipment_date,
            'quantity' => mt_rand(10, 100),
            'lead_time_hours' => $actual_lead_time_hours, // 시간 단위
            'status' => $status,
            'status_class' => $status_class,
        ];

        $total_shipments++;
        $total_lead_time_hours += $actual_lead_time_hours; 

        // 월별 요약 데이터 (차트용: 월별 평균 리드 타임)
        if (!isset($monthly_summary[$month])) {
            $monthly_summary[$month] = ['total_hours' => 0, 'count' => 0];
        }
        $monthly_summary[$month]['total_hours'] += $actual_lead_time_hours;
        $monthly_summary[$month]['count']++;
    }

    $monthly_avg_lead_times = [];
    foreach ($monthly_summary as $month => $data) {
        $monthly_avg_lead_times[$month] = round($data['total_hours'] / $data['count'], 2);
    }
    ksort($monthly_avg_lead_times);

    // KPI 계산
    $avg_lead_time_hours = ($total_shipments > 0) 
        ? round($total_lead_time_hours / $total_shipments, 2) 
        : 0;

    if ($kpi_base_hours > $kpi_target_hours) {
        $target_improvement = $kpi_base_hours - $kpi_target_hours; 
        $actual_improvement = $kpi_base_hours - $avg_lead_time_hours;
        
        $kpi_achievement_rate = ($target_improvement > 0) 
            ? round(($actual_improvement / $target_improvement) * 100, 1)
            : 0; 
    } else {
        $kpi_achievement_rate = 0;
    }
    
    $leadTime_unit = '시간';

    // Javascript로 전달할 데이터 구조화
    $js_data = [
        'summary' => [
            'totalShipments' => number_format($total_shipments),
            // 💡 format_time_value 함수 적용
            'kpiTarget' => format_time_value($kpi_target_hours), 
            'avgLeadTime' => format_time_value($avg_lead_time_hours), 
            'kpiAchievementRate' => number_format($kpi_achievement_rate, 1), 
            'kpiBase' => format_time_value($kpi_base_hours), 
        ],
        'monthlyChart' => [
            'labels' => array_keys($monthly_avg_lead_times),
            'data' => array_values($monthly_avg_lead_times),
        ],
        'dailyChart' => [], 
        'tableData' => $shipment_data,
        'readTimeUnit' => $leadTime_unit,
    ];
    ?>

    <div class='main-container'>
        <h2 style="color: #007bff; border-bottom: 2px solid #e0e0e0; padding-bottom: 10px; margin-bottom: 30px;">🚀 납기 단축 성과 지표 대시보드</h2>
        
        <div class='content-wrapper'>
            <div class="summary-stats">
                
                <div class="summary-card total-card">
                    <h4>총 납기 실적 건수</h4>
                    <div class="combined-metrics">
                        <div class="metric-group" style="width: 100%; text-align: center;">
                            <div class="number" id="totalShipments" style="font-size: 40px;"><?= $js_data['summary']['totalShipments'] ?></div>
                            <div class="unit" style="font-size: 18px;">건</div>
                        </div>
                    </div>
                </div>

                <div class="summary-card target-card">
                    <h4>목표 납기 기간</h4>
                    <div class="number" id="kpiTarget"><?= $js_data['summary']['kpiTarget'] ?></div>
                    <div class="unit"><?= $js_data['readTimeUnit'] ?></div>
                    <hr style="border-color: rgba(255,255,255,0.3); margin: 15px 0 10px 0;">
                    <div style="font-size: 14px; color: white; margin-top: 5px;">
                        (기준 납기 기간: **<?= $js_data['summary']['kpiBase'] ?> <?= $js_data['readTimeUnit'] ?>**)
                    </div>
                </div>
                        
                <div class="summary-card avg-card">
                    <h4>평균 납기 소요시간</h4>
                    <div class="number" id="avgLeadTime"><?= $js_data['summary']['avgLeadTime'] ?></div>
                    <div class="unit"><?= $js_data['readTimeUnit'] ?></div>
                </div>
                        
                <div class="summary-card kpi-card">
                    <h4>KPI 달성률 (목표 단축률: 100%)</h4>
                    <div class="number" id="kpiAchievementRate"><?= $js_data['summary']['kpiAchievementRate'] ?></div>
                    <div class="unit">%</div>
                    <div style="font-size: 14px; color: white; margin-top: 5px;">
                        단축 소요 기간: **<?= $js_data['summary']['avgLeadTime'] ?> <?= $js_data['readTimeUnit'] ?>**
                    </div>
                </div>
            </div>

            <div class="charts-grid">
                <div class="chart-card">
                    <h3>📈 월별 평균 납기 기간 추이 (단위: 시간)</h3>
                    <div class="chart-container">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>

                <div class="chart-card">
                    <h3>📅 일별 납기 기간 실적 (최근 <?= $data_count ?>건, 단위: 시간)</h3>
                    <div class="chart-container">
                        <canvas id="dailyChart"></canvas>
                    </div>          
                </div>          
            </div>

            <div class="data-table-container">
                <h3 style="margin-bottom: 20px; color: #333;">📋 상세 납기 실적 (최근 <?= $data_count ?>건)</h3>
                <table class="list">
                    <thead>
                        <tr>
                            <th>번호</th>
                            <th>거래처</th>
                            <th>제품명</th>
                            <th>주문일</th>
                            <th style="text-align: right;">수량</th>
                            <th style="text-align: right;">실제 납기일</th>
                            <th style="text-align: right;">납기 소요기간 (<?= $js_data['readTimeUnit'] ?>)</th>
                            <th>상태</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // 상세 데이터 테이블 출력 (최근 데이터가 상단에 오도록 역순 정렬)
                        $reversed_data = array_reverse($shipment_data);
                        foreach ($reversed_data as $row) {
                            echo "<tr>";
                            echo "<td>{$row['shipment_no']}</td>";
                            echo "<td>{$row['customer']}</td>";
                            echo "<td>{$row['product']}</td>";
                            echo "<td>{$row['order_date']}</td>";
                            echo "<td>" . number_format($row['quantity']) . "</td>";
                            echo "<td>{$row['shipment_date']}</td>";
                            // 💡 format_time_value 함수 적용
                            echo "<td style='text-align: right;'>" . format_time_value($row['lead_time_hours']) . "</td>"; 
                            echo "<td><span class='{$row['status_class']}'>{$row['status']}</span></td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
                <div class="paging-area mt20"></div>
            </div>
            
        </div>
    </div>

    <script>
        // =======================================================
        // Javascript 프론트엔드 로직 (Chart.js)
        // =======================================================

        // PHP에서 생성된 데이터를 Javascript 변수로 가져오기
        const dataFromPHP = <?= json_encode($js_data) ?>;
        // KPI 타겟 값은 차트의 목표선으로 사용되므로, 포맷팅되지 않은 숫자 값이 필요합니다.
        // PHP에서 포맷팅하기 전의 원본 숫자값을 사용하여 목표선을 그립니다.
        const kpiTarget = 52.0; 
        
        /**
         * Chart.js를 사용하여 월별 평균 납기 기간 차트 렌더링
         */
        function renderMonthlyChart() {
            const ctx = document.getElementById('monthlyChart').getContext('2d');
            
            // 목표선 데이터 생성 (모든 레이블에 목표값 적용)
            const targetData = dataFromPHP.monthlyChart.labels.map(() => kpiTarget);

            new Chart(ctx, {
                type: 'bar', // 월별은 막대형 차트로 표현
                data: {
                    labels: dataFromPHP.monthlyChart.labels,
                    datasets: [
                        {
                            label: '월별 평균 납기 기간 (<?= $js_data['readTimeUnit'] ?>)',
                            data: dataFromPHP.monthlyChart.data,
                            backgroundColor: 'rgba(0, 123, 255, 0.7)', // 파란색
                            borderColor: 'rgba(0, 123, 255, 1)',
                            borderWidth: 1
                        },
                        {
                            label: '목표 납기 기간',
                            data: targetData,
                            type: 'line', // 목표값은 라인 차트로 오버레이
                            borderColor: 'rgba(220, 53, 69, 1)', // 빨간색
                            borderWidth: 2,
                            fill: false,
                            pointRadius: 0 // 점 표시 안함
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false, 
                    scales: {
                        y: {
                            beginAtZero: false,
                            min: 50.0, // 최소값 설정으로 변화 폭 강조 (50시간 근처의 데이터에 맞춤)
                            title: {
                                display: true,
                                text: '평균 납기 소요기간 (<?= $js_data['readTimeUnit'] ?>)'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    // 💡 소수점 이하가 0이면 정수, 아니면 2자리 표시
                                    let value = context.parsed.y;
                                    let formattedValue = (value % 1 === 0) ? value.toFixed(0) : value.toFixed(2);

                                    if (context.parsed.y !== null) {
                                        label += formattedValue + ' <?= $js_data['readTimeUnit'] ?>';
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }

        /**
         * Chart.js를 사용하여 일별 납기 기간 차트 렌더링
         */
        function renderDailyChart() {
            // 상세 데이터(tableData)에서 일별 납기 소요 기간 추출
            const dailyData = dataFromPHP.tableData.map(row => ({
                date: row.shipment_date,
                lead_time: row.lead_time_hours // 시간 단위 사용
            })).sort((a, b) => new Date(a.date) - new Date(b.date)); // 날짜순 정렬

            const dailyLabels = dailyData.map(row => row.date);
            const dailyLeadTimes = dailyData.map(row => row.lead_time);

            // 목표선 데이터 생성
            const targetData = dailyLabels.map(() => kpiTarget);

            const ctx = document.getElementById('dailyChart').getContext('2d');
            new Chart(ctx, {
                type: 'line', 
                data: {
                    labels: dailyLabels,
                    datasets: [
                        {
                            label: '납기 소요기간 실적 (<?= $js_data['readTimeUnit'] ?>)',
                            data: dailyLeadTimes,
                            backgroundColor: 'rgba(40, 167, 69, 0.4)', // 초록색 계열
                            borderColor: 'rgba(40, 167, 69, 1)',
                            borderWidth: 2,
                            pointRadius: 3,
                            tension: 0.3, 
                            fill: false
                        },
                        {
                            label: '목표 납기 기간',
                            data: targetData,
                            borderColor: 'rgba(255, 193, 7, 1)', // 노란색/주황색
                            borderWidth: 2,
                            fill: false,
                            pointRadius: 0 
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: false,
                            min: 50.0,
                            title: {
                                display: true,
                                text: '납기 소요기간 (<?= $js_data['readTimeUnit'] ?>)'
                            }
                        },
                        x: {
                            ticks: {
                                autoSkip: true,
                                maxTicksLimit: 15 
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    // 💡 소수점 이하가 0이면 정수, 아니면 2자리 표시
                                    let value = context.parsed.y;
                                    let formattedValue = (value % 1 === 0) ? value.toFixed(0) : value.toFixed(2);
                                    
                                    if (context.parsed.y !== null) {
                                        label += formattedValue + ' <?= $js_data['readTimeUnit'] ?>';
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }

        // 페이지 로드 후 차트 렌더링 함수 실행
        document.addEventListener('DOMContentLoaded', () => {
            renderMonthlyChart();
            renderDailyChart();
        });

    </script>
</body>
</html>
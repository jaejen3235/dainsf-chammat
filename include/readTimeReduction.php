<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>납기 단축 성과 지표 (KPI)</title>
    <style>
        /* CSS 스타일: 기존 스타일 유지 */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f4f7f9;
            color: #333;
            padding: 20px;
        }

        .main-container {
            max-width: 1400px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .summary-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .summary-card {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-left: 5px solid #1a73e8; /* 기본색 */
            text-align: center;
        }

        .summary-card.total { border-left-color: #1a73e8; background-color: #e8f0fe; }
        .summary-card.on-time { border-left-color: #28a745; background-color: #e6ffed; }
        .summary-card.rate { border-left-color: #ffc107; background-color: #fff8e1; }
        .summary-card.avg-delay { border-left-color: #dc3545; background-color: #fcebeb; }

        .summary-card h4 {
            font-size: 16px;
            color: #333;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .summary-card .number {
            font-size: 40px;
            font-weight: 700;
            color: #1a73e8;
            display: inline-block;
        }
        
        .summary-card.on-time .number { color: #28a745; }
        .summary-card.rate .number { color: #ffc107; }
        .summary-card.avg-delay .number { color: #dc3545; }

        .summary-card .unit {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
            display: block;
        }

        .summary-card .unit-small {
            font-size: 20px;
            margin-left: 5px;
            font-weight: 600;
        }

        /* 테이블 스타일 */
        .data-table-container {
            margin-top: 30px;
        }

        .list {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            text-align: left;
        }

        .list thead {
            background-color: #1a73e8;
            color: #fff;
        }

        .list th, .list td {
            padding: 12px 15px;
            border: 1px solid #ddd;
        }

        .list tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .list tbody tr:hover {
            background-color: #f1f1f1;
        }

        .list th:nth-child(4), .list td:nth-child(4),
        .list th:nth-child(8), .list td:nth-child(8) {
            text-align: right; 
        }

        .status-ontime { color: #28a745; font-weight: bold; }
        .status-delayed { color: #dc3545; font-weight: bold; }

        .paging-area {
            text-align: center;
            margin-top: 20px;
        }

        .btn-page {
            padding: 5px 10px;
            border: 1px solid #ccc;
            background-color: #fff;
            cursor: pointer;
            margin: 0 5px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <?php
    // =======================================================
    // PHP 더미 데이터 생성 및 계산 로직 (데이터 생성 유지)
    // =======================================================

    // KPI 목표 설정 (시간 단위)
    $kpi_target_hours = 52.0; 
    $kpi_base_hours = 56.0;   

    // 데이터 생성 설정
    $data_count = 100; // 전체 납기 건수
    $today = new DateTime();
    $shipment_data = [];
    $total_shipments = 0;
    $on_time_shipments = 0;
    $total_lead_time_hours = 0;

    $target_days = ceil($kpi_target_hours / 24); 

    $products = ['A-3000', 'B-4050', 'C-1002', 'D-9900'];
    $customers = ['(주)대한산업', '(주)세종테크', '신화금속', '미래ENG'];

    for ($i = 1; $i <= $data_count; $i++) {
        $order_date = (clone $today)->modify("-".mt_rand(10, 60)." days")->format('Y-m-d');
        
        $due_date = (new DateTime($order_date))->modify("+$target_days days")->format('Y-m-d');

        // 실제 납기 소요 시간 생성 (51시간 근처: 50시간 ~ 52시간)
        $actual_lead_time_seconds = mt_rand(180000, 187200); 

        $actual_lead_time_days = floor($actual_lead_time_seconds / 86400);

        $actual_shipment_date = (new DateTime($order_date))->modify("+$actual_lead_time_days days")->format('Y-m-d');

        $is_on_time = ($actual_lead_time_days <= $target_days) ? true : false;
        
        $total_shipments++;
        if ($is_on_time) {
            $on_time_shipments++;
        }
        $total_lead_time_hours += round($actual_lead_time_seconds / 3600, 2);

        $shipment_data[] = [
            'id' => 1000 + $i,
            'customer' => $customers[array_rand($customers)],
            'product' => $products[array_rand($products)],
            'quantity' => mt_rand(10, 500),
            'order_date' => $order_date,
            'due_date' => $due_date,
            'shipment_date' => $actual_shipment_date,
            'lead_time_hours' => round($actual_lead_time_seconds / 3600, 1),
            'status' => $is_on_time ? '정시 납기' : '납기 지연',
            'is_on_time' => $is_on_time,
        ];
    }

    // KPI 최종 계산
    $compliance_rate = ($total_shipments > 0) 
        ? round(($on_time_shipments / $total_shipments) * 100, 1) 
        : 0;

    $avg_lead_time_hours = ($total_shipments > 0)
        ? round($total_lead_time_hours / $total_shipments, 1)
        : 0;

    // Javascript로 전달할 데이터 구조화 (이 부분은 유지)
    $js_data = [
        'summary' => [
            'totalShipments' => number_format($total_shipments),
            'onTimeShipments' => number_format($on_time_shipments),
            'complianceRate' => number_format($compliance_rate, 1),
            'avgLeadTimeHours' => number_format($avg_lead_time_hours, 1),
            'targetRate' => 95,
            'targetHours' => $kpi_target_hours,
        ],
        'tableData' => $shipment_data,
        'rowsPerPage' => 10,
    ];

    ?>

    <div class='main-container'>
        <h2 style="color: #1a73e8; border-bottom: 2px solid #e0e0e0; padding-bottom: 10px; margin-bottom: 30px;">🚀 납기 단축 성과 지표 (KPI)</h2>

        <div class='content-wrapper'>
            <div class="summary-stats">
                
                <div class="summary-card total">
                    <h4>전체 납기 건수</h4>
                    <div class="number" id="totalShipments"><?= $js_data['summary']['totalShipments'] ?></div>
                    <div class="unit">건 (최근)</div>
                </div>
                
                <div class="summary-card on-time">
                    <h4>정시 납기</h4>
                    <div class="number" id="onTimeShipments"><?= $js_data['summary']['onTimeShipments'] ?></div>
                    <div class="unit">건</div>
                </div>
                
                <div class="summary-card rate">
                    <h4>납기 준수율</h4>
                    <div class="number" id="complianceRate"><?= $js_data['summary']['complianceRate'] ?><span class="unit-small">%</span></div>
                    <div class="unit">목표: <?= $js_data['summary']['targetRate'] ?>%</div>
                </div>
                
                <div class="summary-card avg-delay">
                    <h4>평균 납기 소요시간</h4>
                    <div class="number" id="avgLeadTime"><?= $js_data['summary']['avgLeadTimeHours'] ?><span class="unit-small">시간</span></div>
                    <div class="unit">목표: <?= $js_data['summary']['targetHours'] ?>시간</div>
                </div>
            </div>
            
            <hr>
            
            <div class="data-table-container">
                <h3 style="margin-bottom: 20px; color: #333;">📋 상세 납기 데이터</h3>
                <table class="list">
                    <thead>
                        <tr>
                            <th>고유번호</th>
                            <th>거래처</th>
                            <th>제품명</th>
                            <th style="text-align: right;">수량</th>
                            <th>주문일</th>
                            <th>납기 예정일</th>
                            <th>실제 납기일</th>
                            <th style="text-align: right;">납기 소요시간</th>
                            <th>상태</th>
                        </tr>
                    </thead>
                    <tbody id="shipmentTableBody">
                        </tbody>
                </table>
            </div>
            
            <div class="paging-area mt20">
                <button onclick="changePage(-1)" class="btn-page">이전</button>
                <span id="currentPage">1</span> / <span id="totalPages">1</span>
                <button onclick="changePage(1)" class="btn-page">다음</button>
            </div>
        </div>
    </div>

    <script>
        // =======================================================
        // Javascript 프론트엔드 로직 (페이징 및 테이블 렌더링)
        // =======================================================

        // PHP에서 생성된 데이터를 Javascript 변수로 가져오기
        const dataFromPHP = <?= json_encode($js_data) ?>;
        let currentPage = 1;
        const rowsPerPage = dataFromPHP.rowsPerPage;
        const totalRows = dataFromPHP.tableData.length;
        // 테이블 데이터가 비어있을 경우 NaN 방지
        const totalPages = totalRows > 0 ? Math.ceil(totalRows / rowsPerPage) : 1;

        /**
         * 테이블 데이터를 현재 페이지에 맞게 렌더링합니다.
         * @param {number} page 현재 페이지 번호
         */
        function renderTable(page) {
            const tableBody = document.getElementById('shipmentTableBody');
            tableBody.innerHTML = '';
            
            const start = (page - 1) * rowsPerPage;
            const end = start + rowsPerPage;
            const paginatedData = dataFromPHP.tableData.slice(start, end);

            paginatedData.forEach(row => {
                const statusClass = row.is_on_time ? 'status-ontime' : 'status-delayed';
                const statusText = row.status;

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${row.id}</td>
                    <td>${row.customer}</td>
                    <td>${row.product}</td>
                    <td style="text-align: right;">${row.quantity.toLocaleString()}</td>
                    <td>${row.order_date}</td>
                    <td>${row.due_date}</td>
                    <td>${row.shipment_date}</td>
                    <td style="text-align: right;">${row.lead_time_hours}시간</td>
                    <td><span class="${statusClass}">${statusText}</span></td>
                `;
                tableBody.appendChild(tr);
            });

            document.getElementById('currentPage').textContent = page;
            document.getElementById('totalPages').textContent = totalPages;
        }

        /**
         * 페이지를 변경합니다.
         * @param {number} delta 변경할 페이지 수 (+1 또는 -1)
         */
        window.changePage = function(delta) {
            const newPage = currentPage + delta;
            if (newPage >= 1 && newPage <= totalPages) {
                currentPage = newPage;
                renderTable(currentPage);
            }
        };

        // 초기 로드 시 테이블 렌더링
        document.addEventListener('DOMContentLoaded', () => {
            renderTable(currentPage);
        });

    </script>
</body>
</html>
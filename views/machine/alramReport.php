<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>예지보전 알람 및 보고서 (상태 변경)</title>
    <style>
        /* ======================================= */
        /* CSS Variables (Theme Styles) */
        /* ======================================= */
        :root {
            --primary-color: #00bcd4;
            --secondary-color: #673ab7;
            --background: #f8f9fa;
            --card-bg: white;
            --main-font: #343a40;
            --border-color: #dee2e6;
            --status-normal: #4caf50;
            --status-warning: #ff9800;
            --status-critical: #f44336;
        }

        body {
            font-family: 'Malgun Gothic', 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--background);
            color: var(--main-font);
        }

        .main-container {
            padding: 30px;
            max-width: 1600px; 
            margin: 0 auto;
        }

        .page-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--secondary-color);
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 3px solid var(--primary-color);
        }

        .analysis-card {
            background: var(--card-bg);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-color);
            margin-bottom: 30px;
        }

        .card-header {
            font-size: 20px;
            font-weight: 600;
            color: var(--main-font);
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 2px solid #eee;
        }

        /* Table Styles */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .data-table th, .data-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .data-table th {
            background-color: #f0f4f7;
            color: var(--secondary-color);
            font-weight: 600;
        }

        .data-table tr:hover {
            background-color: #f5f5f5;
        }

        /* Status Badges */
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: 700;
            font-size: 12px;
            color: white;
            cursor: pointer; /* 클릭 가능하도록 커서 변경 */
            transition: background-color 0.3s ease;
        }
        
        /* Status Badge Colors */
        .status-critical { background-color: var(--status-critical); }
        .status-warning { background-color: var(--status-warning); }
        .status-caution { background-color: var(--primary-color); }
        .status-completed { background-color: var(--status-normal); }
        .status-in-progress { background-color: var(--secondary-color); }
        .status-pending { background-color: #adb5bd; }

    </style>
</head>
<body>

    <div class="main-container">
        <div class="page-title">🔔 예지보전 알람 및 보전 보고서 관리</div>

        <div class="analysis-card">
            <div class="card-header">🔴 실시간 긴급 알람 ($\text{CRITICAL}$ & $\text{WARNING}$)</div>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>위험 등급</th>
                        <th>대상 설비</th>
                        <th>발생 일시</th>
                        <th>주요 원인</th>
                        <th>권고 $\text{RUL}$ (%)</th>
                        <th>조치 상태</th>
                    </tr>
                </thead>
                <tbody id="alarm-list">
                    <tr class="alarm-row" data-severity="critical">
                        <td><span class="status-badge status-critical">CRITICAL</span></td>
                        <td>E102 - 용접 로봇 3호 (Motor Bearing)</td>
                        <td>2025.11.10 10:30</td>
                        <td>진동 $\text{RMS}$ 및 $\text{BPFO}$ 급등</td>
                        <td>25%</td>
                        <td><span class="status-badge status-pending" onclick="changeStatus(this)">미처리</span></td>
                    </tr>
                    <tr class="alarm-row" data-severity="warning">
                        <td><span class="status-badge status-warning">WARNING</span></td>
                        <td>E201 - 레이저 커팅기 (Spindle)</td>
                        <td>2025.11.05 08:15</td>
                        <td>전류 불균형 ($\text{Phase Imbalance}$)</td>
                        <td>75%</td>
                        <td><span class="status-badge status-in-progress" onclick="changeStatus(this)">진행 중</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="analysis-card">
            <div class="card-header">📝 보전 조치 보고서 이력 ($\text{RUL}$ $\text{Reset}$ 포함)</div>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>보고서 $\text{ID}$</th>
                        <th>알람 일시</th>
                        <th>설비</th>
                        <th>주요 원인</th>
                        <th>조치 상태</th>
                        <th>완료 일시</th>
                        <th>담당자</th>
                    </tr>
                </thead>
                <tbody id="report-list">
                    <tr>
                        <td><span style="color: var(--secondary-color);">RPT-007</span></td>
                        <td>2025.11.10</td>
                        <td>E102</td>
                        <td>CRITICAL Vib</td>
                        <td><span class="status-badge status-pending" onclick="changeStatus(this)">미처리</span></td>
                        <td>-</td>
                        <td>-</td>
                    </tr>
                    <tr>
                        <td><span style="color: var(--secondary-color);">RPT-006</span></td>
                        <td>2025.11.05</td>
                        <td>E201</td>
                        <td>Current Imbalance</td>
                        <td><span class="status-badge status-in-progress" onclick="changeStatus(this)">진행 중</span></td>
                        <td>-</td>
                        <td>이보전</td>
                    </tr>
                    <tr>
                        <td><span style="color: var(--secondary-color);">RPT-005</span></td>
                        <td>2025.10.20</td>
                        <td>E201</td>
                        <td>Vib RMS 상승</td>
                        <td><span class="status-badge status-completed" onclick="changeStatus(this)">완료 (RUL Reset)</span></td>
                        <td>2025.10.28</td>
                        <td>김보전</td>
                    </tr>
                    <tr>
                        <td><span style="color: var(--secondary-color);">RPT-004</span></td>
                        <td>2025.09.15</td>
                        <td>E101</td>
                        <td>Caution Temp Rise</td>
                        <td><span class="status-badge status-completed" onclick="changeStatus(this)">완료</span></td>
                        <td>2025.09.18</td>
                        <td>박보전</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // 상태 목록 정의
        const STATUS_STEPS = [
            { text: '미처리', class: 'status-pending' },
            { text: '진행 중', class: 'status-in-progress' },
            { text: '완료 (RUL Reset)', class: 'status-completed' }
        ];

        /**
         * 클릭 시 조치 상태를 순환 변경하는 함수
         * @param {HTMLElement} badgeElement - 클릭된 상태 뱃지 요소
         */
        function changeStatus(badgeElement) {
            const currentText = badgeElement.textContent.trim();
            let currentIndex = STATUS_STEPS.findIndex(step => step.text === currentText);
            
            // 현재 상태 인덱스를 찾지 못했거나, '완료' 상태인 경우 순환 (0, 1, 2)
            if (currentIndex === -1) {
                currentIndex = 0; // 안전 장치
            } else {
                currentIndex = (currentIndex + 1) % STATUS_STEPS.length;
            }
            
            const nextStatus = STATUS_STEPS[currentIndex];
            
            // 텍스트와 CSS 클래스 업데이트
            badgeElement.textContent = nextStatus.text;
            
            // 기존 클래스 제거 (completed, in-progress, pending)
            badgeElement.classList.remove('status-pending', 'status-in-progress', 'status-completed');
            
            // 새 클래스 추가
            badgeElement.classList.add(nextStatus.class);

            // 실제 시스템에서는 여기에 AJAX 호출 등을 통해 서버에 상태 변경을 통보해야 합니다.
            console.log(`[상태 변경] 설비/보고서 ID: (추가 필요), 다음 상태: ${nextStatus.text}`);
            
            if (nextStatus.text.includes('완료')) {
                alert('💡 보전 조치 완료! RUL 예측 모델이 자동으로 재학습되거나 초기화됩니다.');
            }
        }
    </script>
</body>
</html>
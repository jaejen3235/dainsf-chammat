<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>세척기 가동률 현황</title>
    <style>
        /* ======================================= */
        /* Custom CSS (기존 스타일 유지 및 확장) */
        /* ======================================= */
        :root {
            --primary-color: #00bcd4;     /* Cyan Blue: 세척/클린 관련 색상 */
            --background: #f8f9fa;       
            --card-bg: white;
            --main-font: #343a40;
            --table-border: #dee2e6;
            --header-bg: #e0f7fa;         /* 연한 청록색 헤더 */
            --status-run: #28a745;        /* 가동 중 (Green) */
            --status-stop: #ff9800;       /* 비가동/대기 (Orange) */
            --status-error: #dc3545;      /* 오류 (Red) */
        }


        /* Title & Header */
        .report-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        /* ======================================= */
        /* 1. Dashboard Cards (가동률 요약) */
        /* ======================================= */
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: #e0f7fa; /* 연한 청록색 */
            padding: 20px;
            border-radius: 6px;
            border-left: 5px solid var(--primary-color);
        }

        .card h4 {
            margin: 0 0 5px 0;
            font-size: 14px;
            color: #555;
        }

        .card p {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .card.rate p { color: var(--status-run); }
        .card.error p { color: var(--status-error); }
        .card.cycle p { color: var(--status-stop); }


        /* ======================================= */
        /* 2. Detailed History Table */
        /* ======================================= */
        .search-box {
            background-color: var(--header-bg);
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .input, .select {
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            background-color: var(--primary-color);
            color: white;
            transition: background-color 0.2s;
        }
        .btn:hover { background-color: #00a0b2; }

        .list {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
            font-size: 14px;
        }
        .list thead th {
            background-color: var(--header-bg);
            border: 1px solid var(--table-border);
            padding: 12px;
            font-weight: 700;
        }
        .list tbody td {
            border: 1px solid var(--table-border);
            padding: 10px 8px;
        }
        .list tbody tr:nth-child(even) {
            background-color: #fcfcfc;
        }

        /* Highlight Colors */
        .status-run-text { color: var(--status-run); font-weight: 700; }
        .status-stop-text { color: var(--status-stop); font-weight: 700; }
        .status-error-text { color: var(--status-error); font-weight: 700; }
        .total-row { font-weight: 700; background-color: #ccf2f5 !important; }

    </style>
</head>
<body>

    <div class='main-container'>
        <div class='content-wrapper'>
            
            <div id="washer-operation-monitor">
                <div class="report-title">🧼 세척기 (Washer-01) 가동률 현황</div>

                <div class="summary-cards" id="washer-summary">
                    </div>

                <div class="report-title" style="font-size: 18px; margin-top: 30px; color: var(--main-font);">세척 상세 사이클 이력</div>
                <div class="search-box">
                    <label for="search_start_date">기간 설정:</label>
                    <input type='date' class='input' id='search_start_date' value="2025-11-15"/>
                    <span>~</span>
                    <input type='date' class='input' id='search_end_date' value="2025-11-17"/>
                    <label for="search_status">상태:</label>
                    <select id="search_status" class="select">
                        <option value="">--- 전체 상태 ---</option>
                        <option value="Completed">Completed</option>
                        <option value="Error">Error</option>
                        <option value="Stopped">Stopped</option>
                    </select>
                    <input type='button' class='btn' value='이력 조회' onclick='searchOperationHistory()' />
                </div>

                <table class='list'>
                    <thead>
                        <tr>
                            <th>시작 시각</th>
                            <th>종료 시각</th>
                            <th>**소요 시간 (분)**</th>
                            <th>세척 제품</th>
                            <th>**상태**</th>
                            <th>비고 / 오류 내용</th>
                        </tr>
                    </thead>
                    <tbody id="operation-history-body">
                        </tbody>
                </table>
            </div>

        </div>
    </div>

    <script>
        // ===============================================
        // Mock Data (세척기 가동 이력)
        // ===============================================
        const mockOperationHistory = [
            { id: 1, start: '2025-11-17 14:00:00', end: '2025-11-17 14:35:00', duration: 35, item: 'PCB A-100 Lot 05', status: 'Completed', note: '정상 완료' },
            { id: 2, start: '2025-11-17 13:00:00', end: '2025-11-17 13:30:00', duration: 30, item: 'PCB B-200 Lot 12', status: 'Completed', note: '정상 완료' },
            { id: 3, start: '2025-11-17 11:15:00', end: '2025-11-17 11:35:00', duration: 20, item: 'PCB A-100 Lot 04', status: 'Error', note: '수압 이상으로 20분 후 강제 중단' },
            { id: 4, start: '2025-11-16 16:00:00', end: '2025-11-16 16:40:00', duration: 40, item: 'PCB B-200 Lot 11', status: 'Completed', note: '정상 완료' },
            { id: 5, start: '2025-11-16 10:30:00', end: '2025-11-16 11:00:00', duration: 30, item: 'PCB A-100 Lot 03', status: 'Completed', note: '정상 완료' },
            { id: 6, start: '2025-11-15 09:00:00', end: '2025-11-15 09:30:00', duration: 30, item: 'PCB B-200 Lot 10', status: 'Completed', note: '정상 완료' },
            { id: 7, start: '2025-11-15 08:00:00', end: '2025-11-15 08:05:00', duration: 5, item: '점검/테스트', status: 'Stopped', note: '5분간 세척액 예열 후 대기' },
        ];


        // ===============================================
        // Utility Functions
        // ===============================================

        /** 날짜와 시간 포맷팅 */
        function formatDateTime(dateTimeStr) {
            const [date, time] = dateTimeStr.split(' ');
            return `${date}<br>${time.substring(0, 5)}`;
        }
        
        /** 날짜 부분만 추출 */
        function getDatePart(dateTimeStr) {
            return dateTimeStr.split(' ')[0];
        }

        /** 상태에 따른 클래스 결정 */
        function getStatusClass(status) {
            if (status === 'Completed') return 'status-run-text';
            if (status === 'Error') return 'status-error-text';
            if (status === 'Stopped') return 'status-stop-text';
            return '';
        }

        // ===============================================
        // Summary Calculation
        // ===============================================

        /** 선택 기간의 가동률 및 요약 정보 계산 */
        function calculateOperationSummary(data, startDate, endDate) {
            // 날짜 범위 (시간 포함)를 Date 객체로 변환
            const start = new Date(startDate + " 00:00:00").getTime();
            const end = new Date(endDate + " 23:59:59").getTime();
            
            // 전체 기간 시간 (분)
            const totalTimeMs = end - start;
            const totalTimeMins = Math.round(totalTimeMs / (1000 * 60)); // 전체 가용 시간 (분)

            let totalRunDuration = 0; // 총 가동 시간 (분)
            let completedCycles = 0; // 완료된 세척 횟수
            let errorCycles = 0; // 오류 발생 횟수

            data.forEach(item => {
                if (item.status === 'Completed') {
                    totalRunDuration += item.duration;
                    completedCycles++;
                } else if (item.status === 'Error') {
                    totalRunDuration += item.duration;
                    errorCycles++;
                }
            });
            
            // 가동률 계산 (총 가용 시간 대비 가동 시간)
            // 가용 시간이 0일 경우 (예: 검색 기간이 1분 미만) 0% 반환
            const operationRate = totalTimeMins > 0 ? (totalRunDuration / totalTimeMins) * 100 : 0;
            
            return {
                totalRunDuration,
                completedCycles,
                errorCycles,
                totalTimeMins,
                operationRate
            };
        }


        // ===============================================
        // Rendering Functions
        // ===============================================
        
        /** 1. 요약 카드 렌더링 */
        function renderOperationSummary(summary) {
            const summaryContainer = document.getElementById('washer-summary');
            summaryContainer.innerHTML = '';
            
            const rate = summary.operationRate.toFixed(1);

            summaryContainer.innerHTML = `
                <div class="card rate">
                    <h4>가동률 (%)</h4>
                    <p>${rate}%</p>
                </div>
                <div class="card">
                    <h4>총 가동 시간 (분)</h4>
                    <p class="status-run-text">${summary.totalRunDuration.toLocaleString()} 분</p>
                </div>
                <div class="card cycle">
                    <h4>세척 완료 사이클</h4>
                    <p class="status-stop-text">${summary.completedCycles} 회</p>
                </div>
                <div class="card error">
                    <h4>오류 발생 횟수</h4>
                    <p class="status-error-text">${summary.errorCycles} 회</p>
                </div>
            `;
        }
        
        /** 2. 상세 이력 렌더링 함수 */
        function renderOperationHistory(data) {
            const body = document.getElementById('operation-history-body');
            body.innerHTML = '';

            if (data.length === 0) {
                body.innerHTML = `<tr><td colspan='6'>검색 조건에 해당하는 가동 이력이 없습니다.</td></tr>`;
                return;
            }
            
            data.forEach(item => {
                const statusClass = getStatusClass(item.status);
                
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${formatDateTime(item.start)}</td>
                    <td>${formatDateTime(item.end)}</td>
                    <td>${item.duration}</td>
                    <td>${item.item}</td>
                    <td class="${statusClass}">**${item.status}**</td>
                    <td>${item.note}</td>
                `;
                body.appendChild(row);
            });
            
            // 총 합계 행 추가 (가동 시간 요약)
            const summary = calculateOperationSummary(data, document.getElementById('search_start_date').value, document.getElementById('search_end_date').value);

            body.innerHTML += `
                <tr class="total-row">
                    <td colspan="2">총 요약 (기간 내)</td>
                    <td colspan="4" style="text-align: left; padding-left: 20px;">
                        총 가동 시간: <span class="status-run-text">${summary.totalRunDuration} 분</span>,
                        완료 사이클: <span class="status-stop-text">${summary.completedCycles} 회</span>,
                        오류: <span class="status-error-text">${summary.errorCycles} 회</span>
                    </td>
                </tr>
            `;
        }


        // ===============================================
        // Search Event Handlers
        // ===============================================

        /** 세척기 가동 이력 조회 */
        function searchOperationHistory() {
            const startDate = document.getElementById('search_start_date').value;
            const endDate = document.getElementById('search_end_date').value;
            const searchStatus = document.getElementById('search_status').value;

            console.log(`[세척기 조회] 기간: ${startDate} ~ ${endDate}, 상태: ${searchStatus || '전체'}`);
            
            // Mock Data 필터링: 날짜 및 상태 필터링
            const filteredData = mockOperationHistory.filter(d => {
                const logDate = getDatePart(d.start);
                
                const dateMatch = (logDate >= startDate && logDate <= endDate);
                const statusMatch = (searchStatus === '' || d.status === searchStatus);
                
                return dateMatch && statusMatch;
            });
            
            // 시작 시간 순으로 정렬
            filteredData.sort((a, b) => new Date(b.start) - new Date(a.start));

            // 요약 정보 계산 및 렌더링
            const summary = calculateOperationSummary(mockOperationHistory, startDate, endDate);
            renderOperationSummary(summary);
            
            // 상세 이력 렌더링
            renderOperationHistory(filteredData);
        }

        // ===============================================
        // Initial Load
        // ===============================================
        window.onload = () => {
            // 페이지 로드 시 가동률 현황을 바로 표시
            searchOperationHistory(); 
        };
    </script>
</body>
</html>
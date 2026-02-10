<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>금속 검출 현황</title>
    <style>
        /* ======================================= */
        /* Custom CSS (기존 스타일 유지 및 확장) */
        /* ======================================= */
        :root {
            --primary-color: #ffc107;     /* Yellow/Amber: 경고/검출 관련 색상 */
            --background: #f8f9fa;       
            --card-bg: white;
            --main-font: #343a40;
            --table-border: #dee2e6;
            --header-bg: #fffbe6;         /* 연한 노란색 헤더 */
            --status-detect: #dc3545;     /* 금속 검출 (Red - 중요 경고) */
            --status-pass: #28a745;       /* 정상 통과 (Green) */
        }


        /* Title & Header */
        .report-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        /* Search Box */
        .search-box {
            background-color: #fcf8e3; /* 연한 노란색 배경 */
            border: 1px solid var(--primary-color);
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 6px;
        }

        .search-grid {
            display: grid;
            grid-template-columns: 2fr 1.5fr 1fr auto; /* 기간, 라인, 상태, 버튼 */
            gap: 15px;
            align-items: end;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .date-range {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-group label {
            font-size: 14px;
            font-weight: 600;
            color: var(--main-font);
        }

        .input, .select {
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 14px;
            width: 100%;
            box-sizing: border-box;
        }
        
        .btn-box {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
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
        .btn:hover {
            background-color: #e0a800;
        }


        /* Data Table (Detection History) */
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
            color: var(--main-font);
        }
        .list tbody td {
            border: 1px solid var(--table-border);
            padding: 10px 8px;
        }
        .list tbody tr:nth-child(even) {
            background-color: #fcfcfc;
        }

        /* Highlight Colors */
        .detect-text { color: var(--status-detect); font-weight: 700; }
        .pass-text { color: var(--status-pass); font-weight: 700; }
        .total-row { font-weight: 700; background-color: #ffeedd !important; }

    </style>
</head>
<body>

    <div class='main-container'>
        <div class='content-wrapper'>
            
            <div id="metal-detection-inquiry">
                <div class="report-title">🛡️ 금속 검출 현황 및 이력</div>

                <div class="search-box">
                    <div class="search-grid">
                        
                        <div class="form-group">
                            <label for="search_start_date">기간 설정 (검출 시점)</label>
                            <div class="date-range">
                                <input type='date' class='input' id='search_start_date' value="2025-11-10"/>
                                <span>~</span>
                                <input type='date' class='input' id='search_end_date' value="2025-11-17"/>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="search_line">생산 라인</label>
                            <select id="search_line" class="select">
                                <option value="">--- 전체 ---</option>
                                <option value="Line-A">Line A (스마트칩)</option>
                                <option value="Line-B">Line B (모듈케이스)</option>
                                <option value="Line-C">Line C (배터리팩)</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="search_status">검출 여부</label>
                            <select id="search_status" class="select">
                                <option value="">--- 전체 ---</option>
                                <option value="Detected">금속 검출</option>
                                <option value="Passed">정상 통과</option>
                            </select>
                        </div>

                        <div class="btn-box">
                            <input type='button' class='btn' value='현황 조회' onclick='searchDetectionHistory()' />
                        </div>
                    </div>
                </div>

                <table class='list'>
                    <thead>
                        <tr>
                            <th>검출 시각</th>
                            <th>생산 라인</th>
                            <th>제품 코드</th>
                            <th>**검출 결과**</th>
                            <th>이물질 크기 (mm)</th>
                            <th>처리 결과</th>
                            <th>담당자</th>
                            <th>비고</th>
                        </tr>
                    </thead>
                    <tbody id="detection-history-body">
                        </tbody>
                </table>
            </div>

        </div>
    </div>

    <script>
        // ===============================================
        // Mock Data (금속 검출 이력 예시 데이터)
        // ===============================================
        const mockDetectionData = [
            { id: 1, datetime: '2025-11-10 09:30:00', line: 'Line-A', code: 'A-100', result: 'Detected', size: 1.2, treatment: '전량 폐기', user: '관리자 A', note: '미세 철분 검출' },
            { id: 2, datetime: '2025-11-10 14:05:00', line: 'Line-B', code: 'B-200', result: 'Passed', size: 0.0, treatment: '정상 통과', user: '관리자 B', note: '-' },
            { id: 3, datetime: '2025-11-11 11:20:00', line: 'Line-C', code: 'C-300', result: 'Detected', size: 2.5, treatment: '라인 중단 후 재검사', user: '관리자 A', note: '공구 파손 조각 추정' },
            { id: 4, datetime: '2025-11-15 16:45:00', line: 'Line-A', code: 'A-100', result: 'Passed', size: 0.0, treatment: '정상 통과', user: '관리자 C', note: '-' },
            { id: 5, datetime: '2025-11-17 08:00:00', line: 'Line-B', code: 'B-200', result: 'Passed', size: 0.0, treatment: '정상 통과', user: '관리자 B', note: '-' },
            { id: 6, datetime: '2025-11-17 10:15:00', line: 'Line-A', code: 'A-100', result: 'Detected', size: 0.8, treatment: '해당 로트 격리', user: '관리자 C', note: 'SUS 미세 검출, 재검사 예정' },
        ];


        // ===============================================
        // Utility Functions
        // ===============================================

        /** 날짜와 시간 포맷팅 */
        function formatDateTime(dateTimeStr) {
            // "2025-11-10 09:30:00" -> 2025-11-10 <br> 09:30:00
            const [date, time] = dateTimeStr.split(' ');
            return `${date}<br>${time}`;
        }
        
        /** 날짜 부분만 추출 */
        function getDatePart(dateTimeStr) {
            return dateTimeStr.split(' ')[0];
        }

        // ===============================================
        // Rendering Functions
        // ===============================================
        
        /** 금속 검출 이력 렌더링 함수 */
        function renderDetectionHistory(data) {
            const body = document.getElementById('detection-history-body');
            body.innerHTML = '';
            
            let detectedCount = 0;
            let passedCount = 0;

            if (data.length === 0) {
                body.innerHTML = `<tr><td colspan='8'>검색 조건에 해당하는 금속 검출 이력이 없습니다.</td></tr>`;
                return;
            }
            
            data.forEach(item => {
                const statusText = item.result === 'Detected' ? '금속 검출' : '정상 통과';
                const statusClass = item.result === 'Detected' ? 'detect-text' : 'pass-text';
                
                if (item.result === 'Detected') detectedCount++;
                else passedCount++;

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${formatDateTime(item.datetime)}</td>
                    <td>${item.line}</td>
                    <td>${item.code}</td>
                    <td class="${statusClass}">**${statusText}**</td>
                    <td>${item.size > 0 ? item.size.toFixed(1) : '-'}</td>
                    <td>${item.treatment}</td>
                    <td>${item.user}</td>
                    <td>${item.note}</td>
                `;
                body.appendChild(row);
            });

            // 총 합계 행 추가 (요약 정보)
            body.innerHTML += `
                <tr class="total-row">
                    <td colspan="3">총 검사 건수 및 결과</td>
                    <td colspan="5" style="text-align: left; padding-left: 20px;">
                        총 검사 건수: **${detectedCount + passedCount}건** (금속 검출: <span class="detect-text">${detectedCount}건</span>, 
                        정상 통과: <span class="pass-text">${passedCount}건</span>)
                    </td>
                </tr>
            `;
        }


        // ===============================================
        // Search Event Handlers
        // ===============================================

        /** 금속 검출 이력 조회 */
        function searchDetectionHistory() {
            const startDate = document.getElementById('search_start_date').value;
            const endDate = document.getElementById('search_end_date').value;
            const searchLine = document.getElementById('search_line').value;
            const searchStatus = document.getElementById('search_status').value;

            console.log(`[금속 검출 조회] 기간: ${startDate} ~ ${endDate}, 라인: ${searchLine}, 상태: ${searchStatus}`);
            
            // Mock Data 필터링: 날짜, 라인, 검출 상태 필터링
            const filteredData = mockDetectionData.filter(d => {
                const detectionDate = getDatePart(d.datetime);
                
                const dateMatch = (detectionDate >= startDate && detectionDate <= endDate);
                const lineMatch = (searchLine === '' || d.line === searchLine);
                const statusMatch = (searchStatus === '' || d.result === searchStatus);
                
                return dateMatch && lineMatch && statusMatch;
            });
            
            renderDetectionHistory(filteredData);
        }

        // ===============================================
        // Initial Load
        // ===============================================
        window.onload = () => {
            // 페이지 로드 시 금속 검출 현황을 바로 표시
            searchDetectionHistory(); 
        };
    </script>
</body>
</html>
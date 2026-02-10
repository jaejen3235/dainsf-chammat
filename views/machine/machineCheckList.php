<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>설비 점검 현황 및 조치</title>
    <style>
        /* ======================================= */
        /* Global & Theme Styles (Deep Purple) */
        /* ======================================= */
        :root {
            --primary-color: #673ab7;    /* Deep Purple */
            --background: #f8f9fa;       
            --card-bg: white;
            --main-font: #343a40;
            --table-border: #dee2e6;
            --header-bg: #e9ecef;
            --status-pass: #4caf50;      /* PASS (Green) */
            --status-fail: #dc3545;      /* FAIL (Red) */
            --status-pending: #ff9800;   /* 조치 대기 (Orange) */
            --status-done: #6c757d;      /* 조치 완료 (Gray) */
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
            max-width: 1400px;
            margin: 0 auto;
        }

        .content-wrapper {
            background: var(--card-bg);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        /* Header & Search */
        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--primary-color);
        }
        .report-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-color);
        }
        .btn-box {
            display: flex;
            gap: 10px;
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
        }
        .btn:hover { background-color: #5e35b1; }
        
        /* Data Table */
        .list {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
            font-size: 14px;
            margin-top: 20px;
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
            vertical-align: middle;
        }
        .list tbody tr:nth-child(even) {
            background-color: #fcfcfc;
        }

        /* Status Badge */
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 12px;
            color: white;
            white-space: nowrap; /* 텍스트 줄바꿈 방지 */
        }
        .badge-PASS { background-color: var(--status-pass); }
        .badge-FAIL { background-color: var(--status-fail); }
        .badge-PENDING { background-color: var(--status-pending); }
        .badge-DONE { background-color: var(--status-done); }

        /* FAIL 항목 강조 */
        .fail-row {
            background-color: #fce4e4 !important; 
            font-weight: 600;
            border-left: 5px solid var(--status-fail);
        }
        .fail-summary {
            text-align: left;
            padding-left: 15px !important;
            color: var(--status-fail);
        }

        /* Action Button for Maintenance */
        .btn-action {
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 600;
            background-color: var(--status-pending);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .btn-action:hover {
            background-color: #e68a00; /* Darker orange */
        }
    </style>
</head>
<body>

    <div class='main-container'>
        <div class='content-wrapper'>
            
            <div class="report-header">
                <div class="report-title">📋 설비 점검 현황 및 조치</div>
                <div class="btn-box">
                    <label for="start_date">기간:</label>
                    <input type='date' class='input' id='start_date' value="2025-11-01"/>
                    ~
                    <input type='date' class='input' id='end_date' value="2025-11-11"/>
                    <select class="select" id="equipment_select">
                        <option value="">전체 설비</option>
                        <option value="E101">E101 - CNC 가공기 A</option>
                        <option value="E102">E102 - 용접 로봇 3호</option>
                        <option value="E201">E201 - 최종 검사 라인</option>
                    </select>
                    <input type='button' class='btn' value='조회' onclick='searchInspectionStatus()' />
                </div>
            </div>

            <table class='list'>
                <thead>
                    <tr>
                        <th style="width: 5%;">No.</th>
                        <th style="width: 15%;">점검 일시</th>
                        <th style="width: 10%;">설비 ID</th>
                        <th style="width: 15%;">설비명</th>
                        <th style="width: 10%;">점검자</th>
                        <th style="width: 10%;">점검 결과</th>
                        <th style="width: 25%;">FAIL 항목 요약</th>
                        <th style="width: 10%;">조치 처리</th>
                    </tr>
                </thead>
                <tbody id="inspection-status-body">
                    </tbody>
            </table>

        </div>
    </div>

    <script>
        // ===============================================
        // Mock Data: 설비 점검 등록 결과
        // id는 각 점검 건의 고유 ID입니다.
        // ===============================================
        let MOCK_INSPECTION_DATA = [
            { 
                id: 1, date: '2025-11-10 14:30', equipId: 'E101', equipName: 'CNC 가공기 A', inspector: '홍길동', 
                failCount: 0, failSummary: '모든 항목 양호', totalResult: 'PASS', actionStatus: 'DONE'
            },
            { 
                id: 2, date: '2025-11-10 16:00', equipId: 'E102', equipName: '용접 로봇 3호', inspector: '김철수', 
                failCount: 1, failSummary: '로봇 관절부에서 약간의 소음 발견', totalResult: 'FAIL', actionStatus: 'PENDING'
            },
            { 
                id: 3, date: '2025-11-09 09:15', equipId: 'E201', equipName: '최종 검사 라인', inspector: '박영희', 
                failCount: 0, failSummary: '모든 항목 양호', totalResult: 'PASS', actionStatus: 'DONE'
            },
            { 
                id: 4, date: '2025-11-11 10:45', equipId: 'E101', equipName: 'CNC 가공기 A', inspector: '홍길동', 
                failCount: 2, failSummary: '윤활유 수위 부족, 칩 배출 장치 동작 불량', totalResult: 'FAIL', actionStatus: 'PENDING'
            },
            { 
                id: 5, date: '2025-11-11 13:00', equipId: 'E102', equipName: '용접 로봇 3호', inspector: '이영호', 
                failCount: 1, failSummary: '케이블 손상 확인', totalResult: 'FAIL', actionStatus: 'DONE' // 이미 조치 완료된 건
            },
        ];

        const tableBody = document.getElementById('inspection-status-body');

        // ===============================================
        // Action Functions
        // ===============================================

        /**
         * '조치 완료' 버튼을 눌렀을 때 실행되는 함수
         * @param {number} inspectionId - 점검 건의 고유 ID
         */
        function completeMaintenance(inspectionId) {
            // 1. 사용자에게 조치 완료 확인
            if (!confirm(`점검 ID ${inspectionId}에 대한 조치를 '완료' 처리하시겠습니까?`)) {
                return;
            }

            // 2. Mock Data 업데이트 (실제 환경에서는 서버 API 호출)
            const item = MOCK_INSPECTION_DATA.find(d => d.id === inspectionId);
            if (item) {
                // FAIL 항목이 있는 건에 대해서만 PENDING 상태에서 DONE으로 변경
                if (item.totalResult === 'FAIL' && item.actionStatus === 'PENDING') {
                    item.actionStatus = 'DONE';
                    alert(`점검 ID ${inspectionId} 조치 완료 처리되었습니다.`);
                } else if (item.totalResult === 'PASS') {
                    alert('이 점검 건은 PASS 항목이므로 조치 대상이 아닙니다.');
                    return;
                } else {
                    alert('이미 조치 완료된 건입니다.');
                    return;
                }
            } else {
                alert('해당 점검 건을 찾을 수 없습니다.');
                return;
            }

            // 3. 현재 조회된 리스트를 재렌더링
            searchInspectionStatus(false); // 재조회 (필터 유지)
        }

        // ===============================================
        // Rendering Functions
        // ===============================================

        /**
         * 점검 현황 리스트를 렌더링합니다.
         */
        function renderInspectionList(data) {
            tableBody.innerHTML = '';
            
            if (data.length === 0) {
                tableBody.innerHTML = `<tr><td class='center' colspan='8'>검색된 점검 현황 자료가 없습니다.</td></tr>`;
                return;
            }

            data.forEach((item, index) => {
                const rowClass = item.failCount > 0 ? 'fail-row' : '';
                const resultBadgeClass = item.totalResult === 'PASS' ? 'badge-PASS' : 'badge-FAIL';
                
                let actionContent;
                if (item.totalResult === 'PASS') {
                    // PASS 항목은 조치 불필요
                    actionContent = `<span class="badge badge-DONE">조치 불필요</span>`;
                } else if (item.actionStatus === 'PENDING') {
                    // FAIL 항목 중 조치 대기 건은 버튼 표시
                    actionContent = `<button class="btn-action" onclick="completeMaintenance(${item.id})">조치 완료 처리</button>`;
                } else {
                    // FAIL 항목 중 조치 완료 건은 뱃지 표시
                    actionContent = `<span class="badge badge-DONE">조치 완료</span>`;
                }

                const row = document.createElement('tr');
                row.className = rowClass;
                row.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${item.date}</td>
                    <td>${item.equipId}</td>
                    <td>${item.equipName}</td>
                    <td>${item.inspector}</td>
                    <td><span class="badge ${resultBadgeClass}">${item.totalResult}</span></td>
                    <td class="fail-summary">${item.failSummary}</td>
                    <td>${actionContent}</td>
                `;
                tableBody.appendChild(row);
            });
        }

        // ===============================================
        // Event Handlers
        // ===============================================

        /** 설비 점검 현황 검색 */
        function searchInspectionStatus(useInputValues = true) {
            let startDate, endDate, equipId;

            if (useInputValues) {
                startDate = document.getElementById('start_date').value;
                endDate = document.getElementById('end_date').value;
                equipId = document.getElementById('equipment_select').value;
            } else {
                 // completeMaintenance에서 호출될 경우, 현재 필터 값을 재사용
                startDate = document.getElementById('start_date').value;
                endDate = document.getElementById('end_date').value;
                equipId = document.getElementById('equipment_select').value;
            }
            
            // 1. 기간 및 설비 필터링 시뮬레이션
            let filteredData = MOCK_INSPECTION_DATA.filter(item => {
                const itemDate = new Date(item.date).getTime();
                const start = new Date(startDate).getTime();
                const end = new Date(endDate);
                end.setDate(end.getDate() + 1);
                const endTimestamp = end.getTime();
                
                const dateMatch = (itemDate >= start && itemDate <= endTimestamp);
                const equipMatch = !equipId || item.equipId === equipId;
                
                return dateMatch && equipMatch;
            });
            
            // 2. 최신순 정렬
            filteredData.sort((a, b) => new Date(b.date) - new Date(a.date));

            renderInspectionList(filteredData); 
        }

        // ===============================================
        // Initial Load
        // ===============================================
        window.onload = () => {
            searchInspectionStatus();
        };
    </script>
</body>
</html>
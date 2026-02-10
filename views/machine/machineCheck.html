<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>설비 점검 등록</title>
    <style>
        /* ======================================= */
        /* Global & Theme Styles (Deep Purple) */
        /* ======================================= */
        :root {
            --primary-color: #673ab7;    /* Deep Purple: 설비/보전 색상 */
            --background: #f8f9fa;       
            --card-bg: white;
            --main-font: #343a40;
            --border-color: #dee2e6;
            --status-pass: #4caf50;      /* PASS (Green) */
            --status-fail: #dc3545;       /* FAIL (Red) */
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
            max-width: 1000px;
            margin: 0 auto;
        }

        .content-wrapper {
            background: var(--card-bg);
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .page-title {
            font-size: 26px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 3px solid var(--primary-color);
        }

        /* Form Layout */
        .form-group {
            margin-bottom: 15px;
        }
        .form-row {
            display: flex;
            gap: 20px;
        }
        .form-column {
            flex: 1;
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
            color: #495057;
        }

        .input, .select, .textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box; /* 패딩이 너비에 포함되도록 */
        }
        .textarea {
            resize: vertical;
            min-height: 80px;
        }
        
        /* Checklist Styling */
        .checklist-section {
            border: 1px solid #ccc;
            border-radius: 6px;
            padding: 15px;
            margin-top: 20px;
        }
        .checklist-section h3 {
            font-size: 18px;
            color: var(--primary-color);
            margin-top: 0;
            padding-bottom: 10px;
            border-bottom: 1px dashed var(--primary-color);
            margin-bottom: 15px;
        }
        .check-item {
            display: flex;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px dotted #eee;
        }
        .check-item:last-child {
            border-bottom: none;
        }
        .check-label {
            flex: 2;
            font-size: 15px;
        }
        .check-status {
            flex: 1;
            text-align: right;
        }
        .check-radio {
            margin-left: 15px;
            cursor: pointer;
        }
        .radio-label {
            font-weight: 600;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .radio-label-pass {
            color: var(--status-pass);
            border: 1px solid var(--status-pass);
        }
        .radio-label-fail {
            color: var(--status-fail);
            border: 1px solid var(--status-fail);
        }
        /* 라디오 버튼 숨김 */
        .check-radio input[type="radio"] {
            display: none;
        }
        /* 선택된 상태 스타일 */
        .check-radio input[type="radio"]:checked + .radio-label-pass {
            background-color: var(--status-pass);
            color: white;
        }
        .check-radio input[type="radio"]:checked + .radio-label-fail {
            background-color: var(--status-fail);
            color: white;
        }

        /* Action Buttons */
        .action-buttons {
            margin-top: 30px;
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
        }
        .btn {
            padding: 10px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 700;
        }
        .btn-submit {
            background-color: var(--primary-color);
            color: white;
            margin-right: 10px;
        }
        .btn-cancel {
            background-color: #6c757d;
            color: white;
        }
    </style>
</head>
<body>

    <div class='main-container'>
        <div class='content-wrapper'>
            
            <div class="page-title">🛠️ 설비 점검 등록</div>
            
            <form id="inspectionForm">
                <div class="form-row">
                    <div class="form-column">
                        <div class="form-group">
                            <label for="inspection_date">점검 일시</label>
                            <input type="datetime-local" class="input" id="inspection_date" required>
                        </div>
                    </div>
                    <div class="form-column">
                        <div class="form-group">
                            <label for="inspector">점검자</label>
                            <input type="text" class="input" id="inspector" value="홍길동 (작업자 001)" readonly required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="equipment_id">점검 대상 설비</label>
                    <select class="select" id="equipment_id" onchange="loadChecklist(this.value)" required>
                        <option value="">-- 설비를 선택하세요 --</option>
                        <option value="E101">E101 - CNC 가공기 A</option>
                        <option value="E102">E102 - 용접 로봇 3호</option>
                        <option value="E201">E201 - 최종 검사 라인</option>
                    </select>
                </div>
            
                <div class="checklist-section">
                    <h3>점검 체크리스트 (기계/운전부)</h3>
                    <div id="checklist-area">
                        <p style="text-align: center; color: #999;">설비를 선택하면 점검 항목이 로드됩니다.</p>
                    </div>
                </div>

                <div class="form-group" style="margin-top: 25px;">
                    <label for="summary_note">종합 의견 및 특이사항</label>
                    <textarea class="textarea" id="summary_note" placeholder="점검 결과에 대한 종합 의견이나 발견된 문제점(FAIL 항목)에 대한 상세 내용을 입력하세요."></textarea>
                </div>

                <div class="action-buttons">
                    <button type="submit" class="btn btn-submit">등록 완료</button>
                    <button type="button" class="btn btn-cancel" onclick="resetForm()">취소</button>
                </div>
            </form>

        </div>
    </div>

    <script>
        // ===============================================
        // Mock Data: 설비별 점검 항목 정의
        // ===============================================
        const CHECKLIST_DATA = {
            'E101': [
                { id: 'C01', label: '주축(Spindle) 진동/소음 여부' },
                { id: 'C02', label: '윤활유/냉각수 적정 수위 및 오염 여부' },
                { id: 'C03', label: '칩/절삭유 배출 장치 동작 상태' },
                { id: 'C04', label: '가이드 웨이(Guide Way) 오일 누유 여부' },
            ],
            'E102': [
                { id: 'C10', label: '로봇 관절부 이상 유무 (소음/발열)' },
                { id: 'C11', label: '케이블 및 호스 손상 여부' },
                { id: 'C12', label: '안전 펜스 및 센서 동작 상태' },
            ],
            'E201': [
                { id: 'C20', label: '카메라 렌즈 청결 상태' },
                { id: 'C21', label: '조명 장치 밝기 및 오염도' },
                { id: 'C22', label: '데이터 저장 및 통신 상태' },
            ],
        };

        const checklistArea = document.getElementById('checklist-area');
        const inspectionForm = document.getElementById('inspectionForm');

        /**
         * 설비 선택 시 해당 점검 항목을 로드하여 화면에 표시합니다.
         * @param {string} equipmentId - 선택된 설비 ID
         */
        function loadChecklist(equipmentId) {
            if (!equipmentId) {
                checklistArea.innerHTML = '<p style="text-align: center; color: #999;">설비를 선택하면 점검 항목이 로드됩니다.</p>';
                return;
            }

            const items = CHECKLIST_DATA[equipmentId] || [];
            let html = '';

            if (items.length === 0) {
                html = '<p style="text-align: center; color: #999;">해당 설비에 등록된 점검 항목이 없습니다.</p>';
            } else {
                items.forEach(item => {
                    html += `
                        <div class="check-item">
                            <div class="check-label">${item.label}</div>
                            <div class="check-status">
                                <span class="check-radio">
                                    <input type="radio" id="item_${item.id}_pass" name="check_${item.id}" value="PASS" required>
                                    <label for="item_${item.id}_pass" class="radio-label radio-label-pass">PASS</label>
                                </span>
                                <span class="check-radio">
                                    <input type="radio" id="item_${item.id}_fail" name="check_${item.id}" value="FAIL" required>
                                    <label for="item_${item.id}_fail" class="radio-label radio-label-fail">FAIL</label>
                                </span>
                            </div>
                        </div>
                    `;
                });
            }
            checklistArea.innerHTML = html;
        }

        /**
         * 폼 제출 이벤트 처리
         */
        inspectionForm.addEventListener('submit', function(event) {
            event.preventDefault(); // 기본 제출 방지

            const formData = new FormData(this);
            const results = {
                date: formData.get('inspection_date'),
                inspector: formData.get('inspector'),
                equipment_id: formData.get('equipment_id'),
                summary_note: formData.get('summary_note'),
                checklist: []
            };

            let allChecksPassed = true;

            // 체크리스트 결과 수집
            for (const key of formData.keys()) {
                if (key.startsWith('check_')) {
                    const itemId = key.replace('check_', '');
                    const status = formData.get(key);
                    
                    if (status === 'FAIL') {
                        allChecksPassed = false;
                    }

                    // 항목 레이블 찾기 (간단한 표시를 위해)
                    let label = "점검 항목";
                    Object.values(CHECKLIST_DATA).flat().forEach(item => {
                        if (item.id === itemId) label = item.label;
                    });

                    results.checklist.push({
                        id: itemId,
                        label: label,
                        status: status
                    });
                }
            }

            // 결과 요약
            let message = `[설비 점검 등록 완료] \n\n`;
            message += `설비: ${results.equipment_id}\n`;
            message += `점검일: ${results.date}\n`;
            message += `총 점검 항목: ${results.checklist.length}개\n`;
            message += `FAIL 항목: ${results.checklist.filter(i => i.status === 'FAIL').length}개\n`;
            message += `종합 의견: ${results.summary_note || '없음'}\n\n`;
            
            if (allChecksPassed) {
                alert(message + "🎉 모든 점검 항목이 양호(PASS)합니다.");
            } else {
                alert(message + "⚠️ FAIL 항목이 발견되었습니다. 조치 필요!");
            }

            // TODO: 실제로는 이 시점에 서버 API로 results 객체를 전송해야 합니다.

            // 폼 초기화
            // resetForm(); 
        });

        /**
         * 폼 초기화
         */
        function resetForm() {
            inspectionForm.reset();
            loadChecklist(''); // 체크리스트 영역도 초기화
        }

        // 초기 로드 시 점검 일시 기본값 설정 (현재 시각)
        window.onload = function() {
            const now = new Date();
            const year = now.getFullYear();
            const month = (now.getMonth() + 1).toString().padStart(2, '0');
            const day = now.getDate().toString().padStart(2, '0');
            const hours = now.getHours().toString().padStart(2, '0');
            const minutes = now.getMinutes().toString().padStart(2, '0');
            
            const datetimeLocal = `${year}-${month}-${day}T${hours}:${minutes}`;
            document.getElementById('inspection_date').value = datetimeLocal;
        }
    </script>
</body>
</html>
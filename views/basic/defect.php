<div class="main-container">

        <div id="defect-page" style="display: block;">
            <div class="analysis-card">
                <div class="card-header">
                    <span>불량 사유 등록 목록</span>
                    <button class="btn btn-primary" onclick="openDefectReasonModal('New')">+ 신규 불량 사유 등록</button>
                </div>
                <table class="list">
                    <thead>
                        <tr>
                            <th>불량 코드</th>
                            <th>불량 사유명</th>
                            <th>적용 영역</th>
                            <th>등록일</th>
                            <th>사용 여부</th>
                            <th>상세</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr onclick="openDefectReasonModal('D-101')">
                            <td>D-101</td>
                            <td>**표면 흠집**</td>
                            <td><span class="status-badge reason-badge">공정 중 검사</span><span class="status-badge reason-badge">출하 검사</span></td>
                            <td>2025.01.10</td>
                            <td><span class="status-badge status-success">사용</span></td>
                            <td><button class="btn btn-primary btn-sm">상세</button></td>
                        </tr>
                        <tr onclick="openDefectReasonModal('D-300')">
                            <td>D-300</td>
                            <td>**오염**</td>
                            <td><span class="status-badge reason-badge">수입검사</span></td>
                            <td>2025.11.11</td>
                            <td><span class="status-badge status-success">사용</span></td>
                            <td><button class="btn btn-primary btn-sm">상세</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div> <div id="defect-reason-modal" class="modal-overlay">
        <div class="modal-content">
            <span class="modal-close" onclick="closeDefectReasonModal()">&times;</span>
            <h2 id="df-reason-modal-title">신규 불량 사유 등록</h2>
            <form id="defect-reason-form">
                
                <div class="form-group">
                    <label for="reason-code">🚨 불량 코드</label>
                    <input type="text" id="reason-code" placeholder="예: D-101" required>
                </div>

                <div class="form-group">
                    <label for="reason-name">불량 사유명</label>
                    <input type="text" id="reason-name" placeholder="예: 표면 흠집, 치수 오차" required>
                </div>
                
                <div class="form-group">
                    <label>🔍 **적용 영역 (복수 선택 가능)**</label>
                    <div class="checkbox-group">
                        <label><input type="checkbox" name="apply_area" value="수입검사"> 수입검사</label>
                        <label><input type="checkbox" name="apply_area" value="공정 중 검사"> 공정 중 검사</label>
                        <label><input type="checkbox" name="apply_area" value="출하 검사"> 출하 검사</label>
                        <label><input type="checkbox" name="apply_area" value="필드 클레임"> 필드 클레임</label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="reason-detail">상세 정의 및 기준</label>
                    <textarea id="reason-detail" rows="3" placeholder="해당 불량 사유의 판단 기준 및 상세 설명"></textarea>
                </div>

                <button type="submit" class="btn btn-submit" id="df-reason-submit-button">불량 사유 등록/수정</button>
            </form>
        </div>
    </div>


    <script>
        // --- MOCK Data for Defect Reason Master ---
        const MOCK_DEFECT_REASONS = {
            'D-101': { code: 'D-101', name: '표면 흠집', areas: ['공정 중 검사', '출하 검사'], detail: '제품 표면에 육안으로 확인 가능한 흠집 발생.' },
            'D-300': { code: 'D-300', name: '오염', areas: ['수입검사'], detail: '자재 표면에 오일 또는 이물질 발견.' }
        };

        // --- Defect Reason Master Functions ---
        
        /** 체크박스 상태 초기화/설정 */
        function setCheckboxes(areas) {
            document.querySelectorAll('#defect-reason-form input[name="apply_area"]').forEach(cb => {
                // 배열 포함 여부 확인 (예: data.areas가 ['수입검사']를 포함하는지)
                cb.checked = areas.includes(cb.value);
            });
        }

        /** 불량 사유 등록/상세 팝업을 열고 정보를 채웁니다. */
        function openDefectReasonModal(reasonCode) {
            const modal = document.getElementById('defect-reason-modal');
            const form = document.getElementById('defect-reason-form');
            form.reset();
            
            if (reasonCode === 'New') {
                document.getElementById('df-reason-modal-title').textContent = '신규 불량 사유 등록';
                document.getElementById('reason-code').disabled = false;
                document.getElementById('df-reason-submit-button').textContent = '불량 사유 등록';
                setCheckboxes([]);
            } else {
                const data = MOCK_DEFECT_REASONS[reasonCode];
                document.getElementById('df-reason-modal-title').textContent = `불량 사유 상세/수정 (${reasonCode})`;
                document.getElementById('reason-code').value = data.code;
                document.getElementById('reason-code').disabled = true; // 코드는 수정 불가 처리
                document.getElementById('reason-name').value = data.name;
                document.getElementById('reason-detail').value = data.detail;
                setCheckboxes(data.areas);
                document.getElementById('df-reason-submit-button').textContent = '불량 사유 수정';
            }

            modal.style.display = 'block';
        }

        /** 불량 사유 팝업을 닫습니다. */
        function closeDefectReasonModal() {
            document.getElementById('defect-reason-modal').style.display = 'none';
        }

        // --- Form Submission Simulation ---
        document.getElementById('defect-reason-form').addEventListener('submit', (e) => {
            e.preventDefault(); 
            const selectedAreas = Array.from(document.querySelectorAll('#defect-reason-form input[name="apply_area"]:checked'))
                                     .map(cb => cb.value);

            alert(`🎉 불량 사유: ${document.getElementById('reason-name').value}가 성공적으로 처리되었습니다.\n\n[적용 영역]: ${selectedAreas.join(', ')}`); 
            closeDefectReasonModal();
        });
        
        // --- Common Modal Close Logic (외부 클릭 시 닫기) ---
        window.onclick = function(event) {
            const modal = document.getElementById('defect-reason-modal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
        
    </script>
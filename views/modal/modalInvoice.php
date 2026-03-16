<div class="modal" id="modalNewInvoice">
	<div class="modal-content" style="max-width: 900px;">
		<div class="modal-header">
			<span class='modal-title'>거래명세서</span>
			<span class="btn-close" id="btnCloseInvoice" style="cursor:pointer; font-size: 1.5rem;"><i class='bx bx-x'></i></span>
		</div>
		
		<div class="modal-body" style="overflow:auto; padding: 20px; background-color: #f8f9fa;">
            <!-- 인쇄 영역 시작 -->
            <div id="invoiceContent" class="invoice-document">
                
                <div class="invoice-header">
                    <h2>거 래 명 세 서</h2>
                </div>

                <!-- 공급자 / 공급받는자 정보 영역 -->
                <table class="biz-info-table">
                    <colgroup>
                        <col style="width: 30px;">
                        <col style="width: 80px;">
                        <col style="width: 120px;">
                        <col style="width: 45px;">
                        <col style="width: 80px;">
                        
                        <col style="width: 30px;">
                        <col style="width: 80px;">
                        <col style="width: 120px;">
                        <col style="width: 45px;">
                        <col style="width: 80px;">
                    </colgroup>
                    <tr>
                        <th rowspan="4" class="vertical-title">공<br>급<br>받<br>는<br>자</th>
                        <th class="info-label">등록번호</th>
                        <td colspan="3" class="info-data"><span id="buyerBizNo">-</span></td>
                        
                        <th rowspan="4" class="vertical-title">공<br>급<br>자</th>
                        <th class="info-label">등록번호</th>
                        <td colspan="3" class="info-data fw-bold"><span id="supplierBizNo">417-81-42643</span></td>
                    </tr>
                    <tr>
                        <th class="info-label">상호(법인명)</th>
                        <td class="info-data"><span id="buyerName"></span></td>
                        <th class="info-label">대표</th>
                        <td class="info-data">-</td>
                        
                        <th class="info-label">상호(법인명)</th>
                        <td class="info-data"><span id="supplierName">농업회사법인<br>여수참맛(주)</span></td>
                        <th class="info-label">대표</th>
                        <td class="info-data" style="position: relative;">
                            배정옥 (인)
                        </td>
                    </tr>
                    <tr>
                        <th class="info-label">사업장주소</th>
                        <td colspan="3" class="info-data"><span id="buyerAddress">-</span></td>
                        
                        <th class="info-label">사업장주소</th>
                        <td colspan="3" class="info-data"><span id="supplierAddress">전라남도 여수시 돌산읍 우두리 497-13</span></td>
                    </tr>
                    <tr>
                        <th class="info-label">전화번호</th>
                        <td colspan="3" class="info-data"><span id="buyerTel">-</span></td>
                        
                        <th class="info-label">전화번호</th>
                        <td colspan="3" class="info-data"><span id="supplierTel">061-644-0990</span></td>
                    </tr>
                </table>

                <!-- 합계 금액 영역 -->
                <div class="grand-total-box">
                    <span>합계금액 (공급가액 + 세액) : </span>
                    <strong><span id="invoiceGrandTotal">0</span> 원</strong>
                    <span class="sub-text">(아래와 같이 계산합니다)</span>
                </div>

                <!-- 품목 상세 테이블 -->
                <table class="item-table">
                    <thead>
                        <tr>
                            <th style="width: 14%;">거래일자</th>
                            <th style="width: 21%;">품목명</th>
                            <th style="width: 10%;">규격</th>
                            <th style="width: 8%;">수량</th>
                            <th style="width: 12%;">단가</th>
                            <th style="width: 12%;">공급가액</th>
                            <th style="width: 10%;">세액</th>
                            <th style="width: 13%;">합계</th>
                        </tr>
                    </thead>
                    <tbody id="invoiceItems">
                        <!-- 자바스크립트에서 실제 데이터와 빈 줄이 자동으로 생성되어 들어갑니다. -->
                    </tbody>
                </table>
                
                <div class="invoice-footer">
                    인수자 : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; (서명)
                </div>

            </div>
            <!-- 인쇄 영역 끝 -->

            <div class='btn-group' style="text-align: center; margin-top: 30px;">
                <button type='button' class='modal-btn primary' id='btnPrintInvoice' style="padding: 10px 30px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px;">인쇄</button>&nbsp;
                <button type='button' class='modal-btn' id='btnCloseInvoiceModal' style="padding: 10px 30px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px;">닫기</button>
            </div>
		</div>
	</div>
</div>

<style>
/* 문서 기본 스타일 */
.invoice-document {
    background: white; 
    padding: 40px; 
    max-width: 800px; 
    margin: 0 auto;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    font-family: 'Malgun Gothic', '맑은 고딕', sans-serif;
    color: #333;
    box-sizing: border-box;
}

.invoice-document table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
}

.invoice-header {
    text-align: center;
    margin-bottom: 30px;
    border-bottom: 2px solid #000;
    padding-bottom: 10px;
}

.invoice-header h2 {
    margin: 0; 
    font-size: 32px; 
    font-weight: 800;
    letter-spacing: 15px;
    padding-left: 15px; /* 글자 간격때문에 치우쳐보이는 것 보정 */
}

/* 사업자 정보 테이블 */
.biz-info-table {
    margin-bottom: 20px;
    border: 2px solid #000;
}

.biz-info-table th, 
.biz-info-table td {
    border: 1px solid #000;
    padding: 6px 8px;
    font-size: 13px;
    height: 30px;
}

.vertical-title {
    text-align: center;
    background-color: #f4f4f4;
    font-weight: bold;
    line-height: 1.5;
}

.info-label {
    background-color: #f4f4f4;
    text-align: center;
    font-weight: normal;
}

.info-data {
    text-align: left;
}

.fw-bold { font-weight: bold; }

.stamp {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: red;
    font-weight: bold;
    border: 1px solid red;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    line-height: 22px;
    text-align: center;
    font-size: 12px;
}

/* 합계 영역 */
.grand-total-box {
    border: 2px solid #000;
    padding: 10px 15px;
    margin-bottom: 10px;
    font-size: 16px;
}

.grand-total-box strong {
    font-size: 18px;
}

.grand-total-box .sub-text {
    float: right;
    font-size: 13px;
    color: #666;
    margin-top: 4px;
}

/* 품목 테이블 */
.item-table {
    border: 2px solid #000;
    margin-bottom: 20px;
}

.item-table th, 
.item-table td {
    border: 1px solid #000;
    padding: 8px;
    font-size: 13px;
}

.item-table th {
    background-color: #f4f4f4;
    text-align: center;
    font-weight: bold;
}

.item-table tbody tr td {
    height: 30px; /* 빈 줄 높이 확보 */
}

.text-center { text-align: center; }
.text-left { text-align: left; }
.text-right { text-align: right; }

/* 하단 서명 영역 */
.invoice-footer {
    text-align: right;
    margin-top: 30px;
    font-size: 15px;
    font-weight: bold;
    padding-right: 20px;
}

/* 인쇄용 CSS */
@media print {
    body * {
        visibility: hidden;
    }
    #invoiceContent, #invoiceContent * {
        visibility: visible;
    }
    #invoiceContent {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        margin: 0;
        padding: 0;
        box-shadow: none; /* 인쇄시 그림자 제거 */
    }
    .modal-header,
    .btn-group {
        display: none !important;
    }
    /* 인쇄 시 배경색(회색 음영)이 나오도록 설정 */
    * {
        -webkit-print-color-adjust: exact !important;   
        print-color-adjust: exact !important;
    }
}
</style>

<script>
window.addEventListener('DOMContentLoaded', ()=>{	
    // 창닫기
    try {
        const btnCloseInvoice = document.getElementById('btnCloseInvoice');
        if(btnCloseInvoice) {
            btnCloseInvoice.addEventListener('click', function() {
                closeModal('modalNewInvoice');
            });
        }
    } catch(e) {}

    try {
        const btnCloseInvoiceModal = document.getElementById('btnCloseInvoiceModal');
        if(btnCloseInvoiceModal) {
            btnCloseInvoiceModal.addEventListener('click', function() {
                closeModal('modalNewInvoice');
            });
        }
    } catch(e) {}

    // 인쇄
    try {
        const btnPrintInvoice = document.getElementById('btnPrintInvoice');
        if (btnPrintInvoice) {
            btnPrintInvoice.addEventListener('click', () => {
                window.print();
            });
        }
    } catch (e) {
        console.log('An error occurred:', e.message);
    }
});    

const getInvoiceData = async (uid) => {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getDeliveryOrderItem');
    formData.append('uid', uid);

    try {
        const response = await fetch('./handler.php', {
            method: 'post',
            body: formData
        });

        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
        }

        const data = await response.json();
        setInvoiceData(data);
    } catch (error) {
        console.error('거래명세서 데이터를 가져오는 중 오류가 발생했습니다:', error);
        alert('거래명세서 데이터를 불러오는데 실패했습니다.');
    }
}

const setInvoiceData = (data) => {
    if (data) {
        // data가 배열인지 확인
        const items = Array.isArray(data) ? data : [data];
        
        // 공급받는자 정보 (첫 번째 항목 기준)
        document.getElementById('buyerName').textContent = items[0]?.account_name || '-';
        
        const tbody = document.getElementById('invoiceItems');
        tbody.innerHTML = ''; // 기존 내용 초기화
        
        let grandTotal = 0;
        const maxRows = 15; // A4 용지에 맞춘 총 줄 수 (필요시 조정)

        // 1. 실제 데이터 동적 생성
        items.forEach((item) => {
            const date = item.delivery_date || '-';
            const name = item.item_name || '-';
            const standard = item.standard || '-';
            const qty = parseFloat(item.delivery_qty || 0);
            const price = parseFloat(item.price || 0);
            
            const supplyAmount = price * qty;
            const tax = Math.floor(supplyAmount * 0.1);
            const total = supplyAmount + tax;
            
            grandTotal += total; // 총 합계 누적
            
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="text-center">${date}</td>
                <td class="text-left">${name}</td>
                <td class="text-center">${standard}</td>
                <td class="text-right">${comma(qty)}</td>
                <td class="text-right">${comma(price)}</td>
                <td class="text-right">${comma(supplyAmount)}</td>
                <td class="text-right">${comma(tax)}</td>
                <td class="text-right">${comma(total)}</td>
            `;
            tbody.appendChild(tr);
        });

        // 2. 남는 줄을 빈 줄로 채우기 (A4 형태 유지)
        const emptyRowsCount = maxRows - items.length;
        for (let i = 0; i < emptyRowsCount; i++) {
            const tr = document.createElement('tr');
            tr.innerHTML = `<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>`;
            tbody.appendChild(tr);
        }
        
        // 3. 총 합계 금액 업데이트
        document.getElementById('invoiceGrandTotal').textContent = comma(grandTotal);
    }
}
</script>
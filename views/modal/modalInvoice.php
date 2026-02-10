<div class="modal" id="modalInvoice">
	<div class="modal-content">
		<div class="modal-header">
			<span class='modal-title'>거래명세서</span>
			<span class="btn-close" id="btnCloseInvoice"><i class='bx bx-x'></i></span>
		</div>
		<div class="modal-body" style="overflow:auto; padding: 20px;">
            <div id="invoiceContent" style="background: white; padding: 30px; max-width: 800px; margin: 0 auto;">
                <div style="text-align: center; margin-bottom: 30px;">
                    <h2 style="margin: 0; font-size: 24px; font-weight: bold;">거래명세서</h2>
                </div>
                
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                    <tr>
                        <td style="width: 50%; vertical-align: top; padding: 10px;">
                            <div style="border: 1px solid #000; padding: 15px; height: 150px;">
                                <div style="font-weight: bold; margin-bottom: 10px;">공급자</div>
                                <div id="supplierInfo">
                                    <div>상호: <span id="supplierName">농업회사법인 여수참맛(주)</span></div>
                                    <div>사업자번호: <span id="supplierBizNo">417-81-42643</span></div>
                                    <div>주소: <span id="supplierAddress">전라남도 여수시 돌산읍 우두리 497-13</span></div>
                                    <div>전화: <span id="supplierTel">061-644-0990</span></div>
                                </div>
                            </div>
                        </td>
                        <td style="width: 50%; vertical-align: top; padding: 10px;">
                            <div style="border: 1px solid #000; padding: 15px; height: 150px;">
                                <div style="font-weight: bold; margin-bottom: 10px;">공급받는자</div>
                                <div id="buyerInfo">
                                    <div>상호: <span id="buyerName"></span></div>
                                    <div>사업자번호: <span id="buyerBizNo">-</span></div>
                                    <div>주소: <span id="buyerAddress">-</span></div>
                                    <div>전화: <span id="buyerTel">-</span></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>

                <table style="width: 100%; border-collapse: collapse; border: 1px solid #000; margin-bottom: 20px;">
                    <thead>
                        <tr style="background-color: #f5f5f5;">
                            <th style="border: 1px solid #000; padding: 10px; text-align: center;">거래일자</th>
                            <th style="border: 1px solid #000; padding: 10px; text-align: center;">품목명</th>
                            <th style="border: 1px solid #000; padding: 10px; text-align: center;">규격</th>
                            <th style="border: 1px solid #000; padding: 10px; text-align: center;">수량</th>
                            <th style="border: 1px solid #000; padding: 10px; text-align: center;">단가</th>
                            <th style="border: 1px solid #000; padding: 10px; text-align: center;">공급가액</th>
                            <th style="border: 1px solid #000; padding: 10px; text-align: center;">세액</th>
                            <th style="border: 1px solid #000; padding: 10px; text-align: center;">합계</th>
                        </tr>
                    </thead>
                    <tbody id="invoiceItems">
                        <tr>
                            <td style="border: 1px solid #000; padding: 10px; text-align: center;" id="invoiceDate"></td>
                            <td style="border: 1px solid #000; padding: 10px; text-align: center;" id="invoiceItemName"></td>
                            <td style="border: 1px solid #000; padding: 10px; text-align: center;" id="invoiceStandard"></td>
                            <td style="border: 1px solid #000; padding: 10px; text-align: center;" id="invoiceQty"></td>
                            <td style="border: 1px solid #000; padding: 10px; text-align: right;" id="invoiceUnitPrice"></td>
                            <td style="border: 1px solid #000; padding: 10px; text-align: right;" id="invoiceSupplyAmount"></td>
                            <td style="border: 1px solid #000; padding: 10px; text-align: right;" id="invoiceTax"></td>
                            <td style="border: 1px solid #000; padding: 10px; text-align: right;" id="invoiceTotal"></td>
                        </tr>
                    </tbody>
                </table>

                <div style="text-align: right; margin-top: 20px;">
                    <div style="margin-bottom: 10px;">
                        <strong>합계금액: <span id="invoiceGrandTotal">0</span>원</strong>
                    </div>
                </div>
            </div>

            <div class='btn-group' style="text-align: center; margin-top: 20px;">
                <input type='button' class='modal-btn primary' id='btnPrintInvoice' value='인쇄' />&nbsp;
                <input type='button' class='modal-btn' id='btnCloseInvoiceModal' value='닫기' />
            </div>
		</div>
	</div>
</div>

<style>
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
    }
    .modal-header,
    .btn-group {
        display: none !important;
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
                closeModal('modalInvoice');
            });
        }
    } catch(e) {}

    try {
        const btnCloseInvoiceModal = document.getElementById('btnCloseInvoiceModal');
        if(btnCloseInvoiceModal) {
            btnCloseInvoiceModal.addEventListener('click', function() {
                closeModal('modalInvoice');
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
        
        // 공급받는자 정보 (첫 번째 항목 사용)
        document.getElementById('buyerName').textContent = items[0]?.account_name || '-';
        
        // 거래일자 (첫 번째 항목 사용)
        document.getElementById('invoiceDate').textContent = items[0]?.delivery_date || '-';
        
        // 품목 정보 (첫 번째 항목 사용)
        document.getElementById('invoiceItemName').textContent = items[0]?.item_name || '-';
        document.getElementById('invoiceStandard').textContent = items[0]?.standard || '-';
        document.getElementById('invoiceQty').textContent = comma(items[0]?.delivery_qty || 0);
        
        // 단가, 공급가액, 세액, 합계 계산
        const price = parseFloat(items[0]?.price || 0); // 단가 = price
        const qty = parseFloat(items[0]?.delivery_qty || 0);
        const supplyAmount = price * qty; // 공급가액 = price * 수량
        const tax = Math.floor(supplyAmount * 0.1); // 세액 = 공급가액 * 0.1 (부가세 10%)
        const total = supplyAmount + tax; // 합계 = 공급가액 + 세액
        
        document.getElementById('invoiceUnitPrice').textContent = comma(price);
        document.getElementById('invoiceSupplyAmount').textContent = comma(supplyAmount);
        document.getElementById('invoiceTax').textContent = comma(tax);
        document.getElementById('invoiceTotal').textContent = comma(total);
        
        // 합계금액: 모든 row의 합계(공급가액 + 세액)를 합계
        const grandTotal = items.reduce((sum, item) => {
            const itemPrice = parseFloat(item.price || 0);
            const itemQty = parseFloat(item.delivery_qty || 0);
            const itemSupplyAmount = itemPrice * itemQty;
            const itemTax = Math.floor(itemSupplyAmount * 0.1);
            const itemTotal = itemSupplyAmount + itemTax;
            return sum + itemTotal;
        }, 0);
        document.getElementById('invoiceGrandTotal').textContent = comma(grandTotal);
    }
}
</script>


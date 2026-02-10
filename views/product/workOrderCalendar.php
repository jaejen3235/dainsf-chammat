<div class='main-container'>    
    <div class='content-wrapper'>            
        <div id="calendar-container" style="width:100%;margin:0 auto;">
            <div id="calendar-controls" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                <button id="prevMonth" class="btn grey">&lt;</button>
                <span id="calendarMonth" style="font-size:1.2em;font-weight:bold;"></span>
                <button id="nextMonth" class="btn grey">&gt;</button>
            </div>
            <table id="workOrderCalendar" class="calendar-table" style="width:100%;border-collapse:collapse;border:1px solid #ddd;table-layout:fixed;">
                <thead>
                    <tr>
                        <th style="color:red;background:#f5f5f5;padding:12px;border:1px solid #ddd;text-align:center;font-size:14px;">일</th>
                        <th style="background:#f5f5f5;padding:12px;border:1px solid #ddd;text-align:center;font-size:14px;">월</th>
                        <th style="background:#f5f5f5;padding:12px;border:1px solid #ddd;text-align:center;font-size:14px;">화</th>
                        <th style="background:#f5f5f5;padding:12px;border:1px solid #ddd;text-align:center;font-size:14px;">수</th>
                        <th style="background:#f5f5f5;padding:12px;border:1px solid #ddd;text-align:center;font-size:14px;">목</th>
                        <th style="background:#f5f5f5;padding:12px;border:1px solid #ddd;text-align:center;font-size:14px;">금</th>
                        <th style="color:blue;background:#f5f5f5;padding:12px;border:1px solid #ddd;text-align:center;font-size:14px;">토</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
            <div id="calendar-legend" style="display:flex;gap:16px;margin:12px 0 0 0;">
                <div style="display:flex;align-items:center;gap:4px;">
                    <div style="width:16px;height:16px;background:#1976d2;border:1px solid #1976d2;border-radius:3px;"></div>
                    <span style="font-size:13px;">대기</span>
                </div>
                <div style="display:flex;align-items:center;gap:4px;">
                    <div style="width:16px;height:16px;background:#ff9800;border:1px solid #ff9800;border-radius:3px;"></div>
                    <span style="font-size:13px;">진행중</span>
                </div>
                <div style="display:flex;align-items:center;gap:4px;">
                    <div style="width:16px;height:16px;background:#4caf50;border:1px solid #4caf50;border-radius:3px;"></div>
                    <span style="font-size:13px;">완료</span>
                </div>
            </div>
        </div>        
    </div>    
</div>


<?php
include "./views/modal/modalViewWorkOrder.php";
?>

<script>
// 달력 렌더링 및 생산지시 목록 표시
let currentDate = new Date();
// 실제 데이터를 저장할 변수
let workOrders = [];

async function fetchWorkOrders(year, month) {
    // 내가 쓰는 방식(FormData + handler.php + controller/mode)으로 작성
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getWorkOrdersCalendar');
    formData.append('year', year);
    formData.append('month', month);

    try {
        const response = await fetch('./handler.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        // 받아온 데이터를 workOrders에 저장
        workOrders = (data && data.data) ? data.data : [];
        return workOrders; // 데이터를 반환하도록 수정
    } catch (error) {
        console.error('작업지시 데이터를 가져오는 중 오류가 발생했습니다:', error);
        workOrders = [];
        return workOrders; // 오류 시에도 빈 배열 반환
    }
}

function renderCalendar(date, workOrders) {
    const year = date.getFullYear();
    const month = date.getMonth(); // 0~11
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const startDay = firstDay.getDay(); // 0(일)~6(토)
    const totalDays = lastDay.getDate();
    
    // 월 표시 업데이트
    document.getElementById('calendarMonth').textContent = `${year}년 ${month+1}월`;
    
    let tbody = '';
    let day = 1 - startDay;
    
    // 6주까지 표시 (달력의 최대 행 수)
    for(let week = 0; week < 6; week++) {
        let tr = '<tr>';
        for(let dayOfWeek = 0; dayOfWeek < 7; dayOfWeek++) {
            if(day < 1 || day > totalDays) {
                // 이전/다음 달의 날짜
                tr += '<td style="background:#f9f9f9;height:120px;border:1px solid #ddd;padding:4px;"></td>';
            } else {
                // 현재 달의 날짜
                const dayStr = `${year}-${String(month+1).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
                const orders = workOrders.filter(o => {
                    // work_date 필드 사용
                    console.log('비교:', o.order_date, dayStr, o.order_date === dayStr); // 디버깅용
                    return o.order_date === dayStr;
                });
                
                let content = `<div style="font-weight:bold;margin-bottom:6px;font-size:16px;">${day}</div>`;
                
                if(orders.length > 0) {
                    orders.forEach(order => {
                        let color = order.status === '완료' ? '#4caf50' : (order.status === '진행중' ? '#ff9800' : '#2196f3');
                        content += `<div class="work-order-item" data-uid="${order.uid}" style="font-size:11px;background:${color};color:#fff;padding:2px 4px;margin:2px 0;border-radius:3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;cursor:pointer;" onclick="openWorkOrderDetail(${order.uid})">${order.item_name} 생산</div>`;
                    });
                }
                
                tr += `<td style="vertical-align:top;height:120px;border:1px solid #ddd;padding:4px;background:#fff;">${content}</td>`;
            }
            day++;
        }
        tr += '</tr>';
        tbody += tr;
        
        // 마지막 주이고 다음 달의 날짜가 시작되면 중단
        if(day > totalDays) break;
    }
    
    document.querySelector('#workOrderCalendar tbody').innerHTML = tbody;
}
async function updateCalendar() {
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth() + 1;
    
    // 로딩 상태 표시
    document.getElementById('calendarMonth').textContent = `${year}년 ${month}월 (로딩중...)`;
    
    try {
        // 실제 데이터 가져오기
        workOrders = await fetchWorkOrders(year, month);
        console.log('받아온 작업지시 데이터:', workOrders); // 디버깅용 로그
        renderCalendar(currentDate, workOrders);
    } catch (error) {
        console.error('달력 업데이트 중 오류:', error);
        // 오류 발생 시 빈 달력 렌더링
        renderCalendar(currentDate, []);
    }
}
document.getElementById('prevMonth').addEventListener('click', function() {
    currentDate.setMonth(currentDate.getMonth() - 1);
    updateCalendar();
});
document.getElementById('nextMonth').addEventListener('click', function() {
    currentDate.setMonth(currentDate.getMonth() + 1);
    updateCalendar();
});
// 최초 렌더링
updateCalendar();

// 작업지시 상세 모달 열기 함수
function openWorkOrderDetail(uid) {    
    getter(uid);
    openModal('modalViewWorkOrder', 800, 350);
}
</script>
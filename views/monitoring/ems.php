<div class='main-container'>
    <div class='content-wrapper'>
        <div>
            <div class="kpi-summary">
                <div class="kpi-card">
                    <h3>누적 전력사용량</h3>
                    <div class='flex-center'>
                        <p id="totalPowerUsage" class="kpi-value">로딩 중...</p>
                        <p>kW</p>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <div class="table-section">
                <div class="flex">
                    <div class="title red">소비전류 실시간 현황</div>
                </div>
                <table class="list">
                    <thead>
                        <tr>
                            <th>설비명</th>
                            <th>데이터 타입</th>
                            <th>값</th>
                            <th>수집일시</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // 페이지 로드 후 즉시 첫 번째 반복 실행
    repeatInspection();
});

const INTERVAL_MS = 5000; // 5초 (5000ms)

// 비동기 함수를 안전하게 반복 실행하는 함수
const repeatInspection = async () => {
    try {
        // 1. getLeakageInspection 실행 (await을 사용하여 완료될 때까지 대기)
        await getLeakageInspection({page:1});
        
    } catch (error) {
        console.error("누전 검사 데이터 갱신 중 오류 발생:", error);
        // 에러가 발생하더라도 다음 시도는 진행하거나, 필요시 여기에 재시도 로직을 추가할 수 있습니다.
    }
    
    // 2. 현재 작업이 완료된 후, 지정된 시간(5초) 후에 repeatInspection을 다시 호출
    setTimeout(repeatInspection, INTERVAL_MS);
};

const getLeakageInspection = async ({
    page,
    per = 15,
    block = 4,
    orderBy = 'uid',
    order = 'desc'
}) => {    
    let where = `where data_type='current'`;    

    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getLeakageInspection');
    formData.append('where', where);
    formData.append('page', page);
    formData.append('per', per);
    formData.append('orderby', orderBy);
    formData.append('asc', order);

    try {
        const response = await fetch('./handler.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        const tableBody = document.querySelector('.list tbody');
        tableBody.innerHTML = generateTableContent(data);

        //getPaging('mes_account', 'uid', where, page, per, block, 'getAccountList');
    } catch (error) {
        console.error('거래처 데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
};

const generateTableContent = (data) => {
    if (!data || data.data.length === 0) {
        document.getElementById('totalPowerUsage').innerText = '0';
        return `<tr><td class='center' colspan='20'>검색된 자료가 없습니다</td></tr>`;
    }

    let totalPower = 0;

    const tableRows = data.data.map(item => {
        // **!!! 수정된 부분: 안전한 숫자 변환 !!!**
        const numericValue = parseFloat(item.value);
        // numericValue가 NaN이면 0을 더하도록 처리
        totalPower += isNaN(numericValue) ? 0 : numericValue;

        return `
            <tr>
                <td class='center'>${item.machine}</td>
                <td class='center'>${item.data_type}</td>
                <td class='center'>${item.value}</td>
                <td class='center'>${item.timestamp}</td>
            </tr>
        `;
    }).join('');

    const result = calculatePowerAndCost({
        current_ampere: totalPower,
        voltage: 380,
        daily_hours: 8,
        system_type: 'three'
    });
    
    console.log(totalPower);

    // **NaN 또는 유효하지 않은 값에 대한 최종 검사 (안전장치)**
    let displayPower = (typeof result.power_kw === 'number' && !isNaN(result.power_kw)) 
                       ? result.power_kw.toLocaleString() 
                       : '0';

    document.getElementById('totalPowerUsage').innerText = displayPower;

    return tableRows;
};

/**
 * 암페어(A)를 입력받아 kW, 일일 소비 전력량(kWh), 예상 일일 요금을 계산합니다.
 *
 * @param {number} currentAmpere 전류 (암페어, A)
 * @param {number} [voltage=220.0] 전압 (볼트, V). 기본값: 220V
 * @param {number} [powerFactor=0.9] 역률 (Power Factor, Pf). 기본값: 0.9
 * @param {number} [dailyHours=8.0] 하루 가동 시간 (시간). 기본값: 8시간
 * @param {string} [systemType='three'] 시스템 종류 ('single' 또는 'three'). 기본값: 'three'
 * @returns {{input_ampere: number, system_type: string, power_kw: number, daily_kwh: number, estimated_daily_cost: number, unit_cost_kwh: number}}
 * 계산된 전력, 전력량, 예상 요금을 포함하는 객체
 */
/**
 * 암페어(A)를 입력받아 kW, 일일 소비 전력량(kWh), 예상 일일 요금을 계산합니다.
 */
/**
 * 암페어(A)를 입력받아 kW, 일일 소비 전력량(kWh), 예상 일일 요금을 계산합니다.
 *
 * 이 함수는 단일 객체 인수를 받으며, 내부에서 전력(W) 계산 공식을 사용하여
 * 전류(A)를 전력(kW)으로 정확하게 환산합니다.
 *
 * @param {object} args
 * @param {number} args.current_ampere 전류 (암페어, A)
 * @param {number} [args.voltage=220.0] 전압 (볼트, V)
 * @param {number} [args.power_factor=0.9] 역률 (Power Factor, Pf)
 * @param {number} [args.daily_hours=8.0] 하루 가동 시간 (시간)
 * @param {string} [args.system_type='three'] 시스템 종류 ('single' 또는 'three')
 * @returns {object} 계산된 전력, 전력량, 예상 요금을 포함하는 객체
 */
function calculatePowerAndCost({
    current_ampere, // 객체에서 추출된 전류 값 (숫자)
    voltage = 220.0,
    power_factor = 0.9,
    daily_hours = 8.0,
    system_type = 'three'
}) {
    // 1. 상수 정의
    // 산업용 전력 단가 (참고용)
    const UNIT_COST_PER_KWH = 94.0;
    // Math.sqrt(3)의 정밀값
    const SQRT_OF_3 = 1.7320508; 

    let powerWatt = 0.0;

    // **안전 장치:** current_ampere가 유효한 숫자인지 확인 (NaN 또는 null 방지)
    const I = parseFloat(current_ampere);
    if (isNaN(I)) {
         // 전류 값이 유효하지 않으면 0으로 처리하거나, 오류 객체를 반환할 수 있습니다.
         // 여기서는 0으로 처리하여 NaN 전파를 막습니다.
         current_ampere = 0;
    } else {
        current_ampere = I;
    }

    // 2. 전력(W) 계산 (P = V * I * Pf * 계수)
    if (system_type.toLowerCase() === 'three') {
        // 삼상 전력 공식: P = sqrt(3) * V * I * Pf
        powerWatt = SQRT_OF_3 * voltage * current_ampere * power_factor;
    } else {
        // 단상 전력 공식: P = V * I * Pf
        powerWatt = voltage * current_ampere * power_factor;
    }

    // 3. kW로 변환
    const powerKw = powerWatt / 1000.0;

    // 4. 일일 소비 전력량 (kWh) 계산
    const dailyKwh = powerKw * daily_hours;

    // 5. 예상 일일 요금 계산
    const estimatedDailyCost = dailyKwh * UNIT_COST_PER_KWH;

    return {
        'input_ampere': current_ampere,
        'system_type': system_type,
        // toFixed(2) 후 parseFloat()을 사용하여 숫자로 반환
        'power_kw': parseFloat(powerKw.toFixed(2)), 
        'daily_kwh': parseFloat(dailyKwh.toFixed(2)),
        'estimated_daily_cost': Math.round(estimatedDailyCost), // 정수 반올림
        'unit_cost_kwh': UNIT_COST_PER_KWH
    };
}

// 🚀 사용 예시 (PHP 예시와 동일한 조건):
// 30 암페어, 380V 삼상 설비를 하루 10시간 가동한다고 가정
/*
const result = calculatePowerAndCost(
    30,   // currentAmpere
    380,  // voltage
    0.9,  // powerFactor (기본값 사용 가능)
    10,   // dailyHours
    'three' // systemType (기본값 사용 가능)
);

console.log(result);
*/
</script>


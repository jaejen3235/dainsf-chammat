<div class='main-container'>
    <div class="page-title"><i class='bx bxs-food-menu'></i> 기본 단위 설정</div> 
    <div class='flex-wrapper'>
        <div class='flex-section'>
            <div class='card-title flex'>
                <div><i class='bx bxs-category'></i> 품목 구분</div>
                <div>
                    <input type='text' name='itemDiv' id='itemDiv' />
                    <input type='hidden' name='itemDivUid' id='itemDivUid' />
                    <input type='button' class='btn-small primary' id='btnRegisterItemDiv' value='등록' />
                </div>
            </div>
            <div>
                <table class='list item-div'>
                    <colgroup>
                        <col />
                        <col width='150' />
                    </colgroup>
                    <thead>
                        <tr>
                            <th>구분명</th>
                            <th>관리</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <div class='flex-section'>
            <div class='card-title flex'>
                <div><i class='bx bxs-category'></i> 품목 단위</div>
                <div>
                    <input type='text' name='itemUnit' id='itemUnit' />
                    <input type='hidden' name='itemUnitUid' id='itemUnitUid' />
                    <input type='button' class='btn-small primary' id='btnRegisterItemUnit' value='등록' />
                </div>
            </div>
            <div>
                <table class='list item-unit'>
                    <colgroup>                                            
                        <col />
                        <col width='150' />
                    </colgroup>
                    <thead>
                        <tr>
                            <th>단위명</th>
                            <th>관리</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <div class='flex-section'>
            <div class='card-title flex'>
                <div><i class='bx bxs-category'></i> 거래처 구분</div>
                <div>
                    <input type='text' name='accountDiv' id='accountDiv' />
                    <input type='hidden' name='accountDivUid' id='accountDivUid' />
                    <input type='button' class='btn-small primary' id='btnRegisterAccountDiv' value='등록' />
                </div>
            </div>
            <div>
                <table class='list account-div'>
                    <colgroup>                                            
                        <col />
                        <col width='150' />
                    </colgroup>
                    <thead>
                        <tr>
                            <th>구분명</th>
                            <th>관리</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <div class='flex-section'>
            <div class='card-title flex'>
                <div><i class='bx bxs-category'></i> 부서 관리</div>
                <div>
                    <input type='text' name='department' id='department' />
                    <input type='hidden' name='departmentUid' id='departmentUid' />
                    <input type='button' class='btn-small primary' id='btnRegisterDepartment' value='등록' />
                </div>
            </div>
            <div>
                <table class='list department'>
                    <colgroup>                                            
                        <col />
                        <col width='150' />
                    </colgroup>
                    <thead>
                        <tr>
                            <th>구분명</th>
                            <th>관리</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <div class='flex-section'>
            <div class='card-title flex'>
                <div><i class='bx bxs-category'></i> 직급 관리</div>
                <div>
                    <input type='text' name='rank' id='rank' />
                    <input type='hidden' name='rankUid' id='rankUid' />
                    <input type='button' class='btn-small primary' id='btnRegisterRank' value='등록' />
                </div>
            </div>
            <div>
                <table class='list rank'>
                    <colgroup>                                            
                        <col />
                        <col width='150' />
                    </colgroup>
                    <thead>
                        <tr>
                            <th>구분명</th>
                            <th>관리</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <div class='flex-section'>
            <div class='card-title flex'>
                <div><i class='bx bxs-category'></i> 공정 관리</div>
                <div>
                    <label class="custom-checkbox">
                        <input type="checkbox" class='chk' name='lastProcess' id='lastProcess' value='Y'>
                        <span class="checkmark"></span> 입고공정&nbsp;                            
                    </label>
                    <input type='text' name='process' id='process' />
                    <input type='hidden' name='processUid' id='processUid' />
                    <input type='button' class='btn-small primary' id='btnRegisterProcess' value='등록' />
                </div>
            </div>
            <div>
                <table class='list process'>
                    <colgroup>                                            
                        <col />
                        <col />
                        <col width='150' />
                    </colgroup>
                    <thead>
                        <tr>
                            <th>공정명</th>
                            <th>입고공정</th>
                            <th>관리</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<script>
window.addEventListener('DOMContentLoaded', ()=>{
    // 품목 구분
    try {
        const itemDivUid = document.getElementById('itemDivUid');
        itemDivUid.addEventListener('keydown', function(event) {
            if (event.key === 'Enter' || event.keyCode === 13) {
                if(itemDiv.value != '') {
                    registerItemDiv();
                } else {
                    alert('품목 구분명을 입력하세요');
                }
            }
        });
    } catch(e) {}

    try {
        const btnRegisterItemDiv = document.getElementById('btnRegisterItemDiv');
        const itemDiv = document.getElementById('itemDiv');

        if(btnRegisterItemDiv) {
            btnRegisterItemDiv.addEventListener('click', () => {
                if(itemDiv.value != '') {
                    registerItemDiv();
                } else {
                    alert('품목 구분명을 입력하세요');
                }
            });
        }
    } catch(e) {}

    // 품목단위
    try {
        const itemUnit = document.getElementById('itemUnit');
        itemUnit.addEventListener('keydown', function(event) {
            if (event.key === 'Enter' || event.keyCode === 13) {
                if(itemUnit.value != '') {
                    registerItemUnit();
                } else {
                    alert('품목 단위명을 입력하세요');
                }
            }
        });
    } catch(e) {}

    try {
        const btnRegisterItemUnit = document.getElementById('btnRegisterItemUnit');
        const itemDiv = document.getElementById('itemUnit');

        if(btnRegisterItemUnit) {
            btnRegisterItemUnit.addEventListener('click', () => {
                if(itemUnit.value != '') {
                    registerItemUnit();
                } else {
                    alert('품목 단위명을 입력하세요');
                }
            });
        }
    } catch(e) {}

    // 거래처구분
    try {
        const accountDiv = document.getElementById('accountDiv');
        accountDiv.addEventListener('keydown', function(event) {
            if (event.key === 'Enter' || event.keyCode === 13) {
                if(accountDiv.value != '') {
                    registerAccountDiv();
                } else {
                    alert('거래처 구분명을 입력하세요');
                }
            }
        });
    } catch(e) {}
    
    try {
        const btnRegisterAccountDiv = document.getElementById('btnRegisterAccountDiv');
        const accountDiv = document.getElementById('accountDiv');

        if(btnRegisterAccountDiv) {
            btnRegisterAccountDiv.addEventListener('click', () => {
                if(accountDiv.value != '') {
                    registerAccountDiv();
                } else {
                    alert('거래처 구분명을 입력하세요');
                }
            });
        }
    } catch(e) {}

    // 부서관리
    try {
        const department = document.getElementById('department');
        department.addEventListener('keydown', function(event) {
            if (event.key === 'Enter' || event.keyCode === 13) {
                if(department.value != '') {
                    registerDepartment();
                } else {
                    alert('부서명을 입력하세요');
                }
            }
        });
    } catch(e) {}

    try {
        const btnRegisterDepartment = document.getElementById('btnRegisterDepartment');
        const department = document.getElementById('department');

        if(btnRegisterDepartment) {
            btnRegisterDepartment.addEventListener('click', () => {
                if(department.value != '') {
                    registerDepartment();
                } else {
                    alert('부서명을 입력하세요');
                }
            });
        }
    } catch(e) {}


    // 직급관리
    try {
        const rank = document.getElementById('rank');
        rank.addEventListener('keydown', function(event) {
            if (event.key === 'Enter' || event.keyCode === 13) {
                if(rank.value != '') {
                    registerRank();
                } else {
                    alert('직급명을 입력하세요');
                }
            }
        });
    } catch(e) {}

    try {
        const btnRegisterRank = document.getElementById('btnRegisterRank');
        const rank = document.getElementById('rank');

        if(btnRegisterRank) {
            btnRegisterRank.addEventListener('click', () => {
                if(rank.value != '') {
                    registerRank();
                } else {
                    alert('직급명을 입력하세요');
                }
            });
        }
    } catch(e) {}

    // 공정관리
    try {
        const process = document.getElementById('process');
        process.addEventListener('keydown', function(event) {
            if (event.key === 'Enter' || event.keyCode === 13) {
                if(process.value != '') {
                    registerProcess();
                } else {
                    alert('공정명을 입력하세요');
                }
            }
        });
    } catch(e) {}

    try {
        const btnRegisterProcess = document.getElementById('btnRegisterProcess');
        const rank = document.getElementById('process');

        if(btnRegisterProcess) {
            btnRegisterProcess.addEventListener('click', () => {
                if(rank.value != '') {
                    registerProcess();
                } else {
                    alert('공정명을 입력하세요');
                }
            });
        }
    } catch(e) {}

    getItemDivList();
    getItemUnitList();
    getAccountDivList();
    getDepartmentList();
    getRankList();
    getProcessList();
});

// 품목 구분 ========================================================================================
const registerItemDiv = () => {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'registerItemDiv');
    formData.append('name', document.getElementById('itemDiv').value);
    formData.append('uid', document.getElementById('itemDivUid').value);

	fetch('./handler.php', {
		method: 'post',
		body : formData
	})
	.then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        return response.json();
    })
	.then(function(data) {
		if(data != null || data != '') {
			if(data.result == 'success') {
                getItemDivList();
            }

            document.getElementById('itemDiv').value = '';                
            document.getElementById('itemDivUid').value = ''; 
            //alert(data.message);
		}               
	})
	.catch(error => console.log(error));
}

const getItemDivList = async () => {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getItemDivList');

    try {
        const response = await fetch('./handler.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        const tableBody = document.querySelector('.item-div tbody');
        tableBody.innerHTML = generateItemDivTableContent(data);
    } catch (error) {
        console.error('품목구분 데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
};

const generateItemDivTableContent = (data) => {
    if (!data || data.length === 0) {
        return `<tr><td class='center' colspan='20'>검색된 자료가 없습니다</td></tr>`;
    }

    return data.map(item => `
        <tr>
            <td class='center'>${item.name}</td>
            <td class='center'>
                <input type='button' class='btn-small grey' value='수정' onclick='getterItemDiv(${item.uid})' />
                <input type='button' class='btn-small danger' value='삭제' onclick='deleteItemDiv(${item.uid})' />
            </td>
        </tr>
    `).join('');
};

const getterItemDiv = async (uid) => {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getItemDiv');
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
        setterItemDiv(data);
    } catch (error) {
        displayError(error);
    }
}

const setterItemDiv = (data) => {
    if (data) {
        document.getElementById('itemDivUid').value = data.uid;
        document.getElementById('itemDiv').value = data.name;
    }
}

const deleteItemDiv = (uid) => {
    if(confirm('해당 데이터를 삭제하시겠습니까?')) {
        const formData = new FormData();
        formData.append('controller', 'functions');
        formData.append('mode', 'deleteRow');
        formData.append('table', 'mes_classification');
        formData.append('uid', uid);

        fetch('./handler.php', {
            method: 'post',
            body : formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(function(data) {
            if(data != null || data != '') {                
                if(data.result == 'success') {
                    getItemDivList();
                }

                alert(data.message);
            }
        })
        .catch(error => console.log(error));
    }
}

// 품목 단위 ========================================================================================
const registerItemUnit = () => {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'registerItemUnit');
    formData.append('name', document.getElementById('itemUnit').value);
    formData.append('uid', document.getElementById('itemUnitUid').value);

	fetch('./handler.php', {
		method: 'post',
		body : formData
	})
	.then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        return response.json();
    })
	.then(function(data) {
		if(data != null || data != '') {
			if(data.result == 'success') {
                getItemUnitList();
            }

            document.getElementById('itemUnit').value = '';                
            document.getElementById('itemUnitUid').value = ''; 
            //alert(data.message);
		}               
	})
	.catch(error => console.log(error));
}

const getItemUnitList = async () => {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getItemUnitList');

    try {
        const response = await fetch('./handler.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        const tableBody = document.querySelector('.item-unit tbody');
        tableBody.innerHTML = generateItemUnitTableContent(data);
    } catch (error) {
        console.error('품목단위 데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
};

const generateItemUnitTableContent = (data) => {
    if (!data || data.length === 0) {
        return `<tr><td class='center' colspan='20'>검색된 자료가 없습니다</td></tr>`;
    }

    return data.map(item => `
        <tr>
            <td class='center'>${item.name}</td>
            <td class='center'>
                <input type='button' class='btn-small grey' value='수정' onclick='getterItemUnit(${item.uid})' />
                <input type='button' class='btn-small danger' value='삭제' onclick='deleteItemUnit(${item.uid})' />
            </td>
        </tr>
    `).join('');
};

const getterItemUnit = async (uid) => {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getItemUnit');
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
        setterItemUnit(data);
    } catch (error) {
        displayError(error);
    }
}

const setterItemUnit = (data) => {
    if (data) {
        document.getElementById('itemUnitUid').value = data.uid;
        document.getElementById('itemUnit').value = data.name;
    }
}

const deleteItemUnit = (uid) => {
    if(confirm('해당 데이터를 삭제하시겠습니까?')) {
        const formData = new FormData();
        formData.append('controller', 'functions');
        formData.append('mode', 'deleteRow');
        formData.append('table', 'mes_unit');
        formData.append('uid', uid);

        fetch('./handler.php', {
            method: 'post',
            body : formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(function(data) {
            if(data != null || data != '') {                
                if(data.result == 'success') {
                    getItemUnitList();
                }

                alert(data.message);
            }
        })
        .catch(error => console.log(error));
    }
}

// 거래처 구분 ======================================================================================
const registerAccountDiv = () => {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'registerAccountDiv');
    formData.append('name', document.getElementById('accountDiv').value);
    formData.append('uid', document.getElementById('accountDivUid').value);

	fetch('./handler.php', {
		method: 'post',
		body : formData
	})
	.then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        return response.json();
    })
	.then(function(data) {
		if(data != null || data != '') {
			if(data.result == 'success') {
                getAccountDivList();
            }

            document.getElementById('accountDiv').value = '';                
            document.getElementById('accountDivUid').value = ''; 
            //alert(data.message);
		}               
	})
	.catch(error => console.log(error));
}

const getAccountDivList = async () => {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getAccountClassificationList');

    try {
        const response = await fetch('./handler.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        const tableBody = document.querySelector('.account-div tbody');
        tableBody.innerHTML = generateAccountDivTableContent(data);
    } catch (error) {
        console.error('품목구분 데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
};

const generateAccountDivTableContent = (data) => {
    if (!data || data.length === 0) {
        return `<tr><td class='center' colspan='20'>검색된 자료가 없습니다</td></tr>`;
    }

    return data.map(item => `
        <tr>
            <td class='center'>${item.name}</td>
            <td class='center'>
                <input type='button' class='btn-small grey' value='수정' onclick='getterAccountDiv(${item.uid})' />
                <input type='button' class='btn-small danger' value='삭제' onclick='deleteAccountDiv(${item.uid})' />
            </td>
        </tr>
    `).join('');
};

const getterAccountDiv = async (uid) => {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getAccountClassification');
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
        setterAccountDiv(data);
    } catch (error) {
        displayError(error);
    }
}

const setterAccountDiv = (data) => {
    if (data) {
        document.getElementById('accountDivUid').value = data.uid;
        document.getElementById('accountDiv').value = data.name;
    }
}

const deleteAccountDiv = (uid) => {
    if(confirm('해당 데이터를 삭제하시겠습니까?')) {
        const formData = new FormData();
        formData.append('controller', 'functions');
        formData.append('mode', 'deleteRow');
        formData.append('table', 'mes_account_classification');
        formData.append('uid', uid);

        fetch('./handler.php', {
            method: 'post',
            body : formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(function(data) {
            if(data != null || data != '') {                
                if(data.result == 'success') {
                    getAccountDivList();
                }

                alert(data.message);
            }
        })
        .catch(error => console.log(error));
    }
}
// 부서 관리 ========================================================================================
const registerDepartment = () => {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'registerDepartment');
    formData.append('name', document.getElementById('department').value);
    formData.append('uid', document.getElementById('departmentUid').value);

	fetch('./handler.php', {
		method: 'post',
		body : formData
	})
	.then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        return response.json();
    })
	.then(function(data) {
		if(data != null || data != '') {
			if(data.result == 'success') {
                getDepartmentList();
            }

            document.getElementById('department').value = '';                
            document.getElementById('departmentUid').value = ''; 
            //alert(data.message);
		}               
	})
	.catch(error => console.log(error));
}

const getDepartmentList = async () => {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getDepartmentList');

    try {
        const response = await fetch('./handler.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        const tableBody = document.querySelector('.department tbody');
        tableBody.innerHTML = generateDepartmentTableContent(data);
    } catch (error) {
        console.error('부서명 데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
};

const generateDepartmentTableContent = (data) => {
    if (!data || data.length === 0) {
        return `<tr><td class='center' colspan='20'>검색된 자료가 없습니다</td></tr>`;
    }

    return data.map(item => `
        <tr>
            <td class='center'>${item.name}</td>
            <td class='center'>
                <input type='button' class='btn-small grey' value='수정' onclick='getterDepartment(${item.uid})' />
                <input type='button' class='btn-small danger' value='삭제' onclick='deleteDepartment(${item.uid})' />
            </td>
        </tr>
    `).join('');
};

const getterDepartment = async (uid) => {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getDepartment');
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
        setterDepartment(data);
    } catch (error) {
        displayError(error);
    }
}

const setterDepartment = (data) => {
    if (data) {
        document.getElementById('departmentUid').value = data.uid;
        document.getElementById('department').value = data.name;
    }
}

const deleteDepartment = (uid) => {
    if(confirm('해당 데이터를 삭제하시겠습니까?')) {
        const formData = new FormData();
        formData.append('controller', 'functions');
        formData.append('mode', 'deleteRow');
        formData.append('table', 'mes_department');
        formData.append('uid', uid);

        fetch('./handler.php', {
            method: 'post',
            body : formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(function(data) {
            if(data != null || data != '') {                
                if(data.result == 'success') {
                    getDepartmentList();
                }

                alert(data.message);
            }
        })
        .catch(error => console.log(error));
    }
}
// 직급 관리 ========================================================================================
const registerRank = () => {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'registerRank');
    formData.append('name', document.getElementById('rank').value);
    formData.append('uid', document.getElementById('rankUid').value);

	fetch('./handler.php', {
		method: 'post',
		body : formData
	})
	.then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        return response.json();
    })
	.then(function(data) {
		if(data != null || data != '') {
			if(data.result == 'success') {
                getRankList();
            }

            document.getElementById('rank').value = '';                
            document.getElementById('rankUid').value = ''; 
            //alert(data.message);
		}               
	})
	.catch(error => console.log(error));
}

const getRankList = async () => {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getRankList');

    try {
        const response = await fetch('./handler.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        const tableBody = document.querySelector('.rank tbody');
        tableBody.innerHTML = generateRankTableContent(data);
    } catch (error) {
        console.error('품목구분 데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
};

const generateRankTableContent = (data) => {
    if (!data || data.length === 0) {
        return `<tr><td class='center' colspan='20'>검색된 자료가 없습니다</td></tr>`;
    }

    return data.map(item => `
        <tr>
            <td class='center'>${item.name}</td>
            <td class='center'>
                <input type='button' class='btn-small grey' value='수정' onclick='getterRank(${item.uid})' />
                <input type='button' class='btn-small danger' value='삭제' onclick='deleteRank(${item.uid})' />
            </td>
        </tr>
    `).join('');
};

const getterRank = async (uid) => {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getRank');
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
        setterRank(data);
    } catch (error) {
        displayError(error);
    }
}

const setterRank = (data) => {
    if (data) {
        document.getElementById('rankUid').value = data.uid;
        document.getElementById('rank').value = data.name;
    }
}

const deleteRank = (uid) => {
    if(confirm('해당 데이터를 삭제하시겠습니까?')) {
        const formData = new FormData();
        formData.append('controller', 'functions');
        formData.append('mode', 'deleteRow');
        formData.append('table', 'mes_rank');
        formData.append('uid', uid);

        fetch('./handler.php', {
            method: 'post',
            body : formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(function(data) {
            if(data != null || data != '') {                
                if(data.result == 'success') {
                    getRankList();
                }

                alert(data.message);
            }
        })
        .catch(error => console.log(error));
    }
}

// 공정 관리 ========================================================================================
const registerProcess = () => {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'registerProcess');
    formData.append('name', document.getElementById('process').value);
    formData.append('uid', document.getElementById('processUid').value);
    formData.append('lastProcess', document.getElementById('lastProcess').value);

	fetch('./handler.php', {
		method: 'post',
		body : formData
	})
	.then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        return response.json();
    })
	.then(function(data) {
		if(data != null || data != '') {
			if(data.result == 'success') {
                getProcessList();
            }

            document.getElementById('process').value = '';                
            document.getElementById('processUid').value = ''; 
            document.getElementById('lastProcess').checked = false; 
            //alert(data.message);
		}               
	})
	.catch(error => console.log(error));
}

const getProcessList = async () => {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getProcessList');

    try {
        const response = await fetch('./handler.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        const tableBody = document.querySelector('.process tbody');
        tableBody.innerHTML = generateProcessTableContent(data);
    } catch (error) {
        console.error('공정 데이터를 가져오는 중 오류가 발생했습니다:', error);
    }
};

const generateProcessTableContent = (data) => {
    if (!data || data.length === 0) {
        return `<tr><td class='center' colspan='20'>검색된 자료가 없습니다</td></tr>`;
    }

    return data.map(item => `
        <tr>
            <td class='center'>${item.name}</td>
            <td class='center'>${item.lastProcess}</td>
            <td class='center'>
                <input type='button' class='btn-small grey' value='수정' onclick='getterProcess(${item.uid})' />
                <input type='button' class='btn-small danger' value='삭제' onclick='deleteProcess(${item.uid})' />
            </td>
        </tr>
    `).join('');
};

const getterProcess = async (uid) => {
    const formData = new FormData();
    formData.append('controller', 'mes');
    formData.append('mode', 'getProcess');
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
        setterProcess(data);
    } catch (error) {
        displayError(error);
    }
}

const setterProcess = (data) => {
    if (data) {
        document.getElementById('processUid').value = data.uid;
        document.getElementById('process').value = data.name;
        if(data.lastProcess == 'Y') document.getElementById('lastProcess').checked = true;
        else document.getElementById('lastProcess').checked = false;
    }
}

const deleteProcess = (uid) => {
    if(confirm('해당 데이터를 삭제하시겠습니까?')) {
        const formData = new FormData();
        formData.append('controller', 'functions');
        formData.append('mode', 'deleteRow');
        formData.append('table', 'mes_process');
        formData.append('uid', uid);

        fetch('./handler.php', {
            method: 'post',
            body : formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(function(data) {
            if(data != null || data != '') {                
                if(data.result == 'success') {
                    getProcessList();
                }

                alert(data.message);
            }
        })
        .catch(error => console.log(error));
    }
}
</script>
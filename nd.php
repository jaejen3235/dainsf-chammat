<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- Boxicons CDN 추가 -->    
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.8.2/css/all.min.css" />
    <link rel="stylesheet" href="./assets/css/nstyle.css">
    
      
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    
</head>
<body>
<header>
    <!-- logo -->
    <div>
        <span class='logo'>BizSK</span>
        <span class='logo-text'>관리자센터</span>
    </div>
    <!-- logout -->
    <div>로그아웃</div>
</header>

<main>
    <!-- left menu -->
    <div class='left-container'>
<?php
include "./include/left_menu.php";
?>
    </div>

    <!-- hidden button -->
    <div class='hidden-container'>
        <div class='btn-toggle'><i class='bx bx-chevron-left'></i></div>
    </div>

    <!-- main -->
    <div class='main-container'>
        <div class='title-wrapper'>협력사 관리</div>
        <div class='search-wrapper'>
            <div class='search-box'>
                <div>
                    <select>
                        <option>선택</option>
                    </select>
                </div>
                <div class='search-section'>
                    <input type="text" placeholder="검색">
                    <i class="fa fa-search" aria-hidden="true"></i>
                </div>
            </div>
            <div class='button-box'>
                <input type='button' class='btn-large orange' id='btnRegister' value='협력사 등록' />
                <input type='button' class='btn-large' value='선택 삭제' />
            </div>
        </div>
        <div class='content-wrapper'>
            <div>
                <table class='list'>
                    <thead>
                        <tr>
                            <th></th>
                            <th>협력사명</th>
                            <th>코드</th>
                            <th>담당자명</th>
                            <th>연락처</th>
                            <th>로그인 아이디</th>
                            <th>수정 및 삭제</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <label class="custom-checkbox">
                                    <input type="checkbox">
                                    <span class="checkmark"></span>
                                </label>
                            </td>
                            <td>가나다</td>
                            <td>가나다</td>
                            <td>가나다</td>
                            <td>가나다</td>
                            <td>가나다</td>
                            <td>가나다</td>
                        </tr>
                        <tr>
                            <td>
                                <label class="custom-checkbox">
                                    <input type="checkbox">
                                    <span class="checkmark"></span>
                                </label>
                            </td>
                            <td>가나다</td>
                            <td>가나다</td>
                            <td>가나다</td>
                            <td>가나다</td>
                            <td>가나다</td>
                            <td>가나다</td>
                        </tr>
                        <tr>
                            <td>
                                <label class="custom-checkbox">
                                    <input type="checkbox">
                                    <span class="checkmark"></span>
                                </label>
                            </td>
                            <td>가나다</td>
                            <td>가나다</td>
                            <td>가나다</td>
                            <td>가나다</td>
                            <td>가나다</td>
                            <td>가나다</td>
                        </tr>
                        <tr>
                            <td>
                                <label class="custom-checkbox">
                                    <input type="checkbox">
                                    <span class="checkmark"></span>
                                </label>
                            </td>
                            <td>가나다</td>
                            <td>가나다</td>
                            <td>가나다</td>
                            <td>가나다</td>
                            <td>가나다</td>
                            <td>가나다</td>
                        </tr>
                        <tr>
                            <td>
                                <label class="custom-checkbox">
                                    <input type="checkbox">
                                    <span class="checkmark"></span>
                                </label>
                            </td>
                            <td>가나다</td>
                            <td>가나다</td>
                            <td>가나다</td>
                            <td>가나다</td>
                            <td>가나다</td>
                            <td>
                                <input type='button' class='btn grey' value='수정' />
                                <input type='button' class='btn' value='삭제' />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

</body>
</html>

<div class="modal" id="registCarModal">
	<div class="modal-content">
		<div class="modal-header">
			<span class='modal-title'>차량 등록(수정)</span>
			<span class="btn-close" id="btnClose"><i class='bx bx-x'></i></span>
		</div>
		<div class="modal-body center" style="overflow:auto">
            <form id='registCarForm'>
                <input type='hidden' name='controller' value='hng' />
                <input type='hidden' name='mode' value='registCar' />					
                <input type="hidden" class='text' name="carUid" id="carUid" />					
                
                <table class="wtable">
                    <colgroup>
                        <col width='150' />
                        <col width='270' />
                        <col width='150' />
                        <col width='273' />
                    </colgroup>
                    <tr>
                        <th><i class='bx bx-check red'></i> 차량번호</th>
                        <td>
                        <input type='text' class='text' name='carNo' id='carNo' validation='yes' err='차량번호를 입력하세요' />
                        </td>
                        <th><i class='bx bx-check red'></i> 업체명</th>
                        <td>
                            <input type='text' class='text' name='companyName' id='companyName' validation='yes' err='업체명을 입력하세요' />
                        </td>
                    </tr>
                    <tr>
                        <th><i class='bx bx-check red'></i> 소속</th>
                        <td>
                            <input type='text' class='text' name='department' id='department' />
                        </td>	
                        <th>운전자</th>
                        <td>
                            <input type='text' class='text' name='driver' id='driver' />
                        </td>
                    </tr>
                    <tr>
                        <th>연락처</th>
                        <td>
                            <input type="text" class='text' name="contact" id="contact">
                        </td>
                        <th>차량종류</th>
                        <td>
                            <input type="text" class='text' name="carModel" id="carModel">
                        </td>
                    </tr>
                    <tr>
                        <th>Serial No</th>
                        <td>
                            <input type="text" class='text' name="sn" id="sn">
                        </td>
                        <th>장착 일시</th>
                        <td>
                            <input type="text" class='text datepicker' name="instDate" id="instDate">
                        </td>
                    </tr>	
                    <tr>
                        <th>설치 장소</th>
                        <td colspan='3'>
                            <input type="text" class='text' name="instPlace" id="instPlace">
                        </td>
                    </tr>							
                </table>
            </form>
            <div class='mt5 help left'>
                <i class='bx bx-check'></i> 은 필수입력 사항입니다
            </div>

            <div class='center mt30'>
                <input type='button' class='btn-large primary' id='btnRegistCar' value='저장하기' />&nbsp;
                <input type='button' class='btn-large gray-200' id='btnCloseModal' value='창닫기' />
            </div>
		</div>
	</div>
</div>

<script src="./assets/js/script.js"></script>  
<script src="./assets/js/common.js"></script>

<script>
window.addEventListener('DOMContentLoaded', () => {
    const btnRegister = document.getElementById('btnRegister');
    if(btnRegister) {
        btnRegister.addEventListener('click', () => {
            openModal('registCarModal', 600, 800);
        });
    }

    const btnClose = document.getElementById('btnClose');
    if(btnClose) {
        btnClose.addEventListener('click', () => {
            closeModal('registCarModal');
        });
    }
});
</script>
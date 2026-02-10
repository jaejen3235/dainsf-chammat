<div class='main-container'>
    <div class='title-wrapper'>지원금 설정</div>
        <div class='search-wrapper'>
            <div class='search-box'>
                <div>
                    <select id='category'>
                        <option value='0'>선택</option>
                        <option value='5G'>5G</option>
                        <option value='LTE'>LTE</option>
                    </select>
                </div>
                <div class='search-section'>
                    <input type="text" id='searchText' placeholder="검색">
                    <i class="fa fa-search hands" aria-hidden="true"></i>
                </div>
                <button class='btn-large success revision' id='btnRevision'><i class='bx bx-revision'></i></button>
            </div>
            <div class='button-box'>
                <input type='button' class='btn-large orange' id='btnOpenModal' value='요금제 등록' />
                <input type='button' class='btn-large' id='btnDeleteSelected' value='선택 삭제' />
            </div>
        </div>
        <div class='content-wrapper'>
            <div>
                <table class='list'>
                    <colgroup>
                        <col width='50'>
                        <col width='100'>
                        <col>
                        <col width='150'>
                        <col width='200'>
                        <col width='150'>
                        <col width='150'>
                        <col width='150'>
                    </colgroup>
                    <thead>
                        <tr>
                            <th>
                                <label class="custom-checkbox">
                                    <input type="checkbox" id='chkAll'>
                                    <span class="checkmark"></span>
                                </label>
                            </th>
                            <th>요금제 구분</th>
                            <th>요금제명</th>
                            <th>데이터</th>
                            <th>월 이용료</th>
                            <th>세대 구분</th>
                            <th>사용</th>
                            <th>관리</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="paging-area mt20"></div>
        </div>
    </div>
</div> 
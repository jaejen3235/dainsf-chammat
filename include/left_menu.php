<?php
// 메뉴 배열 정의
$left_menu = [
    '기준정보 관리' => [
        'icon' => "<i class='bx bx-food-menu'></i>",
        'submenus' => [
            [
                'controller' => 'basic',
                'action' => 'settingBasic',
                'title' => '기본 단위 설정'
            ],
            [
                'controller' => 'basic',
                'action' => 'listItem',
                'title' => '품목 관리'
            ],
            [
                'controller' => 'basic',
                'action' => 'listAccount',
                'title' => '거래처 관리'
            ],
            [
                'controller' => 'basic',
                'action' => 'listEmployee',
                'title' => '사원 관리'
            ],
            /*
            [
                'controller' => 'basic',
                'action' => 'listBom',
                'title' => 'BOM 관리'
            ],
            [
                'controller' => 'basic',
                'action' => 'listDefect',
                'title' => '불량유형 관리'
            ],
            */
            [
                'controller' => 'basic',
                'action' => 'listDefect',
                'title' => '불량정보 관리'
            ],
            /*
            [
                'controller' => 'basic',
                'action' => 'machine',
                'title' => '설비정보 관리'
            ],
            [
                'controller' => 'basic',
                'action' => 'outsource',
                'title' => '외주정보 관리'
            ],
            */
        ]
    ],
    '영업 관리' => [
        'icon' => "<i class='bx bx-folder-plus'></i>",
        'submenus' => [
            [
                'controller' => 'sales',
                'action' => 'listOrder',
                'title' => '수주정보 관리'
            ],
            [
                'controller' => 'sales',
                'action' => 'listShipment',
                'title' => '출하지시 관리'
            ],
            [
                'controller' => 'sales',
                'action' => 'searchShipment',
                'title' => '출하현황 조회'
            ],
            /*
            [
                'controller' => 'sales',
                'action' => 'salesManagement',
                'title' => '판매정보 관리'
            ]
            */
        ]
    ],
    '생산 관리' => [
        'icon' => "<i class='bx bx-barcode-reader'></i>",
        'submenus' => [
            [
                'controller' => 'product',
                'action' => 'workOrderManagement',
                'title' => '작업지시서 관리'
            ],
            /*
            [
                'controller' => 'product',
                'action' => 'workOrderCalendar',
                'title' => '작업일정 계획'
            ],
            [
                'controller' => 'product',
                'action' => 'reportProduct',
                'title' => '생산실적 관리'
            ],
            */
            [
                'controller' => 'product',
                'action' => 'reportPeriodProduct',
                'title' => '생산실적 관리'
            ],
            [
                'controller' => 'product',
                'action' => 'reportDayWork',
                'title' => '작업일보 관리'
            ],
        ]
    ],
    '품질 관리' => [
        'icon' => "<i class='bx bxs-flask'></i>",
        'submenus' => [
            /*
            [
                'controller' => 'quality',
                'action' => 'importInspection',
                'title' => '수입검사 관리'
            ],
            */
            [
                'controller' => 'quality',
                'action' => 'shortCircuitTest',
                'title' => '수입검사 관리'
            ],
            [
                'controller' => 'quality',
                'action' => 'metalDetection',
                'title' => '금속검출 관리'
            ],
            /*
            [
                'controller' => 'quality',
                'action' => 'reworkList',
                'title' => '리워크 관리'
            ],
            [
                'controller' => 'quality',
                'action' => 'inspectionList',
                'title' => '검사이력조회'
            ],            
            [
                'controller' => 'quality',
                'action' => 'inspectionList',
                'title' => '제조이력 관리'
            ]
            */
        ]
    ],
    '제품 입출하 관리' => [
        'icon' => "<i class='bx bx-bus-school'></i>",
        'submenus' => [
            /*
            [
                'controller' => 'shipment',
                'action' => 'shipmentManagement',
                'title' => '출하 지시서'
            ],
            [
                'controller' => 'shipment',
                'action' => 'deliveryManagement',
                'title' => '출하 관리'
            ],
            */
            [
                'controller' => 'shipment',
                'action' => 'inManagement',
                'title' => '제품 입고 관리'
            ],
            /*
            [
                'controller' => 'shipment',
                'action' => 'inStatus',
                'title' => '제품 입고 현황'
            ],
            */
            [
                'controller' => 'shipment',
                'action' => 'outManagement',
                'title' => '제품 출하 처리'
            ],
            /*
            [
                'controller' => 'shipment',
                'action' => 'outStatus',
                'title' => '제품 출하 현황'
            ],
            [
                'controller' => 'shipment',
                'action' => 'notOutStatus',
                'title' => '제품 미출하 현황'
            ],
            */
        ]
    ],
    '자재 관리' => [
        'icon' => "<i class='bx bx-cube'></i>",
        'submenus' => [
            /*
            [
                'controller' => 'items',
                'action' => 'preReceiving',
                'title' => '가입고 관리'
            ],
            [
                'controller' => 'items',
                'action' => 'listInItem',
                'title' => '입고 관리'
            ],
            */
            [
                'controller' => 'items',
                'action' => 'stock',
                'title' => '재고 현황'
            ],
            /*
            [
                'controller' => 'items',
                'action' => 'stockManagement',
                'title' => '재고 관리'
            ],
            */
            [
                'controller' => 'items',
                'action' => 'reportInOutItem',
                'title' => '자재 수불부'
            ],
            /*
            [
                'controller' => 'items',
                'action' => 'listPurchase',
                'title' => '자재요청/발주관리'
            ],
            [
                'controller' => 'items',
                'action' => 'warehouse',
                'title' => '창고이동 관리'
            ],
            */
        ]
    ], 
    /*    
    '전기에너지 관리' => [
        'icon' => "<i class='bx bx-selection'></i>",
        'submenus' => [
            [
                'controller' => 'electricity',
                'action' => 'totalPower',
                'title' => '종합 전력 사용량 정보'
            ],
            [
                'controller' => 'electricity',
                'action' => 'timePower',
                'title' => '시간대별 전력 사용량 정보'
            ],
            [
                'controller' => 'electricity',
                'action' => 'dayPower',
                'title' => '일별 전력 사용량 정보'
            ],
            [
                'controller' => 'electricity',
                'action' => 'monthPower',
                'title' => '월별 전력 사용량 정보'
            ],
            [
                'controller' => 'electricity',
                'action' => 'yearPower',
                'title' => '연도별 전력 사용량 정보'
            ],
            [
                'controller' => 'electricity',
                'action' => 'peakPower',
                'title' => '설비별 피크 전력 관리'
            ]
        ]
    ],
    
    '설비 관리' => [
        'icon' => "<i class='bx bx-cog'></i>",
        'submenus' => [
            [
                'controller' => 'machine',
                'action' => 'machineCheck',
                'title' => '설비 점검 등록'
            ],
            [
                'controller' => 'machine',
                'action' => 'machineCheckList',
                'title' => '설비 점검 현황'
            ],
            [
                'controller' => 'machine',
                'action' => 'runningMachine',
                'title' => '설비 가동 현황'
            ]
        ]
    ],
    */
    '시스템 관리' => [
        'icon' => "<i class='bx bx-cog'></i>",
        'submenus' => [
            [
                'controller' => 'system',
                'action' => 'configSystem',
                'title' => '사용자 설정'
            ],
            /*
            [
                'controller' => 'system',
                'action' => 'notice',
                'title' => '공지사항 관리'
            ],
            */
            [
                'controller' => 'system',
                'action' => 'haccpDocs',
                'title' => 'HACCP 문서 관리'
            ],
            [
                'controller' => 'system',
                'action' => 'loginReport',
                'title' => '로그인 이력'
            ],
            [
                'controller' => 'system',
                'action' => 'setting',
                'title' => '환경설정'
            ]
        ]
    ],
    'HACCP 관리' => [
        'icon' => "<i class='bx bx-cog'></i>",
        'submenus' => [
            [
                'controller' => 'haccp',
                'action' => 'CCP-1BP_모니터링일지',
                'title' => 'CCP-1BP 모니터링일지 관리'
            ],
            [
                'controller' => 'haccp',
                'action' => 'CCP-2BP_모니터링일지',
                'title' => 'CCP-2BP 모니터링일지 관리'
            ],
            [
                'controller' => 'haccp',
                'action' => 'HC01_CCP-1BP_모니터링일지',
                'title' => 'CCP-1BP 모니터링일지'
            ],
            [
                'controller' => 'haccp',
                'action' => 'HC02_CCP-2BP_모니터링일지',
                'title' => 'CCP-2BP 모니터링일지'
            ],
            [
                'controller' => 'haccp',
                'action' => 'HC03_CCP-3P_모니터링일지',
                'title' => 'CCP-3P 모니터링일지'
            ],
            [
                'controller' => 'haccp',
                'action' => 'HC04_검교정_성적서',
                'title' => '검교정 성적서'
            ],
            [
                'controller' => 'haccp',
                'action' => 'HC05_방충_및_방서점검표',
                'title' => '방충 및 방서점검표'
            ],
            [
                'controller' => 'haccp',
                'action' => 'HC06_온도점검표',
                'title' => '온도점검표'
            ],
            [
                'controller' => 'haccp',
                'action' => 'HC07_원부재료_입고_점검표',
                'title' => '원부재료 입고 점검표'
            ],
            [
                'controller' => 'haccp',
                'action' => 'HC08_일반위생관리_및_공정점검표1',
                'title' => '일반위생관리 공정점검표1'
            ],
            [
                'controller' => 'haccp',
                'action' => 'HC09_일반위생관리_및_공정점검표2',
                'title' => '일반위생관리 공정점검표2'
            ],
            [
                'controller' => 'haccp',
                'action' => 'HC10_중요관리점(CCP)_검증점검표',
                'title' => '중요관리점 검증점검표'
            ]
        ]
    ],
    '모니터링' => [
        'icon' => "<i class='bx bx-camera-home'></i>",
        'submenus' => [
            // [
            //     'controller' => 'monitoring',
            //     'action' => 'workOrder',
            //     'title' => '생산 현황'
            // ],
            // [
            //     'controller' => 'monitoring',
            //     'action' => 'leakageInspection',
            //     'title' => '누전검사현황'
            // ],
            [
                'controller' => 'monitoring',
                'action' => 'ems',
                'title' => '전력사용량 모니터링'
            ],            
            // [
            //     'controller' => 'monitoring',
            //     'action' => 'dayWeekMonth',
            //     'title' => '일일/주간/월간 생산현황'
            // ],
            // [
            //     'controller' => 'monitoring',
            //     'action' => 'deliveryStatus',
            //     'title' => '납기 현황'
            // ],
            [
                'controller' => 'monitoring',
                'action' => 'metalDetection2',
                'title' => '금속검출 현황'
            ],
            [
                'controller' => 'monitoring',
                'action' => 'warehouseTemperature',
                'title' => '창고온도 현황'
            ],
            // [
            //     'controller' => 'monitoring',
            //     'action' => 'washerUtilization',
            //     'title' => '세척기 가동률 현황'
            // ],
            // [
            //     'controller' => 'monitoring',
            //     'action' => 'goal',
            //     'title' => '목표 대비 달성률'
            // ],
            // [
            //     'controller' => 'monitoring',
            //     'action' => 'machine',
            //     'title' => '설비 가동률 현황'
            // ],
        ]
    ]
    /*
    '설비 예지 보전' => [
        'icon' => "<i class='bx bx-cog'></i>",
        'submenus' => [
            [
                'controller' => 'machine',
                'action' => 'machineMonitoring',
                'title' => '설비 모니터링'
            ],
            [
                'controller' => 'machine',
                'action' => 'pdmReport',
                'title' => '예지보전 분석'
            ],
            [
                'controller' => 'machine',
                'action' => 'alramReport',
                'title' => '알림 및 보고서'
            ],
            [
                'controller' => 'machine',
                'action' => 'maintence',
                'title' => '유지보수 관리'
            ]
        ]
    ],    
    'KPI지표 관리' => [
        'icon' => "<i class='bx bx-camera-home'></i>",
        'submenus' => [
            [
                'controller' => 'kpi',
                'action' => 'readTimeReduction2',
                'title' => '납기단축'
            ],
            [
                'controller' => 'kpi',
                'action' => 'defectRate2',
                'title' => '완제품불량률'
            ],
            [
                'controller' => 'kpi',
                'action' => 'readTime2',
                'title' => '제조리드타임'
            ],
            [
                'controller' => 'kpi',
                'action' => 'productHour2',
                'title' => '시간당생산량'
            ],
            [
                'controller' => 'kpi',
                'action' => 'inventoryCost2',
                'title' => '재고비용    '
            ],
            [
                'controller' => 'kpi',
                'action' => 'shipmentReadTime',
                'title' => '수주출하리드타임'
            ],
            [
                'controller' => 'kpi',
                'action' => 'workEffort',
                'title' => '작업공수'
            ],
            [
                'controller' => 'kpi',
                'action' => 'workEffort2',
                'title' => '작업공수'
            ],
        ]
    ]
    */
];

// 로그인 레벨이 1000일 경우 새로운 메뉴 추가
if ($_SESSION['loginLevel'] == 1000) {
    $left_menu['시스템 관리']['submenus'][] = [
        'controller' => 'system',
        'action' => 'dbManagement',
        'title' => 'DB 설정'
    ];
    $left_menu['시스템 관리']['submenus'][] = [
        'controller' => 'system',
        'action' => 'addAdmin',
        'title' => '관리자 계정 설정'
    ];
    
    $left_menu['시스템 관리']['submenus'][] = [        
        'controller' => 'system',
        'action' => 'createLoginReport',
        'title' => '로그인 이력 생성'
    ];

    /*
    $left_menu['전기에너지 관리']['submenus'][] = [        
        'controller' => 'electricity',
        'action' => 'createPowerData',
        'title' => '전력데이터 생성'
    ];
    $left_menu['전기에너지 관리']['submenus'][] = [        
        'controller' => 'electricity',
        'action' => 'createMonthPowerData',
        'title' => '월별 전력데이터 생성'
    ];
    $left_menu['전기에너지 관리']['submenus'][] = [        
        'controller' => 'electricity',
        'action' => 'createDayPowerData',
        'title' => '일별 전력데이터 생성'
    ];
    */
}


// 메뉴 출력
// URL 파라미터 가져오기
$currentController = isset($_GET['controller']) ? $_GET['controller'] : '';
$currentAction = isset($_GET['action']) ? $_GET['action'] : '';

$tag = '<div class="menu">';
foreach ($left_menu as $menu_title => $menu_data) {
    $tag .= "<div class='menu-item'>";
    $tag .= "<div class='menu-section'>";
    $tag .= "<div class='menu-title-box'>";
    $tag .= $menu_data['icon'];

    // 상위 메뉴의 활성화 클래스 추가
    $isActiveMenu = false; // 상위 메뉴 활성화 여부
    foreach ($menu_data['submenus'] as $submenu) {
        if ($currentController === $submenu['controller'] && $currentAction === $submenu['action']) {
            $isActiveMenu = true; // 서브메뉴가 활성화된 경우
            break;
        }
    }
    $menuActive = $isActiveMenu ? 'active' : '';

    $tag .= "<a href='#' class='menu-title {$menuActive}'>{$menu_title}</a>";
    $tag .= "</div>";
    $tag .= "<div><i class='bx bx-chevron-down'></i></div>";
    $tag .= "</div>";

    $submenuActive = ($currentController === $submenu['controller']) ? 'active' : '';

    $tag .= "<ul class='submenu {$submenuActive}'>";
    foreach ($menu_data['submenus'] as $submenu) {
        // 현재 서브메뉴에 active 클래스를 추가
        $itemActive = ($currentAction === $submenu['action']) ? 'active' : '';

        $tag .= "<li class='{$itemActive}'><i class='bx bx-chevron-right'></i> <a href='#'  onclick=\"movePage('{$submenu['controller']}', '{$submenu['action']}')\">{$submenu['title']}</a></li>";
    }
    $tag .= "</ul>";
    $tag .= "</div>";
}
$tag .= '</div>';

echo $tag;
?>

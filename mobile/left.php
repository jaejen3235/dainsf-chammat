<?php
// 메뉴 배열 정의
$left_menu = [
    '대리점 관리' => [
        '총판 목록' => [
            'controller' => 'distributor',
            'action' => 'list',
            'title' => '대리점 목록'
        ],
        '총판 등록' => [
            'controller' => 'distributor',
            'action' => 'register',
            'title' => '대리점 등록'
        ],
        '총판 정보 수정' => [
            'controller' => 'distributor',
            'action' => 'edit',
            'title' => '대리점 정보 수정'
        ]
    ],
    '학원 관리' => [
        '학원 목록' => [
            'controller' => 'academy',
            'action' => 'list',
            'title' => '학원 목록'
        ],
        '학원 등록' => [
            'controller' => 'academy',
            'action' => 'register',
            'title' => '학원 등록'
        ],
        '학원 정보 수정' => [
            'controller' => 'academy',
            'action' => 'edit',
            'title' => '학원 정보 수정'
        ],
        '학원 정보 수정2' => [
            'controller' => 'academy',
            'action' => 'edit',
            'title' => '학원 정보 수정'
        ],
        '학원 정보 수정3' => [
            'controller' => 'academy',
            'action' => 'edit',
            'title' => '학원 정보 수정'
        ]
    ],
    '고객 관리' => [
        '고객 목록' => [
            'controller' => 'customer',
            'action' => 'list',
            'title' => '고객 목록'
        ],
        '고객 등록' => [
            'controller' => 'customer',
            'action' => 'register',
            'title' => '고객 등록'
        ],
        '고객 정보 수정' => [
            'controller' => 'customer',
            'action' => 'edit',
            'title' => '고객 정보 수정'
        ]
    ]
];

// 메뉴 출력
$tag = '<div class="menu">';
foreach ($left_menu as $menu_title => $submenus) {
    $tag .= "<div class='menu-item'>";
    $tag .= "<a href='#' class='menu-title'>{$menu_title}</a>";
    $tag .= "<ul class='submenu'>";
    foreach ($submenus as $submenu) {
        $tag .= "<li><a href=\"?controller={$submenu['controller']}&action={$submenu['action']}\">{$submenu['title']}</a></li>";
    }
    $tag .= "</ul>";
    $tag .= "</div>";
}
$tag .= '</div>';

echo $tag;
?>

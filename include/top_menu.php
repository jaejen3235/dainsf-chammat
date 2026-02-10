<?php
$sysadmin_menu = [
    '총판 관리' => [
        'menu' => 'distributor',
        'controller' => 'distributor',
        'action' => 'listDistributor'
    ],
    
];

$admin_menu = [
    '협력사 관리' => [
        'menu' => 'agency',
        'controller' => 'agency',
        'action' => 'listAgency'
    ],
    '고객사 관리' => [
        'menu' => 'client',
        'controller' => 'agency',
        'action' => 'listClient'
    ],
    '지원금 관리' => [
        'menu' => 'support',
        'controller' => 'agency',
        'action' => 'registerSupport'
    ],
    '요금제 관리' => [
        'menu' => 'payment',
        'controller' => 'agency',
        'action' => 'listPayment'
    ],
    '상품 관리' => [
        'menu' => 'goods',
        'controller' => 'agency',
        'action' => 'listGoods'
    ],
    '기기 관리' => [
        'menu' => 'device',
        'controller' => 'agency',
        'action' => 'managementCategory'
    ],
    '게시물 관리' => [
        'menu' => 'board',
        'controller' => 'agency',
        'action' => 'managementMainSlide'
    ],
    '정보 관리' => [
        'menu' => 'info',
        'controller' => 'agency',
        'action' => 'registerInfo'
    ],
];

$agency_menu = [
    '메뉴1' => [
        'controller' => 'aaa',
        'action' => 'bbb'
    ],
];

if($_SESSION['loginLevel'] > 100) {
    $top_menu = $sysadmin_menu;
} else if($_SESSION['loginLevel'] == 100) {
    $top_menu = $admin_menu;
} else if($_SESSION['loginLevel'] < 100) {
    $top_menu = $agency_menu;
}

foreach($top_menu as $key => $value) {    
    $active = ($_GET['menu'] == $value['menu']) ? "active" : "";
    $tag .= <<<HTML
        <li onclick="movePage('{$value['controller']}', '{$value['action']}', '{$value['menu']}')">
            <div class="{$active}"><a href="#">{$key}</a></div>
        </li>
HTML;
}

echo $tag;
?>
<?php
// $Id: menu.php 881 2011-12-28 02:08:50Z i.bitcero $
// --------------------------------------------------------------
// bXpress Forums
// An simple forums module for XOOPS and Common Utilities
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

$adminmenu[] = array(
    'title' => __('Dashboard','bxpress'),
    'link' => "admin/index.php",
    'icon' => '../images/dash.png',
    'location' => "dashboard"
);

$adminmenu[] = array(
    'title' => __('Categories','bxpress'),
    'link' => "admin/categories.php",
    'icon' => '../images/categos.png',
    'location' => "categories"
);

$adminmenu[] = array(
    'title' => __('Forums','bxpress'),
    'link' => "admin/forums.php",
    'icon' => '../images/forums.png',
    'location' => "forums"
);


$adminmenu[] = array(
    'title' => __('Announcements','bxpress'),
    'link' => "admin/announcements.php",
    'icon' => '../images/bell.png',
    'location' => "messages"
);

$adminmenu[] = array(
    'title' => __('Reports','bxpress'),
    'link' => "admin/reports.php",
    'icon' => '../images/reports.png',
    'location' => "reports",
    'options' => array(
        array('title'=>__('All reports','bxpress'),'link'=>'admin/reports.php','selected'=>'allreps'),
        array('title'=>__('Read','bxpress'),'link'=>'admin/reports.php?show=1','selected'=>'reviews'),
        array('title'=>__('Not Read','bxpress'),'link'=>'admin/reports.php?show=2','selected'=>'noreviewd')
    )
);

$adminmenu[5]['title'] = __('Prune','bxpress');
$adminmenu[5]['link'] = "admin/prune.php";
$adminmenu[5]['icon'] = '../images/prune.png';
$adminmenu[5]['location'] = "prune";


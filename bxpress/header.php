<?php
// $Id: header.php 896 2012-01-02 18:43:23Z i.bitcero $
// --------------------------------------------------------------
// bXpress Forums
// An simple forums module for XOOPS and Common Utilities
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

require  dirname(dirname(__DIR__)) . '/header.php';

define('BX_URL', XOOPS_URL . '/modules/bxpress');

// Actualizamos los usuarios online
$db = XoopsDatabaseFactory::getDatabaseConnection();
$tpl = $xoopsTpl;
require_once XOOPS_ROOT_PATH . '/kernel/online.php';
$online = new XoopsOnlineHandler($db);
$online->write($xoopsUser ? $xoopsUser->uid() : 0, $xoopsUser ? $xoopsUser->uname() : '', time(), $xoopsModule->mid(), $_SERVER['REMOTE_ADDR']);

$mc = &$xoopsModuleConfig;

RMTemplate::get()->add_style('bxpress.min.css', 'bxpress');
RMTemplate::get()->add_script('bxpress.min.js', 'bxpress', ['footer' => 1]);

// Header language
$xoopsTpl->assign('lang_search_ph', __('Search for...', 'bxpress')); // Search placeholder

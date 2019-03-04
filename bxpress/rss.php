<?php
// $Id: files.php 819 2011-12-08 23:43:13Z i.bitcero $
// --------------------------------------------------------------
// bXpress Forums
// An simple forums module for XOOPS and Common Utilities
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

load_mod_locale('dtransport');
$show = rmc_server_var($_GET, 'show', 'all');

$xoopsModule = RMFunctions::load_module('bxpress');
$config = RMSettings::module_settings('bxpress');

require_once XOOPS_ROOT_PATH . '/modules/bxpress/class/bxforum.class.php';
require_once XOOPS_ROOT_PATH . '/modules/bxpress/class/bxpost.class.php';
require_once XOOPS_ROOT_PATH . '/modules/bxpress/class/bxtopic.class.php';
require_once XOOPS_ROOT_PATH . '/modules/bxpress/class/bxfunctions.class.php';

$rss_channel = [];
$rss_items = [];

$bxFunc = new bXFunctions();
$db = XoopsDatabaseFactory::getDatabaseConnection();
$tc = TextCleaner::getInstance();

$tbl1 = $db->prefix('mod_bxpress_posts');
$tbl2 = $db->prefix('mod_bxpress_topics');
$tbl3 = $db->prefix('mod_bxpress_posts_text');
$tbl4 = $db->prefix('mod_bxpress_forums');

switch ($show) {
    case 'forum':

        $id = rmc_server_var($_GET, 'forum', 0);
        if ($id <= 0) {
            redirect_header('backend.php', 1, __('Sorry, specified forum was not foud!', 'bxpress'));
            die();
        }

        $forum = new bXForum($id);
        if ($forum->isNew()) {
            redirect_header('backend.php', 1, __('Sorry, specified forum was not foud!', 'bxpress'));
            die();
        }

        $rss_channel['title'] = sprintf(__('%s :: Posts on forum %s'), $xoopsModule->name(), $forum->name());
        $rss_channel['link'] = $forum->permalink();
        $rss_channel['description'] = sprintf(__('All recent messages posted on %s', 'dtransport'), $forum->name());
        $rss_channel['lastbuild'] = formatTimestamp(time(), 'rss');
        $rss_channel['webmaster'] = checkEmail($xoopsConfig['adminmail'], true);
        $rss_channel['editor'] = checkEmail($xoopsConfig['adminmail'], true);
        $rss_channel['category'] = $forum->name();
        $rss_channel['generator'] = 'Common Utilities';
        $rss_channel['language'] = RMCLANG;

        $sql = "SELECT * FROM $tbl1 WHERE id_forum=$id AND approved=1 ORDER BY post_time DESC LIMIT 0,50";

        $result = $db->queryF($sql);

        $topics = [];
        $block = [];

        $post = new bXPost();
        $forum = new bXForum();

        while (false !== ($row = $db->fetchArray($result))) {
            $post = new bXPost();
            $post->assignVars($row);
            $topic = new bXTopic($post->topic());
            $forum = new bXForum($post->forum());

            $item = [];
            $item['title'] = sprintf(__('Posted on: %s :: %s', 'bxpress'), $topic->title(), $forum->name());
            $item['link'] = $post->permalink();
            $item['description'] = XoopsLocal::convert_encoding(htmlspecialchars($post->text(), ENT_QUOTES));
            $item['pubdate'] = formatTimestamp($post->date(), 'rss');
            $item['guid'] = $post->permalink();
            $rss_items[] = $item;
        }

        break;
    case 'all':
    default:

        $rss_channel['title'] = $xoopsModule->name();
        $rss_channel['link'] = XOOPS_URL . ($config->urlmode ? $config->htbase : '/modules/bxpress');
        $rss_channel['description'] = __('All recent messages posted on forum', 'bxpress');
        $rss_channel['lastbuild'] = formatTimestamp(time(), 'rss');
        $rss_channel['webmaster'] = checkEmail($xoopsConfig['adminmail'], true);
        $rss_channel['editor'] = checkEmail($xoopsConfig['adminmail'], true);
        $rss_channel['category'] = 'Forum';
        $rss_channel['generator'] = 'Common Utilities';
        $rss_channel['language'] = RMCLANG;

        $sql = "SELECT * FROM $tbl1 WHERE approved=1 ORDER BY post_time DESC LIMIT 0,50";

        $result = $db->queryF($sql);

        $topics = [];
        $block = [];

        $post = new bXPost();
        $forum = new bXForum();
        $tf = new RMTimeFormatter(0, '%T%-%d%-%Y% at %h%:%i%');

        while (false !== ($row = $db->fetchArray($result))) {
            $post = new bXPost();
            $post->assignVars($row);
            $topic = new bXTopic($post->topic());
            $forum = new bXForum($post->forum());

            $item = [];
            $item['title'] = sprintf(__('Posted on: %s :: %s'), $topic->title(), $forum->name());
            $item['link'] = $post->permalink();
            $item['description'] = XoopsLocal::convert_encoding(htmlspecialchars($post->text(), ENT_QUOTES));
            $item['pubdate'] = formatTimestamp($post->date(), 'rss');
            $item['guid'] = $post->permalink();
            $rss_items[] = $item;
        }

        break;
}

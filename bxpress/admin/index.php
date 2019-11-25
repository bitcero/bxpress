<?php
// $Id: index.php 896 2012-01-02 18:43:23Z i.bitcero $
// --------------------------------------------------------------
// EXMBB Forums
// An simple forums module for XOOPS and Common Utilities
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

define('RMCLOCATION', 'dashboard');
require __DIR__ . '/header.php';

$db = $xoopsDB;

// Categorías
$sql = 'SELECT COUNT(*) FROM ' . $db->prefix('mod_bxpress_categories');
list($catnum) = $db->fetchRow($db->query($sql));

// Forums
$sql = 'SELECT COUNT(*) FROM ' . $db->prefix('mod_bxpress_forums');
list($forumnum) = $db->fetchRow($db->query($sql));

// Topics
$sql = 'SELECT COUNT(*) FROM ' . $db->prefix('mod_bxpress_topics');
list($topicnum) = $db->fetchRow($db->query($sql));

// Posts
$sql = 'SELECT COUNT(*) FROM ' . $db->prefix('mod_bxpress_posts');
list($postnum) = $db->fetchRow($db->query($sql));

// Announcements
$sql = 'SELECT COUNT(*) FROM ' . $db->prefix('mod_bxpress_announcements');
list($annum) = $db->fetchRow($db->query($sql));

//Attachments
$sql = 'SELECT COUNT(*) FROM ' . $db->prefix('mod_bxpress_attachments');
list($attnum) = $db->fetchRow($db->query($sql));

// Reports
$sql = 'SELECT COUNT(*) FROM ' . $db->prefix('mod_bxpress_report');
list($repnum) = $db->fetchRow($db->query($sql));

// Likes
$sql = 'SELECT COUNT(*) FROM ' . $db->prefix('mod_bxpress_likes');
list($likes_num) = $db->fetchRow($db->query($sql));

// Days running
$sql = 'SELECT post_time FROM ' . $db->prefix('mod_bxpress_posts') . ' ORDER BY post_time ASC LIMIT 0, 1';
list($daysnum) = $db->fetchRow($db->query($sql));
$daysnum = time() - $daysnum;
$daysnum = ceil($daysnum / 86400);

//Lista de Mensajes recientes
$tbl1 = $db->prefix('mod_bxpress_posts');
$tbl2 = $db->prefix('mod_bxpress_topics');
$tbl3 = $db->prefix('mod_bxpress_posts_text');
$tbl4 = $db->prefix('mod_bxpress_forums');
$sql = "SELECT a.*, b.*, c.post_text, d.* 
        FROM $tbl1 a, $tbl2 b, $tbl3 c, $tbl4 d 
        WHERE b.id_topic = a.id_topic AND c.post_id=a.id_post AND d.id_forum=b.id_forum
        GROUP BY a.id_topic 
        ORDER BY a.post_time DESC 
        LIMIT 0,5";
$result = $db->query($sql);

$posts = [];
$topics = [];
$topic = new bXTopic();
$forum = new bXForum();
$pt = new bXPost();
while (false !== ($row = $db->fetchArray($result))) {
    //print_r($row);
    $pt->assignVars($row);
    $post = [
            'id' => $row['last_post'],
            'date' => sprintf(__('Last post on %s', 'bxpress'), bXFunctions::formatDate($row['post_time'])),
            'by' => sprintf(__('By %s', 'bxpress'), $row['poster_name']),
            'link' => $pt->permalink(),
            'uid' => $row['uid'],
        ];
    $topic->assignVars($row);
    $forum->assignVars($row);
    $topics[] = [
            'id' => $row['id_topic'],
            'title' => $row['title'],
            'post' => $post,
            'link' => $topic->permalink(),
            'forum' => [
                'id' => $forum->id(),
                'name' => $forum->name(),
                'link' => $forum->permalink(),
            ],
        ];
}

$sql = "SELECT * FROM $tbl2 ORDER BY replies DESC LIMIT 0,5";
$result = $db->query($sql);
$poptops = [];
$topic = new bXTopic();
while (false !== ($row = $db->fetchArray($result))) {
    $topic->assignVars($row);
    $forum->assignVars($row);
    $poptops[] = [
        'id' => $topic->id(),
        'title' => $topic->title(),
        'date' => sprintf(__('Created on %s', 'bxpress'), bXFunctions::formatDate($row['date'])),
        'replies' => $topic->replies(),
        'link' => $topic->permalink(),
        'forum' => [
                'id' => $forum->id(),
                'name' => $forum->name(),
                'link' => $forum->permalink(),
            ],
    ];
}

unset($post,$pt,$topic,$result,$row,$sql,$tbl1,$tbl2,$tbl3);

RMTemplate::getInstance()->add_style('dashboard.css', 'bxpress');
RMTemplate::getInstance()->add_style('style.css', 'bxpress'); //mb
RMTemplate::getInstance()->add_script('dashboard.js', 'bxpress', [
    'footer' => 1,
]);
RMTemplate::getInstance()->add_help('Ayuda de bXpress', 'http://www.xoopsmexico.net/docs/bitcero/bxpress-forums/standalone/1/');

// Activity
// 30 Days
$ago = strtotime('-30 days');
$sql = 'SELECT id_post,post_time,id_forum FROM ' . $db->prefix('mod_bxpress_posts') . " WHERE post_time>=$ago ORDER BY post_time ASC";
$result = $db->query($sql);
$posts = [];
$forums = [];
$p = '';
while (false !== ($row = $db->fetchArray($result))) {
    $ds = date('d-M-Y', $row['post_time']);

    if (!isset($posts[$row['id_forum']])) {
        $forums[$row['id_forum']] = new bXForum($row['id_forum']);
    }

    if (!isset($posts[$row['id_forum']][$ds])) {
        $posts[$row['id_forum']][$ds] = 1;
    } else {
        $posts[$row['id_forum']][$ds]++;
    }
}

// Days
$days_rows = [];
$j = 0; $max = 0;
for ($i = 30; $i >= 0; $i--) {
    $j++;
    $ds = date('d-M-Y', strtotime('-' . $i . ' days'));
    $days_rows[$i] = '["' . $ds . '"';
    foreach ($forums as $id => $f) {
        $max = isset($posts[$id][$ds]) ? ($posts[$id][$ds] > $max ? $posts[$id][$ds] : $max) : $max;
        $days_rows[$i] .= ',' . (isset($posts[$id][$ds]) ? $posts[$id][$ds] : '0');
    }
    $days_rows[$i] .= "]\n";
}
unset($d,$posts);
$max += 10 - ($max % 10);

$bc = RMBreadCrumb::get();
$bc->add_crumb(__('Forum Dashboard', 'bxpress'));

RMTemplate::getInstance()->add_body_class('dashboard');

xoops_cp_header();

include RMTemplate::getInstance()->path('admin/forums-index.php', 'module', 'bxpress');

xoops_cp_footer();

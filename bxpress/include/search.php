<?php
// $Id: notification.php 1034 2012-09-06 02:30:13Z i.bitcero $
// --------------------------------------------------------------
// bXpress
// A simple forums module for XOOPS and Common Utilities
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

/**
 * @desc Realiza una búsqueda en el módulo desde EXM
 * @param mixed $queryarray
 * @param mixed $andor
 * @param mixed $limit
 * @param mixed $offset
 * @param mixed $userid
 */
function bxpressSearch($queryarray, $andor, $limit, $offset, $userid = 0)
{
    global $myts, $module;

    $db = XoopsDatabaseFactory::getDatabaseConnection();

    $tbl1 = $db->prefix('mod_bxpress_topics');
    $tbl2 = $db->prefix('mod_bxpress_posts_text');
    $tbl3 = $db->prefix('mod_bxpress_posts');

    if ($userid <= 0) {
        $sql = "SELECT a.*,b.*,c.* FROM $tbl1 a, $tbl2 b, $tbl3 c ";
        $sql1 = '';
        foreach ($queryarray as $k) {
            $sql1 .= ('' == $sql1 ? '' : " $andor ") . " (
        	    (a.title LIKE '%$k%' AND a.id_topic=c.id_topic) OR 
        	     (b.post_text LIKE '%$k%' AND b.post_id=c.id_post))";
        }
        $sql .= '' != $sql1 ? "WHERE $sql1" : '';

        $sql .= $userid > 0 ? 'GROUP BY c.id_topic' : ' GROUP BY c.id_topic';
        $sql .= " ORDER BY c.post_time DESC LIMIT $offset, $limit";

        $result = $db->queryF($sql);
    } else {
        $sql = "SELECT a.*, b.*, c.post_text FROM $tbl3 a, $tbl1 b, $tbl2 c WHERE a.uid='$userid' AND b.id_topic=a.id_topic 
                AND c.post_id=a.id_post ";
        $sql1 = '';
        foreach ($queryarray as $k) {
            $sql1 .= ('' == $sql1 ? 'AND ' : " $andor ") . "
                b.title LIKE '%$k%' AND c.post_text LIKE '%$k%'";
        }
        $sql .= $sql1;
        $sql .= "ORDER BY a.post_time DESC
                LIMIT $offset, $limit";

        $result = $db->query($sql);
    }

    require_once XOOPS_ROOT_PATH . '/modules/bxpress/class/bxpost.class.php';
    require_once XOOPS_ROOT_PATH . '/modules/bxpress/class/bxfunctions.class.php';
    $tc = TextCleaner::getInstance();
    $ret = [];
    while (false !== ($row = $db->fetchArray($result))) {
        $post = new bXPost();
        $post->assignVars($row);
        $rtn = [];
        $rtn['image'] = 'images/forum16.png';
        $rtn['link'] = $post->permalink();
        $rtn['title'] = $row['title'];
        $rtn['time'] = $row['post_time'];
        $rtn['uid'] = $row['uid'];
        $rtn['desc'] = mb_substr($tc->clean_disabled_tags($row['post_text']), 0, 150) . '...';
        $ret[] = $rtn;
    }

    return $ret;
}

<?php
// $Id: notification.php 1034 2012-09-06 02:30:13Z i.bitcero $
// --------------------------------------------------------------
// bXpress
// A simple forums module for XOOPS and Common Utilities
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

function exmbb_rssdesc(){
	global $util;
	
	$mc = RMSettings::module_settings('exmbb');
	return $mc->rssdesc;
}

/**
* @desc Muestra el menu de opciones para sindicación
* @param int $limit Limite de resultados solicitados. 0 Indica ilimitado
* @param bool $more Referencia. Debe devolver true si existen mas resultados que el límite deseado
* @return array
*/
function &exmbb_rssfeed($limit, &$more){
	global $db;
	$limit = $limit > 0 ? $limit-1 : 0;
	include_once XOOPS_ROOT_PATH.'/modules/exmbb/class/bbforum.class.php';
	
	$ret = array();
	$rtn = array();
	$ret['name'] = _MI_BB_RSSALL;
	$ret['desc'] = _MI_BB_RSSALLDESC;
	$ret['params'] = "show=all";
	$rtn[] = $ret;
	
	$sql = "SELECT COUNT(*) FROM ".$db->prefix("exmbb_forums")." ORDER BY cat, `order`";
	list($num) = $db->fetchRow($db->query($sql));
	if ($num>$limit && $limit > 0) $more = true;
	
	$sql = str_replace("COUNT(*)",'*', $sql);
	if ($limit> 0) $sql .= " LIMIT 0, $limit";
	$result = $db->query($sql);
	while ($row = $db->fetchArray($result)){
		$forum = new BBForum();
		$forum->assignVars($row);
		$ret = array();
		$ret['name'] = $forum->name();
		$ret['desc'] = $forum->description();
		$ret['params'] = "show=forum&amp;id=".$forum->id();
		$rtn[] = $ret;
	}
	return $rtn;
	
}

/**
* @desc Genera la información para mostrar la Sindicación
* @param int Limite de resultados
* @return Array
*/
function &exmbb_rssshow($limit){
	global $util, $mc;
	
	$db = XoopsDatabaseFactory::getDatabaseConnection();
	include_once XOOPS_ROOT_PATH.'/modules/exmbb/class/bbforum.class.php';
	include_once XOOPS_ROOT_PATH.'/modules/exmbb/class/bbpost.class.php';
	
	foreach ($_GET as $k => $v){
		$$k = $v;
	}
	
	$feed = array();		// Información General
	$ret = array();
	$mc =& $util->moduleConfig('exmbb');
	
	if ($show == 'all'){
		$feed['title'] = htmlspecialchars(_MI_BB_RSSALL);
		$feed['link'] = XOOPS_URL.'/modules/exmbb';
		$feed['description'] = htmlspecialchars($util->filterTags($mc['rssdesc']));
		
		$sql = "SELECT a.*, b.title FROM ".$db->prefix("exmbb_posts")." a,".$db->prefix("exmbb_topics")." b WHERE 
				a.approved='1' AND b.id_topic=a.id_topic ORDER BY a.post_time DESC LIMIT 0,$limit";
	} else{
		
		if ($id<=0) return;
		$forum = new BBForum($id);
		if ($forum->isNew()) return;
		
		$feed['title'] = htmlspecialchars(sprintf(_MI_BB_RSSNAMEFORUM, $forum->name()));
		$feed['link'] = XOOPS_URL.'/modules/exmbb/forum.php?id='.$forum->id();
		$feed['description'] = htmlspecialchars($forum->description());
		
		$sql = "SELECT a.*, b.title FROM ".$db->prefix("mod_bxpress_posts")." a,".$db->prefix("mod_bxpress_topics")." b WHERE a.id_forum='$id' AND a.approved='1' AND b.id_topic=a.id_topic ORDER BY a.post_time DESC LIMIT 0,$limit";
		
	}
	
	// Generamos los elementos
	$result = $db->query($sql);
	$posts = array();
	while ($row = $db->fetchArray($result)){
		$post = new BBPost();
		$post->assignVars($row);
		$rtn = array();
		$rtn['title'] = htmlspecialchars($row['title']);
		$rtn['link'] = htmlspecialchars(XOOPS_URL.'/modules/exmbb/topic.php?pid='.$post->id()."#p".$post->id(), ENT_QUOTES);
		$rtn['description'] = utf8_encode(($post->text()));
		$rtn['date'] = formatTimestamp($post->date());
		$posts[] = $rtn;
	}

	
	$ret = array('feed'=>$feed, 'items'=>$posts);
	return $ret;
	
}
?>

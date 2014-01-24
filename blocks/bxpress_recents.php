<?php
// $Id: delete.php 861 2011-12-19 02:38:22Z i.bitcero $
// --------------------------------------------------------------
// bXpress Forums
// A simple forums module for XOOPS and Common Utilities
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

load_mod_locale('bxpress');

function bxpress_recents_show($options){
	
    $util = RMUtilities::get();
    $tc = TextCleaner::getInstance();
	$db = XoopsDatabaseFactory::getDatabaseConnection();
	$xoopsModuleConfig = $util->module_config('exmbb');
    $mc = RMSettings::module_settings('bxpress');
	
	$tbl1 = $db->prefix('mod_bxpress_posts');
	$tbl2 = $db->prefix('mod_bxpress_topics');
	$tbl3 = $db->prefix('mod_bxpress_posts_text');
    $tbl4 = $db->prefix('mod_bxpress_forums');
    
    $sql = "SELECT MAX(id_post) AS id FROM $tbl1 WHERE approved=1 GROUP BY id_topic ORDER BY MAX(id_post) DESC LIMIT 0,$options[0]";
    	
	$result=$db->queryF($sql);
	
	$topics = array();
	$block = array();
    
    include_once XOOPS_ROOT_PATH.'/modules/bxpress/class/bxforum.class.php';
    include_once XOOPS_ROOT_PATH.'/modules/bxpress/class/bxpost.class.php';
    include_once XOOPS_ROOT_PATH.'/modules/bxpress/class/bxtopic.class.php';
    include_once XOOPS_ROOT_PATH.'/modules/bxpress/class/bxfunctions.class.php';
    
    $post = new bXPost();
    $forum = new bXForum();
    $tf = new RMTimeFormatter(0, '%T%-%d%-%Y% at %h%:%i%');
    
	while ($row=$db->fetchArray($result)){
        $post = new bXPost($row['id']);
        $topic = new bXTopic($post->topic());
        $forum = new bXForum($post->forum());
        
		$ret = array();
		$ret['id'] = $topic->id();
		$ret['post'] = $post->id();
		$ret['replies'] = $topic->replies();
		$ret['views'] = $topic->views();
        $ret['link'] = $post->permalink();
		if ($options[2]) $ret['date'] = $tf->format($post->date());
		if ($options[3]) $ret['poster']= sprintf(__('Posted by: %s','bxpress'), "<a href='".$post->permalink()."'>".$post->uname()."</a>");
		$ret['title'] = $topic->title();
		if ($options[4]) $ret['text'] = $tc->clean_disabled_tags($post->text());
        $ret['forum'] = array(
            'id' => $forum->id(),
            'name' => $forum->name(),
            'link' => $forum->permalink()
        );
		$topics[] = $ret;
	}
	
	// Opciones
	$block['showdates'] = $options[2];
	$block['showuname'] = $options[3];
	$block['showtext'] = $options[4];
	
	$block['topics'] = $topics;
	$block['lang_topic'] = __('Topic','bxpress');
	$block['lang_date'] = __('Date','bxpress');
	$block['lang_poster'] = __('Poster','bxpress');
		
	return $block;
	
}

function bxpress_recents_edit($options, &$form=null){
	
	$form->addElement(new RMSubTitle(_AS_BKM_BOPTIONS, 1, 'head'));
	$form->addElement(new RMText(_BS_BB_NMTOPICS,'options[0]',10,3,$options[0]), true, 'num');
	$form->addElement(new RMYesNo(_BS_BB_TOPICSREP, 'options[1]', $options[1]));
	$form->addElement(new RMYesNo(_BS_BB_SHOWDATE, 'options[2]',$options[2]));
	$form->addElement(new RMYesNo(_BS_BB_SHOWUNAME, 'options[3]', $options[3]));
	$form->addElement(new RMYesNo(_BS_BB_SHOWRES, 'options[4]', $options[4]));
	
	return $form;
}

?>

<?php
// $Id: report.php 861 2011-12-19 02:38:22Z i.bitcero $
// --------------------------------------------------------------
// bXpress Forums
// An simple forums module for XOOPS and Common Utilities
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

include '../../mainfile.php';
	
$op=isset($_REQUEST['op']) ? $_REQUEST['op'] : '';


if ($op=='report'){
		
	$xoopsOption['template_main']='bxpress_report.html';
	$xoopsOption['module_subpage'] = "report";	

	include 'header.php';
	
	bXFunctions::makeHeader();
	//Id de mensaje
	$pid = isset($_REQUEST['pid']) ? intval($_REQUEST['pid']) : 0;
	
	$post=new bXPost($pid);
	$forum=new bXForum($post->forum());
	$topic=new bXTopic($post->topic());


	$form=new RMForm(__('Report Post','bxpress'),'formrep','report.php');
	$form->styles('width: 30%;','odd');
	$form->addElement(new RMFormEditor(__('Your reasons to report this post','bxpress'),'report','90%','300px','','textarea'),true);
	$form->addElement(new RMFormHidden('op','savereport'));
	$form->addElement(new RMFormHidden('pid',$pid));
	$form->addElement(new RMFormHidden('id',$topic->id()));

	$buttons= new RMFormButtonGroup();
	$buttons->addButton('sbt', _SUBMIT, 'submit');
	$buttons->addButton('cancel', _CANCEL, 'button', 'onclick="history.go(-1);"');
	
	$form->addElement($buttons);

	$tpl->assign('report_contents', $form->render());
	$tpl->assign('forumtitle',$forum->name());
	$tpl->assign('topictitle',$topic->title());	
	$tpl->assign('forumid',$forum->id());
	$tpl->assign('topicid',$topic->id());
	$tpl->assign('report',__('Report Post','bxpress'));


	include 'footer.php';

}elseif ($op=='savereport'){
		foreach ($_POST as $k=>$v){
			$$k=$v;
		}

		//Verificamos que el mensaje sea válido
		if ($pid<=0){
			redirect_header('./topic.php?id='.$id,1,__('Sepecified post is not valid!','bxpress'));
			die();
		}
		
		//Comprobamos que el mensaje exista
		$post=new bXPost($pid);
		if ($post->isNew()){
			redirect_header('./topic.php?id='.$id,1,__('Specified post does not exists!','bxpress'));
			die();
		}
		
		
		if (!$xoopsSecurity->check()){
			redirect_header('./topic.php?pid='.$pid.'#p'.$pid, 2, __('Session token expired!','bxpress'));
			die();
		}
		
		$rep=new bXReport();
		$rep->setPost($pid);
		$rep->setUser($xoopsUser->uid());
		$rep->setIp($_SERVER['REMOTE_ADDR']);
		$rep->setTime(time());
		$rep->setReport($report);

		
		if ($rep->save()){
			redirect_header('./topic.php?id='.$id,1,__('Thanks for reporting! Moderators will be notified.','bxpress'));
		}
		else{
			redirect_header('./topic.php?id='.$id,1, __('Report could not be sent! Please try again later.','bxpress'));
		}

}

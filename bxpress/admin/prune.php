<?php
// $Id: reports.php 861 2011-12-19 02:38:22Z i.bitcero $
// --------------------------------------------------------------
// bXpress Forums
// An simple forums module for XOOPS and Common Utilities
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------


define('RMCLOCATION', 'prune');
include 'header.php';

/**
* @desc Permitirá al administrador elegir los temas que serán 
* eliminados despues de un cierto período
**/
function prune(){

	global $xoopsModule;

    $bc = RMBreadCrumb::get();
    $bc->add_crumb( __('Prune forum', 'bxpress' ) );

	xoops_cp_header();
    
    $db = XoopsDatabaseFactory::getDatabaseConnection();
	$form=new RMForm(__('Prune Posts','bxpress'),'frmprune','prune.php');
	
	//Lista de foros
	$ele=new RMFormSelect(__('Prune from forum','bxpress'),'forums');
	$ele->addOption('',__('Select option...','bxpress'));
	$ele->addOption(0,__('All forums','bxpress'));
	$sql="SELECT id_forum,name FROM ".$db->prefix('mod_bxpress_forums');
	$result=$db->queryF($sql);
	while ($row=$db->fetchArray($result)){
		$ele->addOption($row['id_forum'],$row['name']);
	}
	$form->addElement($ele,true);

	//Dias de antigüedad de temas
	$days=new RMFormText(__('Days old','bxpress'),'days',3,3, 30);
	$days->setDescription(__('Delete topics older than these days','bxpress'));
	$form->addElement($days,true);

	//Lista de opciones para purgar temas
	$opc=new RMFormSelect(__('Topics to delete','bxpress'),'option');
	$opc->addOption('',__('Select option','bxpress'));
	$opc->addOption(1,__('All topics','bxpress'));
	$opc->addOption(2,__('Unanswered Topics','bxpress'));
	
	$form->addElement($opc,true);

	//Temas fijos
	$form->addElement(new RMFormYesno(__('Delete Sticky Topics','bxpress'),'fixed'));
	
	$buttons= new RMFormButtonGroup();
	$buttons->addButton('sbt', __('Prune Now!'), 'submit', 'onclick="return confirm(\''.__('Do you really wish to delete the topics? \nThis action will delete the data permanently.','bxpress').'\');"');
	$buttons->addButton('cancel', __('Cancel','bxpress'), 'button', 'onclick="history.go(-1);"');
	
	$form->addElement($buttons);
	
	$form->addElement(new RMFormHidden('action','deltopics'));

	$form->display();
	   
	xoops_cp_footer();

}


/**
* @desc Elimina los temas especificados
**/
function deleteTopics(){
	global $xoopsSecurity;	

	foreach ($_POST as $k=>$v){
		$$k=$v;
	}
        
	if (!$xoopsSecurity->check()){
		redirectMsg('prune.php', __('Session token expired!','bxpress'),0);
		die();
	}
        
        $db = XoopsDatabaseFactory::getDatabaseConnection();

	$sql= "SELECT id_topic FROM ".$db->prefix('mod_bxpress_topics')." WHERE ";
	$sql.=($forums==0 ? '' : "id_forum='$forums' "); //Determinamos de que foro se va a limpiar temas	
	$sql.=($forums ? " AND date<".(time()-($days*86400)) : " date<".(time()-($days*86400))); //Determinamos los temas con los dias de antigüedad especificados
	$sql.=($option==2 ? " AND replies=0" : ''); //Determinamos los temas a eliminar
	$sql.=($fixed ? " AND sticky=1 " : ' AND sticky=0'); //Temas fijos
	 
	$result=$db->queryF($sql);
        $num = $db->getRowsNum($result);
	while ($rows=$db->fetchArray($result)){
		$topic=new BBTopic();
		$topic->assignVars($rows);
		
		$topic->delete();
	}
	
	redirectMsg('prune.php', sprintf(__('Prune done! %u topics deleted','bxpress'), $num),0);
	
}




$action = rmc_server_var($_POST, 'action', '');

switch ($action){
	case 'deltopics':
            deleteTopics();
            break;
	default:
            prune();
}

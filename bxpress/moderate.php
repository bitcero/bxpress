<?php
// $Id: moderate.php 857 2011-12-14 10:52:30Z mambax7@gmail.com $
// --------------------------------------------------------------
// bXpress Forums
// An simple forums module for XOOPS and Common Utilities
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

include '../../mainfile.php';

$id = rmc_server_var($_REQUEST, 'id', 0);
if ($id<=0){
	redirect_header('./', 2, __('Please, specify the forum you want to moderate!','bxpress'));
	die();
}

$forum = new bXForum($id);
if ($forum->isNew()){
	redirect_header('./', 2, __('Specified forum doesn\'t exists!','bxpress'));
	die();
}

// Comprobamos los permisos de moderador
if (!$xoopsUser || (!$forum->isModerator($xoopsUser->uid()) && !$xoopsUser->isAdmin())){
	redirect_header('forum.php?id='.$id, 2, __('Sorry, you don\'t have permission to do this action!','bxpress'));
	die();
}

/**
* @desc Muestra todas las opciones configurables
*/
function showItemsAndOptions(){
	global $xoopsUser, $db, $xoopsOption, $tpl, $xoopsModule, $xoopsConfig, $xoopsSecurity;
	global $xoopsModuleConfig, $forum;
	
	$xoopsOption['template_main'] = "bxpress_moderate.html";
	$xoopsOption['module_subpage'] = "moderate";
	include 'header.php';
	
	/**
	* Cargamos los temas
	*/
	$tbl1 = $db->prefix("mod_bxpress_topics");
	$tbl2 = $db->prefix("mod_bxpress_forumtopics");
        
	$sql = "SELECT COUNT(*) FROM $tbl1 WHERE id_forum='".$forum->id()."' ";
	list($num)=$db->fetchRow($db->queryF($sql));
	    
	$page = isset($_REQUEST['pag']) ? $_REQUEST['pag'] : '';
	$limit = $xoopsModuleConfig['topicperpage'] > 0 ? $xoopsModuleConfig['topicperpage'] : 15;
	if ($page > 0){ $page -= 1; }
	        
	$start = $page * $limit;
	$tpages = (int)($num / $limit);
	if($num % $limit > 0) $tpages++;
	    
	$pactual = $page + 1;
	if ($pactual>$tpages){
	    $rest = $pactual - $tpages;
	    $pactual = $pactual - $rest + 1;
	    $start = ($pactual - 1) * $limit;
	}
	    
	if ($tpages > 0) {
	    $nav = new RMPageNav($num, $limit, $pactual);
            $nav->target_url('moderate.php?id='.$forum->id().'&amp;pag={PAGE_NUM}');
	    $tpl->assign('itemsNavPage', $nav->render(false));
	}

	$sql = str_replace("COUNT(*)", '*', $sql);
	$sql .= " ORDER BY sticky DESC, date DESC LIMIT $start,$limit";
	$result = $db->query($sql);
        
	while ($row = $db->fetchArray($result)){
	    $topic = new bXTopic();
	    $topic->assignVars($row);
	    $last = new bXPost($topic->lastPost());
	    $lastpost = array();
	    if (!$last->isNew()){
    		$lastpost['date'] = bXFunctions::formatDate($last->date());
    		$lastpost['by'] = sprintf(__('By: %s','bxpress'), $last->uname());
    		$lastpost['id'] = $last->id();
    		if ($xoopsUser){
    			$lastpost['new'] = $last->date()>$xoopsUser->getVar('last_login') && (time()-$last->date()) < $xoopsModuleConfig['time_new'];
    		} else {
    			$lastpost['new'] = (time()-$last->date())<=$xoopsModuleConfig['time_new'];
			}
		}
		$tpages = ceil($topic->replies()/$xoopsModuleConfig['perpage']);
		if ($tpages>1){
			$pages = bXFunctions::paginateIndex($tpages);
		} else {
			$pages = null;
		}
	    $tpl->append('topics', array('id'=>$topic->id(), 'title'=>$topic->title(),'replies'=>$topic->replies(),
    				'views'=>$topic->views(),'by'=>sprintf(__('By: %s','bxpress'), $topic->posterName()),
    				'last'=>$lastpost,'popular'=>($topic->replies()>=$forum->hotThreshold()),
    				'sticky'=>$topic->sticky(),'pages'=>$pages, 'tpages'=>$tpages,
				'approved'=>$topic->approved(),'closed'=>$topic->status()));
	}
	
	$tpl->assign('forum', array('id'=>$forum->id(), 'title'=>$forum->name()));
	$tpl->assign('lang_topic', __('Topic','bxpress'));
	$tpl->assign('lang_replies', __('Replies','bxpress'));
	$tpl->assign('lang_views', __('Views','bxpress'));
	$tpl->assign('lang_lastpost', __('Last Post','bxpress'));
	$tpl->assign('lang_sticky', __('Sticky','bxpress'));
	$tpl->assign('lang_moderating', __('Moderating Forum','bxpress'));
	$tpl->assign('lang_pages', __('Pages','bxpress'));
	$tpl->assign('lang_move', __('Move','bxpress'));
	$tpl->assign('lang_open', __('Unlock','bxpress'));
	$tpl->assign('lang_close', __('Lock','bxpress'));
	$tpl->assign('lang_dosticky', __('Sticky','bxpress'));
	$tpl->assign('lang_dounsticky', __('Unsticky','bxpress'));
	$tpl->assign('lang_approved',__('Approved','bxpress'));
	$tpl->assign('lang_app',__('Approve','bxpress'));
	$tpl->assign('lang_noapp',__('Unapprove','bxpress'));
	$tpl->assign('lang_delete', __('Delete','bxpress'));
    $tpl->assign('lang_confirm', __('Do you really want to delete selected topics?','bxpress'));
	$tpl->assign('token_input',$xoopsSecurity->getTokenHTML());
	
	bXFunctions::makeHeader();
    
    RMTemplate::get()->add_xoops_style('style.css', 'bxpress');
	
	include 'footer.php';
	
}

/**
* @desc Mover temas de un foro a otro
*/
function moveTopics(){
	
    global $db, $xoopsModuleConfig, $xoopsSecurity, $forum, $xoopsUser, $xoopsOption, $xoopsConfig;
	
    $topics = isset($_REQUEST['topics']) ? $_REQUEST['topics'] : null;
	$ok = isset($_POST['ok']) ? $_POST['ok'] : 0;
        $moveforum = rmc_server_var($_POST, 'moveforum', 0);
	
	if (empty($topics) || (is_array($topics) && empty($topics))){
		redirect_header('moderate.php?id='.$moveforum, 2, __('Select at least a topic to moderate!','bxpress'));
		die();
	}

	$topics = !is_array($topics) ? array($topics) : $topics;
	
	if ($ok){
		
		if (!$xoopsSecurity->check()){
			redirect_header('moderate.php?id='.$moveforum, 2, __('Session token expired!','bxpress'));
			die();
		}
		
		if ($moveforum<=0){
			redirect_header('moderate.php?id='.$forum->id(), 2, __('Please select the target forum','bxpress'));
			die();
		}
		
		$mf = new bXForum($moveforum);
		if ($mf->isNew()){
			redirect_header('moderate.php?id='.$forum->id(), 2, __('Specified forum does not exists!','bxpress'));
			die();
		}
	
		$lastpost = false;
		foreach ($topics as $k){
			$topic = new bXTopic($k);
			if ($topic->forum()!=$forum->id()) continue;

			
			//Verificamos si el tema contiene el último mensaje del foro
			if(!$lastpost && array_key_exists($forum->lastPostId(),$topic->getPosts(0))){
				$lastpost = true;
			}
			
			$topic->setForum($moveforum);
			if ($topic->save()){
				//Decrementa el número de temas
				$forum->setTopics(($forum->topics()-1>0) ? $forum->topics()-1 : 0);
				$forum->setPosts(($forum->posts()-($topic->replies() + 1)>0) ? $forum->posts()-($topic->replies()+1) : 0);
				$forum->save();
				
				$mf->setPosts($mf->posts()+($topic->replies() + 1));
				$mf->addTopic();
				$mf->save();

				//Cambiamos el foro de los mensajes del tema
				if ($topic->getPosts()){
					foreach ($topic->getPosts() as $k=>$v){
						$v->setForum($moveforum);
						$v->save();
					}

				}
			}				
		}
		
		//Actualizamos el último mensaje del foro
		if($lastpost){
			
			$post = $forum->getLastPost();
			$forum->setPostId($post);
			$forum->save();	
		}

		//Actualizamos el último mensaje del foro al que fue movido el tema
		$post = $mf->getLastPost();
		$post ? $mf->setPostId($post) : '';
		$mf->save();	

		redirect_header('moderate.php?id='.$forum->id(), 1, __('Topics has been relocated!','bxpress'));
		die();
		
	} else {
		
		global $xoopsTpl;
                $tpl = $xoopsTpl;
		$xoopsOption['template_main'] = "bxpress_moderateforms.html";
		$xoopsOption['module_subpage'] = "moderate";
		include 'header.php';
		
		bXFunctions::makeHeader();
		$form = new RMForm(__('Move Topics','bxpress'), 'frmMove', 'moderate.php');
		$form->addElement(new RMFormHidden('id', $forum->id()));
		$form->addElement(new RMFormHidden('op','move'));
		$form->addElement(new RMFormHidden('ok','1'));
		$i = 0;
		foreach ($topics as $k){
			$form->addElement(new RMFormHidden('topics['.$i.']',$k));
			++$i;
		}
		$form->addElement(new RMFormSubTitle('&nbsp',1,''));
		$form->addElement(new RMFormSubTitle(__('Select the forum where you wish to move selected topics','bxpress'),1,'even'));
		$ele = new RMFormSelect(__('Forum','bxpress'), 'moveforum');
		$ele->addOption(0,'',1);
		
		$tbl1 = $db->prefix("mod_bxpress_categories");
		$tbl2 = $db->prefix("mod_bxpress_forums");
		$sql = "SELECT b.*, a.title FROM $tbl1 a, $tbl2 b WHERE b.cat=a.id_cat AND b.active='1' AND id_forum<>".$forum->id()." ORDER BY a.order, b.order";
		$result = $db->query($sql);
		$categories = array();
		while ($row = $db->fetchArray($result)){
			$cforum = array('id'=>$row['id_forum'], 'name'=>$row['name']);
			if (isset($categores[$row['cat']])){
				$categories[$row['cat']]['forums'][] = $cforum;
			} else {
				$categories[$row['cat']]['title'] = $row['title'];
				$categories[$row['cat']]['forums'][] = $cforum;
			}
		}
		
		foreach ($categories as $cat){
			
			$ele->addOption(0, $cat['title'], 0, true, 'color: #000; font-weight: bold; font-style: italic; border-bottom: 1px solid #c8c8c8;');
			foreach ($cat['forums'] as $cforum){
				$ele->addOption($cforum['id'], $cforum['name'],0,false,'padding-left: 10px;');
			}
			
		}
		$form->addElement($ele, true, "noselect:0");
		$ele = new RMFormButtonGroup();
		$ele->addButton('sbt',__('Move Topics Now!','bxpress'),'submit');
		$ele->addButton('cancel', __('Cancel','bxpress'), 'button', 'onclick="history.go(-1);"');
		$form->addElement($ele);
		$tpl->assign('moderate_form', $form->render());
		
		include 'footer.php';
		
	}
	
}

/**
* @desc Cerrar o abrir un tema
*/
function closeTopic($close){
	global $xoopsSecurity, $forum, $xoopsUser;


	$topics = isset($_REQUEST['topics']) ? $_REQUEST['topics'] : null;
		
	if (empty($topics) || (is_array($topics) && empty($topics))){
		redirect_header('moderate.php?id='.$forum->id(), 2, __('Select at least one topic to moderate!','bxpress'));
		die();
	}
	
	$topics = !is_array($topics) ? array($topics) : $topics;
	
	foreach ($topics as $k){
		$topic = new bXTopic($k);
		if ($topic->isNew()) continue;
		
		$topic->setStatus($close);
		$topic->save();
		
	}
	
	redirect_header('moderate.php?id='.$forum->id(), 1, __('Action completed!','bxpress'));
	
}

/**
* @desc Cerrar o abrir un tema
*/
function stickyTopic($sticky){
	global $forum, $xoopsSecurity;
		
	if (!$xoopsSecurity->check()){
		redirect_header('moderate.php?id='.$forum->id(), 2, __('Session token expired!','bxpress'));
		die();
	}
	
	$topics = isset($_REQUEST['topics']) ? $_REQUEST['topics'] : null;
		
	if (empty($topics) || (is_array($topics) && empty($topics))){
		redirect_header('moderate.php?id='.$forum->id(), 2, __('Select at least one topic to set as sticky!','bxpress'));
		die();
	}
	
	$topics = !is_array($topics) ? array($topics) : $topics;
	
	foreach ($topics as $k){
		$topic = new bXTopic($k);
		if ($topic->isNew()) continue;
		
		$topic->setSticky($sticky);
		$topic->save();
		
	}
	
	redirect_header('moderate.php?id='.$forum->id(), 1, __('Action completed!','bxpress'));
	
}

/**
* @desc Eliminar temas
*/
function deleteTopics(){
	global $db, $xoopsModuleConfig, $bxpress, $forum, $xoopsUser, $xoopsSecurity;
	
	$ok = isset($_POST['ok']) ? $_POST['ok'] : 0;	
	$topics = isset($_REQUEST['topics']) ? $_REQUEST['topics'] : null;
		
	if (empty($topics) || (is_array($topics) && empty($topics))){
		redirect_header('moderate.php?id='.$forum->id(), 2, __('Select at least one topic to delete!','bxpress'));
		die();
	}
	
	$topics = !is_array($topics) ? array($topics) : $topics;
	
	$lastpost = false;
		
	if (!$xoopsSecurity->check()){
		redirect_header('moderate.php?id='.$forum->id(), 2, __('Session token expired!','bxpress'));
		die();
	}
	
    foreach ($topics as $k){
		$topic = new bXTopic($k);
		if ($topic->isNew()) continue;
		if ($topic->forum()!=$forum->id()) continue;

		//Verificamos si el tema contiene el último mensaje del foro
		if(!$lastpost && array_key_exists($forum->lastPostId(),$topic->getPosts(0))){
            $lastpost = true;
		}
			
		$topic->delete();
			
	}

	//Actualizamos el último mensaje del foro
	if($lastpost){
		$forum = new bXForum($forum->id());		

		$post = $forum->getLastPost();
		$forum->setPostId($post);
		$forum->save();	
	}
		
	redirect_header('moderate.php?id='.$forum->id(), 1, __('Action completed!','bxpress'));
	
}



/**
* @desc Aprueba o no un tema
**/
function approvedTopics($app=0){

	global $forum, $xoopsSecurity;

	$topics = isset($_REQUEST['topics']) ? $_REQUEST['topics'] : null;
		
	if (empty($topics) || (is_array($topics) && empty($topics))){
		redirect_header('moderate.php?id='.$forum->id(), 2, __('Select at least one topic to moderate','bxpress'));
		die();
	}
	
	$topics = !is_array($topics) ? array($topics) : $topics;
		
	if (!$xoopsSecurity->check()){
		redirect_header('moderate.php?id='.$forum->id(), 2, __('Session token expired!','bxpress'));
		die();
	}

	$lastpost = false;
	foreach ($topics as $k){
		$topic = new bXTopic($k);
		if ($topic->isNew()) continue;

		$lastapp = $topic->approved();

		$topic->setApproved($app);
		$topic->save();
		
	}

	//Actualizamos el último mensaje del foro
	$post = $forum->getLastPost();
	$forum->setPostId($post);
	$forum->save();	

	redirect_header('moderate.php?id='.$forum->id(), 1, __('Action completed!','bxpress'));

}


/**
* @desc Aprueba o no un mensaje editado
**/
function approvedPosts($app=0){
	global $xoopsUser, $xoopsSecurity;
    
	$posts=isset($_REQUEST['posts']) ? intval($_REQUEST['posts']) : 0;

	
	//Verifica que el mensaje sea válido
	if ($posts<=0){
		redirect_header('./topic.php?id='.$posts,1,__('Topic not valid!','bxpress'));
		die();
	}

	//Comprueba que el mensaje exista
	$post=new bXPost($posts);
	if ($post->isNew()){
		redirect_header('./topic.php?id='.$posts,1,__('Post doesn\'t exists!','bxpress'));
		die();
	}	

	//Comprueba si usuario es moderador del foro
	$forum=new bXForum($post->forum());
	if (!$forum->isModerator($xoopsUser->uid()) || !$xoopsUser->isAdmin()){
		redirect_header('./topic.php?id='.$posts,1,__('You don\'t have permission to do this action!','bxpress'));
		die();
	}

		
	if (!$xoopsSecurity->check()){
		redirect_header('./topic.php?id='.$posts, 2, __('Session token expired!','bxpress'));
		die();
	}

	$post->setApproved($app);
	if ($post->editText()){
		$post->setText($post->editText());
	}
	$post->setEditText('');
	$post->save();

	redirect_header('./topic.php?id='.$post->topic(), 1, __('Operation completed!','bxpress'));

	

}



$op = isset($_REQUEST['op']) ? $_REQUEST['op'] : '';

switch($op){
	case 'move':
		moveTopics();
		break;
	case 'close':
		closeTopic(1);
		break;
	case 'open':
		closeTopic(0);
		break;
	case 'sticky':
		stickyTopic(1);
		break;
	case 'unsticky':
		stickyTopic(0);
		break;
	case 'delete':
		deleteTopics();
		break;
	case 'approved':
		approvedTopics(1);
	break;
	case 'noapproved':
		approvedTopics();
	break;
	case 'approvedpost':
		approvedPosts(1);
	break;
	case 'noapprovedpost':
		approvedPosts();
	break;
	default:
		showItemsAndOptions();
		break;
}

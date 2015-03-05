<?php
// $Id: post.php 1013 2012-08-23 05:35:18Z i.bitcero $
// --------------------------------------------------------------
// bXpress Forums
// An simple forums module for XOOPS and Common Utilities
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

define('BB_LOCATION','post');
include '../../mainfile.php';

$op = isset($_REQUEST['op']) ? $_REQUEST['op'] : '';

$fid = RMHttpRequest::request( 'fid', 'integer', 0 ); // Forum ID
$tid = RMHttpRequest::request( 'tid', 'integer', 0 ); // Topic ID
$pid = RMHttpRequest::request( 'pid', 'integer', 0 ); // Post ID (replies)

if ($fid<=0 && $tid<=0){
	redirect_header('./', 2, __('You must specify a forum in order to create a new topic!','bxpress'));
	die();
}

if ($fid>0){
	$forum = new bXForum($fid);
	$retlink = './forum.php?id='.$forum->id();
	$create = true;
} else {
	$topic = new bXTopic($tid);
	if ($topic->isNew()){
		redirect_header('./', 2, __('Specified topic does not exists!','bxpress'));
		die();
	}
	$forum = new bXForum($topic->forum());
	$retlink = './topic.php?id='.$topic->id();
	$create = false;
}

if ($forum->isNew()){
	redirect_header('./', 2, __('Specified forum does not exists!','bxpress'));
	die();
}

if (!$forum->isAllowed($xoopsUser ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS, $fid>0 ? 'topic' : 'reply')){
	redirect_header($retlink, 2, __('You do not have permission to do this!','bxpress'));
	die();
}

// Load specified post
if ( $pid > 0 ){
    $parent = new bXPost( $pid );
    if ( $parent->isNew() )
        $pid = 0;
}

switch($op){
	case 'post':
		
		foreach ($_POST as $k => $v){
			$$k = $v;
		}
		
		if (!$xoopsSecurity->check()){
			redirect_header('./'.($create ? 'forum.php?id='.$forum->id() : 'topic.php?id='.$topic->id()), 2, __('Session token expired!','bxpress'));
			die();
		}
		
		$myts =& MyTextSanitizer::getInstance();
		
		if ($create){
			$topic = new bXTopic();
			$topic->setApproved($forum->isAllowed($xoopsUser ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS, 'approve'));
			$topic->setDate(time());
			$topic->setForum($forum->id());
			$topic->setPoster($xoopsUser ? $xoopsUser->uid() : 0);
			$topic->setPosterName($xoopsUser ? $xoopsUser->uname() : $name);
			$topic->setRating(0);
			$topic->setReplies(0);
			$topic->setStatus(0);
			if ($xoopsUser && $xoopsModuleConfig['sticky']){
				$csticky = $xoopsUser->isAdmin() || $forum->isModerator($xoopsUser->uid()) || $xoopsUser->posts()>$xoopsModuleConfig['sticky_posts'];
				if ($sticky) $topic->sticky(isset($sticky) ? $sticky : 0);
				
			} else {
				$topic->setSticky(0);
			}
			$topic->setTitle($myts->addSlashes($subject));
			$topic->setViews(0);
			$topic->setVotes(0);
			$topic->setFriendName(TextCleaner::getInstance()->sweetstring($subject));
			if ($xoopsUser && isset($sticky) && $xoopsModuleConfig['sticky']){
				if ($xoopsUser->isAdmin() || $forum->isModerator($xoopsUser->uid()) || $xoopsUser->posts()>$xoopsModuleConfig['sticky_posts']){
					$topic->setSticky($sticky);
				}
			}
			if (!$topic->save()){
				redirect_header('./forum.php?id='.$forum->id(), 2, __('Message could not be posted! Please try again','bxpress'));
				die();
			}
		}
		
		
		$post = new bXPost();
		$post->setPid(0);
		
		$post->setTopic($topic->id());
		$post->setForum($forum->id());
		$post->setDate(time());
		$post->setVar( 'parent', $pid );
		$post->setUser($xoopsUser ? $xoopsUser->uid() : 0);
		$post->setUname($xoopsUser ? $xoopsUser->uname() : $name);
		$post->setIP($_SERVER['REMOTE_ADDR']);
		$post->setHTML(isset($dohtml) ? 1 : 0);
		$post->setBBCode(isset($doxcode) ? 1 : 0);
		$post->setSmiley(isset($dosmiley) ? 1 : 0);
		$post->setBR(isset($dobr) ? 1 : 0);
		$post->setImage(isset($doimg) ? 1 : 0);
		$post->setIcon('');
		$post->setApproved($forum->isAllowed($xoopsUser ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS, 'approve'));
		$post->setSignature(isset($sig) ? 1 : 0);
		$post->setText($msg);
		if (!$post->save() && $create){
			$topic->delete();
			redirect_header($retlink, 2, __('Message could not be posted! Please try again','bxpress'));
			die();
		}
		if (!$topic->approved()){
			bXFunctions::notifyAdmin($forum->moderators(),$forum, $topic, $post);
		}
		// Adjuntamos archivos si existen
		if ($forum->attachments() && $forum->isAllowed($xoopsUser ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS, 'attach')){
			
			$folder = $xoopsModuleConfig['attachdir'];
			$exts = array();
			
            include_once RMCPATH.'/class/uploader.php';
            $up = new RMFileUploader($folder, $xoopsModuleConfig['maxfilesize']*1024, $forum->extensions());

			$errors = '';
			$filename = '';
			
			if ($up->fetchMedia('attach')){
				if (!$up->upload()){
					$errors .= $up->getErrors();
				} else {
				
					$filename = $up->getSavedFileName();
					$fullpath = $up->getSavedDestination();
				
					$attach = new bXAttachment();
					$attach->setPost($post->id());
					$attach->setFile($filename);
					$attach->setMime($up->getMediaType());
					$attach->setDate(time());
					$attach->downloads(0);
					$attach->setName($up->getMediaName());
					if (!$attach->save()) $errors .= $attach->getErrors();
				}
				
			}
			
		}
		
		$topic->setLastPost($post->id());
		if (!$create) $topic->addReply();
		$topic->save();
		
		$forum->setPostId($post->id());
		$forum->addPost();
		if ($create) $forum->addTopic();
		$forum->save();
		
		// Incrementamos el nivel de posts del usuario
		if ($xoopsUser){
            $member_handler =& xoops_gethandler('member');
            $member_handler->updateUserByField($xoopsUser, 'posts', $xoopsUser->getVar('posts') + 1);
		}
		
		// Notificaciones
        include_once XOOPS_ROOT_PATH.'/kernel/notification.php';
		$not = new XoopsNotificationHandler($xoopsDB);
		if ($create){
			//Notificacion de nuevo tema en foro
			$not->triggerEvent('forum',$forum->id(), 'newtopic',array('topic'=>$topic->id()));
			//Notificación de nuevo mensaje en cualquier foro
			$not->triggerEvent('any_forum','', 'postanyforum',array('forum'=>$forum->id(),'post'=>$post->id()));
		}else{
			//Notificación de nuevo mensaje en tema
			$not->triggerEvent('topic',$topic->id(), 'newpost',array('post'=>$post->id()));
			//Notificación de nuevo mensaje en foro especificado
			$not->triggerEvent('forum',$forum->id(), 'postforum',array('post'=>$post->id()));
			//Notificación de nuevo mensaje en cualquier foro
			$not->triggerEvent('any_forum','', 'postanyforum',array('forum'=>$forum->id(),'post'=>$post->id()));
			
		}

		redirect_header('topic.php?pid='.$post->id().'#p'.$post->id(), 1, $errors == '' ? __('Your posts has been sent!','bxpress') : __('Message posted, however some errors ocurred while sending!','bxpress') .'<br />'.$errors);
			
		break;
		
	default:
		
		$xoopsOption['template_main'] = "bxpress-postform.tpl";
		$xoopsOption['module_subpage'] = "post";

		include 'header.php';

		bXFunctions::makeHeader();
				
		$form = new RMForm($tid>0 ? __('Reply','bxpress') : __('Create New Topic','bxpress'), 'frmTopic', 'post.php');
		$form->addElement(new RMFormSubTitle(__('Write your post and send it','bxpress'), 1, 'even'));
		if (!$xoopsUser){
			$form->addElement(new RMFormText(__('Your name:','bxpress'), 'name', 50, 255), true);
			$form->addElement(new RMFormText(__('Your email:','bxpress'), 'email', 50, 255), true, 'email');
		}
		if ($create) $form->addElement(new RMFormText(__('Topic subject:','bxpress'), 'subject', 50, 255, $tid>0 ? $topic->title() : ''), true);
		
		// Sticky
		if ($xoopsUser && $xoopsModuleConfig['sticky'] && $create){
			
			$sticky = $xoopsUser->isAdmin() || $forum->isModerator($xoopsUser->uid()) || $xoopsUser->posts()>$xoopsModuleConfig['sticky_posts'];
			if ($sticky){
				if ($create || bXFunctions::getFirstId($topic->id())==$topic->id())
					$form->addElement(new RMFormYesNo(__('Sticky topic','bxpress'), 'sticky', !$create ? $topic->sticky() : 0));
			}
			
		}
		
		// Si se especifico una acotación entonces la cargamos
		$idq = isset($_GET['quote']) ? intval($_GET['quote']) : 0;
		if ($idq>0){
			$post = new bXPost($idq);

            $user = new RMUser( $post->uid );

			if ($post->isNew()) break;
			$quote = "[quote author=".str_replace(" ", "+", ($user->name != '' ? $user->name : $user->uname))."]".$post->getVar('post_text','n')."[/quote]\n\n";
		}
                
                $type = $rmc_config['editor_type'];
                
                // Verificamos el tipo de editor
                if(!$xoopsModuleConfig['html']){
                    if($type=='tiny' || $type=='html'){
                        $type = 'simple';
                    }
                }
		
		$form->addElement(new RMFormEditor(__('Post','bxpress'), 'msg', 'auto', '400px', isset($quote) ? $quote : ''), true);
		
		// Adjuntar Archivos
		if ($forum->attachments() && $forum->isAllowed($xoopsUser ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS, 'attach')){
			$ele = new RMFormFile(__('Attach file','bxpress'), 'attach', 45, $xoopsModuleConfig['maxfilesize'] * 1024);
			$ele->setDescription(sprintf(__('Allowed file types: %s','bxpress'), implode(',', $forum->extensions())));
			$form->addElement($ele);
			$form->setExtra('enctype="multipart/form-data"');
		}
		
		$form->addElement(new RMFormHidden('op','post'));
		$form->addElement(new RMFormHidden('pid',$pid));
		$form->addElement(new RMFormHidden($fid>0 ? 'fid' : 'tid', $fid>0 ? $fid : $tid));
		$ele = new RMFormButtonGroup();
		$ele->addButton('sbt', __('Send','bxpress'), 'submit');
		$ele->addButton('cancel', __('Cancel','bxpress'), 'button', 'onclick="history.go(-1)";');
		$form->addElement($ele);

		$tpl->assign('topic_form', $form->render());
		
		/**
		* @desc Cargamos los mensajes realizados en este tema
		*/
		if ($mc['numpost']>0 && !$create){
			$sql = "SELECT * FROM ".$db->prefix("mod_bxpress_posts")." WHERE id_topic='".$topic->id()."' ORDER BY post_time DESC LIMIT 0, $mc[numpost]";
			$result = $db->query($sql);
			while ($row = $db->fetchArray($result)){
				$post = new bXPost();
				$post->assignVars($row);
				$tpl->append('posts', array('id'=>$post->id(), 'text'=>$post->text(),
						'time'=>date($xoopsConfig['datestring'], $post->date()),'uname'=>$post->uname()));
			}
		}
		
		$tpl->assign('lang_topicreview', __('Topic review (newest first)','bxpress'));

		include 'footer.php';
		
		break;
}

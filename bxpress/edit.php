<?php
// $Id: edit.php 1034 2012-09-06 02:30:13Z i.bitcero $
// --------------------------------------------------------------
// bXpress Forums
// An simple forums module for XOOPS and Common Utilities
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

define('BB_LOCATION','posts');
include '../../mainfile.php';

$op = RMHttpRequest::request( 'op', 'string', '');
$id = RMHttpRequest::request( 'id', 'integer', 0);

if ($id<=0){
	redirect_header('./', 2, __('No post has been specified!','bxpress'));
	die();
}

$post = new bXPost($id);
if ($post->isNew()){
	redirect_header('./', 2, __('Specified post does not exists!','bxpress'));
	die();
}

$topic = new bXTopic($post->topic());
$forum = new bXForum($topic->forum());

// Verificamos si el usuario tiene permisos de edición en el foro
if (!$xoopsUser || !$forum->isAllowed($xoopsUser->getGroups(), 'edit')){
	redirect_header('topic.php?pid='.$id.'#p'.$id, 2, __('You don\'t have permission to edit this post!','bxpress'));
	die();
}

// Verificamos si el usuario tiene permiso de edición para el post
if ($xoopsUser->uid()!=$post->user() && (!$xoopsUser->isAdmin() && !$forum->isModerator($xoopsUser->uid()))){
	redirect_header('topic.php?pid='.$id.'#p'.$id, 2, __('You don\'t have permission to edit this post!','bxpress'));
	die();
}


switch($op){
	case 'post':
		
		foreach ($_POST as $k => $v){
			$$k = $v;
		}
		
		if (!$xoopsSecurity->check()){
			redirect_header('edit.php?id='.$id, 2, __('Session token expired!','bxpress'));
			die();
		}

		if(!isset($msg) || $msg=='')
			redirect_header('edit.php?id='.$id, 2, __('You must provide a message text!','bxpress'));
		
		$myts =& MyTextSanitizer::getInstance();
		
		if (bXFunctions::getFirstId($topic->id())==$id){
			$topic->setDate(time());
			$topic->setTitle($myts->addSlashes($subject));
			if ($xoopsUser && isset($sticky) && $xoopsModuleConfig['sticky']){
				if ($xoopsUser->isAdmin() || $forum->isModerator($xoopsUser->uid()) || ($xoopsUser->posts()>$xoopsModuleConfig['sticky_posts'] && $xoopsUser->uid()==$topic->poster())){
					$topic->setSticky($sticky);
				}
			}
		}
		
		$post->setPid(0);
		$post->setIP($_SERVER['REMOTE_ADDR']);
		$post->setIcon('');
		$post->setSignature(isset($sig) ? 1 : 0);
		if ($forum->isAllowed($xoopsUser ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS, 'approve') || $xoopsUser->isAdmin() || $forum->isModerator()){
			$post->setText($msg);
		} else {
			$post->setEditText($msg);
			bXFunctions::notifyAdmin($forum->moderators(),$forum, $topic, $post,1);
		}
		
		if (!$post->save() || !$topic->save()){
			redirect_header('edit.php?id='.$id, 2, __('Changes could not be stored. Please try again!','bxpress'));
			die();
		}
		
		redirect_header('topic.php?pid='.$post->id().'#p'.$post->id(), 1, __('Changes stored successfully!','bxpress'));
			
		break;
	
	case 'delete':
		
		/**
		* Eliminamos archivos siempre y cuando el usuario se al propietario
		* del mensaje, sea administrador o moderador
		*/
		if (!$xoopsSecurity->check()){
			redirect_header('edit.php?id='.$post->id().'#attachments', 2, __('Session token expired!','bxpress'));
			die();
		}
		
		$files = rmc_server_var($_POST, 'files', array()); 
		
		if (empty($files)){
			redirect_header('edit.php?id='.$post->id().'#attachments', 2, __('You have not selected any file to delete!','bxpress'));
			die();
		}
		$errors = '';
		foreach ($files as $k){
			$file = new bXAttachment($k);
			if (!$file->delete()) $errors .= $file->errors()."<br />";
		}
		
		redirect_header('edit.php?id='.$post->id().'#attachments', 1, $errors != '' ? __('Errors ocurred during this operation!','bxpress')."<br />".$errors : __('Files deleted successfully!','bxpress'));
		
		break;
	
	case 'upload':
		
		/**
		* Almacenamos archivos siempre y cuando el usuario
		* actual tenga permisos y no haya rebasado el límite de archivos
		* adjuntos por mensaje
		*/
		
		if (!$xoopsSecurity->check()){
			redirect_header('edit.php?id='.$post->id().'#attachments', 2, __('Session token expired!','bxpress'));
			die();
		}
		
		if ($forum->attachments() && $forum->isAllowed($xoopsUser->getGroups(), 'attach')){
			
			// Comprobamos si no ha alcanzado su número limite de envios
			if ($post->totalAttachments()>= $xoopsModuleConfig['attachlimit']){
				redirect_header('edit.php?id='.$post->id().'#attachments', 2, __('You have reached the maximum attachments number for this post','bxpress'));
				die();
			}
			
                        include_once RMCPATH.'/class/uploader.php';
			$folder = $xoopsModuleConfig['attachdir'];
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
					if (!$attach->save()){
						redirect_header('edit.php?id='.$post->id().'#attachments', 2, __('The file was not saved.','bxpress')."<br />".$up->getErrors());
						die();
					}
				}
				
			}
			
		} else {
			redirect_header('edit.php?id='.$post->id().'#attachments', 2, __('Sorry, you do not have permission to do this action','bxpress'));
		}
		
		redirect_header('edit.php?id='.$post->id().'#attachments', 1, __('File attached successfully!','bxpress').$errors);
		
		break;
	
	default:
		
		$xoopsOption['template_main'] = "bxpress-postform.tpl";
		$xoopsOption['module_subpage'] = "edit";
		include 'header.php';

		bXFunctions::makeHeader();
				
		$form = new RMForm(__('Edit Topic','bxpress'), 'frmTopic', 'edit.php');
		$first_id = bXFunctions::getFirstId($topic->id());
		if ($id==$first_id){
			$form->addElement(new RMFormText(__('Topic Subject:','bxpress'), 'subject', 50, 255, $topic->title()), true);
			// Sticky
			if ($xoopsUser && $xoopsModuleConfig['sticky']){
				
				$sticky = $xoopsUser->isAdmin() || $forum->isModerator($xoopsUser->uid()) || ($xoopsUser->posts()>$xoopsModuleConfig['sticky_posts'] && $topic->poster()==$xoopsUser->uid());
				if ($sticky){
					$form->addElement(new RMFormYesNo(__('Sticky Topic','bxpress'), 'sticky', $topic->sticky()));
				}
				
			}
		}
		
		// Si se especifico una acotación entonces la cargamos
		$idq = isset($_GET['quote']) ? intval($_GET['quote']) : 0;
		if ($idq>0){
			$post = new bXPost($idq);
			if ($post->isNew()) break;
			$quote = "[quote=".$post->uname()."]".$post->getVar('post_text','e')."[/quote]\n\n";
		}
		
		$form->addElement(new RMFormEditor(__('Post','bxpress'), 'msg', '90%', '300px', $rmc_config['editor_type']=='tiny' ? $post->getVar('post_text', 'e') : $post->getVar('post_text','e')), true);
		
		$form->addElement(new RMFormHidden('op','post'));
		$form->addElement(new RMFormHidden('id', $id));
		$ele = new RMFormButtonGroup();
		$ele->addButton('sbt', __('Save Changes','bxpress'), 'submit');
		$ele->addButton('cancel', _CANCEL, 'button', 'onclick="window.location = \'topic.php?pid='.$post->id().'#p'.$post->id().'\'";');
		$form->addElement($ele);
		
		// Adjuntar Archivos
		if ($forum->attachments() && $forum->isAllowed($xoopsUser ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS, 'attach')){
			$forma = new RMForm('<a name="attachments"></a>'.__('Attached Files','bxpress'), 'frmAttach', 'edit.php');
			$forma->addElement(new RMFormSubTitle(sprintf(__('You can upload new files to this post. You have a limit of <strong>%s</strong> attachment per post.','bxpress'), $xoopsModuleConfig['attachlimit']), 1, 'even'));
			if ($post->totalAttachments()<$xoopsModuleConfig['attachlimit']){
				$ele = new RMFormFile(__('Attach File:','bxpress'), 'attach', 45, $xoopsModuleConfig['maxfilesize'] * 1024);
				$ele->setDescription(sprintf(__('Allowed File Types: %s','bxpress'), implode(',', $forum->extensions())));
				$forma->addElement($ele, true);
				$forma->setExtra('enctype="multipart/form-data"');
			}
			// Lista de Archivos Adjuntos
			$list = new RMFormCheck(__('Cuerrent Attachments','bxpress'));
			$list->asTable(1);
			foreach ($post->attachments() as $file){
				$list->addOption("<img src='".$file->getIcon()."' align='absmiddle' /> ".$file->name()." (".RMUtilities::formatBytesSize($file->size()).")", 'files[]', $file->id());
			}
			$forma->addElement($list);
			$ele = new RMFormButtonGroup();
			if ($post->totalAttachments()<$xoopsModuleConfig['attachlimit']) $ele->addButton('upload', __('Upload File','bxpress'), 'submit');
			$ele->addButton('delete', __('Delete File(s)','bxpress'), 'button', 'onclick="document.forms[\'frmAttach\'].op.value=\'delete\'; submit();"');
			$ele->addButton('cancel', __('Cancel','bxpress'), 'button', 'onclick="window.location = \'topic.php?pid='.$post->id().'#p'.$post->id().'\'";');
			$forma->addElement($ele);
			$forma->addElement(new RMFormHidden('op', 'upload'));
			$forma->addElement(new RMFormHidden('id', $id));
		}

		$tpl->assign('topic_form', $form->render()."<br />".$forma->render());
		
		$tpl->assign('lang_topicreview', __('Topic Review (Newest First)','bxpress'));

		include 'footer.php';
		
		break;
}

<?php
// $Id: announcements.php 897 2012-01-02 21:38:00Z i.bitcero $
// --------------------------------------------------------------
// bXpress Forums
// An simple forums module for XOOPS and Common Utilities
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

define('RMCLOCATION','messages');
include 'header.php';

$db = XoopsDatabaseFactory::getDatabaseConnection();
$db->queryF("DELETE FROM ".$db->prefix("mod_bxpress_announcements")." WHERE expire<='".time()."'");

/**
* @desc Muestra la lista de los anuncios existentes
*/
function showAnnounces(){
    global $db, $xoopsModule, $xoopsSecurity;
	
    $result = $db->query("SELECT * FROM ".$db->prefix("mod_bxpress_announcements")." ORDER BY date");
    $announcements = array();

    while ($row = $db->fetchArray($result)){
        $an = new bXAnnouncement();
	$an->assignVars($row);
	$announcements[] = array(
            'id'=>$an->id(),
            'text'=>TextCleaner::getInstance()->truncate($an->text(), 100),
            'date'=>formatTimestamp($an->date()),
            'expire'=>formatTimeStamp($an->expire()),
            'where'=>constant('BX_FWHERE'.$an->where()),
            'wherelink'=>$an->where()==1 ? '../forum.php?id='.$an->forum() : '../',
            'by'=>$an->byName()
        );
    }
    
    $announcements = RMEvents::get()->run_event('bxpress.announcements.list', $announcements);
	
    RMTemplate::get()->add_help(array(
        'caption'   => __('Announcements Help', 'bxpress' ),
        'link'      => 'http://www.redmexico.com.mx/docs/bxpress-forums/anuncios/standalone/1/'
    ));

    $bc = RMBreadCrumb::get();
    $bc->add_crumb( __('Announcements Management','bxpress') );

    xoops_cp_header();

    RMTemplate::get()->add_local_script('jquery.checkboxes.js','rmcommon','include');
    RMTemplate::get()->add_style('admin.css', 'bxpress');
    RMTemplate::get()->add_local_script('admin.js','bxpress');
    include RMTemplate::get()->get_template("admin/forums-announcements.php", 'module', 'bxpress');

	xoops_cp_footer();
	
}

/**
* @desc Presenta el formulario para creación o edición de un anuncio
*/
function showForm($edit = 0){
	global $tpl, $xoopsModule, $db;
	
	if ($edit){
		$id = rmc_server_var($_GET, 'id', 0);
		if ($id<=0){
			redirectMsg('announcements.php', __('Provided ID is not valid!','bxpress'), 1);
			die();
		}
		
		$an = new bXAnnouncement($id);
		if ($an->isNew()){
			redirectMsg('announcements.php', __('Specified announcement does not exists!','bxpress'), 1);
			die();
		}
	}
	
    RMTemplate::get()->set_help('http://www.redmexico.com.mx/docs/bxpress-forums/anuncios/standalone/1/#crear-un-anuncio');

    $bc = RMBreadCrumb::get();
    $bc->add_crumb( __('Announcements', 'bxpress'), 'announcements.php' );
    $bc->add_crumb( $edit ? __('Edit Announcement','bxpress') : __('New Announcement','bxpress') );

	xoops_cp_header();
	
	$form = new RMForm($edit ? __('Edit Announcement','bxpress') : __('New Announcement','bxpress'), 'frmAnnouncements', 'announcements.php');
	$form->oddClass('oddForm');
	$form->addElement(new RMFormEditor(__('Text','bxpress'), 'text', '100%','300px', $edit ? $an->text('e') : ''), true);
	
	// Caducidad
	$ele = new RMFormDate(__('Expire on','bxpress'), 'expire', $edit ? $an->expire() : time());
	$form->addElement($ele);
	// Mostran en
	$ele = new RMFormRadio(__('Show on','bxpress'), 'where', 1, 0);
	$ele->addOption(__('Module home page','bxpress'), 0, $edit ? $an->where()==0 : 1);
	$ele->addOption(__('Forum','bxpress'), 1, $edit ? $an->where()==1 : 0);
	$ele->addOption(__('All module','bxpress'), 2, $edit ? $an->where()==2 : 0);
	$form->addElement($ele);
	
	// Foros
	$ele = new RMFormSelect(__('Forum','bxpress'), 'forum',0,$edit ? array($an->forum()) : array());
	$ele->setDescription(__('Please select the forum where this announcement will be shown. This option only is valid when "In Forum" has been selected.','bxpress'));
	$tbl1 = $db->prefix("mod_bxpress_categories");
	$tbl2 = $db->prefix("mod_bxpress_forums");
	$sql = "SELECT b.*, a.title FROM $tbl1 a, $tbl2 b WHERE b.cat=a.id_cat AND b.active='1' ORDER BY a.order, b.order";
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
	$form->addElement($ele);
	
	$ele = new RMFormButtonGroup();
	$ele->addButton('sbt', $edit ? __('Save Changes','bxpress') : __('Create Announcement','bxpress'), 'submit');
	$ele->addButton('cancel', __('Cancel','bxpress'), 'button', 'onclick="window.location=\'announcements.php\';"');
	$form->addElement($ele);
	$form->addElement(new RMFormHidden('action',$edit ? 'saveedit' : 'save'));
	if ($edit) $form->addElement(new RMFormHidden('id',$id));
	
        $form = RMEvents::get()->run_event('bxpress.form.announcement',$form);
        
	$form->display();
	xoops_cp_footer();

}

/**
* @desc Almacena los datos de un anuncio
*/
function saveAnnouncement($edit = 0){
	global $xoopsUser, $xoopsSecurity;

    $q = 'action='.($edit?'edit':'new');
	foreach ($_POST as $k => $v){
		$$k = $v;
        if($k=='XOOPS_TOKEN_REQUEST' || $k=='action') continue;
        $q .= '&'.$k.'='.$v;
	}
	
	if (!$xoopsSecurity->check()){
		redirectMsg('announcements.php?'.$q, __('Session token expired!','bxpress'), 1);
		die();
	}
	
	if ($edit){
		$id = rmc_server_var($_POST, 'id', 0);
		if ($id<=0){
			redirectMsg('announcements.php', __('Provided ID is not valid!','bxpress'), 1);
			die();
		}
		
		$an = new bXAnnouncement($id);
		if ($an->isNew()){
			redirectMsg('announcements.php', __('Specified announcement does no exists!','bxpress'), 1);
			die();
		}
	} else {
		$an = new bXAnnouncement();
	}
	
	if ($expire<=time()){
		redirectMsg('announcements.php?'.$q, __('The expiration time can not be minor than current time!','bxpress'), 1);
		die();
	}
	
	$an->setBy($xoopsUser->uid());
	$an->setByName($xoopsUser->uname());
	if (!$edit) $an->setDate(time());
	$an->setExpire($expire);
	$an->setForum($forum);
	$an->setText($text);
	$an->setWhere($where);
        
        $an = RMEvents::get()->run_event("bxpress.before.save.announcement",$an);
	
	if ($an->save()){
                $an = RMEvents::get()->run_event("bxpress.announcement.saved",$an);
		redirectMsg('announcements.php', __('Announcement saved successfully!','bxpress'), 0);
	} else {
                $an = RMEvents::get()->run_event("bxpress.announcement.save.error",$an);
		redirectMsg('announcements.php?'.$q, __('Announcement could not be saved!','bxpress') . '<br />' . $an->errors(), 1);
	}
		
}

/**
* @desc Elimina anuncios de la base de datos
*/
function deleteAnnouncements(){
	global $xoopsSecurity, $db;
	
	if (!$xoopsSecurity->check()){
		redirectMsg('announcements.php', __('Session token expired!','bxpress'), 1);
		die();
	}
	
	$an = rmc_server_var($_POST, 'ids', array());
	
	if (!is_array($an) || empty($an)){
		redirectMsg('announcements.php', __('You must select at least one announcement to delete!','bxpress'), 1);
		die();
	}
	
	$sql = "DELETE FROM ".$db->prefix("mod_bxpress_announcements")." WHERE id_an IN (".implode(',',$an).")";
	
        RMEvents::get()->run_event("bxpress.delete.announcement", $an, $sql);
        
	if ($db->queryF($sql)){
		redirectMsg('announcements.php', __('Announcements deleted successfully!','bxpress'), 0);
	} else {
		redirectMsg('announcements.php', __('Errors ocurred while trying to delete announcements.','bxpress') . '<br />' . $db->error(), 0);
	}
	
}


$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

switch($action){
	case 'new':
		showForm();
		break;
	case 'edit':
		showForm(1);
		break;
	case 'save':
		saveAnnouncement();
		break;
	case 'saveedit':
		saveAnnouncement(1);
		break;
	case 'delete':
		deleteAnnouncements();
		break;
	default:
		showAnnounces();
		break;
}

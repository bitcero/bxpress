<?php
// $Id: categos.php 948 2012-04-14 04:16:33Z i.bitcero $
// --------------------------------------------------------------
// bXpress Forums
// An simple forums module for XOOPS and Common Utilities
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

define('RMCLOCATION', 'categories');
include 'header.php';

/**
* @desc Muestra la lista de categorías existentes
*/
function showCategories(){
    global $xoopsModuleConfig, $xoopsConfig, $xoopsModule, $xoopsSecurity;
    
    $db = XoopsDatabaseFactory::getDatabaseConnection();
    
    $result = $db->query("SELECT * FROM ".$db->prefix("mod_bxpress_categories")." ORDER BY `order`, title");
    $categos = array();
    
    while ($row = $db->fetchArray($result)){
        $catego = new bXCategory();
        $catego->assignVars($row);
        $categos[] = array(
            'id'=>$catego->id(),
            'title'=>$catego->title(),
            'desc'=>$catego->description(),
            'status'=>$catego->status()
        );
    }
    
    $form = new RMForm('','','');
    $groups = new RMFormGroups('','groups', 1, 1, 2, array(0));
    
    $bc = RMBreadCrumb::get();
    $bc->add_crumb( __('Categories', 'bxpress') );

    xoops_cp_header();

    RMTemplate::get()->add_script('admin.js','bxpress');
    RMTemplate::get()->add_help(__('Categories', 'bxpress'), 'http://www.redmexico.com.mx/docs/bxpress-forums/categorias/standalone/1/');

    RMTemplate::get()->add_head_script('var bx_select_message = "'.__('You must select a category at least in order to run this action!','bxpress').'";
        var bx_message = "'.__('Do you really want to delete selected categories?\n\nAll forums under this category will be deleted also!','bxpress').'";');

    include RMTemplate::get()->get_template('admin/forums_categos.php', 'module', 'bxpress');
    
    xoops_cp_footer();
    
}

/**
* @desc Muestra el formulario para edición/creación de categorías
*/
function showForm($edit = 0){
    global $xoopsModule, $xoopsConfig, $xoopsModuleConfig;
    
    define('RMSUBLOCATION','newcategory');
    
    if ($edit){
        $id = RMHttpRequest::get( 'id', 'integer', 0 );
        if ($id<=0){
            RMUris::redirect_with_message( __('You had not provided a category ID','bxpress'), 'categos.php', RMMSG_WARN );
            die();
        }
        
        $catego = new bXCategory($id);
        if ($catego->isNew()){
            RMUris::redirect_with_message( __('Specified category does not exists!','bxpress'), 'categos.php', RMMSG_ERROR );
            die();
        }
    }

    $bc = RMBreadCrumb::get();
    $bc->add_crumb( __('Categories', 'bxpress'), 'categos.php' );
    $bc->add_crumb( __('Edit category', 'bxpress') );
    xoops_cp_header();
    
    $form = new RMForm($edit ? __('Edit Category','bxpress') : __('New Category','bxpress'), 'frmCat', 'categos.php');
    $form->addElement(new RMFormText(__('Name','bxpress'), 'title', 50, 100, $edit ? $catego->title() : ''), true);
    if ($edit){
        $form->addElement(new RMFormText(__('Short name','bxpress'), 'friendname', 50, 100, $catego->friendName()))   ;
    }
    $form->addElement(new RMFormEditor(__('Description','bxpress'), 'desc', '90%', '300px', $edit ? $catego->description() : ''));
    $form->addElement(new RMFormYesNo(__('Show description','bxpress'), 'showdesc', $edit ? $catego->showDesc() : 1));
    $form->addElement(new RMFormYesNo(__('Activate','bxpress'), 'status', $edit ? $catego->status() : 1));
    $form->addElement(new RMFormGroups(__('Groups','bxpress'), 'groups', 1, 1, 4, $edit ? $catego->groups() : array(0)), true, 'checked');
    $form->addElement(new RMFormHidden('action', $edit ? 'saveedit' : 'save'));
    if ($edit) $form->addElement(new RMFormHidden('id', $catego->id()));
    $buttons = new RMFormButtonGroup();
    $buttons->addButton('sbt', __('Submit','bxpress'), 'submit', '', true);
    $buttons->addButton('cancel', __('Cancel','bxpress'), 'button', 'onclick="window.location=\'categos.php\';"');
    $form->addElement($buttons);
    
    $form->display();
    
    xoops_cp_footer();
}

/**
* @desc Almacena los datos de una categoría
*/
function saveCatego($edit = 0){
    global $xoopsConfig, $xoopsModuleConfig, $xoopsSecurity;
    
    $db = XoopsDatabaseFactory::getDatabaseConnection();
    
    $friendname = '';
    $showdesc = 0;
    $status = 0;
    $q = ''; //Query string
    foreach ($_POST as $k => $v){
        $$k = $v;
        if($k=='XOOPS_TOKEN_REQUEST' || $k=='action') continue;
        $q = ($q==''?'':'&').$k.'='.urlencode($v);
    }

    if (!$xoopsSecurity->check()){
        redirectMsg('categos.php', __('Session token expired!','bxpress'), 1);
        die();
    }
    
    if($title==''){
        redirectMsg('categos.php?'.$q, __('Please provide a name for this category!','bxpress'), RMMSG_ERROR);
        die();
    }
    
    if ($edit){
        
        if ($id<=0){
            redirectMsg('categos.php', __('The specified category ID is not valid!','bxpress'), 1);
            die();
        }
        
        $catego = new bXCategory($id);
        if ($catego->isNew()){
            redirectMsg('categos.php', __('Specified category does not exists!','bxpress'), 1);
            die();
        }
        
        // Comprobamos que no exista el nombre
        list($num) = $db->fetchRow($db->query("SELECT COUNT(*) FROM ".$db->prefix("mod_bxpress_categories")." WHERE title='$title' AND id_cat<>'$id'"));
        if ($num>0){
            redirectMsg('categos.php?'.$q, __('Already exists a category with same name!','bxpress'), 1);
            die();
        }
        
    } else {
        $catego = new bXCategory();
    }
    
    // Asignamos valores
    $catego->setTitle($title);
    $friendname = $friendname!='' ? TextCleaner::getInstance()->sweetstring($friendname) : TextCleaner::getInstance()->sweetstring($title);
    
    // Comprobamos que el nombre no este asignada a otra categoría
    list($num) = $db->fetchRow($db->query("SELECT COUNT(*) FROM ".$db->prefix("mod_bxpress_categories")." WHERE friendname='$friendname' AND id_cat<>'$id'"));
    if ($num>0){
        redirectMsg('categos.php?op=edit&id='.$id, __('Already exist a category with the same short name!','bxpress'), 1);
        die();
    }
    
    $catego->setDescription($desc);
    $catego->setFriendName($friendname);
    $catego->setGroups(!isset($groups) || is_array($groups) ? array(0) : $groups);
    $catego->setOrder($order<=0 ? 0 : intval($order));
    $catego->setShowDesc($showdesc);
    $catego->setStatus($status);
    
    if ($catego->save()){
        redirectMsg('categos.php', __('Category saved succesfully!','bxpress'), 0);
    } else {
        redirectMsg('categos.php', __('Category could not be saved!','bxpress') . '<br />' . $catego->errors(), 1);
    }
    
}


/**
* @desc Eliminina categorías
**/
function deleteCatego(){
	global $xoopsModule, $xoopsSecurity;	
	
	$ids = rmc_server_var($_POST, 'ids', array());
	
	//Verificamos si se ha proporcionado una categoría
	if (empty($ids)){
		redirectMsg('./categos.php',__('You must select at least one category','bxpress'),1);
		die();		
	}	

	if (!$xoopsSecurity->check()){
	    redirectMsg('categos.php', __('Session token expired!','bxpress'), 1);
		die();
	}

	$errors='';
	foreach ($ids as $k){
	    //Verificamos que la categoría sea válida
		if ($k<=0){
		    $errors.=sprintf(__('Category ID %s is not valid!','bxpress'), '<strong>'.$k.'</strong>').'<br />';
			continue;
		}	
	
	    //Verificamos que categoría exista
		$cat=new bXCategory($k);
		if ($cat->isNew()){
		    $errors.=sprintf(__('Category with id %s does not exists!','bxpress'), '<strong>'.$k.'</strong>').'<br />';
			continue;
		}
	
	    if (!$cat->delete()){
		    $errors.=sprintf(__('Category %s could not be deleted!','bxpress'),'<strong>'.$k.'</strong>').'<br />';
		}
	}
	
	if ($errors!=''){
	    redirectMsg('./categos.php',__('There was errors during this operation','bxpress').'<br />'.$errors,1);
		die();
	}else{
	    redirectMsg('./categos.php',__('Categories deleted successfully!','bxpress'),0);
		die();
	}

}

/**
* @desc Activa o desactiva una categoría
**/
function activeCatego($act=0){
	global $xoopsSecurity;

	$cats = rmc_server_var($_REQUEST,'ids', array());

	//Verificamos si se ha proporcionado una categoría
	if (empty($cats)){
		redirectMsg('./categos.php', __('You must select at least one category','bxpress'),1);
		die();		
	}

	if (!$xoopsSecurity->check()){
	        redirectMsg('categos.php', __('Session token expired!','bxpress'), 1);
	        die();
	}

	$errors='';
	foreach ($cats as $k){
	
		
		//Verificamos que la categoría sea válida
		if ($k<=0){
			$errors.=sprintf(__('Category ID %s is not valid!','bxpress'), '<strong>'.$k.'</strong>').'<br />';
			continue;
		}	
		//Verificamos que categoría exista
		$cat=new bXCategory($k);
		if ($cat->isNew()){
			$errors.=sprintf(_AS_BB_ERRCATNOEXIST,$k);
			continue;
		}
	
		$cat->setStatus($act);
		if (!$cat->save()){
			$errors.=sprintf(_AS_BB_ERRCATNOSAVE,$k);
		}
	}
	
	if ($errors!=''){
		redirectMsg('./categos.php',_AS_BB_ERRACTION.$errors,1);
		die();
	}else{
		redirectMsg('./categos.php',_AS_BB_DBOK,0);
		die();
	}
	

}


/**
* @desc Almacena los camobios realizados en el orden de las categorías
**/
function updateOrderCatego(){
	global $util;

	$orders=isset($_POST['orders']) ? $_POST['orders'] : array();

	if (!$util->validateToken()){
		        redirectMsg('categos.php', _AS_BB_ERRTOKEN, 1);
		        die();
		}
	

	foreach ($orders as $k=>$v){

				
		//Verificamos que la categoría sea válida
		if ($k<=0){
			$errors.=sprintf(_AS_BB_ERRCATNOVALID,$k);
			continue;
		}	
		//Verificamos que categoría exista
		$cat=new bXCategory($k);
		if ($cat->isNew()){
			$errors.=sprintf(_AS_BB_ERRCATNOEXIST,$k);
			continue;
		}

		//Actualizamos el orden
		$cat->setOrder($v);
		if (!$cat->save()){
			$errors.=sprintf(_AS_BB_ERRCATNOSAVE,$k);
		}
	}
	
	if ($errors!=''){
		redirectMsg('./categos.php',_AS_BB_ERRACTION.$errors,1);
		die();
	}else{
		redirectMsg('./categos.php',_AS_BB_DBOK,0);
		die();
	}




}


$action = rmc_server_var($_REQUEST, 'action', '');

switch($action){
    case 'new':
        showForm();
        break;
    case 'save':
        saveCatego();
        break;
    case 'edit':
        showForm(1);
        break;
    case 'saveedit':
        saveCatego(1);
        break;
    case 'savechanges':
	    updateOrderCatego();
        break;
    case 'delete':
	    deleteCatego();
        break;
    case 'enable':
	    activeCatego(1);
        break;
    case 'disable':
	    activeCatego();
        break;
    default:
        showCategories();
        break;
}

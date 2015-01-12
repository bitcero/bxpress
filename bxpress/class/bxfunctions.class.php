<?php
// $Id: bxfunctions.class.php 1034 2012-09-06 02:30:13Z i.bitcero $
// --------------------------------------------------------------
// bXpress Forums
// An simple forums module for XOOPS and Common Utilities
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

/**
* @desc Clase para el manejo de funciones internas del foro
*/
class bXFunctions
{
    private $db;
    
    public function __construct(){
        $this->db = XoopsDatabaseFactory::getDatabaseConnection();
    }

    public function menu_bar(){
        RMTemplate::get()->add_tool(__('Dashboard','exmbb'), './index.php', '../images/dash.png', 'dashboard');
        RMTemplate::get()->add_tool(__('Categories','exmbb'), './categos.php', '../images/categos.png', 'categories');
        RMTemplate::get()->add_tool(__('Forums','exmbb'), './forums.php', '../images/forums.png', 'forums');
        RMTemplate::get()->add_tool(__('Announcements','exmbb'), './announcements.php', '../images/bell.png', 'messages');
        RMTemplate::get()->add_tool(__('Reports','exmbb'), './reports.php', '../images/reports.png', 'reports');
        RMTemplate::get()->add_tool(__('Prune','exmbb'), './prune.php', '../images/prune.png', 'prune');
    }
    
    public function get(){
        static $instance;
        if (!isset($instance)) {
            $instance = new bXFunctions();
        }
        return $instance;
    }
    /**
    * @desc Obtiene el último usuario registrado
    * @return objeto {@link XoopsUser}
    */
    function getLastUser(){
        $db = XoopsDatabaseFactory::getDatabaseConnection();
        $result = $db->query("SELECT * FROM ".$db->prefix("users")." WHERE level>'0' ORDER BY uid DESC LIMIT 0,1");
        if ($db->getRowsNum($result)>0){
            $row = $db->fetchArray($result);
            $user = new XoopsUser();
            $user->assignVars($row);
            return $user;
        }
        return false;
    }
    /**
    * @desc Obtiene el número de usuarios conectados
    * @param int $type Determina el tipo de usuario que devolvera:
    *         0 Devuelve usuarios anonimos
    *         1 Devuelve Usuarios registrados
    *         2 Devuelve todos los usuarios conectados
    * @return int
    */
    public function getOnlineCount($type = 1){
        global $xoopsModule;
        
        $db = XoopsDatabaseFactory::getDatabaseConnection();
        $sql = "SELECT COUNT(*) FROM ".$db->prefix("online")." WHERE online_module='".$xoopsModule->mid()."'";
        
        if ($type==0){
            $sql .= " AND online_uid<'1'";
        }elseif($type==1){
            $sql .= " AND online_uid>'0'";
        }
        
        list($num) = $db->fetchRow($db->query($sql));
        return $num;
        
    }
    /**
    * @desc Total de Usuarios Registrados
    * @return int
    */
    public function totalUsers(){
        $db = XoopsDatabaseFactory::getDatabaseConnection();
        list($num) = $db->fetchRow($db->query("SELECT COUNT(*) FROM ".$db->prefix("users")." WHERE level>'0'"));
        return $num;
    }
    /**
    * @desc Total de Temas en los Foros
    */
    public function totalTopics(){
        $db = XoopsDatabaseFactory::getDatabaseConnection();
        list($num) = $db->fetchRow($db->query("SELECT COUNT(*) FROM ".$db->prefix("mod_bxpress_topics")));
        return $num;
    }
    /**
    * @desc Total de Mensajes en los Foros
    */
    public function totalPosts(){
        $db = XoopsDatabaseFactory::getDatabaseConnection();
        list($num) = $db->fetchRow($db->query("SELECT COUNT(*) FROM ".$db->prefix("mod_bxpress_posts")));
        return $num;
    }
    /**
    * @desc Formatea la fecha
    */
    public function formatDate($time){
    	global $mc;
    	
    	$real = time() - $time;
    
    	$today = date('G',time()) * 3600;
    	$today += (date('i', time()) * 60);
    	$today += date('s', time());
    	
    	if ($real<=$today){
    		return sprintf(__('Today %s','bxpress'), date('H:i:s', $time));
    	}elseif ($real<=($today-86400)){
    		return sprintf(__('Yesterday %s','bxpress'), date('H:i:s', $time));
	}else{
		return formatTimeStamp($time);
	}
    	
    }
    /**
    * @desc Creamos el encabezado del módulo
    */
    public function makeHeader(){
    	global $xoopsTpl, $xoopsModuleConfig, $xoopsUser;
    	
        $tpl = $xoopsTpl;
    	$tpl->assign('lang_index', __('Forum Index','bxpress'));
    	$tpl->assign('forums_title', $xoopsModuleConfig['forum_title']);
    	if ($xoopsUser || $xoopsModuleConfig['search']){
    		$tpl->assign('lang_search',__('Search','bxpress'));
    		$tpl->assign('lang_searchq', __('Search:','bxpress'));
    		$tpl->assign('can_search',1);
    	}
    	
	}
	/**
	* @desc Crea las páginas para el indice de temas
	*/
	public function paginateIndex($tpages, $limit=3){
		
		$ret = array();
		
		for ($i=1;$i<=$tpages;$i++){
			$ret[] = $i;
			if ($i==$limit && $tpages>$limit){
				$i = $tpages-1;
				$ret[] = '...';
			}
		}
		
		return $ret;
		
	}
	/**
	* @desc Determina la página del tema dependiendo del id de un post
	*/
	public function pageFromPID($pid){
		global $xoopsModuleConfig;
		
		$db = XoopsDatabaseFactory::getDatabaseConnection();
		
		$result = $db->query('SELECT id_topic FROM '.$db->prefix('mod_bxpress_posts')." WHERE id_post='$pid'");
		if (!$db->getRowsNum($result)) return;

		list($id) = $db->fetchRow($result);

		// Determine on what page the post is located (depending on $pun_user['disp_posts'])
		$result = $db->query("SELECT id_post FROM ".$db->prefix('mod_bxpress_posts')." WHERE id_topic='$id' ORDER BY post_time");
		$num = $db->getRowsNum($result);
		
		for ($i = 0; $i < $num; ++$i)
		{
			list($cur_id) = $db->fetchRow($result);
			if ($cur_id == $pid)
				break;
		}
		++$i;	// we started at 0
		$_GET['pag'] = ceil($i / $xoopsModuleConfig['perpage']);
		return $id;
	}
	/**
	* @desc Obtiene el id del primer mensaje de un tema
	* @param int Id del tema
	* @return int
	*/
	public function getFirstId($topic_id){
		
		$db = XoopsDatabaseFactory::getDatabaseConnection();
		$sql = "SELECT MIN(id_post) FROM ".$db->prefix("mod_bxpress_posts")." WHERE id_topic='".$topic_id."'";
		list($first_id) = $db->fetchRow($db->query($sql));
		return $first_id;
		
	}
	
	public function forumList($varname = 'forums', $assign=true){
		global $db, $tpl;
		$db = XoopsDatabaseFactory::getDatabaseConnection();
		$sql = "SELECT * FROM ".$db->prefix("mod_bxpress_forums")." WHERE active='1' ORDER BY cat,`order`";
		$result = $db->query($sql);
        
        $forums = array();
        
		while ($row = $db->fetchArray($result)){
			$forum = new bXForum();
			$forum->assignVars($row);
			$forums[] = array('id'=>$forum->id(),'title'=>$forum->name());
		}
        
        if($assign)
            $tpl->assign($varname, $forums);
        else
            return $forums;
        
	}
	
        /**
         * Load announcements form database
         * @param int Where to search (0 = home page, 1 = froum, 2 = all module)
         * @return array
         */
	public function loadAnnouncements($w, $forum=0){
		global $xoopsModuleConfig, $tpl;
		
		if (!$xoopsModuleConfig['announcements']) return;
                
                $db = XoopsDatabaseFactory::getDatabaseConnection();
		
		// Primero purgamos la tabla
		$db->queryF("DELETE FROM ".$db->prefix("mod_bxpress_announcements")." WHERE expire<='".time()."'");
		
		$mc =& $xoopsModuleConfig;
		$sql = "SELECT * FROM ".$db->prefix("mod_bxpress_announcements");
		
		switch ($w){
			case 0:
				$sql .= " WHERE `where`=0 OR `where`=2 ";
				break;
			case 1:
				$sql .= " WHERE `where`=2 OR (`where`='1' AND forum='$forum') ";
				break;
		}
                
		if ($mc['announcements_mode']){
			$sql .= " ORDER BY RAND() ";
		} else {
			$sql .= " ORDER BY `date` DESC ";
		}
                
		$sql .= "LIMIT 0, $mc[announcements_max]";
		$result = $db->query($sql);
                
		while ($row = $db->fetchArray($result)){
			$an = new bXAnnouncement();
			$an->assignVars($row);
			$tpl->append('announcements', array('text'=>$an->text('s')));
		}
		
		return true;
		
	}

	/**
	* @desc Notifica al grupo de administradores la creación de un nuevo tema no aprobado
	* @param {@link } Objetos de Foro, Tema y mensaje
	* @param int edit indica si es la edición de un mensaje o un nuevo tema no aprobado
	**/
	public function notifyAdmin($moderators,BBForum &$forum, BBTopic &$topic, BBPost &$post,$edit=0){
		global $db, $xoopsModule, $rmc_config;
	    
        $bxf = bXFunctions::get();
        
		$mhand = new XoopsMemberHandler($db);
		$configCat = new XoopsConfigCategory('mailer', 'mailer');
		$config =& $configCat->getConfigs(3);
    	
		$users = $moderators;
		
		if (!$edit){
			if (file_exists(XOOPS_ROOT_PATH.'/modules/bxpress/lang/'.RMCLANG.'/admin_notify.tpl')){
				$tpldir = XOOPS_ROOT_PATH.'/modules/bxpress/lang/'.RMCLANG;
			} else {
				$tpldir = XOOPS_ROOT_PATH.'/modules/bxpress/lang/en';
			}
		}else{
			if (file_exists(XOOPS_ROOT_PATH.'/modules/bxpress/lang/'.RMCLANG.'/admin_notify_post.tpl')){
				$tpldir = XOOPS_ROOT_PATH.'/modules/bxpress/lang/'.RMCLANG;
			} else {
				$tpldir = XOOPS_ROOT_PATH.'/modules/bxpress/lang/en';
			}
		}
	
		
		foreach ($users as $k){
			$xoopsMailer =& getMailer();
			$xoopsMailer->setFromEmail($config['from']);
			$xoopsMailer->setFromName($config['fromname']);
			$xoopsMailer->setTemplateDir($tpldir);
			if (!$edit){
				$xoopsMailer->setSubject(sprintf(__('New topic created','bxpress'), $forum->name()));
				$xoopsMailer->setTemplate('admin_notify.tpl');
			}else{
				$xoopsMailer->setSubject(sprintf(__('A unapproved message has been edited','dtransport'), $topic->title()));
				$xoopsMailer->setTemplate('admin_notify_post.tpl');
			}
		
			$xoopsMailer->assign('FORUM_NAME',$forum->name());
			$xoopsMailer->assign('FORUM_MODNAME', $xoopsModule->name());
			$xoopsMailer->assign('TOPIC_UNAME', $topic->posterName());
			$xoopsMailer->assign('TOPIC_NAME', $topic->title());
			$xoopsMailer->assign('TOPIC_APPROVED', $topic->approved() ? _YES : _NO);
			$xoopsMailer->assign('TOPIC_LINK', $bxf->url().'/moderate.php?id='.$forum->id());
			$xoopsMailer->assign('POST_UNAME',$post->uname());
			$xoopsMailer->assign('POST_LINK',$post->permalink());

			$user = new XoopsUser($k);
			$xoopsMailer->setToUsers($user);
			$xoopsMailer->isMail = $user->getVar('notify_method')==2;
			$xoopsMailer->isPM = $user->getVar('notify_method')==1;
			$xoopsMailer->send(true);
			$xoopsMailer->clearAddresses();
			
		}
			
		echo $xoopsMailer->getErrors();
		
	}
    
    public function getRanks(){
        
        $db =& XoopsDatabaseFactory::getDatabaseConnection();
        $myts =& MyTextSanitizer::getInstance();
        $sql = sprintf('SELECT rank_id, rank_title, rank_image FROM ' . $db->prefix('ranks') . ' WHERE rank_special = %u', 1);
        $ret = array();
        $result = $db->query($sql);
        while ($myrow = $db->fetchArray($result)) {
            $ret[$myrow['rank_id']] = array('title'=>$myrow['rank_title'],'image'=>$myrow['rank_image']);
        }
        return $ret;
        
    }
    
    public function url(){
        
        $mc = RMSettings::module_settings('bxpress');
        if($mc['urlmode']){
            return XOOPS_URL.'/'.$mc['htbase'];
        } else {
            return XOOPS_URL.'/modules/bxpress';
        }
        
    }

    static public function help(){


        RMTemplate::get()->add_help('http://www.redmexico.com.mx/docs/bxpress-forums/foros/standalone/1/#crear-foro');

    }

}

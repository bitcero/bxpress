<?php
// $Id: notification.php 1034 2012-09-06 02:30:13Z i.bitcero $
// --------------------------------------------------------------
// bXpress
// A simple forums module for XOOPS and Common Utilities
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

/**
* @desc Genera los datos para el envio de las notificaciones
* @param string I de la categoría
* @param int Id del elemento
* @param string Id del Evento generado
* @param array Parámetros adicionales
* @return string
*/
function bxNotifications($category,$id,$event,$params=array()){

    $bxf = bXFunctions::get();
	
	if ($category=='forum'){
		//Notificación de nuevo tema en foro
		if ($event=='newtopic'){
			$forum=new bXForum($id);
			$info['name']=$forum->name();
			$info['url']= $bxf->url()."/topic.php?id=$params[topic]";
			//$info['desc']=$param['topic'];
			return $info;		
		}
		
		//Notificación de nuevo mensaje en foro
		if ($event=='postforum'){
			$forum=new bXForum($id);
			$info['name']=$forum->name();
			$info['url']=$bxf->url()."/topic.php?pid=$params[post]#p$params[post]";
			//$info['desc']=$param['topic'];
			return $info;		
		}
	}

	//Notificación de nuevo mensaje en tema
	if ($category=='topic'){
		$topic=new bXTopic($id);
		$info['name']=$topic->title();
		$info['url']=$bxf->url()."/topic.php?pid=$params[post]#p$params[post]";
		//$info['desc']=$param['topic'];
		
		return $info;
	}


	//Notificación de mensaje en cualquier foro
	if ($category=='any_forum'){
		$forum=new bXForum($params['forum']);
		$info['name']=$forum->name();
		$info['url']=$bxf->url()."/topic.php?pid=$params[post]#p$params[post]";
		//$info['desc']=$param['topic'];
		return $info;

	}
	
	
	
}

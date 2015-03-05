<?php
// $Id: index.php 819 2011-12-08 23:43:13Z i.bitcero $
// --------------------------------------------------------------
// bXpress Forums
// An simple forums module for XOOPS and Common Utilities
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

class bXReport extends RMObject
{

	public function __construct($id=null){
        	$this->db = XoopsDatabaseFactory::getDatabaseConnection();
        	$this->_dbtable = $this->db->prefix("mod_bxpress_report");
        	$this->setNew();
        	$this->initVarsFromTable();
        
        
        	if (!isset($id)) return;
        	/**
        	 * Cargamos los datos del reporte
        	 */
      		if (is_numeric($id)){
		        if (!$this->loadValues($id)) return;     
			$this->unsetNew();
        	} 
	}


	/**
	* @desc Metodos para acceso a las propiedades
	*/
   
	public function id(){
		return $this->getVar('report_id');
	}

	//Id del mensaje
	public function post(){
		return $this->getVar('post_id');
	}
 
	public function setPost($post){
		return $this->setVar('post_id',$post);
	}

	//Nombre del usuario del reporte
	public function user(){
		return $this->getVar('reporter_uid');
	}

	public function setUser($user){
		return $this->setVar('reporter_uid',$user);
	}
	
	//Ip de usuario
	public function ip(){
		return $this->getVar('reporter_ip');
	}

	public function setIp($ip){
		return $this->setVar('reporter_ip',$ip);
	}

	//Fecha de creación del reporte
	public function time(){
		return $this->getVar('report_time');
	}

	public function setTime($time){
		return $this->setVar('report_time',$time);
	}


	//Texto del reporte
	public function report(){
		return $this->getVar('report_text');
	}
	
	public function setReport($report){
		return $this->setVar('report_text',$report);
	}

	//Id de usuario que revisa el reporte
	public function zappedBy(){
		return $this->getVar('zappedby');
	}

	public function setZappedBy($zappedby){
		return $this->setVar('zappedby',$zappedby);
	}

	//Fecha de revisión del reporte
	public function zappedTime(){
		return $this->getVar('zappedtime');
	}

	public function setZappedTime($zappedtime){
		return $this->setVar('zappedtime',$zappedtime);
	}
	
	//Indica reporte revisado
	public function zapped(){
		return $this->getVar('zapped');
	}

	public function setZapped($zapped){
		return $this->setVar('zapped',$zapped);
	}

	public function save(){
		if ($this->isNew()){
			return $this->saveToTable();
		}else{
			return $this->UpdateTable();
		}
	}

	public function delete(){
		return $this->deleteFromTable();
	}
}

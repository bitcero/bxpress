<?php
// $Id: bxcategory.class.php 896 2012-01-02 18:43:23Z i.bitcero $
// --------------------------------------------------------------
// bXpress Forums
// An simple forums module for XOOPS and Common Utilities
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

class bXCategory extends RMObject
{

	private $_tbl = '';
	private $_found = false;
	private $_grupos = array();

	function __construct($id=null){
		$this->db = XoopsDatabaseFactory::getDatabaseConnection();
        $this->_dbtable = $this->db->prefix("mod_bxpress_categories");
        $this->setNew();
        $this->initVarsFromTable();
        
        $this->setVarType('groups', XOBJ_DTYPE_ARRAY);
		
        if (!isset($id)) return;
		/**
		 * Cargamos los datos de la categoría seleccionada
		 */
        if (is_numeric($id)){
		    if (!$this->loadValues($id)) return;     
            $this->unsetNew();
        } else {
            $this->primary = 'friendname';
            if ($this->loadValues($id)) $this->unsetNew();
            $this->primary = 'id_cat';   
        }
        
	}
	/**
    * @desc Métodos para acceder a las propiedades
    */
    function id(){
        return $this->getVar('id_cat');   
    }
    
    function title(){
        return $this->getVar('title');
    }
    function setTitle($value){
        return $this->setVar('title', $value);   
    }
    
    function description(){
        return $this->getVar('description');
    }
    function setDescription($value){
        return $this->setVar('description', $value);   
    }
    
    function order(){
        return $this->getVar('order');
    }
    function setOrder($value){
        return $this->setVar('order', $value);   
    }
    
    function status(){
        return $this->getVar('status');
    }
    function setStatus($value){
        return $this->setVar('status', $value);   
    }
    
    function showDesc(){
        return $this->getVar('showdesc');
    }
    function setShowDesc($value){
        return $this->setVar('showdesc', $value);   
    }
    
    function groups(){
        return $this->getVar('groups');
    }
    function setGroups($value){
        return $this->setVar('groups', $value);   
    }
    
    function friendName(){
        return $this->getVar('friendname');
    }
    function setFriendName($value){
        return $this->setVar('friendname', $value);   
    }
    /**
    * @desc Obtiene los foros que pertenecen a esta categoría
    */
    
    /**
    * @desc Comprueba que un grupo tenga permisos de acceso a esta
    * categoría
    * @param int $gid Id del Grupo
    * @param array $gid Array con ids de grupos
    * @return bool
    */
    function groupAllowed($gid){
        $groups =& $this->getVar('groups');
        
        if (in_array(0, $groups)) return true;
        
        if (!is_array($gid)) return in_array($gid, $groups);
        foreach($gid as $id){
            if (in_array($id, $groups)) return true;
        }
        
        return false;
    }
    
    /**
    * @desc Almacena los valores de la categoría
    */
    function save(){
        if ($this->isNew()){
            return $this->saveToTable();
        } else {
            return $this->updateTable();   
        }
    }



    /**
    * @desc Elimina las categorías
    **/
    function delete(){
	
        //Eliminamos foros que pertenecen a la categoria
        $sql="DELETE FROM ".$this->db->prefix('mod_bxpress_forums')." WHERE cat=".$this->id();
        $result=$this->db->queryF($sql);

        if (!$result) return false;

        return $this->deleteFromTable();
	
    }
    
}

/**
* @desc Manejador para las categorías
*/
class bXCategoryHandler
{
    private $db;
    private $table = '';
    
    function __construct(){
        $this->db = XoopsDatabaseFactory::getDatabaseConnection();
        $this->table = $this->db->prefix("mod_bxpress_categories");
    }
    
    /**
    * @desc Obtiene la lista de categorías en un array para utilizar en un campo RMSelect
    */
    public function getForSelect(){
        
        $result = $this->db->query("SELECT id_cat, title FROM $this->table ORDER BY `order`");
        $rtn = array();
        while (list($id,$title) = $this->db->fetchRow($result)){
            $rtn[$id] = $title;
        }
        return $rtn;
    }
    /**
    * @desc Obtiene las categorías especificadas
    * @param int $active Categorías activas o inactivas (1 o 0, 2 Todas);
    */
    public function getObjects($active = 1){
        $db = XoopsDatabaseFactory::getDatabaseConnection();
        $sql = "SELECT * FROM ".$db->prefix("mod_bxpress_categories");
        if ($active==1 || $active==0){
            $sql .= " WHERE status='$active'";
        }
        $sql .= " ORDER BY `order`";
        $result = $db->query($sql);
        $categos = array();
        while ($row = $db->fetchArray($result)){
            $catego = new bXCategory();
            $catego->assignVars($row);
            $categos[] = $catego;
        }
        return $categos;
    }
    
}

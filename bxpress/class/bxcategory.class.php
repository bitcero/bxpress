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
    private $_grupos = [];

    public function __construct($id = null)
    {
        $this->db = XoopsDatabaseFactory::getDatabaseConnection();
        $this->_dbtable = $this->db->prefix('mod_bxpress_categories');
        $this->setNew();
        $this->initVarsFromTable();

        $this->setVarType('groups', XOBJ_DTYPE_ARRAY);

        if (!isset($id)) {
            return;
        }
        /**
         * Cargamos los datos de la categoría seleccionada
         */
        if (is_numeric($id)) {
            if (!$this->loadValues($id)) {
                return;
            }
            $this->unsetNew();
        } else {
            $this->primary = 'friendname';
            if ($this->loadValues($id)) {
                $this->unsetNew();
            }
            $this->primary = 'id_cat';
        }
    }

    /**
     * @desc Métodos para acceder a las propiedades
     */
    public function id()
    {
        return $this->getVar('id_cat');
    }

    public function title()
    {
        return $this->getVar('title');
    }

    public function setTitle($value)
    {
        return $this->setVar('title', $value);
    }

    public function description()
    {
        return $this->getVar('description');
    }

    public function setDescription($value)
    {
        return $this->setVar('description', $value);
    }

    public function order()
    {
        return $this->getVar('order');
    }

    public function setOrder($value)
    {
        return $this->setVar('order', $value);
    }

    public function status()
    {
        return $this->getVar('status');
    }

    public function setStatus($value)
    {
        return $this->setVar('status', $value);
    }

    public function showDesc()
    {
        return $this->getVar('showdesc');
    }

    public function setShowDesc($value)
    {
        return $this->setVar('showdesc', $value);
    }

    public function groups()
    {
        return $this->getVar('groups');
    }

    public function setGroups($value)
    {
        return $this->setVar('groups', $value);
    }

    public function friendName()
    {
        return $this->getVar('friendname');
    }

    public function setFriendName($value)
    {
        return $this->setVar('friendname', $value);
    }

    /**
     * @desc Obtiene los foros que pertenecen a esta categoría
     * @param mixed $gid
     */

    /**
     * @desc Comprueba que un grupo tenga permisos de acceso a esta
     * categoría
     * @param int $gid Id del Grupo
     * @param array $gid Array con ids de grupos
     * @return bool
     */
    public function groupAllowed($gid)
    {
        $groups = &$this->getVar('groups');

        if (in_array(0, $groups, true)) {
            return true;
        }

        if (!is_array($gid)) {
            return in_array($gid, $groups, true);
        }
        foreach ($gid as $id) {
            if (in_array($id, $groups, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @desc Almacena los valores de la categoría
     */
    public function save()
    {
        if ($this->isNew()) {
            return $this->saveToTable();
        }

        return $this->updateTable();
    }

    /**
     * @desc Elimina las categorías
     **/
    public function delete()
    {
        //Eliminamos foros que pertenecen a la categoria
        $sql = 'DELETE FROM ' . $this->db->prefix('mod_bxpress_forums') . ' WHERE cat=' . $this->id();
        $result = $this->db->queryF($sql);

        if (!$result) {
            return false;
        }

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

    public function __construct()
    {
        $this->db = XoopsDatabaseFactory::getDatabaseConnection();
        $this->table = $this->db->prefix('mod_bxpress_categories');
    }

    /**
     * @desc Obtiene la lista de categorías en un array para utilizar en un campo RMSelect
     */
    public function getForSelect()
    {
        $result = $this->db->query("SELECT id_cat, title FROM $this->table ORDER BY `order`");
        $rtn = [];
        while (false !== (list($id, $title) = $this->db->fetchRow($result))) {
            $rtn[$title] = $id;
        }

        return $rtn;
    }

    /**
     * @desc Obtiene las categorías especificadas
     * @param int $active Categorías activas o inactivas (1 o 0, 2 Todas);
     */
    public function getObjects($active = 1)
    {
        $db = XoopsDatabaseFactory::getDatabaseConnection();
        $sql = 'SELECT * FROM ' . $db->prefix('mod_bxpress_categories');
        if (1 == $active || 0 == $active) {
            $sql .= " WHERE status='$active'";
        }
        $sql .= ' ORDER BY `order`';
        $result = $db->query($sql);
        $categos = [];
        while (false !== ($row = $db->fetchArray($result))) {
            $catego = new bXCategory();
            $catego->assignVars($row);
            $categos[] = $catego;
        }

        return $categos;
    }
}

<?php
// $Id: bxannouncement.class.php 896 2012-01-02 18:43:23Z i.bitcero $
// --------------------------------------------------------------
// bXpress Forums
// An simple forums module for XOOPS and Common Utilities
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

/**
 * @desc Clase para el manejo de los anuncios en EXM BB
 */
class bXAnnouncement extends RMObject
{
    public function __construct($id = null)
    {
        $this->db = XoopsDatabaseFactory::getDatabaseConnection();
        $this->_dbtable = $this->db->prefix('mod_bxpress_announcements');
        $this->setNew();
        $this->initVarsFromTable();

        if (!isset($id)) {
            return;
        }

        if (!$this->loadValues($id)) {
            return;
        }

        $this->unsetNew();
    }

    public function id()
    {
        return $this->getVar('id_an');
    }

    /**
     * @desc Obtiene el texto del anuncio
     * @param string Formato del texto devuelto
     * @param mixed $format
     * @return string
     */
    public function text($format = 's')
    {
        return $this->getVar('text', $format);
    }

    public function setText($value)
    {
        return $this->setVar('text', $value);
    }

    /**
     * @desc Id de quien creo el anuncio
     * @return int
     */
    public function by()
    {
        return $this->getVar('by');
    }

    public function setBy($value)
    {
        return $this->setVar('by', $value);
    }

    public function byName()
    {
        return $this->getVar('byname');
    }

    public function setByName($value)
    {
        return $this->setVar('byname', $value);
    }

    public function date()
    {
        return $this->getVar('date');
    }

    public function setDate($value)
    {
        return $this->setVar('date', $value);
    }

    public function expire()
    {
        return $this->getVar('expire');
    }

    public function setExpire($value)
    {
        return $this->setVar('expire', $value);
    }

    public function where()
    {
        return $this->getVar('where');
    }

    public function setWhere($value)
    {
        return $this->setVar('where', $value);
    }

    public function forum()
    {
        return $this->getVar('forum');
    }

    public function setForum($value)
    {
        return $this->setVar('forum', $value);
    }

    public function html()
    {
        return $this->getVar('dohtml');
    }

    public function setHtml($value)
    {
        return $this->setVar('dohtml', $value);
    }

    public function bbcode()
    {
        return $this->getVar('doxcode');
    }

    public function setBBCode($value)
    {
        return $this->setVar('doxcode', $value);
    }

    public function doImage()
    {
        return $this->getVar('doimage');
    }

    public function setDoImage($value)
    {
        return $this->setVar('doimage', $value);
    }

    public function wrap()
    {
        return $this->getVar('dobr');
    }

    public function setWrap($value)
    {
        return $this->setVar('dobr', $value);
    }

    public function smiley()
    {
        return $this->getVar('dosmiley');
    }

    public function setSmiley($value)
    {
        return $this->setVar('dosmiley', $value);
    }

    public function save()
    {
        if ($this->isNew()) {
            return $this->saveToTable();
        }

        return $this->updateTable();
    }

    public function delete()
    {
        return $this->deleteFromTable();
    }
}

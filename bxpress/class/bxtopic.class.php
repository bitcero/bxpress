<?php
// $Id: bxtopic.class.php 896 2012-01-02 18:43:23Z i.bitcero $
// --------------------------------------------------------------
// bXpress Forums
// An simple forums module for XOOPS and Common Utilities
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

/**
 * @desc Clase para el manejo de temas en EXM BB
 */
class bXTopic extends RMObject
{
    public function __construct($id = null)
    {
        $this->db = XoopsDatabaseFactory::getDatabaseConnection();
        $this->_dbtable = $this->db->prefix('mod_bxpress_topics');
        $this->setNew();
        $this->initVarsFromTable();

        if (!isset($id)) {
            return;
        }
        /**
         * Cargamos los datos del foro
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
            $this->primary = 'id_topic';
        }
    }

    /**
     * Funciones para obtener los datos del Tema
     */
    public function id()
    {
        return $this->getVar('id_topic');
    }

    public function title()
    {
        return $this->getVar('title');
    }

    public function setTitle($value)
    {
        return $this->setVar('title', $value);
    }

    public function poster()
    {
        return $this->getVar('poster');
    }

    public function setPoster($value)
    {
        return $this->setVar('poster', $value);
    }

    public function date()
    {
        return $this->getVar('date');
    }

    public function setDate($value)
    {
        return $this->setVar('date', $value);
    }

    public function views()
    {
        return $this->getVar('views');
    }

    public function setViews($value)
    {
        return $this->setVar('views', $value);
    }

    public function addView()
    {
        $this->setViews($this->views() + 1);
    }

    public function replies()
    {
        return $this->getVar('replies');
    }

    public function setReplies($value)
    {
        return $this->setVar('replies', $value);
    }

    public function addReply()
    {
        $this->setReplies($this->replies() + 1);
    }

    public function lastPost()
    {
        return $this->getVar('last_post');
    }

    public function setLastPost($value)
    {
        return $this->setVar('last_post', $value);
    }

    public function forum()
    {
        return $this->getVar('id_forum');
    }

    public function setForum($value)
    {
        return $this->setVar('id_forum', $value);
    }

    public function status()
    {
        return $this->getVar('status');
    }

    public function setStatus($value)
    {
        return $this->setVar('status', $value);
    }

    public function sticky()
    {
        return $this->getVar('sticky');
    }

    public function setSticky($value)
    {
        return $this->setVar('sticky', $value);
    }

    public function digest()
    {
        return $this->getVar('digest');
    }

    public function setDigest($value)
    {
        return $this->setVar('digest', $value);
    }

    public function digestTime()
    {
        return $this->getVar('digest_time');
    }

    public function setDigestTime($value)
    {
        return $this->setVar('digest_time', $value);
    }

    public function approved()
    {
        return $this->getVar('approved');
    }

    public function setApproved($value)
    {
        return $this->setVar('approved', $value);
    }

    public function posterName()
    {
        return $this->getVar('poster_name');
    }

    public function setPosterName($value)
    {
        return $this->setVar('poster_name', $value);
    }

    public function rating()
    {
        return $this->getVar('rating');
    }

    public function setRating($value)
    {
        return $this->setVar('rating', $value);
    }

    public function votes()
    {
        return $this->getVar('votes');
    }

    public function setVotes($value)
    {
        return $this->setVar('votes', $value);
    }

    public function friendName()
    {
        return $this->getVar('friendname');
    }

    public function setFriendName($value)
    {
        return $this->setVar('friendname', $value);
    }

    public function permalink()
    {
        $mc = RMSettings::module_settings('bxpress');

        if ($mc->urlmode) {
            $link = XOOPS_URL . $mc->htbase . '/topic.php?id=' . $this->id();
        } else {
            $link = XOOPS_URL . '/modules/bxpress/';
            $link .= 'topic.php?id=' . $this->id();
        }

        return $link;
    }

    public function getPosts($object = true, $id_as_key = true)
    {
        $result = $this->db->query('SELECT * FROM ' . $this->db->prefix('mod_bxpress_posts') . " WHERE id_topic='" . $this->id() . "'");
        $ret = [];
        while (false !== ($row = $this->db->fetchArray($result))) {
            if ($object) {
                $attach = new bXPost();
                $attach->assignVars($row);
                if ($id_as_key) {
                    $ret[$row['id_post']] = $attach;
                } else {
                    $ret[] = $attach;
                }
            } else {
                if ($id_as_key) {
                    $ret[$row['id_post']] = $row;
                } else {
                    $ret[] = $row;
                }
            }
        }

        return $ret;
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
        foreach ($this->getPosts() as $post) {
            $post->delete();
        }

        $forum = new bXForum($this->forum());
        $forum->setTopics($forum->topics() - 1 > 0 ? $forum->topics() - 1 : 0);
        $forum->save();

        return $this->deleteFromTable();
    }
}

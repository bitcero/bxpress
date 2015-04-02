CREATE TABLE `mod_bxpress_announcements` (
  `id_an` int(11) NOT NULL auto_increment,
  `text` text NOT NULL,
  `by` int(11) NOT NULL,
  `byname` varchar(50) NOT NULL,
  `date` int(10) NOT NULL,
  `expire` int(10) NOT NULL,
  `where` tinyint(1) NOT NULL,
  `forum` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id_an`)
) ENGINE=InnoDB;

CREATE TABLE `mod_bxpress_attachments` (
  `attach_id` int(8) NOT NULL auto_increment,
  `post_id` int(10) default NULL,
  `file` varchar(255) default NULL,
  `name` varchar(255) NOT NULL,
  `mimetype` varchar(255) default NULL,
  `date` int(10) NOT NULL default '0',
  `downloads` int(10) NOT NULL default '0',
  PRIMARY KEY  (`attach_id`),
  KEY `post_id` (`post_id`)
) ENGINE=InnoDB;

CREATE TABLE `mod_bxpress_categories` (
  `id_cat` smallint(3) NOT NULL auto_increment,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `order` smallint(3) NOT NULL default '0',
  `status` int(1) NOT NULL default '0',
  `showdesc` tinyint(1) NOT NULL default '0',
  `groups` text NOT NULL,
  `friendname` varchar(100) NOT NULL,
  PRIMARY KEY  (`id_cat`),
  UNIQUE KEY `friendname` (`friendname`)
) ENGINE=InnoDB;

CREATE TABLE `mod_bxpress_forums` (
  `id_forum` int(10) NOT NULL auto_increment,
  `name` varchar(150) NOT NULL,
  `desc` text collate utf8_spanish_ci,
  `image` varchar(255) NOT NULL,
  `parent` int(10) NOT NULL default '0',
  `moderators` text NOT NULL,
  `topics` int(8) NOT NULL default '0',
  `posts` int(8) NOT NULL default '0',
  `last_post_id` int(5) NOT NULL default '0',
  `cat` int(2) NOT NULL default '0',
  `active` tinyint(1) NOT NULL default '1',
  `sig` tinyint(1) NOT NULL default '1',
  `prefix` tinyint(1) NOT NULL default '0',
  `hot_threshold` tinyint(3) NOT NULL default '10',
  `order` int(8) NOT NULL default '0',
  `attachments` tinyint(1) NOT NULL default '1',
  `attach_maxkb` int(10) NOT NULL default '1000',
  `attach_ext` text NOT NULL,
  `subforums` int(10) NOT NULL default '0',
  `friendname` varchar(150) NOT NULL,
  `permissions` text NOT NULL,
  PRIMARY KEY  (`id_forum`),
  UNIQUE KEY `friendname` (`friendname`)
) ENGINE=InnoDB;

CREATE TABLE `mod_bxpress_likes` (
  `id_like` int(11) NOT NULL AUTO_INCREMENT,
  `post` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY (`id_like`),
  KEY `post` (`post`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `mod_bxpress_posts` (
  `id_post` int(8) NOT NULL auto_increment,
  `pid` int(8) NOT NULL default '0',
  `id_topic` int(8) NOT NULL default '0',
  `id_forum` int(4) NOT NULL default '0',
  `post_time` int(10) NOT NULL default '0',
  `uid` int(5) NOT NULL default '0',
  `poster_name` varchar(255) NOT NULL default '',
  `poster_ip` varchar(20) NOT NULL default '0',
  `icon` varchar(25) NOT NULL default '',
  `attachsig` tinyint(1) NOT NULL default '0',
  `approved` int(1) NOT NULL default '1',
  `post_karma` int(10) NOT NULL default '0',
  `attachment` text collate utf8_spanish_ci,
  `require_reply` int(1) NOT NULL default '0',
  `parent` int(11) NOT NULL default '0',
  `likes` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id_post`),
  KEY `uid` (`uid`),
  KEY `pid` (`pid`),
  KEY `forumid_uid` (`id_forum`,`uid`),
  KEY `topicid_uid` (`id_topic`,`uid`),
  KEY `topicid_postid_pid` (`id_topic`,`id_post`,`pid`)
) ENGINE=InnoDB;

CREATE TABLE `mod_bxpress_posts_text` (
  `post_id` int(8) NOT NULL default '0',
  `post_text` text collate utf8_spanish_ci,
  `post_edit` text collate utf8_spanish_ci,
  PRIMARY KEY  (`post_id`),
  FULLTEXT KEY `search` (`post_text`)
) ENGINE=InnoDB;

CREATE TABLE `mod_bxpress_report` (
  `report_id` int(8) NOT NULL auto_increment,
  `post_id` int(10) default NULL,
  `reporter_uid` int(10) default NULL,
  `reporter_ip` varchar(15) NOT NULL,
  `report_time` int(10) NOT NULL default '0',
  `report_text` varchar(255) default NULL,
  `zappedby` int(11) NOT NULL default '0',
  `zappedtime` int(10) NOT NULL DEFAULT '0',
  `zapped` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`report_id`),
  KEY `post_id` (`post_id`)
) ENGINE=InnoDB;

CREATE TABLE `mod_bxpress_topics` (
  `id_topic` int(11) NOT NULL auto_increment,
  `title` varchar(255) default NULL,
  `poster` int(5) NOT NULL default '0',
  `date` int(10) NOT NULL default '0',
  `views` int(5) NOT NULL default '0',
  `replies` int(4) NOT NULL default '0',
  `last_post` int(8) NOT NULL default '0',
  `id_forum` int(4) NOT NULL default '0',
  `status` tinyint(1) NOT NULL default '0',
  `subject` int(3) NOT NULL default '0',
  `sticky` tinyint(1) NOT NULL default '0',
  `digest` tinyint(1) NOT NULL default '0',
  `digest_time` int(10) NOT NULL default '0',
  `approved` int(1) NOT NULL default '1',
  `poster_name` varchar(255) NOT NULL,
  `rating` double(6,4) NOT NULL default '0.0000',
  `votes` int(11) NOT NULL default '0',
  `friendname` varchar(255) NOT NULL,
  PRIMARY KEY  (`id_topic`),
  KEY `forum_id` (`id_forum`),
  KEY `topic_last_post_id` (`last_post`),
  KEY `topic_poster` (`poster`),
  KEY `topic_forum` (`id_topic`,`id_forum`),
  KEY `topic_sticky` (`sticky`),
  KEY `topic_digest` (`digest`)
) ENGINE=InnoDB;

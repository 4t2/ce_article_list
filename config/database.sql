-- ********************************************************
-- *                                                      *
-- * IMPORTANT NOTE                                       *
-- *                                                      *
-- * Do not import this file manually but use the Contao  *
-- * install tool to create and maintain database tables! *
-- *                                                      *
-- ********************************************************

-- --------------------------------------------------------

-- 
-- Table `tl_content`
-- 

CREATE TABLE `tl_content` (
  `article_list_childrens` char(1) NOT NULL default '',
  `article_list_recursive` char(1) NOT NULL default '',
  `article_list_hidden` char(1) NOT NULL default '1',
  `article_list_pages` blob NULL,
  `article_list_page_link` char(1) NOT NULL default '',
  `article_list_page_headline` char(1) NOT NULL default '1',
  `article_list_teaser` char(1) NOT NULL default '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

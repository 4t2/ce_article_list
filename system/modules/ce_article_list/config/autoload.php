<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2012 Leo Feyer
 * 
 * @package Ce_article_list
 * @link    http://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	'ArticleList' => 'system/modules/ce_article_list/ArticleList.php',
	'PageList'    => 'system/modules/ce_article_list/PageList.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'ce_article_list' => 'system/modules/ce_article_list/templates',
	'ce_page_list'    => 'system/modules/ce_article_list/templates',
));

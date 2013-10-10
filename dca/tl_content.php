<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  Lingo4you 2013
 * @author     Mario Müller <http://www.lingolia.com/>
 * @package    ArticleList
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */


$GLOBALS['TL_DCA']['tl_content']['palettes']['page_list'] = '{type_legend},type,headline;{article_list_legend},article_list_pages,article_list_childrens,article_list_recursive,article_list_hidden;{protected_legend:hide},protected;{expert_legend:hide},cssID,space';

$GLOBALS['TL_DCA']['tl_content']['palettes']['article_list'] = '{type_legend},type,headline;{article_list_legend},article_list_pages,article_list_childrens,article_list_recursive,article_list_hidden;{article_list_options_legend},article_list_page_link,article_list_page_headline,article_list_teaser;{protected_legend:hide},protected;{expert_legend:hide},cssID,space';



$GLOBALS['TL_DCA']['tl_content']['fields']['article_list_childrens'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_content']['article_list_childrens'],
	'default'       => '1',
	'exclude'		=> true,
	'inputType'		=> 'checkbox'
);

$GLOBALS['TL_DCA']['tl_content']['fields']['article_list_recursive'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_content']['article_list_recursive'],
	'exclude'		=> true,
	'inputType'		=> 'checkbox',
	'eval'          => array('tl_class'=>'w50')
);

$GLOBALS['TL_DCA']['tl_content']['fields']['article_list_hidden'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_content']['article_list_hidden'],
	'exclude'		=> true,
	'inputType'		=> 'checkbox',
	'eval'          => array('tl_class'=>'w50')
);


$GLOBALS['TL_DCA']['tl_content']['fields']['article_list_pages'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['article_list_pages'],
	'exclude'                 => true,
	'inputType'               => 'pageTree',
	'eval'                    => array
	(
		'mandatory'		=> false,
		'multiple'		=> true,
		'fieldType'		=> 'checkbox'
	)
);

$GLOBALS['TL_DCA']['tl_content']['fields']['article_list_page_link'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_content']['article_list_page_link'],
	'exclude'		=> true,
	'inputType'		=> 'checkbox'
);

$GLOBALS['TL_DCA']['tl_content']['fields']['article_list_page_headline'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_content']['article_list_page_headline'],
	'default'       => '1',
	'exclude'		=> true,
	'inputType'		=> 'checkbox',
	'eval'          => array('tl_class'=>'w50')
);

$GLOBALS['TL_DCA']['tl_content']['fields']['article_list_teaser'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_content']['article_list_teaser'],
	'default'       => '1',
	'exclude'		=> true,
	'inputType'		=> 'checkbox',
	'eval'          => array('tl_class'=>'w50')
);

<?php
/**
 * PHP version 5
 * @copyright  Lingo4you 2013
 * @author     Mario Müller <http://www.lingolia.com/>
 * @package    ArticleList
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */


$GLOBALS['TL_DCA']['tl_content']['palettes']['page_list'] = '{type_legend},type,headline;{article_list_legend},article_list_pages,article_list_childrens,article_list_recursive,article_list_hidden;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},cssID,space';

$GLOBALS['TL_DCA']['tl_content']['palettes']['article_list'] = '{type_legend},type,headline;{article_list_legend},article_list_pages,article_list_childrens,article_list_recursive,article_list_hidden;{article_list_options_legend},article_list_page_link,article_list_page_headline,article_list_teaser;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},cssID,space';



$GLOBALS['TL_DCA']['tl_content']['fields']['article_list_childrens'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_content']['article_list_childrens'],
	'default'       => '1',
	'exclude'		=> true,
	'inputType'		=> 'checkbox',
    'sql' => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['article_list_recursive'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_content']['article_list_recursive'],
	'exclude'		=> true,
	'inputType'		=> 'checkbox',
	'eval'          => array('tl_class'=>'w50'),
    'sql' => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['article_list_hidden'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_content']['article_list_hidden'],
	'exclude'		=> true,
	'inputType'		=> 'checkbox',
	'eval'          => array('tl_class'=>'w50'),
    'sql' => "char(1) NOT NULL default '1'"
);


$GLOBALS['TL_DCA']['tl_content']['fields']['article_list_pages'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_content']['article_list_pages'],
	'default'       		  => '1',
	'exclude'                 => true,
	'inputType'               => 'pageTree',
	'eval'                    => array
	(
		'mandatory'		=> false,
		'multiple'		=> true,
		'fieldType'		=> 'checkbox'
	),
    'sql' => "blob NULL"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['article_list_page_link'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_content']['article_list_page_link'],
	'exclude'		=> true,
	'inputType'		=> 'checkbox',
    'sql' => "char(1) NOT NULL default '1'"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['article_list_page_headline'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_content']['article_list_page_headline'],
	'default'       => '1',
	'exclude'		=> true,
	'inputType'		=> 'checkbox',
	'eval'          => array('tl_class'=>'w50'),
    'sql' => "char(1) NOT NULL default '1'"

);

$GLOBALS['TL_DCA']['tl_content']['fields']['article_list_teaser'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_content']['article_list_teaser'],
	'default'       => '1',
	'exclude'		=> true,
	'inputType'		=> 'checkbox',
	'eval'          => array('tl_class'=>'w50'),
    'sql' => "char(1) NOT NULL default '1'"
);

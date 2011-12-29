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
 * @copyright  Lingo4you 2011
 * @author     Mario Müller <http://www.lingo4u.de/>
 * @package    ArticleList
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */

class ArticleList extends ContentElement
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'ce_article_list';
	
	protected $idLevels = array();

	/**
	 * Helper function for usort
	 */
	protected function pageSort($a, $b)
	{
		if ($a['sort'] == $b['sort']) {
		    return 0;
		}
		return ($a['sort'] < $b['sort']) ? -1 : 1;
	}

	/**
	 * Gets all child pages if article_list_recursive is set
	 */
	protected function getChildPages($pageId, $recursive = true, $hidden = false, $level=0)
	{
		$pageArray = array();

		$objPages = $this->Database->prepare("
			SELECT
				`id`
			FROM
				`tl_page`
			WHERE
				`pid`=? AND
				`type`='regular'
				".(!$this->Input->cookie('FE_PREVIEW') ? " AND `published`='1' " : "")."
			ORDER BY
				`sorting`
		")->execute($pageId);

		while ($objPages->next())
		{
			$pageArray[] = $objPages->id;
			$this->idLevels[$objPages->id] = $level;
			
			if ($recursive)
			{
				$pageArray = array_merge($pageArray, $this->getChildPages($objPages->id, $recursive, $hidden, $level+1));
			}
		}

		return $pageArray;
	}

	/**
	 * Generate content element
	 */
	protected function compile()
	{
		global $objPage;
		$query = '';
		$pages = array();

		$selectedPages = deserialize($this->arrData['article_list_pages']);
		$articleListPages = $selectedPages;
		
		if (is_array($articleListPages) && count($articleListPages) > 0)
		{
			#$query = '`id` IN ('.implode(',', $articleListPages).')';
		}
		else {
			$articleListPages = array();
		}

		if ($this->article_list_childrens)
		{
			if (TL_MODE == 'FE')
			{
				$pageId = $objPage->id;
			}
			else
			{
				$objArticle = $this->Database->prepare("SELECT `pid` FROM `tl_article` WHERE `id`=?")
					->execute($this->pid);
			
				if ($objArticle->next())
				{
					$pageId = $objArticle->pid;
				}
				else
				{
					// $this->log('No parent page found.', 'ArticleList', TL_ERROR);
				}
			}

			array_splice($articleListPages, 0, 0, $this->getChildPages($pageId, false));
#			$this->log('articleListPages: '.implode($articleListPages, ','), 'ArticleList', TL_ERROR);
		}

		if ($this->article_list_recursive)
		{
			for ($i=count($articleListPages)-1; $i>=0; $i--)
			{
				array_splice($articleListPages, $i+1, 0, $this->getChildPages($articleListPages[$i]));
			}
		}
		
		if (count($articleListPages))
		{
			$objPages = $this->Database->prepare("
			SELECT
				`id`,
				`alias`,
				`pageTitle`,
				`type`,
				`hide`,
				`protected`,
				`groups`,
				`sorting`
			FROM
				tl_page
			WHERE
				" . (!$this->Input->cookie('FE_PREVIEW') ? "`published`='1' AND " : "") . "
				`id` IN (" . implode(',', $articleListPages) . ")
			ORDER BY `sorting`
			")
			->execute();

			if ($objPages->numRows > 0)
			{
				$arrInactiveModules = deserialize($GLOBALS['TL_CONFIG']['inactiveModules']);
				
				/**
				 * fetch all columns if zArticleImage or teaserimages is installed and not inactive
				 */
				if ((is_dir(TL_ROOT . '/system/modules/zArticleImage') && (!is_array($arrInactiveModules) || !in_array('zArticleImage', $arrInactiveModules))) ||
					(is_dir(TL_ROOT . '/system/modules/teaserimages') && (!is_array($arrInactiveModules) || !in_array('teaserimages', $arrInactiveModules))))
				{
					$sql = "SELECT * FROM tl_article WHERE " . (!$this->Input->cookie('FE_PREVIEW') ? "`published`='1' AND " : "") . " `pid`=? ORDER BY `sorting`";
				}
				else
				{
					$sql = "SELECT `id`, `alias`, `title`, `teaser`, `inColumn` FROM tl_article WHERE " . (!$this->Input->cookie('FE_PREVIEW') ? "`published`='1' AND " : "") . " `pid`=? ORDER BY `sorting`";
				}
						
				while ($objPages->next())
				{
					if ($this->article_list_hidden || ($objPages->hide != '1') || in_array($objPages->id, $selectedPages))
					{
						$objArticles = $this->Database->prepare($sql)->execute($objPages->id);
	
						if ($objArticles->numRows > 0)
						{
							$articles = array();
	
							while ($objArticles->next())
							{
								if ($this->article_list_page_link && ($objArticles->numRows == 1))
								{
									$link = '';
								}
								else
								{
									$link = '/articles/';
			
									if ($objArticles->inColumn != 'main')
									{
										$link .= $objArticles->inColumn . ':';
									}
							
									$link .= (strlen($objArticles->alias) && !$GLOBALS['TL_CONFIG']['disableAlias']) ? $objArticles->alias : $objArticles->id;
								}
	
								/**
								 * Special handling for zArticleImage or teaserimages extension
								 */
								if ($objArticles->addImage && strlen($objArticles->singleSRC) && is_file(TL_ROOT . '/' . $objArticles->singleSRC))
								{
									$imageTemplate = new SubTemplate();
									$this->addImageToTemplate($imageTemplate, $objArticles->row());
								}
								else
								{
									$imageTemplate = false;
								}
	
								$articles[] = array(
									'id' => $objArticles->id,
									'title' => $objArticles->title,
									'teaser' => ($this->article_list_teaser ? $objArticles->teaser : ''),
									'link' => $this->generateFrontendUrl($objPages->row(), $link),
									'image' => $imageTemplate
								);
							}
	
							$pages[] = array(
								'title' => $objPages->pageTitle,
								'link' => $this->generateFrontendUrl($objPages->row()),
								'articles' => $articles,
								'level' => (isset($this->idLevels[$objPages->id]) ? $this->idLevels[$objPages->id] : 0),
								'sort' => (array_search($objPages->id, $articleListPages) !== FALSE ? array_search($objPages->id, $articleListPages) + 9000000 : $objPages->sorting)
							);
						}
					}
				}
			}
		}
		elseif (TL_MODE == 'FE')
		{
			$this->log(sprintf('No articles for ID %d found.', $objPage->id), 'ArticleList', TL_NOTICE);
		}

		if ((count($pages) > 0) && (count($articleListPages) > 0))
		{
			usort($pages, array($this, 'pageSort'));
		}

		$this->Template->pages = $pages;
		
		if ($this->article_list_page_headline)
		{
			$hlNext = array('h1'=>'h2','h2'=>'h3','h3'=>'h4','h4'=>'h5','h5'=>'h6','h6'=>'p');
			$this->Template->hlPage = (isset($hlNext[$this->hl]) ? $hlNext[$this->hl] : 'p');
		}
		else
		{
			$this->Template->hlPage = false;
		}
	}

}




/**
 * Class SubTemplate
 *
 * Simple template class for zArticleImage or teaserimages extension.
 * @copyright  Mario Müller 2011
 * @author     Mario Müller <http://www.lingo4u.de>
 * @package    ce_article_list
 */
class SubTemplate
{

	/**
	 * Template data
	 * @var array
	 */
	protected $arrData = array();


	/**
	 * Create a new template instance
	 * @param string
	 * @param string
	 * @throws Exception
	 */
	public function __construct()
	{
	}


	/**
	 * Set an object property
	 * @param string
	 * @param mixed
	 */
	public function __set($strKey, $varValue)
	{
		$this->arrData[$strKey] = $varValue;
	}


	/**
	 * Return an object property
	 * @param string
	 * @return mixed
	 */
	public function __get($strKey)
	{
		return $this->arrData[$strKey];
	}


	/**
	 * Check whether a property is set
	 * @param string
	 * @return boolean
	 */
	public function __isset($strKey)
	{
		return isset($this->arrData[$strKey]);
	}


	/**
	 * Set the template data from an array
	 * @param array
	 */
	public function setData($arrData)
	{
		$this->arrData = $arrData;
	}


	/**
	 * Return the template data as array
	 * @return array
	 */
	public function getData()
	{
		return $this->arrData;
	}

}

?>
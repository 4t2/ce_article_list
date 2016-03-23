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
 * @copyright  Lingo4you 2014
 * @author     Mario Müller <http://www.lingolia.com/>
 * @package    ArticleList
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */

class PageList extends \ContentElement
{
	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'ce_page_list';
	
	protected $idLevels = array();

	/**
	 * Generate content element
	 */
	protected function compile()
	{
		global $objPage;
		$query = '';
		$pages = array();

		$selectedPages = deserialize($this->article_list_pages);

		if (is_array($selectedPages))
		{
			$articleListPages = $selectedPages;
		}
		elseif (!empty($selectedPages))
		{
			$articleListPages = array($selectedPages);
		}
		else
		{
			$articleListPages = array();
		}

		if (TL_MODE == 'FE')
		{
			$pageId = $objPage->id;
		}
		else
		{
			$objArticle = $this->Database->prepare("SELECT `pid` FROM `tl_article` WHERE `id`=?")->limit(1)->execute($this->pid);

			if ($objArticle->next())
			{
				$pageId = $objArticle->pid;
			}
		}

		if ($this->article_list_childrens)
		{
			array_splice($articleListPages, 0, 0, $this->getChildPages($pageId, false));
		}

		if ($this->article_list_recursive)
		{
			for ($i=count($articleListPages)-1; $i>=0; $i--)
			{
				array_splice($articleListPages, $i+1, 0, $this->getChildPages($articleListPages[$i], true, $this->idLevels[$articleListPages[$i]]+1));
			}
		}

		if (count($articleListPages))
		{
			$objPages = $this->Database->prepare("
			SELECT
				`id`,
				`alias`,
				`title`,
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
				$this->import('FrontendUser', 'User');

				while ($objPages->next())
				{
					if ($this->article_list_hidden || ($objPages->hide != '1') || in_array($objPages->id, $selectedPages))
					{
						$isProtected = false;

						// Protected element
						if (!BE_USER_LOGGED_IN && $objPages->protected)
						{
							if (!FE_USER_LOGGED_IN)
							{
								$isProtected = true;
							}
							else
							{
								$groups = deserialize($objPages->groups);
					
								if (!is_array($groups) || empty($groups) || !count(array_intersect($groups, $this->User->groups)))
								{
									$isProtected = true;
								}
							}
						}
						
						$level = (isset($this->idLevels[$objPages->id]) ? $this->idLevels[$objPages->id] : 0);

						$pages[] = array
						(
							'name'			=> $objPages->title,
							'title'			=> ($objPages->pageTitle != '' ? $objPages->pageTitle : $objPages->title),
							'link'			=> $this->generateFrontendUrl($objPages->row()),
							'protected'		=> $isProtected,
							'level'			=> $level,
							'active'		=> ($pageId == $objPages->id),
							'class'			=> 'level'.$level.' '.($pageId == $objPages->id ? ' active'.($isProtected ? ' protected' : '') : ($isProtected ? 'protected' : '')),
							'sort'			=> (array_search($objPages->id, $articleListPages) !== FALSE ? array_search($objPages->id, $articleListPages) + 9000000 : $objPages->sorting)
						);
					}
				}
			}
		}
		elseif (TL_MODE == 'FE')
		{
			$this->log(sprintf('No pages for ID %d (%s) found.', $objPage->id, $objPage->pageTitle), 'PageList', TL_ERROR);
		}

		if ((count($pages) > 0) && (count($articleListPages) > 0))
		{
			usort($pages, array($this, 'pageSort'));
		}

		$this->Template->pages = $pages;		
	}


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
	protected function getChildPages($pageId, $recursive = true, $level=0)
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
				$pageArray = array_merge($pageArray, $this->getChildPages($objPages->id, $recursive, $level+1));
			}
		}

		return $pageArray;
	}

}

?>
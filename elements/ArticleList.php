<?php

namespace fortytwo\CeArticleList;

use Contao\ArticleModel;
use Contao\ContentElement;
use Contao\Database;
use Contao\FilesModel;
use Contao\Template;

/**
 * @copyright  Lingo4you 2018
 * @author     Mario Müller https://www.lingolia.com/
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
            $objArticle = ArticleModel::findByPid($this->pid);

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
			$objPages = \Database::getInstance()->prepare("
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
				" . (!\Input::cookie('FE_PREVIEW') ? "`published`='1' AND " : "") . "
				`id` IN (" . implode(',', $articleListPages) . ")
			ORDER BY `sorting`
			")
			->execute();

			if ($objPages->numRows > 0)
			{
				$arrInactiveModules = deserialize($GLOBALS['TL_CONFIG']['inactiveModules']);

				$sql = "
					SELECT
						*
					FROM
						tl_article
					WHERE " . 
						(!\Input::cookie('FE_PREVIEW') ? "`published`='1' AND" : "") . " `pid`=? 
					ORDER BY `sorting`";

				$this->import('FrontendUser', 'User');
				
				while ($objPages->next())
				{
					if ($this->article_list_hidden || ($objPages->hide != '1') || in_array($objPages->id, $selectedPages))
					{
						$protectedPage = false;
						
						// Protected element
						if (!BE_USER_LOGGED_IN && $objPages->protected)
						{
							if (!FE_USER_LOGGED_IN)
							{
								$protectedPage = true;
							}
							else
							{
								$groups = deserialize($objPages->groups);

								if (!is_array($groups) || empty($groups) || !count(array_intersect($groups, $this->User->groups)))
								{
									$protectedPage = true;
								}
							}
						}

						$objArticles = Database::getInstance()->prepare($sql)->execute($objPages->id);

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
								$imageTemplate = false;
								$addImage = false;
								
								if (version_compare(VERSION, '3', '>='))
								{
									if ($objArticles->addImage && $objArticles->singleSRC != '')
									{
										if (version_compare(VERSION, '3.2', '>='))
										{
											$objModel = FilesModel::findByUuid($this->singleSRC);
										}
										else
										{
											$objModel = FilesModel::findByPk($objArticles->singleSRC);
										}
																	
										if ($objModel !== null && is_file(TL_ROOT . '/' . $objModel->path))
										{
											$addImage = true;
											$imageTemplate = new SubTemplate();

											$objArticles->singleSRC = $objModel->path;
											$this->addImageToTemplate($imageTemplate, $objArticles->row());
										}
									}
								}
								else
								{
									if ($objArticles->addImage && strlen($objArticles->singleSRC) && is_file(TL_ROOT . '/' . $objArticles->singleSRC))
									{
										$addImage = true;
										$imageTemplate = new SubTemplate();

										$this->addImageToTemplate($imageTemplate, $objArticles->row());
									}
								}

								$arrTeaserCssID = deserialize($objArticles->teaserCssID);

								$isProtected = $protectedPage;

								// Protected element
								if (!$protectedPage && !BE_USER_LOGGED_IN && $objArticles->protected)
								{
									if (!FE_USER_LOGGED_IN)
									{
										$isProtected = true;
									}
									else
									{
										$groups = deserialize($objArticles->groups);
							
										if (!is_array($groups) || empty($groups) || !count(array_intersect($groups, $this->User->groups)))
										{
											$isProtected = true;
										}
									}
								}

								$articles[] = array
								(
									'id'			=> $objArticles->id,
									'active'		=> ($this->pid == $objArticles->id),
									'class'			=> ($this->pid == $objArticles->id ? 'active'.($isProtected ? ' protected' : '') : ($isProtected ? 'protected' : '')),
									'title'			=> $objArticles->title,
									'teaser'		=> ($this->article_list_teaser ? $objArticles->teaser : ''),
									'teaser_cssID'	=> $arrTeaserCssID[0],
									'teaser_class'	=> $arrTeaserCssID[1],
									'link'			=> $this->generateFrontendUrl($objPages->row(), $link, null, true),
									'protected'		=> $isProtected,
									'image'			=> $imageTemplate,
									'addImage'		=> $addImage,
									'row'			=> $objArticles->row()
								);
							}
							
							$pages[] = array
							(
								'name' => $objPages->title,
								'title' => ($objPages->pageTitle != '' ? $objPages->pageTitle : $objPages->title),
								'link' => $this->generateFrontendUrl($objPages->row(), null, null, true),
								'protected' => $protectedPage,
								'articles' => $articles,
								'level' => (isset($this->idLevels[$objPages->id]) ? $this->idLevels[$objPages->id] : 0),
								'active' => ($pageId == $objPages->id),
								'class' => ($pageId == $objPages->id ? 'active'.($protectedPage ? ' protected' : '') : ($protectedPage ? 'protected' : '')),
								'sort' => (array_search($objPages->id, $articleListPages) !== FALSE ? array_search($objPages->id, $articleListPages) + 9000000 : $objPages->sorting)
							);
						}
					}
				}
			}
		}
		elseif (TL_MODE == 'FE')
		{
			$this->log(sprintf('No articles for ID %d (%s) found.', $objPage->id, $objPage->title), 'ArticleList', TL_ERROR);
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

		$objPages = Database::getInstance()->prepare("
			SELECT
				`id`
			FROM
				`tl_page`
			WHERE
				`pid`=? AND
				`type`='regular'
				".(!\Input::cookie('FE_PREVIEW') ? " AND `published`='1' " : "")."
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


/**
 * Class SubTemplate
 *
 * Template class for zArticleImage or teaserimages extension.
 * @copyright  Mario Müller 2016
 * @author     Mario Müller https://www.lingolia.com/
 * @package    ce_article_list
 */
class SubTemplate extends Template
{
    /**
     * SubTemplate constructor.
     * Create a new template instance
     */
	public function __construct()
	{
		parent::__construct('article_list_image');
	}
}

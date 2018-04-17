<?php
/*
 * Language relations for Contao Open Source CMS.
 *
 * @copyright  Arne Stappen (alias aGoat) 2018
 * @package    contao-languagerelation
 * @author     Arne Stappen <mehh@agoat.xyz>
 * @link       https://agoat.xyz
 * @license    LGPL-3.0
 */
 
namespace Agoat\LanguageRelationBundle\LanguageRelation;

use Contao\Backend;
use Contao\Database;
use Contao\NewsArchiveModel;
use Contao\PageModel;



class NewsArchiveLanguageRelationProvider extends AbstractLanguageRelationProvider implements LanguageRelationProviderInterface
{
	
	/**
     * {@inheritdoc}
     */	
	public function getContext()
	{
		return 'newsarchive';
	}
	
	/**
     * {@inheritdoc}
     */	
	public function getDcaTable()
	{
		return 'tl_news_archive';
	}
	
	
	public function build($id, $published)
	{
		$this->currentEntity = NewsArchiveModel::findByPk($id);

		if (null === $this->currentEntity) {
			return null;
		} 
		
		$this->parentEntity = PageModel::findByPk($this->currentEntity->jumpTo);

		$this->setRootLanguages($this->parentEntity, $published);
		
		return new LanguageRelation(
			$this, 
			$this->currentLanguage,
			array_keys($this->rootPages), 
			$this->getRelations($published)
		);
	}

	
	public function getEditUrl($related)
	{
		return Backend::addToUrl('id='.$related->id);
	}
	
	
	public function getViewUrl($related)
	{
		return Backend::addToUrl('id='.$related->id);
	}
	
	
	public function supportsPicker()
	{
		return false;
	}

	
	public function getSelectOptions($language)
	{
		$options = array();

		$archives = NewsArchiveModel::findBy([NewsArchiveModel::getTable().'.jumpTo IN (\''.implode("','", Database::getInstance()->getChildRecords($this->rootPages[$language]->id, 'tl_page')).'\')'], null);
		
		if (null === $archives) {
			return $options;
		}
		
		foreach ($archives as $archive) {
			$options[] = array(
				'value' => $archive->id,
				'label' => $archive->title
			);
		}
	
		return $options;
	}
	
	
	public function getCreateUrl($language)
	{
		if (0 == $this->currentArticle->pid) {
			return null;
		}
		
		$parentPage = \PageModel::findByPk($this->currentArticle->pid);
		
		/** It may be possible to look on the next parent level and insert there (but that's maybe confusing for the user to know where the new page will be created)
		while (!$parentPage->relation && 'root' != $parentPage->type) {
			$parentPage = \PageModel::findByPk($parentPage->pid);
		}
		*/
	
		$related = $this->getRelations($this->getDcaTable(), $parentPage);
	
		if (!array_key_exists($language, $related)) {
			return null;
		}
		
		$subPages = \PageModel::findByPid($related[$language]->id, ['order'=>'sorting']);
	
		if (null === $subPages) {
			$query = 'act=copy&mode=2&id='.$this->currentArticle->id.'&pid='.$related[$language]->id;
		} else {
			$query = 'act=copy&mode=1&id='.$this->currentArticle->id.'&pid='.$subPages->last()->id;
		}
		
		return Backend::addToUrl($query);
	}
	
	
	private function setParentRelations($published)
	{
		if (!isset($this->parentRelations)) {
			$this->parentRelations = array();
			
			$relation = $this->getRelations($published, $this->parentEntity);
		
			if (null !== $relation) {
				foreach ($relation as $model) {
					$this->parentRelations[$model->language] = $model;
				}
			}
		}
	}
}

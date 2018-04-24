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
use Contao\PageModel;


class PageLanguageRelationProvider extends AbstractLanguageRelationProvider implements LanguageRelationProviderInterface
{
    	
	/**
     * {@inheritdoc}
     */	
	public function getContext()
	{
		return 'page';
	}

	
	/**
     * {@inheritdoc}
     */	
	public function getDcaTable()
	{
		return 'tl_page';
	}
	
	
	public function build($id, $published)
	{
		$this->currentEntity = PageModel::findByPk($id);

		if (null === $this->currentEntity) {
			return null;
		} 

		$this->setRootLanguages($published);
		
		return new LanguageRelation(
			$this, 
			$this->currentLanguage,
			array_keys($this->rootPages), 
			$this->getRelations($published)
		);
	}

	
	public function getFrontendUrl($related)
	{
		return $related->getFrontendUrl();
	}


	public function getAlternativeUrl($language, $onlyRoot)
	{
		$alternative = $this->getAlternative($language, $onlyRoot);
	
		if (null === $alternative) {
			return null;
		}
		
		return $alternative->getFrontendUrl();
	}


	public function getAlternativeTitle($language, $onlyRoot)
	{
		$alternative = $this->getAlternative($language, $onlyRoot);
		
		if (null === $alternative) {
			return null;
		}
		
		return $alternative->title;
	}


	public function getEditUrl($related)
	{
		return Backend::addToUrl('id='.$related->id);
	}
	
	
	public function getViewUrl($related)
	{
		return Backend::addToUrl('pn='.$related->id);
	}
	
	
	public function supportsPicker()
	{
		return true;
	}

	
	public function getPickerUrl($language)
	{
		$options = [
			'rootNodes' => $this->rootPages[$language]->id,
			'source' => $this->getDcaTable().'.'.$this->currentEntity->id
		];
		
		return \System::getContainer()->get('contao.picker.builder')->getUrl('page', $options);
	}
	
	
	public function getCreateUrl($language)
	{
		if (0 == $this->currentEntity->pid || 0 == $this->currentEntity->tstamp) {
			return null;
		}
		
		$this->setParentRelations(false);
		
		/** It may be possible to look on the next parent level and insert there (but that's maybe confusing for the user to know where the new page will be created)
		while (!$parentPage->relation && 'root' != $parentPage->type) {
			$parentPage = PageModel::findByPk($parentPage->pid);
		}
		*/

		if (!array_key_exists($language, $this->parentRelations)) {
			return null;
		}

		$childPages = PageModel::findByPid($this->parentRelations[$language]->id, ['order'=>'sorting']);
		
		$query = 'act=copy&id='.$this->currentEntity->id.'&rid='.$this->currentEntity->relation;
		
		$query .= (null === $childPages) ?
			'&mode=2&pid='.$this->parentRelations[$language]->id :
			'&mode=1&pid='.$childPages->last()->id;
		
		return Backend::addToUrl($query);
	}
	
	
	private function setParentRelations($published)
	{
		if (!isset($this->parentEntity)) {
			$this->parentEntity = PageModel::findByPk($this->currentEntity->pid);
		}
		
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
	
	
	private function getAlternative($language, $onlyRoot)
	{
		if (!$onlyRoot) {
			$this->setParentRelations(true);
			
			if (isset($this->parentRelations[$language]) && 'root' != $this->parentRelations[$language]->type) {
				return $this->parentRelations[$language];
			}
		}
		
		return PageModel::findFirstPublishedByPid($this->rootPages[$language]->id);		
	}
	
	
	public function tryAutoRelation()
	{
		
		$this->setParentRelations(false);
		
		foreach (array_keys($this->rootPages) as $language) {
			if ($language == $this->currentLanguage) {
				continue;
			}
			
			$childPages = PageModel::findByPid($this->parentRelations[$language]->id, ['order'=>'sorting']);

			if (null === $childPages) {
				continue;
			}
			
			if (1 == $childPages->count()) {
				$this->setRelation($language, $childPages->id, false);
			}
		}

		return $this->getRelations(false);
	}
	
	

}

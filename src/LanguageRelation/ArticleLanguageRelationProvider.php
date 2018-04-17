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



class ArticleLanguageRelationProvider extends AbstractLanguageRelationProvider implements LanguageRelationProviderInterface
{
	
	/**
     * {@inheritdoc}
     */	
	public function getContext()
	{
		return 'article';
	}
	
	/**
     * {@inheritdoc}
     */	
	public function getDcaTable()
	{
		return 'tl_article';
	}
	
	
	public function legacyMode()
	{
		return true;
	}


	public function build($id, $published)
	{
		$this->currentEntity = \ArticleModel::findByPk($id);

		if (null === $this->currentEntity) {
			return null;
		} 
		
		$this->parentEntity = \PageModel::findByPk($this->currentEntity->pid);
		
		$this->setRootLanguages($this->parentEntity, $published);

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
		return Backend::addToUrl('id='.$related->id);
	}
	
	
	public function supportsPicker()
	{
		return false;
	}

	
	public function getPickerUrl($language)
	{
		return false; // ArticlePicker not yet implemented
		
		$options = [
			'rootNodes' => $this->rootPages[$language]->id
		];
		
		return \System::getContainer()->get('contao.picker.builder')->getUrl('article', $options);
	}
	
	
	public function getSelectOptions($language)
	{
		$options = array();

		$this->setParentRelations(false);	

		if (!array_key_exists($language, $this->parentRelations)) {
			return $options;
		}
	
		$articles = \ArticleModel::findByPid($this->parentRelations[$language]->id, ['order'=>'sorting']);
		
		if (null === $articles) {
			return $options;
		}
		
		foreach ($articles as $article) {
			$options[] = array(
				'value' => $article->id,
				'label' => $article->title
			);
		}
	
		return $options;
	}
	
	
	public function getCreateUrl($language)
	{
		$this->setParentRelations(false);	
	
		if (!array_key_exists($language, $this->parentRelations)) {
			return null;
		}

		$articles = \ArticleModel::findByPid($this->parentRelations[$language]->id, ['order'=>'sorting']);
	
		if (null === $articles) {
			$query = 'act=copy&mode=2&id='.$this->currentEntity->id.'&pid='.$this->parentRelations[$language]->id;
		} else {
			$query = 'act=copy&mode=1&id='.$this->currentEntity->id.'&pid='.$articles->last()->id;
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
	
	
	private function getAlternative($language, $onlyRoot)
	{
		if (!$onlyRoot) {
			$this->setParentRelations(true);
			
			if (isset($this->parentRelations[$language]) && 'root' != $this->parentRelations[$language]->type) {
				return $this->parentRelations[$language];
			}				
		}
		
		return \PageModel::findFirstPublishedByPid($this->rootPages[$language]->id);
	}
}

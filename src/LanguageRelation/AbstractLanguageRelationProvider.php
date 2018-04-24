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

use Contao\Database;
use Contao\PageModel;


abstract class AbstractLanguageRelationProvider implements LanguageRelationProviderInterface
{
	
 	protected $currentEntity;

	protected $currentLanguage;

	protected $rootPages = array();

	
	protected function getRelations($published, $currentEntity=null)
	{
		if (null === $currentEntity) {
			$currentEntity = $this->currentEntity;
		}

		if ($currentEntity instanceof \PageModel && 'root' == $currentEntity->type) {
			return $this->getRelatedRootPages(
				$currentEntity, 
				$published,
				true
			);
		} elseif (0 < $currentEntity->relation) {
			$model = get_class($currentEntity);
			$table = $model::getTable();
			
			$column = [
				$table.'.relation=?', 
				$table.'.id<>?'
			];
			
			$value = [
				$currentEntity->relation, 
				$currentEntity->id
			];
			
			if ($published) {
				$this->addPublishedCondition($column, $table);
			}
			
			return $model::findBy($column, $value);
		}
	}

	
	protected function setRootLanguages($published, $currentEntity=null)
	{
		if (null === $currentEntity) {
			$currentEntity = $this->currentEntity;
		}

		if ($currentEntity instanceof PageModel) {
			$rootPages = $this->getRelatedRootPages(
				PageModel::findByPk($currentEntity->loadDetails()->rootId),
				$published,
				false
			);
			
			if (null !== $rootPages) {
				foreach ($rootPages as $rootPage) {
					$this->rootPages[$rootPage->language] = $rootPage;
				}
			}
	
			$this->currentLanguage = $currentEntity->rootLanguage;
		}
	}

	
	private function getRelatedRootPages(PageModel $root, $published, $exclSelf)
	{
		$table = PageModel::getTable();

		if ($root->fallbackPage) {
			$column = [
				$table.'.type=\'root\'', 
				'('.$table.'.dns=? OR '.$table.'.id=? OR '.$table.'.fallbackPage=?)'
			];
			
			$value = [
				$root->dns,
				$root->fallbackPage,
				$root->fallbackPage
			];
		} else {
			$column = [
				$table.'.type=\'root\'', 
				'('.$table.'.dns=? OR '.$table.'.fallbackPage=?)'
			];
			
			$value = [
				$root->dns,
				$root->id
			];
		}
			
		if ($exclSelf) {
			$column[] = $table.'.id<>?';
			$value[] = $root->id;
		}
			
		if ($published) {
			$this->addPublishedCondition($column, $table);
		}
			
		return PageModel::findBy($column, $value);
	}
	
	
	private function addPublishedCondition(&$column, $table)
	{
		$time = \Date::floorToMinute();
		
		$column[] = '('.$table.'.start=\'\' OR '.$table.'.start<='.$time.')';
		$column[] = '('.$table.'.stop=\'\' OR '.$table.'.stop>'.($time+60).')';
		$column[] = $table.'.published=\'1\'';
	}

	
	protected function newRelationId()
	{
		$db = \Database::getInstance();

		return $db->execute("SELECT MAX(relation) as relation FROM ".$this->getDcaTable())->relation + 1;
	}

	
	public function initRelation($register, $relationId=false)
	{
		if (0 == $this->currentEntity->relation || false !== $relationId) {
			$model = get_class($this->currentEntity);
			
			$this->currentEntity = $model::findByPk($this->currentEntity->id);

			$this->currentEntity->relation = false !== $relationId ? $relationId : $this->newRelationId();
			$this->currentEntity->language = $this->currentLanguage;

			if ($register) {
				$this->currentEntity->save();
			}
		}
	}

	
	public function setRelation($language, $id, $register)
	{
		$this->initRelation($register);
		
		$model = get_class($this->currentEntity);
		
		$newRelation = $model::findByPk($id);
	
		if (null === $newRelation) {
			return false;
		}
	
		$newRelation->relation = $this->currentEntity->relation;
		$newRelation->language = $language;

		if ($register) {
			$newRelation->save();
		}
		
		return $newRelation;
	}

	
	public function removeRelation($id, $register)
	{
		$model = get_class($this->currentEntity);
		
		$removeRelation = $model::findByPk($id);
		$removeRelation->relation = 0;

		if ($register) {
			$removeRelation->save();	
		}
	}
	
	
	public function attachTo($relation)
	{
		if (0 < $relation->getRelationId()) {
			$this->initRelation(true, $relation->getRelationId());
		} else {
			$this->setRelation($relation->getCurrentLanguage(), $relation->getCurrentId(), true);
		}
	
		return $this->getRelations(false);
	}
	
	
	public function getRelationId()
	{
		return $this->currentEntity->relation;
	}
	
	
	public function getCurrentId()
	{
		return $this->currentEntity->id;
	}
	
	
 	/**
     * Set query name to n/a
     */	
	public function getQueryName()
	{
		return 'n/a';
	}
	
	
    /**
     * Dummy for provider without frontend support
	 */
	public function getFrontendUrl($related)
	{
		return null;
	}


    /**
     * Dummy for provider without frontend support
	 */
	public function getAlternativeUrl($language, $onlyRoot)
	{
		return null;
	}


    /**
     * Dummy for provider without frontend support
	 */
	public function getAlternativeTitle($language, $onlyRoot)
	{
		return null;
	}


    /**
     * Dummy for legacy reasons (not all contexts (tables) are supported by the picker)
	 */
	 public function getPickerUrl($language)
	{
		return null;
	}
	
	
    /**
     * Dummy for legacy reasons (not all contexts (tables) are supported by the picker)
	 */
	 public function getSelectOptions($language)
	{
		return null;
	}
}

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

	protected $rootPages;

	
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

	
	protected function setRootLanguages($page, $published)
	{
		if ($page instanceof PageModel) {
			$rootPages = $this->getRelatedRootPages(
				PageModel::findByPk($page->loadDetails()->rootId),
				$published,
				false
			);
			
			if (null !== $rootPages) {
				foreach ($rootPages as $rootPage) {
					$this->rootPages[$rootPage->language] = $rootPage;
				}
			}
	
			$this->currentLanguage = $page->rootLanguage;
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

	
	protected function newRelationId($table)
	{
		$db = \Database::getInstance();

		return $db->execute("SELECT MAX(relation) as relation FROM ".$table)->relation + 1;
	}

	
	public function setRelation($language, $id, $register)
	{
		$model = get_class($this->currentEntity);
		
		if (1 > $this->currentEntity->relation) {
			$this->currentEntity = $model::findByPk($this->currentEntity->id);

			$this->currentEntity->relation = $this->newRelationId($this->getDcaTable());
			$this->currentEntity->language = $this->currentLanguage;

			if ($register) {
				$this->currentEntity->save();
			}
		}
		
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

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
use Contao\CalendarModel;
use Contao\PageModel;



class CalendarLanguageRelationProvider extends AbstractLanguageRelationProvider implements LanguageRelationProviderInterface
{
	
	/**
     * {@inheritdoc}
     */	
	public function getContext()
	{
		return 'calendar';
	}
	
	/**
     * {@inheritdoc}
     */	
	public function getDcaTable()
	{
		return 'tl_calendar';
	}
	
	
	public function build($id, $published)
	{
		$this->currentEntity = CalendarModel::findByPk($id);

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
	
		$calendars = CalendarModel::findBy([CalendarModel::getTable().'.jumpTo IN (\''.implode("','", Database::getInstance()->getChildRecords($this->rootPages[$language]->id, 'tl_page')).'\')'], null);

		if (null === $calendars) {
			return $options;
		}
		
		foreach ($calendars as $calendar) {
			$options[] = array(
				'value' => $calendar->id,
				'label' => $calendar->title
			);
		}
	
		return $options;
	}
	
	
	public function getCreateUrl($language)
	{
		return false; // Calendars shouldn't be copied to another language (?)
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

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
use Contao\CalendarEventsModel;
use Contao\CalendarModel;
use Contao\PageModel;



class CalendarEventsLanguageRelationProvider extends AbstractLanguageRelationProvider implements LanguageRelationProviderInterface
{
	
	/**
     * {@inheritdoc}
     */	
	public function getContext()
	{
		return 'calendarevents';
	}
	
	/**
     * {@inheritdoc}
     */	
	public function getDcaTable()
	{
		return 'tl_calendar_events';
	}
	
	
	/**
     * {@inheritdoc}
     */	
	public function getQueryName()
	{
		return 'events';
	}


	public function build($id, $published)
	{
		$this->currentEntity = CalendarEventsModel::findByPk($id);

		if (null === $this->currentEntity) {
			return null;
		} 
		
		$this->parentEntity = CalendarModel::findByPk($this->currentEntity->pid);
	
		$this->setRootLanguages(PageModel::findByPk($this->parentEntity->jumpTo), $published);
		
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

	
	public function getSelectOptions($language)
	{
		$options = array();

		$this->setParentRelations(false);	

		if (!array_key_exists($language, $this->parentRelations)) {
			return $options;
		}
	
		$events = CalendarEventsModel::findByPid($this->parentRelations[$language]->id, ['order'=>'startTime DESC']);
		
		if (null === $events) {
			return $options;
		}
		
		foreach ($events as $event) {
			$options[] = array(
				'value' => $event->id,
				'label' => $event->title
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
		
		return Backend::addToUrl('act=copy&mode=2&id='.$this->currentEntity->id.'&pid='.$this->parentRelations[$language]->id);
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
			$this->setParentRelations(false);
			
			if (!isset($this->alternativeRelations)) {
				$this->alternativeRelations = array();
				
				$relation = $this->getRelations(true, PageModel::findByPk($this->parentEntity->jumpTo));
			
				if (null !== $relation) {
					foreach ($relation as $model) {
						$this->alternativeRelations[$model->language] = $model;
					}
				}
			}
	
			if (isset($this->alternativeRelations[$language]) && 'root' != $this->alternativeRelations[$language]->type) {
				return $this->alternativeRelations[$language];
			}
		}
	
		return PageModel::findFirstPublishedByPid($this->rootPages[$language]->id);
	}
}

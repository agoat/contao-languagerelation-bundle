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

use Contao\Model;
use Contao\Model\Collection;


class LanguageRelation
{

	private $provider;
	
	private $currentLanguage;

	private $languages;
	
	private $related;
	
	
	public function __construct(LanguageRelationProviderInterface $provider, string $current, array $languages, $related)
	{
		$this->provider = $provider;
		$this->currentLanguage = $current;
		$this->languages = $languages;
		$this->related = array();
		
		if ($related instanceof Collection) {
			foreach ($related as $model) {
				if ($model instanceof Model) {
					$this->related[$model->language] = $model;
				}
			}
		}
	}
	
	
	public function getContext()
	{
		return $this->provider->getContext();
	}

	
	public function getTableName()
	{
		return $this->provider->getDcaTable();
	}

	
	public function getLanguages()
	{
		return $this->languages;
	}

	
	public function getRelatedLanguages()
	{
		return array_keys($this->related);
	}

	
	public function getCurrentLanguage()
	{
		return $this->currentLanguage;
	}

	
	public function isCurrent($language)
	{
		return $language == $this->currentLanguage;
	}

	
	public function hasRelations()
	{
		return !empty($this->related);
	}

	
	public function isRelated($language)
	{
		return array_key_exists($language, $this->related);
	}

	
	public function isRegistered($language)
	{
		if ($this->isRelated($language)) {
			return !$this->related[$language]->isModified();
		}
	}

	
	public function getId($language)
	{
		return $this->related[$language]->id;
	}
	
	
	public function getTitle($language)
	{
		return $this->related[$language]->title?: $this->related[$language]->headline;
	}

	
	public function getFrontendUrl($language, $onlyRoot)
	{
		if (in_array($language, $this->languages) && $language != $this->currentLanguage) {
			if ($this->isRelated($language)) {
				return $this->provider->getFrontendUrl($this->related[$language]);
			} else {
				return $this->provider->getAlternativeUrl($language, $onlyRoot);
			}
		}
		
		return null;
	}
	
	
	public function getFrontendTitle($language, $onlyRoot)
	{
		if (in_array($language, $this->languages) && $language != $this->currentLanguage) {
			if ($this->isRelated($language)) {
				return $this->getTitle($language);
			} else {
				return $this->provider->getAlternativeTitle($language, $onlyRoot);
			}
		}
		
		return null;
	}

	
	public function getEditUrl($language)
	{
		if ($this->isRelated($language)) {
			return $this->provider->getEditUrl($this->related[$language]);
		}
		
		return null;
	}

	
	public function getViewUrl($language)
	{
		if ($this->isRelated($language)) {
			return $this->provider->getViewUrl($this->related[$language]);
		}
		
		return null;
	}

	
	public function supportsPicker()
	{
		return $this->provider->supportsPicker();
	}
	
	
	public function getPickerUrl($language)
	{
		if (in_array($language, $this->languages) && $language != $this->currentLanguage) {
			return $this->provider->getPickerUrl($language);
		}
		
		return null;
	}
	
	
	public function getSelectOptions($language)
	{
		if (in_array($language, $this->languages) && $language != $this->currentLanguage) {
			return $this->provider->getSelectOptions($language);
		}
		
		return null;
	}
	
	
	public function getCreateUrl($language)
	{
		if (in_array($language, $this->languages) && $language != $this->currentLanguage) {
			return $this->provider->getCreateUrl($language);
		}
		
		return null;
	}
	
	
	public function setRelation($language, $id, $register=false)
	{
		if (in_array($language, $this->languages) && $language != $this->currentLanguage) {
			if ($this->isRelated($language) && $id == $this->related[$language]->id) {
				return false;
			} 
			
			$this->removeRelation($language, $register);
			
			$newRelation = $this->provider->setRelation($language, $id, $register);
			
			if ($newRelation instanceof \Model) {
				$this->related[$language] = $newRelation;
			}
			
			return true;
		}
		
		return false;
	}
	
	public function removeRelation($language, $register=false)
	{
		if ($this->isRelated($language)) {
			$this->provider->removeRelation($this->related[$language]->id, $register);
			unset($this->related[$language]);
			
			return true;
		}
		
		return false;
	}
	
	

}

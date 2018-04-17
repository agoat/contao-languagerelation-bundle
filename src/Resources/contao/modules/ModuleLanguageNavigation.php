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
 
namespace Agoat\LanguageRelationBundle\Contao;

use Contao\System;
use Contao\Environment;
use Contao\Module;
use Contao\CoreBundle\Exception\AccessDeniedException;
use Agoat\LanguageRelationBundle\LanguageRelation\LanguageRelationGenerator;

/**
 * Datacontainer class
 */
class ModuleLanguageNavigation extends Module
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_navigation';


	/**
	 * Do not display the module if there are no menu items
	 *
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			/** @var BackendTemplate|object $objTemplate */
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### ' . Utf8::strtoupper($GLOBALS['TL_LANG']['FMD']['navigation'][0]) . ' ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		$strBuffer = parent::generate();

		return empty($this->Template->items) ? '' : $strBuffer;
	}
	
	
	/**
	 * Generate the module
	 */
	protected function compile()
	{
		/** @var PageModel $objPage */
		global $objPage;

		/** @var LanguageRelation */
		$languageRelation = \System::getContainer()->get('contao.language.relation')->buildFromRequest($objPage);
	
		if (null === $languageRelation) {
			return;
		}

		System::loadLanguageFile('languages');
		
		$labels = array();
		$items = array();
		$links = array();

		if ($this->customLanguages) {
			foreach ((array) \StringUtil::deserialize($this->languageLabels) as $label) {
				$labels[$label['language']] = $label['label'];
			}
		}
	
		foreach ($languageRelation->getLanguages() as $language) {
			if (!$languageRelation->isRelated($language) && !$languageRelation->isCurrent($language) && $this->hideAlternative) {
				continue;
			}
			
			if ($languageRelation->isRelated($language)) {
				$links[] = array(
					'hreflang' => $language,
					'href' => $languageRelation->getFrontendUrl($language, $this->onlyRoot),
				);
			}

			if ($languageRelation->isCurrent($language) && $this->hideActive) {
				continue;
			}
			
			$items[$language] = array(
				'link' => isset($labels[$language]) ? $labels[$language] : $GLOBALS['TL_LANG']['LNG'][$language],
				'hreflang' => $language,
				'isActive' => $languageRelation->isCurrent($language),
				'href' => $languageRelation->getFrontendUrl($language, $this->onlyRoot),
				'title' => $languageRelation->getFrontendTitle($language, $this->onlyRoot),
				'isAlternative' => !$languageRelation->isRelated($language) && !$languageRelation->isCurrent($language),
			);
		}

		
		// Don't render the module if there is no menu item
		if (empty($items)) {
			return;
		}

		if (!empty($links)) {
			$this->addAlternateLinks($links);
		}

		if (!empty($labels)) {
			$items = $this->sortLanguages($labels, $items);
		}
		
		$this->Template->items = $this->renderLanguageNavigation($items);

		$this->Template->request = ampersand(\Environment::get('indexFreeRequest'));
		$this->Template->skipId = 'skipNavigation' . $this->id;
		$this->Template->skipNavigation = \StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['skipNavigation']);
	}
	
	
	protected function sortLanguages($labels, $items)
	{
		return array_replace(array_intersect_key($labels, $items), $items);
	}
	
	
	protected function renderLanguageNavigation($items)
	{
		/** @var FrontendTemplate|object $objTemplate */
		$objTemplate = new \FrontendTemplate($this->navigationTpl);
		
		$objTemplate->cssID = $this->cssID;
		$objTemplate->items = $items;
	
		return $objTemplate->parse();
	}
	
	
	protected function addAlternateLinks($items)
	{
		$GLOBALS['TL_HEAD'][] = '<link rel="alternate" hreflang="x" href="'.Environment::get('requestUri').'">';
		
		foreach ($items as $item) {
			$GLOBALS['TL_HEAD'][] = '<link rel="alternate" hreflang="'.$item['hreflang'].'" href="'.$item['href'].'">';
		}
	}
}

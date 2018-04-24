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
 
namespace Agoat\LanguageRelationBundle\DataContainer;

use Agoat\LanguageRelationBundle\LanguageRelation\LanguageRelationGenerator;
use Contao\Model;


/**
 * Add Language relation widget field
 */
abstract class AbstractConstructor
{
	/**
     * @var table
     */
	protected $table;
	
	public function __construct ($table)
	{
		$this->table = $table;
	}

	abstract public function buildDca ();

	
	protected function createRelationButton($languageRelation)
	{
		
		$GLOBALS['TL_DCA'][$this->table]['list']['sorting']['panelLayout'] .= ',related';
		
		$GLOBALS['TL_DCA'][$this->table]['list']['sorting']['panel_callback']['related'] = function () use ($languageRelation) {
		   $list = '';
			$markup = '
	<div class="header_switchLanguage" style="position: absolute; margin: 2px 0 0 12px;transform: translateY(50px);">
		<svg viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg" style="width: 16px"><path d="M782 1078q-1 3-12.5-.5t-31.5-11.5l-20-9q-44-20-87-49-7-5-41-31.5t-38-28.5q-67 103-134 181-81 95-105 110-4 2-19.5 4t-18.5 0q6-4 82-92 21-24 85.5-115t78.5-118q17-30 51-98.5t36-77.5q-8-1-110 33-8 2-27.5 7.5t-34.5 9.5-17 5q-2 2-2 10.5t-1 9.5q-5 10-31 15-23 7-47 0-18-4-28-21-4-6-5-23 6-2 24.5-5t29.5-6q58-16 105-32 100-35 102-35 10-2 43-19.5t44-21.5q9-3 21.5-8t14.5-5.5 6 .5q2 12-1 33 0 2-12.5 27t-26.5 53.5-17 33.5q-25 50-77 131l64 28q12 6 74.5 32t67.5 28q4 1 10.5 25.5t4.5 30.5zm-205-486q3 15-4 28-12 23-50 38-30 12-60 12-26-3-49-26-14-15-18-41l1-3q3 3 19.5 5t26.5 0 58-16q36-12 55-14 17 0 21 17zm698 129l63 227-139-42zm-1108 800l694-232v-1032l-694 233v1031zm1241-317l102 31-181-657-100-31-216 536 102 31 45-110 211 65zm-503-962l573 184v-380zm311 1323l158 13-54 160-40-66q-130 83-276 108-58 12-91 12h-84q-79 0-199.5-39t-183.5-85q-8-7-8-16 0-8 5-13.5t13-5.5q4 0 18 7.5t30.5 16.5 20.5 11q73 37 159.5 61.5t157.5 24.5q95 0 167-14.5t157-50.5q15-7 30.5-15.5t34-19 28.5-16.5zm448-1079v1079l-774-246q-14 6-375 127.5t-368 121.5q-13 0-18-13 0-1-1-3v-1078q3-9 4-10 5-6 20-11 106-35 149-50v-384l558 198q2 0 160.5-55t316-108.5 161.5-53.5q20 0 20 21v418z"/></svg>
		<strong>%s: </strong> 
		<span> 
			<span>
				%s
			</span>
		</span>
	</div>
	';

			foreach ($languageRelation->getRelatedLanguages() as $language) {
				$list .= sprintf(
					' | <span><a href="%s" title="%s">%s</a></span>',
					$languageRelation->getViewUrl($language),
					//sprintf($GLOBALS['TL_LANG']['MSC']['switchLanguageTo'][1], $language), 'German - Deutsch (de)'
					$language,
					$language.' ('.$languageRelation->getTitle($language).')'
				);
			}

			return sprintf(
				$markup,
				//$GLOBALS['TL_LANG']['MSC']['switchLanguage'],
				$languageRelation->getCurrentLanguage(),
				//$GLOBALS['TL_LANG']['MSC']['switchLanguageTo'][0].'related languages',
				$list
			);
		};
	}
}

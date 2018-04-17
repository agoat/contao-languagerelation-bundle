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


/**
 * Add Language relation widget field
 */
class FallbackPageFieldConstructor extends AbstractConstructor
{
	public function buildDca()
	{
		$GLOBALS['TL_DCA'][$this->table]['config']['onload_callback'][] = function(\DataContainer $dc) {
			if (isset($GLOBALS['TL_DCA'][$dc->table]['palettes']['root'])) {
				$GLOBALS['TL_DCA'][$dc->table]['palettes']['__selector__'][] = 'fallback';
				$GLOBALS['TL_DCA'][$dc->table]['subpalettes']['fallback'] = 'fallbackPage';
				$GLOBALS['TL_DCA'][$dc->table]['fields']['fallback']['eval']['submitOnChange'] = true;
			}
		};

		$GLOBALS['TL_DCA'][$this->table]['fields']['fallbackPage'] = array
		(
			'label'			=> &$GLOBALS['TL_LANG'][$this->table]['fallbackPage'],
			'exclude'		=> true,
			'search'		=> true,
			'inputType'		=> 'select',
			'eval'			=> array('mandatory'=>false, 'doNotCopy'=>true, 'includeBlankOption'=>true, 'maxlength'=>128, 'tl_class'=>'w50'),
			'options_callback' => function(\DataContainer $dc) {
				$fallbackPages = \PageModel::findByFallback(1);
				
				if (null === $fallbackPages) {
					return;
				}
				
				$options = array();
				
				foreach ($fallbackPages as $page) {
					if ($page->id != $dc->activeRecord->id && !$page->fallbackPage) {
						$options[$page->id] = $page->title;
					}
				}
				
				return $options;
			},
			'sql'			=> "varchar(128) NOT NULL default ''"
		);
	}
}
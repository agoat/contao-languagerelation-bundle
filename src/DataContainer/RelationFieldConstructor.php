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
class RelationFieldConstructor extends AbstractConstructor
{

	public function buildDca()
	{
		$GLOBALS['TL_DCA'][$this->table]['config']['onload_callback'][] = function(\DataContainer $dc) {
			$palettes = array_diff(
				array_keys($GLOBALS['TL_DCA'][$dc->table]['palettes']), 
				array('__selector__', 'root')
			);
	
			foreach ($palettes as $palette) {
				$GLOBALS['TL_DCA'][$dc->table]['palettes'][$palette] = preg_replace(
					['/{title_legend}.*?;/'], 
					['$0{relation_legend},relation;'], 
					$GLOBALS['TL_DCA'][$dc->table]['palettes'][$palette]
				);
			}
			
			if (isset($GLOBALS['TL_DCA'][$dc->table]['palettes']['root'])) {
				$GLOBALS['TL_DCA'][$dc->table]['palettes']['__selector__'][] = 'fallback';
				$GLOBALS['TL_DCA'][$dc->table]['subpalettes']['fallback'] = 'fallbackPage';
				$GLOBALS['TL_DCA'][$dc->table]['fields']['fallback']['eval']['submitOnChange'] = true;
			}
		};
		
		if (!isset($GLOBALS['TL_DCA'][$this->table]['fields']['language'])) {
			$GLOBALS['TL_DCA'][$this->table]['fields']['language'] = array(
				'sql'	=> "varchar(5) NOT NULL default ''"
			);
		}

		$GLOBALS['TL_DCA'][$this->table]['fields']['relation'] = array(
			'label'		=> &$GLOBALS['TL_LANG'][$this->table]['relation'],
			'exclude'	=> true,
			'search'	=> true,
			'inputType'	=> 'relationWizard',
			'eval'		=> array('doNotSaveEmpty'=>true, 'helpwizard'=>false, 'doNotCopy'=>true, 'tl_class'=>'clr'),
			'sql'		=> "int(10) unsigned NOT NULL default '0'"
		);
	}
}
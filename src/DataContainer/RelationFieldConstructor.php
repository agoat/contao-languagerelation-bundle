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
use Contao\System;


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
		
		$GLOBALS['TL_DCA'][$this->table]['config']['oncopy_callback'][] = function($id, \DataContainer $dc) {
			$old = System::getContainer()->get('contao.language.relation')->buildFromDca($dc);
			$new = System::getContainer()->get('contao.language.relation')->buildFromDca($dc, false, $id);
		
			if ($new->getCurrentLanguage() != $old->getCurrentLanguage()) {
				$new->attachTo($old);
			}
		};

		
		if (!isset($GLOBALS['TL_DCA'][$this->table]['fields']['language'])) {
			$GLOBALS['TL_DCA'][$this->table]['fields']['language'] = array(
				'eval'	=> array('doNotCopy'=>true),
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

		$GLOBALS['TL_LANG'][$this->table]['relation'] = $GLOBALS['TL_LANG']['DCA']['relation'];
		$GLOBALS['TL_LANG'][$this->table]['relation_legend'] = $GLOBALS['TL_LANG']['DCA']['relation_legend'];
	}
}
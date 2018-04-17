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



// Add loadDataContainer hook
$GLOBALS['TL_HOOKS']['loadDataContainer'][] = array('Agoat\\LanguageRelationBundle\\DataContainer\\LanguageRelationAssembler','buildDca');

// Add executePostActions hook
$GLOBALS['TL_HOOKS']['executePostActions'][] = array('Agoat\\LanguageRelationBundle\\Contao\\Ajax','postActions');

// Frontend module
$GLOBALS['FE_MOD']['navigationMenu']['languagenav'] = 'Agoat\LanguageRelationBundle\Contao\ModuleLanguageNavigation';

// Back end form fields (widgets)
$GLOBALS['BE_FFL']['relationWizard'] = '\Agoat\LanguageRelationBundle\Contao\LanguageRelationWizard';
$GLOBALS['BE_FFL']['labelWizard'] = '\Agoat\LanguageRelationBundle\Contao\LanguageLabelWizard';


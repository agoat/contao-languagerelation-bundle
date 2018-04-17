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

 
// Palettes
$GLOBALS['TL_DCA']['tl_module']['palettes']['languagenav']  = '{title_legend},name,headline,type;{nav_legend},hideActive,hideAlternative,onlyRoot;{language_legend},customLanguages,labels;{template_legend:hide},navigationTpl,customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'customLanguages';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['customLanguages']  = 'languageLabels';

// Fields
$GLOBALS['TL_DCA']['tl_module']['fields']['hideActive'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['hideActive'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'w50 m12'),
	'sql'                     => "char(1) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['hideAlternative'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['hideAlternative'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'w50 m12'),
	'sql'                     => "char(1) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['onlyRoot'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['onlyRoot'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'w50 m12'),
	'sql'                     => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['customLanguages'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['customLanguages'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('submitOnChange'=>true, 'tl_class'=>'w50 m12'),
	'sql'                     => "char(1) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['languageLabels'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['languageLabels'],
	'exclude'                 => true,
	'inputType'               => 'labelWizard',
	'eval'                    => array('allowHtml'=>true, 'tl_class'=>'clr'),
	'sql'                     => "blob NULL"
);


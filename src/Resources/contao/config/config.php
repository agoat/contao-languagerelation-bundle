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
$GLOBALS['TL_HOOKS']['loadDataContainer'][] = array('Agoat\\LanguageRelationBundle\\Contao\\DataContainer','onLoadDataContainer');

dump('config');
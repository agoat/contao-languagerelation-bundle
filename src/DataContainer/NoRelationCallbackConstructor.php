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
use Contao\Image;


/**
 * Add Language relation widget field
 */
class NoRelationCallbackConstructor extends AbstractConstructor
{

	public function buildDca()
	{
		if (4 == $GLOBALS['TL_DCA'][$this->table]['list']['sorting']['mode']) {
			$callback = $GLOBALS['TL_DCA'][$this->table]['list']['sorting']['child_record_callback'];
			$GLOBALS['TL_DCA'][$this->table]['list']['sorting']['child_record_callback'] = function($row) use ($callback) {
				if (\is_array($callback))
				{
					$strClass = $callback[0];
					$strMethod = $callback[1];
					$static = System::importStatic($strClass);
					$args = $static->$strMethod($row);
				}
				elseif (\is_callable($callback))
				{
					$args = $callback($row);
				}			
	
				$hint = ' <svg viewBox="0 0 14 14" xmlns="http://www.w3.org/2000/svg" style="width: 12px;vertical-align:middle;"><circle cx="5.966" cy="6.026" r="5.871" style="fill:#a2a2a2;"/><path d="M7.18,8.658l0,1.997l-2.428,0l0,-1.997l2.428,0Zm-0.679,-0.778l-1.101,0l-0.586,-3.994l0,-2.49l2.283,0l0,2.49l-0.596,3.994Z" style="fill:#fff;fill-rule:nonzero;"/></svg>';
				
				if (0 == $row['relation'] && 'root' != $row['type']) {
					if (\is_array($args)) {
						$args[] = $hint;
					} else {
						$args .= $hint;
					}
				}
				return $args;
			};
		} else {
			$callback = $GLOBALS['TL_DCA'][$this->table]['list']['label']['label_callback'];
			$GLOBALS['TL_DCA'][$this->table]['list']['label']['label_callback'] = function($row, $label, $dc, $args) use ($callback) {
				if (\is_array($callback))
				{
					$strClass = $callback[0];
					$strMethod = $callback[1];
					$static = System::importStatic($strClass);
					$args = $static->$strMethod($row, $label, $dc, $args);
				}
				elseif (\is_callable($callback))
				{
					$args = $callback($row, $label, $this, $args);
				}			
				
				$hint = ' <svg viewBox="0 0 14 14" xmlns="http://www.w3.org/2000/svg" style="width: 12px;vertical-align:middle;"><circle cx="5.966" cy="6.026" r="5.871" style="fill:#a2a2a2;"/><path d="M7.18,8.658l0,1.997l-2.428,0l0,-1.997l2.428,0Zm-0.679,-0.778l-1.101,0l-0.586,-3.994l0,-2.49l2.283,0l0,2.49l-0.596,3.994Z" style="fill:#fff;fill-rule:nonzero;"/></svg>';
				
				if (0 == $row['relation'] && 'root' != $row['type']) {
					if (\is_array($args)) {
						$args[] = $hint;
					} else {
						$args .= $hint;
					}
				}
				return $args;
			};
		}
	}
}

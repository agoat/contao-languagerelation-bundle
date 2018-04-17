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



/**
 * Add Language relation widget field
 */
class EventCalendarViewConstructor extends AbstractConstructor
{

	public function buildDca ()
	{
		$GLOBALS['TL_DCA'][$this->table]['config']['onload_callback'][] = function(\DataContainer $dc) {
			if ('edit' == $_GET['act']) {
				return;
			}
			
			/** @var LanguageRelation */
			$languageRelation = \System::getContainer()->get('contao.language.relation')->buildFromTableAndId('tl_calendar', $dc->id);

			if (null !== $languageRelation && $languageRelation->hasRelations()) {
				$this->createRelationButton($languageRelation);
			}
		};
	}
}

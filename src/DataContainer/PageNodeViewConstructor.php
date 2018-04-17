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
use Contao\Session;
use Contao\Backend;


/**
 * Add language switch button
 */
class PageNodeViewConstructor extends AbstractConstructor
{

	public function buildDca ()
	{
		$GLOBALS['TL_DCA'][$this->table]['config']['onload_callback'][] = function(\DataContainer $dc) {
			if ('edit' == $_GET['act']) {
				return;
			}

			$node = Session::getInstance()->get('tl_page_node');
			
			if (0 == $node) {
				return;
			}

			/** @var LanguageRelation */
			$languageRelation = \System::getContainer()->get('contao.language.relation')->buildFromTableAndId('tl_page', $node);

			if (null !== $languageRelation && $languageRelation->hasRelations()) {
				$this->createRelationButton($languageRelation);
			}

		};
	}
}

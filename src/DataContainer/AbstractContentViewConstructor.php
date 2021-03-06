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
use Contao\Backend;


/**
 * Add Language relation widget field
 */
class AbstractContentViewConstructor extends AbstractConstructor
{

	public function buildDca ()
	{

		$GLOBALS['TL_DCA'][$this->table]['config']['onload_callback'][] = function(\DataContainer $dc) {
			if ($_GET['act'] && 'edit' == $_GET['act']) {
				return;
			}

			if ($_GET['mode']) {
				$content = \ContentModel::findByPk($dc->id);
				$id = $content->pid;
			} else {
				$id = $dc->id;
			}

			/** @var LanguageRelation */
			$languageRelation = \System::getContainer()->get('contao.language.relation')->buildFromDca($dc, true);

			if (null !== $languageRelation && $languageRelation->hasRelations()) {
				$this->createRelationButton($languageRelation);
			}
		};

	}
}

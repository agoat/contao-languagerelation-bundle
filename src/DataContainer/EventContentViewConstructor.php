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
class EventContentViewConstructor
{

	private $table;
	
	
	public function __construct ($table)
	{
		$this->table = $table;
	}

	public function buildDca ()
	{


		$GLOBALS['TL_DCA'][$this->table]['config']['onload_callback'][] = function(\DataContainer $dc) {
			if ('calendar' != $_GET['do']) {
				return;
			}
		
				//	$generator = new LanguageRelationGenerator();
				//	$options = $generator->getOptions($dc);
	
		dump($dc);


		};

	}
}
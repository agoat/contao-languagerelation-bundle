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

use Contao\System;
use Contao\Database;


/**
 * Maintenance language relation configuration
 */
class TableMaintenanceWorker
{

	/**
	 * Reset dangling relations
	 *
	 * @param string $table
	 */
	public function resetDanglingRelations ($table)
	{
		if (System::getContainer()->get('contao.language.relation')->hasProviderForTable($table)) {
			$db = Database::getInstance();
			
			$db->execute("UPDATE $table SET $table.relation=0 WHERE $table.relation IN (SELECT relation FROM (SELECT * FROM $table) AS relation GROUP BY relation HAVING count(*) < 2)");
		}
	}
}


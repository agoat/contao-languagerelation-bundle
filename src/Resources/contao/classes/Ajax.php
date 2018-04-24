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
 
namespace Agoat\LanguageRelationBundle\Contao;
 
use Contao\CoreBundle\Exception\ResponseException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Contao\Backend;


/**
 * Ajax class
 */
class Ajax extends Backend
{

	/**
	 * Handle Achive-, Post- and Statictree AJAX requests
	 *
	 * @param string        $strAction
	 * @param DataContainer $dc
	 *
	 * @thows BadRequestHttpException
	 */
	public function postActions ($strAction, $dc)
	{
		switch ($strAction)
		{
			case 'reloadLanguageRelation':
				$intId = \Input::get('id');
				$strLanguage = \Input::post('language');
				$strField = $dc->inputName = \Input::post('name');

				// Handle the keys in "edit multiple" mode
				if (\Input::get('act') == 'editAll')
				{
					$intId = preg_replace('/.*_([0-9a-zA-Z]+)$/', '$1', $strField);
					$strField = preg_replace('/(.*)_[0-9a-zA-Z]+$/', '$1', $strField);
				}

				$dc->field = $strField;

				// The field does not exist
				if (!isset($GLOBALS['TL_DCA'][$dc->table]['fields'][$strField]))
				{
					$this->log('Field "' . $strField . '" does not exist in DCA "' . $dc->table . '"', __METHOD__, TL_ERROR);
					throw new BadRequestHttpException('Bad request');
				}

				$objRow = null;
				$varValue = null;

				// Call the load_callback
				if (is_array($GLOBALS['TL_DCA'][$dc->table]['fields'][$strField]['load_callback']))
				{
					foreach ($GLOBALS['TL_DCA'][$dc->table]['fields'][$strField]['load_callback'] as $callback)
					{
						if (is_array($callback))
						{
							$this->import($callback[0]);
							$varValue = $this->{$callback[0]}->{$callback[1]}($varValue, $dc);
						}
						elseif (is_callable($callback))
						{
							$varValue = $callback($varValue, $dc);
						}
					}
				}

				// Set the new value
				$varValue = \Input::post('value', true);
dump($dc);
				// Convert the selected values
				$languageRelation = \System::getContainer()->get('contao.language.relation')->buildFromDca($dc);
dump($languageRelation);
				if (empty($varValue)) {
					$languageRelation->removeRelation($strLanguage);
				} else {
					$varValue = trim($varValue);
					$languageRelation->setRelation($strLanguage, $varValue);
				}

				/** @var relationWizard $strClass */
				$strClass = $GLOBALS['BE_FFL']['relationWizard'];

				/** @var relationWizard $objWidget */
				$objWidget = new $strClass($strClass::getAttributesFromDca($GLOBALS['TL_DCA'][$dc->table]['fields'][$strField], $dc->inputName, $varValue, $strField, $dc->table, $dc));
				throw new ResponseException($this->convertToResponse($objWidget->generate($strLanguage)));
		}
	}

	/**
	 * Convert a string to a response object
	 *
	 * @param string $str
	 *
	 * @return Response
	 */
	protected function convertToResponse($str)
	{
		return new Response(\Controller::replaceOldBePaths($str));
	}
	
}

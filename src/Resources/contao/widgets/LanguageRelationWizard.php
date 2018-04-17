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

use Agoat\LanguageRelationBundle\LanguageRelation\LanguageRelationGenerator;
use Contao\Widget;
use Contao\System;
use Contao\Backend;
use Contao\Image;


/**
 * Provide methods to handle language relation options
 */
class LanguageRelationWizard extends Widget
{

	/**
	 * Submit user input
	 * @var boolean
	 */
	protected $blnSubmitInput = true;

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'be_widget';


	/**
	 * Save the relations to the database
	 *
	 * @return string
	 */
	protected function validator($varInput)
	{
		if (!is_array($varInput))
		{
			return;
		}
		
		/** @var LanguageRelation */
		$languageRelation = System::getContainer()->get('contao.language.relation')->buildFromDca($this->objDca);
	
		foreach ($varInput as $language=>$id) {
			if (empty($id)) {
				$languageRelation->removeRelation($language, true);
			} else {
				$varValue = trim($id);
				$languageRelation->setRelation($language, $id, true);
			}
		}
		
		return '';
	}
	
	
	/**
	 * Generate the widget and return it as string
	 *
	 * @return string
	 */
	public function generate($language=null)
	{
		/** @var LanguageRelation */
		$languageRelation = System::getContainer()->get('contao.language.relation')->buildFromDca($this->objDca);

		if (null === $languageRelation) {
			return '<div style="padding:6px 0;"><div>Unable to find a language relation provider that can handle this context!!</div></div>';
		}

		if (!empty($language)) {
			return $languageRelation->supportsPicker()?
				$this->renderLanguagePickerBlock($languageRelation, $language, false):
				$this->renderLanguageSelectBlock($languageRelation, $language, false);
		} 
		
		foreach ($languageRelation->getLanguages() as $language)
		{
 			$block = $languageRelation->supportsPicker()?
				$this->renderLanguagePickerBlock($languageRelation, $language):
				$this->renderLanguageSelectBlock($languageRelation, $language);
			
			if ($languageRelation->isCurrent($language)) {
				$html = $block.$html;
			} else {
				$html = $html.$block;
			}
		}
		
		return '<div id="ctrl_'.$this->strId.'" style="padding:6px 0;">'.$html.'</div>';
 	}
	
	
	private function renderLanguagePickerBlock($languageRelation, $language, $outerBlock=true)
	{
		$block = '';
		
		if ($outerBlock) {
			$block .= $this->startBlock($language);
		}
		
		if ($languageRelation->isCurrent($language)) {
			$block .= $this->addLanguageName($language);
			$block .= $this->addTitle($this->objDca->activeRecord->title, $GLOBALS['TL_LANG']['MSC']['currentLanguage']);
			
		} elseif ($languageRelation->isRelated($language)) {
			$block .= $this->addHiddenField($language, $id ?: $languageRelation->getId($language));
			$block .= $this->addLanguageName($language);
			$block .= $this->addTitle($languageRelation->getTitle($language));
			$block .= $this->addEditIcon($language, $languageRelation->isRegistered($language), $languageRelation->getEditUrl($language), $languageRelation->getTableName());
			
			$block .= ' <span style="margin-left: 5px">';
			$block .= $this->addSelectButton($language, $languageRelation->getPickerUrl($language));
			$block .= '</span>';
			
		} else {
			$block .= $this->addHiddenField($language);
			$block .= $this->addLanguageName($language);
			$block .= $this->addTitle('-', $GLOBALS['TL_LANG']['MSC']['noRelation']);

			$block .= ' <span style="margin-left: 5px">';
			$block .= $this->addSelectButton($language, $languageRelation->getPickerUrl($language));
			$block .= $this->addCreateButton($language, $languageRelation->getCreateUrl($language));
			$block .= '</span>';
		}
		
		if ($outerBlock) {
			$block .= $this->stopBlock();
		}
		
		return $block;
	}


	private function renderLanguageSelectBlock($languageRelation, $language, $outerBlock=true)
	{
		$block = '';
		
		if ($outerBlock) {
			$block .= $this->startBlock($language);
		}
		
		if ($languageRelation->isCurrent($language)) {
			$block .= $this->addLanguageName($language);
			$block .= $this->addTitle($this->objDca->activeRecord->title, $GLOBALS['TL_LANG']['MSC']['currentLanguage']);
			
		} elseif ($languageRelation->isRelated($language)) {
			$block .= $this->addHiddenField($language, $id ?: $languageRelation->getId($language));
			$block .= $this->addLanguageName($language);
			$block .= $this->addSelectField($language, $languageRelation->getSelectOptions($language), $languageRelation->getId($language));
			$block .= $this->addEditIcon($language, $languageRelation->isRegistered($language), $languageRelation->getEditUrl($language), $languageRelation->getTableName());
			
		} else {
			$block .= $this->addHiddenField($language);
			$block .= $this->addLanguageName($language);
			$block .= $this->addSelectField($language, $languageRelation->getSelectOptions($language), $languageRelation->getId($language));
			$block .= $this->addCreateButton($language, $languageRelation->getCreateUrl($language));

		}
		
		if ($outerBlock) {
			$block .= $this->stopBlock();
		}
		
		return $block;
	}

	
	private function startBlock($language)
	{
		return '<div style="padding: 4px 0;" id="ctrl_'.$this->strId.'_'.$language.'" >';
	}

	
	private function stopBlock()
	{
		return '</div>';
	}

	
	private function addHiddenField($language, $id='')
	{
		return '<input type="hidden" name="'.$this->strName.'['.$language.']" id="ctrl_'.$this->strId.'_'.$language.'_id" value="'.$id.'">';
	}

	
	private function addLanguageName($language)
	{
		System::loadLanguageFile('languages');
	
		return '<span class="tl_gray" style="display: inline-block; padding: 7px 0">'.$GLOBALS['TL_LANG']['LNG'][$language].' ('.$language.') '.Image::getHtml('navexp.svg'). '  </span> ';
	}

	
	private function addTitle($title, $hint=false)
	{
		return '<span class="title">'.$title.($hint?' <span class="tl_gray">('.$hint.')</span>':'').'</span>';
	}

	
	private function addSelectField($language, $options, $selected)
	{
		$select = '<span style="min-width:200px;margin-right:2px;display:inline-block"><select id="pt_' . $this->strName . '_select_' . $language . '" class="tl_select">';
		$select .= '<option value="">-</option>';
		
		foreach ($options as $option) {
			$select .= '<option value="'.$option['value'].'" '.($option['value']==$selected?'selected':'').'>'.$option['label'].'</option>';
		}
		
		return $select . '</select></span>
	<script>
	  $("pt_' . $this->strName . '_select_' . $language . '").addEvent("change", function(e) {
		e.preventDefault();
		console.log(this);
		new Request.Contao({
			  evalScripts: false,
			  onSuccess: function(txt, json) {
				$("ctrl_'.$this->strId.'_'.$language.'").set("html", json.content);
				json.javascript && Browser.exec(json.javascript);
			  }
			}).post({"action":"reloadLanguageRelation", "name":"' . $this->strId . '", "language":"' . $language . '", "value":this.value, "REQUEST_TOKEN":"' . REQUEST_TOKEN . '"});
	  });
	</script>';
	}

	
	private function addEditIcon($language, $registered, $editUrl, $table)
	{
		if (null !== $editUrl) {
			return $registered ?
				' <a href="'.ampersand($editUrl).'" >'.
				Image::getHtml(
					$GLOBALS['TL_DCA'][$table]['list']['operations']['edit']['icon'],
					$GLOBALS['TL_LANG'][$table]['edit'][0],
					'title="'.$GLOBALS['TL_LANG'][$table]['edit'][0].'"'
				).
				'</a>':
				' <span>'.
				Image::getHtml(
					preg_replace('/(.*)\.(.*)/', '$1_.$2', $GLOBALS['TL_DCA'][$table]['list']['operations']['edit']['icon']),
					$GLOBALS['TL_LANG'][$table]['edit'][0]
				).
				'</span>';
		}

		return '';
	}

	
	private function addSelectButton($language, $pickerUrl)
	{
		return empty($pickerUrl)?
			' <button class="tl_submit" disabled>'.$GLOBALS['TL_LANG']['MSC']['selectRelation'].'</button>':
			' <a href="'.ampersand($pickerUrl).'" class="tl_submit" id="pt_' . $this->strName . '_select_' . $language . '">'.$GLOBALS['TL_LANG']['MSC']['selectRelation'].'</a>
	<script>
	  $("pt_' . $this->strName . '_select_' . $language . '").addEvent("click", function(e) {
		e.preventDefault();
		Backend.openModalSelector({
		  "id": "tl_listing",
		  "title": "' . \StringUtil::specialchars(str_replace("'", "\\'", $GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['label'][0])) . '",
		  "url": this.href + document.getElementById("ctrl_'.$this->strId.'_'.$language.'_id").value,
		  "callback": function(table, value) {
			new Request.Contao({
			  evalScripts: false,
			  onSuccess: function(txt, json) {
				$("ctrl_'.$this->strId.'_'.$language.'").set("html", json.content);
				json.javascript && Browser.exec(json.javascript);
			  }
			}).post({"action":"reloadLanguageRelation", "name":"' . $this->strId . '", "language":"' . $language . '", "value":value.join("\t"), "REQUEST_TOKEN":"' . REQUEST_TOKEN . '"});
		  }
		});
	  });
	</script>';
	}

	
	private function addCreateButton($language, $createUrl)
	{
		if (false === $createUrl) {
			return '';
		}
		
		return (null === $createUrl) ?
			' <button class="tl_submit" disabled>'.$GLOBALS['TL_LANG']['MSC']['createRelation'].'</button> ':
			' <a href="'.ampersand($createUrl).'" class="tl_submit">'.$GLOBALS['TL_LANG']['MSC']['createRelation'].'</a> ';
	}

}

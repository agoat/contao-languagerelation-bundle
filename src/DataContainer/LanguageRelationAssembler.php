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
 * Add language relation configuration to DCA
 */
class LanguageRelationAssembler
{

	private $constructors = [
		'tl_page' => [
			'Agoat\LanguageRelationBundle\DataContainer\PageNodeViewConstructor',
			'Agoat\LanguageRelationBundle\DataContainer\RelationFieldConstructor',
			'Agoat\LanguageRelationBundle\DataContainer\FallbackPageFieldConstructor'
		],
		'tl_article' => [
			'Agoat\LanguageRelationBundle\DataContainer\PageNodeViewConstructor',
			'Agoat\LanguageRelationBundle\DataContainer\RelationFieldConstructor'
		],
		'tl_content' => [
			'Agoat\LanguageRelationBundle\DataContainer\ArticleContentViewConstructor',
		//	'Agoat\LanguageRelationBundle\DataContainer\EventContentViewConstructor',
		//	'Agoat\LanguageRelationBundle\DataContainer\PostContentViewConstructor',
		],
		'tl_calendar' => [
			'Agoat\LanguageRelationBundle\DataContainer\RelationFieldConstructor'
		],
		'tl_calendar_events' => [
			'Agoat\LanguageRelationBundle\DataContainer\EventCalendarViewConstructor',
			'Agoat\LanguageRelationBundle\DataContainer\RelationFieldConstructor'
		],
		'tl_news' => [
			//'Agoat\LanguageRelationBundle\DataContainer\NewsArchiveViewConstructor',
			'Agoat\LanguageRelationBundle\DataContainer\RelationFieldConstructor'
		],
		'tl_news_archive' => [
			'Agoat\LanguageRelationBundle\DataContainer\RelationFieldConstructor'
		],
		
		
		'tl_archive' => [
			'Agoat\LanguageRelationBundle\DataContainer\PageNodeViewConstructor',
			'Agoat\LanguageRelationBundle\DataContainer\RelationFieldConstructor'
		],
		'tl_posts' => [
			'Agoat\LanguageRelationBundle\DataContainer\PostArchiveViewConstructor',
			'Agoat\LanguageRelationBundle\DataContainer\RelationFieldConstructor'
		],
		'tl_container' => [
			'Agoat\LanguageRelationBundle\DataContainer\PageNodeViewConstructor'
		],
	];

	
	/**
	 * Add language relation field for supported tables
	 *
	 * @param string $strTable
	 */
	public function buildDca ($table)
	{
		if ('FE' == TL_MODE)
		{
			return;
		}

		foreach ($this->constructors as $context=>$constructors) {
			if ($table == $context) {
				foreach ($constructors as $constructor) {
					$worker = new $constructor($table);
					$worker->buildDca();
				}
			}
		}
	}
}

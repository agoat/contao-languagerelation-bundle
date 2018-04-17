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
 
namespace Agoat\LanguageRelationBundle\LanguageRelation;



class LanguageRelationGenerator
{
	/**
 	 * LanguageRelationProviderInterface
     * @var LanguageRelationProviderInterface
     */
	private $providers;
   
	/**
 	 * Context
     * @var array
     */
	private $context;
	
	/**
 	 * Query
     * @var array
     */
	private $query;
	
	/**
 	 * LanguageRelation
     * @var array
     */
	private $relationCache;
	
	
    /**
	 * Constructor
	 */
	public function __construct()
	{
		$this->providers = array();
		$this->context = array();
		$this->query = array();
		$this->relationCache = array();
	}
	
	private function getContextForTable($table)
	{
		return $this->context[$table];
	}

    /**
	 * Register provider
	 *
	 * @param LanguageRelationProviderInterface $provider
     */
	public function addProvider($provider)
	{
		$context = $provider->getContext();
		
		$this->providers[$context] = $provider;
		
		$this->context[$provider->getDcaTable()] = $context;
		//$this->query[$provider->getQueryName()] = $context;
	}


    /**
	 * Build and return the LanguageRelation
	 *
	 * @param string  $context
	 * @param int     $id
	 * @param boolean $published Consider only published entities
	 * 
	 * @return LanguageRelation
     */
	private function build($context, $id, $published=false, $bypassCache=false)
	{
		if (array_key_exists($context.$id, $this->relationCache) && !$bypassCache) {
			return $this->relationCache[$context.$id];
		} else {
			$relation = $this->providers[$context]->build($id, $published);
	dump($relation);
			
			if (null !== $relation) {
				$this->relationCache[$context.$id] = $relation;
			} 
			
			return $relation;
		}

	}

	
	public function buildFromDca(\DataContainer $dc)
	{
		$context = $this->getContextForTable($dc->table);

		if (empty($context)) {
			return null;
		}
		
		return $this->build($context, $dc->id);
	}

	
	public function buildFromTableAndId($table, $id)
	{
		$context = $this->getContextForTable($table);

		if (empty($context)) {
			return null;
		}
		
		return $this->build($context, $id);
	}

	
	public function buildFromRequest($objpage)
	{
		$query = [
			'posts' => 'post',
			'events' => 'event'
		];
		
		foreach ($query as $key=>$context) {
			if (null !== ($id = \Input::get($key))) {
				dump($context);
				return $this->build($context, $id, true);
			}
		}
		
		return $this->build('page', $objpage->id, true);
	}

	


}

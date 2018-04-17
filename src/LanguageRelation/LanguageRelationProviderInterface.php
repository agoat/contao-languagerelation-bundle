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


/**
 * Language relation provider interface
 */
interface LanguageRelationProviderInterface
{

	/**
     * Returns the context
     *
     * @return string
     */
    public function getContext();

 
   /**
     * Returns the table name
     *
     * @return string
     */
    public function getDcaTable();

 
	/**
     * Builds a LanguageRelation object
     *
     * @param string  $id
     *
     * @return LanguageRelation | null
     */
    public function build($id, $published);


    /**
     * Returns the frontend url for a related entity
     *
     * @param Model  $related
     *
     * @return string
     */
    public function getFrontendUrl($related);

	
    /**
     * Returns the alternative (usually the related parent or root) frontend url for a language
     *
     * @param string  $related
     *
     * @return string
     */
    public function getAlternativeUrl($language, $onlyRoot);


    /**
     * Returns the alternative (usually the root) title for a language
     *
     * @param string  $language
     *
     * @return string
     */
    public function getAlternativeTitle($language, $onlyRoot);

	
    /**
     * Returns the (backend) edit url for a related entity
     *
     * @param Model  $related
     *
     * @return string
     */
    public function getEditUrl($related);

	
    /**
     * Returns the view url (change language in tree|list view)
     *
     * @param Model  $related
     *
     * @return string
     */
    public function getViewUrl($related);

	
    /**
     * Returns true if the contao picker is supported
     *
     * @return boolean
     */
    public function supportsPicker();


    /**
     * Returns the picker url to use the contao picker or false if not supported
     *
     * @param string  $language
     *
     * @return string | false
     */
    public function getPickerUrl($language);

	
    /**
     * Returns an array with selectable entities or null if the picker is supported
     *
     * @param string  $language
     *
     * @return array | false
     */
    public function getSelectOptions($language);

	
     /**
     * Returns the create (copy&paste) url
     *
     * @param string  $language
     *
     * @return string
     */
    public function getCreateUrl($language);

}
 
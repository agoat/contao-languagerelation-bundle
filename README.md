# Language relations in Contao 4

[![Version](https://img.shields.io/packagist/v/agoat/contao-languagerelation.svg?style=flat-square)](http://packagist.org/packages/agoat/contao-languagerelation)
[![License](https://img.shields.io/packagist/l/agoat/contao-languagerelation.svg?style=flat-square)](http://packagist.org/packages/agoat/contao-languagerelation)
[![Downloads](https://img.shields.io/packagist/dt/agoat/contao-languagerelation.svg?style=flat-square)](http://packagist.org/packages/agoat/contao-languagerelation) 

## About
Easy management of different languages. Contao itself already has the ability to manage multilingual pages. This is achieved by creating a seprate page tree for each of the languages.

This extension adds the missing language relations for the single pages. This makes it easy to manage a page in multiple languages. Of course there is also a language navigation module for the frontend.


## Install
### Contao manager
Search for the package and install it
```bash
agoat/contao-languagerelation
```

### Managed edition
Add the package
```bash
# Using the composer
composer require agoat/contao-languagerelation
```
Registration and configuration is done by the manager-plugin automatically.

### Standard edition
Add the package
```bash
# Using the composer
composer require agoat/contao-languagerelation
```
Register the bundle in the AppKernel
```php
# app/AppKernel.php
class AppKernel
{
    // ...
    public function registerBundles()
    {
        $bundles = [
            // ...
            // after Contao\CoreBundle\ContaoCoreBundle
            new Agoat\LanguageRelationBundle\AgoatLanguageRelationBundle(),
        ];
    }
}
```

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
 
namespace Agoat\LanguageRelationBundle;

use Agoat\LanguageRelationBundle\DependencyInjection\Compiler\LanguageRelationProviderPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;


/**
 * Configures the language relation bundle.
 *
 * @author Arne Stappen (alias aGoat) <https://github.com/agoat>
 */
class AgoatLanguageRelationBundle extends Bundle
{
   public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new LanguageRelationProviderPass());
    }
}

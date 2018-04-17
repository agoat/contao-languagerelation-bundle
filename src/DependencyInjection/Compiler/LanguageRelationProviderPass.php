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
 
namespace Agoat\LanguageRelationBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;


/**
 * Registers the language relation providers
 */
class LanguageRelationProviderPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

	
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('contao.language.relation')) {
            return;
        }

        $definition = $container->findDefinition('contao.language.relation');
		
        $provider = $container->findTaggedServiceIds('contao.languagerelation_provider');

        foreach ($provider as $id=>$tags) {
			$definition->addMethodCall('addProvider', [new Reference($id)]);
        }
    }
}

<?php

declare(strict_types=1);

namespace ZamboDaniel\SyliusOtpSimplePlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('zambo_daniel_sylius_otp_simple');
        $rootNode = $treeBuilder->getRootNode();

        /**
         * @psalm-suppress MixedMethodCall,PossiblyUndefinedMethod
         */
        //$rootNode
        //    ->children()
        //        ->scalarNode('option')
        //            ->info('This is an example configuration option')
        //            ->isRequired()
        //            ->cannotBeEmpty()
        //;

        return $treeBuilder;
    }
}

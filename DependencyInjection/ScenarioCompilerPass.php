<?php

namespace Bezb\ModelBundle\DependencyInjection;

use Bezb\ModelBundle\Component\ModelFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Bezb\ModelBundle\Exception\RuntimeException;

/**
 * Class ScenarioCompilerPass
 * @package Bezb\ModelBundle\DependencyInjection
 */
class ScenarioCompilerPass implements CompilerPassInterface 
{
    public function process(ContainerBuilder $container)
    {
        $taggedServices = $container->findTaggedServiceIds('model.scenario');

        foreach ($taggedServices as $serviceId => $tagAttributes) {

            if (false === $container->hasDefinition(ModelFactory::class) ) {
                throw new RuntimeException("Could not find model.factory service");
            }

            $container->getDefinition(ModelFactory::class)->addMethodCall(
                'addScenario', [$serviceId]
            );
        }
    }
}
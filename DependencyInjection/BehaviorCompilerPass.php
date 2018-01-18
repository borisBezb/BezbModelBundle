<?php

namespace Bezb\ModelBundle\DependencyInjection;

use Bezb\ModelBundle\Component\ModelFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;
use Bezb\ModelBundle\Exception\RuntimeException;

/**
 * Class BehaviorCompilerPass
 * @package Bezb\ModelBundle\DependencyInjection
 */
class BehaviorCompilerPass implements CompilerPassInterface 
{
	public function process(ContainerBuilder $container) 
    {

        $taggedServices = $container->findTaggedServiceIds('model.behavior');

        foreach ($taggedServices as $serviceId => $tagAttributes) {

            if (false === $container->hasDefinition(ModelFactory::class) ) {
                throw new RuntimeException("Could not find model.factory service");
            }

            $container->getDefinition(ModelFactory::class)->addMethodCall(
                'addBehavior', [new Reference($serviceId)]
            );
        }
	}
}
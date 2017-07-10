<?php

namespace Bezb\ModelBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;
use Bezb\ModelBundle\Exception\RuntimeException;

class BehaviorCompilerPass implements CompilerPassInterface 
{
	public function process(ContainerBuilder $container) 
    {
		$taggedServices = $container->findTaggedServiceIds('model.behavior');
		foreach ($taggedServices as $serviceId => $tagAttributes) {
			
			if (isset($tagAttributes[0]) === false) {
				continue;
			}

			$tagProperty = $tagAttributes[0];
			if (!isset($tagProperty['behavior'])) {
				continue;
			}
            
			$factoryService = "model.factory";
			if (false === $container->hasDefinition($factoryService) ) {
				throw new RuntimeException("Could not find model.factory service");
			}

			$container->getDefinition($factoryService)->addMethodCall(
				'addBehavior',
				[
					$tagProperty['behavior'],
					new Reference($serviceId),
				]
			);
		}
	}
}
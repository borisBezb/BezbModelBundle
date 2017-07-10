<?php
namespace Bezb\ModelBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class ModelCompilerPass implements CompilerPassInterface
{
	public function process(ContainerBuilder $container)
    {
		$taggedServices = $container->findTaggedServiceIds(
			'model.scenario'
		);

		foreach ($taggedServices as $serviceId => $tagAttributes) {
			if(isset($tagAttributes[0]) === false) {
				continue;
			}

			$tagProperty = $tagAttributes[0];
			if((isset($tagProperty['model']) || isset($tagProperty['scenario'])) === false) {
				continue;
			}

			$factoryService = "model.factory." . $tagProperty['model'];
			if($container->hasDefinition($factoryService) === false) {
				$factoryService = $tagProperty['model'];

				if($container->hasDefinition($factoryService) === false) {
					continue;
				}
			}


			$container->getDefinition($factoryService)->addMethodCall(
				'addScenario',
				[
					$tagProperty['scenario'],
					new Reference($serviceId),
				]
			);
		}
	}
}
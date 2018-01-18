<?php

namespace Bezb\ModelBundle;

use Bezb\ModelBundle\Component\{ BehaviorInterface, ScenarioInterface };
use Bezb\ModelBundle\DependencyInjection\{ BehaviorCompilerPass, ScenarioCompilerPass };
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class BezbModelBundle extends Bundle
{
    public function build(ContainerBuilder $builder)
    {
        parent::build($builder);

        $builder->addCompilerPass(new ScenarioCompilerPass());
        $builder->addCompilerPass(new BehaviorCompilerPass());

        $builder->registerForAutoconfiguration(ScenarioInterface::class)
            ->addTag('model.scenario')
            ->setPublic(true);

        $builder->registerForAutoconfiguration(BehaviorInterface::class)
            ->addTag('model.behavior');
    }
}

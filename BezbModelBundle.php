<?php

namespace Bezb\ModelBundle;

use Bezb\ModelBundle\DependencyInjection\BehaviorCompilerPass;
use Bezb\ModelBundle\DependencyInjection\ScenarioCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class BezbModelBundle extends Bundle
{
    public function build(ContainerBuilder $builder)
    {
        parent::build($builder);

        $builder->addCompilerPass(new ScenarioCompilerPass());
        $builder->addCompilerPass(new BehaviorCompilerPass());
    }
}

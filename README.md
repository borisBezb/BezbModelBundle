BezbModelBundle
===================

Model - is a smart wrapper above Doctrine entities what makes a data validation, database saving or updating and can contain 
specific business logic needed in your application.

Model using makes simpler a work with entities and helps you to keep maintainable code. 

##Quick Start


```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Bezb\ModelBundle\Annotation\Model;

/**
* Class Order
 * @ORM\Entity()
 * @ORM\Table(name="schema.orders")
 * @Model()
 */
class Order
{
    /** Some properties there */
}
```

```php
<?php

namespace App\Controller;

use App\Entity\Order;
use App\Form\Type\OrderType;
use Bezb\ModelBundle\Component\ModelFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    /**
    * @param ModelFactoryInterface $modelFactory
    * @return mixed
    */
    public function createOrder(ModelFactoryInterface $modelFactory)
    {
        $model = $modelFactory->create(Order::class);
        $form = $model->setForm($this->createForm(OrderType::class, $model->getEntity()));
        $model->setForm($form);
        
        if (false === $model->save()) {
            return $this->renderFormError($form);
        }

        return $model->getEntity();
    }
}
```

```php
<?php

namespace App\Scenario\Order;

use App\Service\EmailSender;
use Bezb\ModelBundle\Annotation\Scenario;
use Bezb\ModelBundle\Component\{ BaseScenario, ModelEvent };

/**
 * @Scenario(model="order")
 */
class Create extends BaseScenario
{
    protected $emailSender;
    
    public function __construct(EmailSender $emailSender) 
    {
        $this->emailSender = $emailSender;
    }

    /**
     * @param ModelEvent $event
     * @throws \Exception
     * @return void
     */
    public function onAfterSave(ModelEvent $event)
    {
        $model = $event->getModel();
        $this->emailSender->sendOrder($model->getEntity());
    }
}
```
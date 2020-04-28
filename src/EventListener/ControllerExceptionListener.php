<?php

namespace App\EventListener;

use App\Annotations\ApiResource;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Doctrine\Common\Annotations\Reader;

class ControllerExceptionListener
{
    private $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function __invoke(ControllerEvent $event)
    {
        if (!is_array($controller = $event->getController())) {
            return;
        }

        $controller = $event->getController();
        $object = new \ReflectionClass($controller[0]);

        foreach ($this->reader->getClassAnnotations($object) as $formResource) {
            if ($formResource instanceof ApiResource) {
                $request = $event->getRequest();
                $request->attributes->set('_resource', $formResource->resource);
            }
        }
    }
}
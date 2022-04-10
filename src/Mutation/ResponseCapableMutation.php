<?php

declare(strict_types=1);

namespace Chiron\ResponseCreator\Mutation;

use Chiron\Config\InjectableConfigInterface;
use Chiron\Config\Configure;
use Chiron\Container\Container;
use Chiron\Event\EventDispatcherAwareInterface;
use Chiron\Event\EventDispatcherAwareTrait;
use Psr\EventDispatcher\EventDispatcherInterface;
use Chiron\ResponseCreator\Traits\ResponseCapableInterface;
use Chiron\ResponseCreator\Traits\ResponseCapableTrait;
use Chiron\ResponseCreator\ResponseCreator;

final class ResponseCapableMutation
{
    public static function mutation(ResponseCapableInterface $respondable)
    {
        // Inject the response creator if not already present in the ResponseCapable instance object.
        if (! $respondable->hasResponder()) {
            $respondable->setResponder((Container::$instance)->get(ResponseCreator::class)); // TODO : améliorer le code c'est pas super propre !!!!
        }
    }
}

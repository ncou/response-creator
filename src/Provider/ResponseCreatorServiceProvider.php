<?php

declare(strict_types=1);

namespace Chiron\ResponseCreator\Provider;

use Chiron\Container\Container;
use Chiron\Core\Container\Bootloader\BootloaderInterface;
use Chiron\Core\Container\Provider\ServiceProviderInterface;
use Chiron\Container\BindingInterface;
use Chiron\Container\ContainerAwareInterface;
use Chiron\Event\EventDispatcherAwareInterface;

use Chiron\Core\Directories;

use Chiron\Config\InjectableConfigInterface;
use Chiron\Service\Mutation\InjectableConfigMutation;
use Chiron\Service\Mutation\ContainerAwareMutation;
use Chiron\Service\Mutation\EventDispatcherAwareMutation;

use Chiron\Publisher\Publisher;
use Chiron\Config\Configure;
use Chiron\Console\Console;
use Chiron\Core\Command\CommandLoader;
use Chiron\Config\ConsoleConfig;

use Chiron\Config\Loader\LoaderInterface;
use Chiron\Config\Loader\PhpLoader;
use Closure;
use Chiron\Filesystem\Filesystem;

use Chiron\Config\EventsConfig;
use Chiron\Event\EventDispatcher;
use Psr\EventDispatcher\EventDispatcherInterface;
use Chiron\Event\ListenerProvider;
use Psr\EventDispatcher\ListenerProviderInterface;

use Chiron\Core\Core;
use Chiron\Config\SettingsConfig;

use Chiron\Core\Memory;

use Chiron\ResponseCreator\Mutation\ResponseCapableMutation;
use Chiron\ResponseCreator\Traits\ResponseCapableInterface;
use Chiron\ResponseCreator\ResponseCreator;

final class ResponseCreatorServiceProvider implements ServiceProviderInterface
{
    public function register(BindingInterface $binder): void
    {
        $binder->mutation(ResponseCapableInterface::class, [ResponseCapableMutation::class, 'mutation']);
        $binder->singleton(ResponseCreator::class);
    }
}

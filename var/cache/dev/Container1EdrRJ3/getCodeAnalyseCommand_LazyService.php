<?php

namespace Container1EdrRJ3;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getCodeAnalyseCommand_LazyService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private '.App\Command\CodeAnalyseCommand.lazy' shared service.
     *
     * @return \Symfony\Component\Console\Command\LazyCommand
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/symfony/console/Command/Command.php';
        include_once \dirname(__DIR__, 4).'/vendor/symfony/console/Command/LazyCommand.php';

        return $container->privates['.App\\Command\\CodeAnalyseCommand.lazy'] = new \Symfony\Component\Console\Command\LazyCommand('app:code-analyse', [], 'Add a short description for your command', false, #[\Closure(name: 'App\\Command\\CodeAnalyseCommand')] function () use ($container): \App\Command\CodeAnalyseCommand {
            return ($container->privates['App\\Command\\CodeAnalyseCommand'] ?? $container->load('getCodeAnalyseCommandService'));
        });
    }
}
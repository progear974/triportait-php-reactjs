<?php

namespace Container4EbycIi;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getZipImageCommand_LazyService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private '.App\Command\ZipImageCommand.lazy' shared service.
     *
     * @return \Symfony\Component\Console\Command\LazyCommand
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/symfony/console/Command/Command.php';
        include_once \dirname(__DIR__, 4).'/vendor/symfony/console/Command/LazyCommand.php';

        return $container->privates['.App\\Command\\ZipImageCommand.lazy'] = new \Symfony\Component\Console\Command\LazyCommand('app:zip-image', [], 'Add a short description for your command', false, #[\Closure(name: 'App\\Command\\ZipImageCommand')] function () use ($container): \App\Command\ZipImageCommand {
            return ($container->privates['App\\Command\\ZipImageCommand'] ?? $container->load('getZipImageCommandService'));
        });
    }
}

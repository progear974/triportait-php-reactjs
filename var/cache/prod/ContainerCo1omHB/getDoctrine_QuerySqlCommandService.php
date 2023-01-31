<?php

namespace ContainerCo1omHB;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/*
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getDoctrine_QuerySqlCommandService extends App_KernelProdContainer
{
    /*
     * Gets the private 'doctrine.query_sql_command' shared service.
     *
     * @return \Doctrine\Bundle\DoctrineBundle\Command\Proxy\RunSqlDoctrineCommand
     */
    public static function do($container, $lazyLoad = true)
    {
        $container->privates['doctrine.query_sql_command'] = $instance = new \Doctrine\Bundle\DoctrineBundle\Command\Proxy\RunSqlDoctrineCommand(($container->privates['Doctrine\\Bundle\\DoctrineBundle\\Dbal\\ManagerRegistryAwareConnectionProvider'] ?? $container->load('getManagerRegistryAwareConnectionProviderService')));

        $instance->setName('doctrine:query:sql');

        return $instance;
    }
}

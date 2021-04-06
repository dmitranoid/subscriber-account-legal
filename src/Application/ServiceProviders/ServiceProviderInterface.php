<?php


namespace App\Application\ServiceProviders;


use Psr\Container\ContainerInterface;

interface ServiceProviderInterface
{
    public static function register(ContainerInterface $c);
}
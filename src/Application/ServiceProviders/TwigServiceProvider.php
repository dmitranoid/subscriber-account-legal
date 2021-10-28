<?php
declare(strict_types=1);


namespace App\Application\ServiceProviders;


use App\Application\View\TwigTextHelper;
use Psr\Container\ContainerInterface;
use Twig\Extension\DebugExtension;
use Twig\TwigFunction;

class TwigServiceProvider implements ServiceProviderInterface
{
    public static function register(ContainerInterface $c)
    {
        $settings = $c->get('settings');
        $twigSettings = $settings['twig'];
        $twig = Twig::create(
            $twigSettings['path'],
            [
                'debug' => true,
                'cache' => $twigSettings['cachePath']
            ]);
        $twig->addExtension(new DebugExtension());
        $twig->getEnvironment()->addFunction(
            new TwigFunction('connectionLink', [TwigTextHelper::class, 'connectionLink'])
        );
        $twig->getEnvironment()->addFunction(
            new TwigFunction('counter_code', [TwigTextHelper::class, 'counterCode'])
        );

        return $twig;
    }

}
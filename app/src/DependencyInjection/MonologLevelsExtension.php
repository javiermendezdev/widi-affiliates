<?php

namespace App\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

/**
 * @codeCoverageIgnore
 */
class MonologLevelsExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        // empty
    }

    public function prepend(ContainerBuilder $container)
    {
        if (getenv('APP_ENV') === 'test') {
            return;
        }

        if ($container->hasExtension('monolog')) {

            $level = $container->resolveEnvPlaceholders('%env(APP_LOG_LEVEL)%', true);
            $formatter = $container->resolveEnvPlaceholders('%env(APP_LOG_FORMATTER)%', true);

            //"!security", "!php"
            $channelsMonolog = ["!event", "!doctrine", "!console","!php"];

            $config = [
                "handlers" => [
                    'console' => [
                        "type" => 'console',
                        'level' => 'info',
                        "process_psr_3_messages" => false,
                        "channels" => $channelsMonolog,
                        "formatter" => $formatter
                    ],
                    "stdout" => [
                        'type' => 'stream',
                        'path' => 'php://stdout',
                        'level' => $level,
                        'channels' => $channelsMonolog,
                        "formatter" => $formatter
                    ],
                ],
            ];

            $container->prependExtensionConfig('monolog', $config);
        }
    }
}

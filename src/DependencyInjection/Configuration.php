<?php

declare(strict_types=1);

/*
 * This file is part of richardhj/contao-nc-mailjet-sms.
 *
 * Copyright (c) 2020-2020 Richard Henkenjohann
 *
 * @package   richardhj/contao-nc-mailjet-sms
 * @author    Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 * @copyright 2020-2020 Richard Henkenjohann
 * @license   MIT
 */

namespace Richardhj\NotificationCenterMailjetSms\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('richardhj_notification_center_maijet_sms');
        $treeBuilder->getRootNode()
            ->children()
                // Do not set the access token in the config but define the environment value if necessary
                ->scalarNode('access_token')
                    ->defaultValue('%env(default::MAILJETSMS_TOKEN)%')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}

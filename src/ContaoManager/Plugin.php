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

namespace Richardhj\NotificationCenterMailjetSms\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Richardhj\NotificationCenterMailjetSms\RichardhjNotificationCenterMailjetSmsBundle;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create(RichardhjNotificationCenterMailjetSmsBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class, 'notification_center']),
        ];
    }
}

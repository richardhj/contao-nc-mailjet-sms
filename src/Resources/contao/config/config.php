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

use Richardhj\NotificationCenterMailjetSms\NotificationCenter\Gateway\MailjetSms;

$GLOBALS['NOTIFICATION_CENTER']['GATEWAY']['mailjetsms'] = MailjetSms::class;

$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE'] = array_merge_recursive(
    (array) $GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE'],
    [
        'contao' => [
            'core_form'           => [
                'sms_recipients'        => [
                    'form_*',
                ],
                'sms_recipients_region' => [
                    'form_*',
                ],
                'sms_text'              => [
                    'form_*',
                    'formconfig_*',
                    'raw_data',
                    'admin_email',
                ],
                'sms_sender'            => [
                    'form_*',
                ],
            ],
            'member_registration' => [
                'sms_recipients'        => [
                    'member_mobile',
                    'member_phone',
                ],
                'sms_recipients_region' => [
                    'member_country',
                ],
                'sms_text'              => [
                    'domain',
                    'link',
                    'member_*',
                    'admin_email',
                ],
                'sms_sender'            => [
                    'member_*',
                ],
            ],
            'member_personaldata' => [
                'sms_recipients'        => [
                    'member_mobile',
                    'member_phone',
                ],
                'sms_recipients_region' => [
                    'member_country',
                ],
                'sms_text'              => [
                    'domain',
                    'member_*',
                    'member_old_*',
                    'admin_email',
                ],
                'sms_sender'            => [
                    'member_*',
                ],
            ],
            'member_password'     => [
                'sms_recipients'        => [
                    'member_mobile',
                    'member_phone',
                ],
                'sms_recipients_region' => [
                    'member_country',
                ],
                'sms_text'              => [
                    'domain',
                    'link',
                    'member_*',
                    'admin_email',
                ],
                'sms_sender'            => [
                    'member_*',
                ],
            ],
        ],
    ]
);

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

$GLOBALS['TL_DCA']['tl_nc_language']['palettes']['mailjetsms'] =
    '{general_legend},language,fallback;{meta_legend},sms_sender,sms_recipients,sms_recipients_region;{content_legend},sms_text';

$GLOBALS['TL_DCA']['tl_nc_language']['fields']['sms_sender'] = [
    'exclude'       => true,
    'inputType'     => 'text',
    'eval'          => [
        'rgxp'           => 'nc_tokens',
        'decodeEntities' => true,
        'tl_class'       => 'w50',
    ],
    'sql'           => "varchar(255) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_nc_language']['fields']['sms_recipients'] = [
    'exclude'       => true,
    'inputType'     => 'text',
    'eval'          => [
        'rgxp'           => 'nc_tokens',
        'tl_class'       => 'long clr',
        'decodeEntities' => true,
        'mandatory'      => true,
    ],
    'sql'           => "varchar(255) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_nc_language']['fields']['sms_recipients_region'] = [
    'exclude'   => true,
    'inputType' => 'text',
    'eval'      => [
        'rgxp'           => 'nc_tokens',
        'tl_class'       => 'w50',
        'decodeEntities' => true,
    ],
    'sql'       => "varchar(255) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_nc_language']['fields']['sms_text'] = [
    'exclude'   => true,
    'inputType' => 'textarea',
    'eval'      => [
        'rgxp'           => 'nc_tokens',
        'tl_class'       => 'clr',
        'decodeEntities' => true,
        'mandatory'      => true,
    ],
    'sql'       => 'text NULL',
];

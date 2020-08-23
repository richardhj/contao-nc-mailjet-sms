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

$GLOBALS['TL_DCA']['tl_nc_gateway']['palettes']['mailjetsms'] = '{title_legend},title,type;{gateway_legend},mailjetsms_accessToken';

$GLOBALS['TL_DCA']['tl_nc_gateway']['fields']['mailjetsms_accessToken'] = [
    'exclude'   => true,
    'inputType' => 'text',
    'eval'      => [
        'mandatory' => true,
        'tl_class'  => 'w50',
    ],
    'sql'       => "varchar(64) NOT NULL default ''",
];

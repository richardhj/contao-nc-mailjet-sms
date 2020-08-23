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

namespace Richardhj\NotificationCenterMailjetSms\EventListener;

use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\StringUtil;
use Contao\System;
use Contao\Validator;

class SaveCallbackListener
{
    /**
     * @Callback(table="tl_nc_language", target="fields.sms_sender.save")
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function onSaveSmsSender($value)
    {
        if ('' !== $value) {
            if (false !== strpos($value, '##') || false !== strpos($value, '{{')) {
                return $value;
            }

            if ((!Validator::isAlphanumeric($value) && !preg_match('/^[1-9][0-9]{7,12}$/', $value))
                || (Validator::isAlphanumeric($value) && \strlen($value) > 11)
            ) {
                throw new \RuntimeException($GLOBALS['TL_LANG']['ERR']['mailjetsms_invalid_sender']);
            }
        }

        return $value;
    }

    /**
     * @Callback(table="tl_nc_language", target="fields.sms_recipients.save")
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function onSaveSmsRecipient($value)
    {
        if ('' !== $value) {
            foreach (StringUtil::trimsplit(',', $value) as $chunk) {
                // Skip string with tokens or inserttags
                if (false !== strpos($chunk, '##') || false !== strpos($chunk, '{{')) {
                    continue;
                }

                if (!Validator::isPhone($chunk)) {
                    throw new \RuntimeException($GLOBALS['TL_LANG']['ERR']['phone']);
                }
            }
        }

        return $value;
    }

    /**
     * @Callback(table="tl_nc_gateway", target="config.onload")
     */
    public function onLoadTable()
    {
        if (System::getContainer()->getParameter('mailjet_sms.access_token')) {
            $GLOBALS['TL_DCA']['tl_nc_gateway']['fields']['mailjetsms_accessToken']['eval']['disabled']  = true;
            $GLOBALS['TL_DCA']['tl_nc_gateway']['fields']['mailjetsms_accessToken']['eval']['mandatory'] = false;
        }
    }

    /**
     * @Callback(table="tl_nc_gateway", target="fields.mailjetsms_accessToken.load")
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function onLoadSmsAccessToken($value)
    {
        if ($accessToken = System::getContainer()->getParameter('mailjet_sms.access_token')) {
            return $accessToken;
        }

        return $value;
    }

    /**
     * @Callback(table="tl_nc_gateway", target="fields.mailjetsms_accessToken.save")
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function onSaveSmsAccessToken($value)
    {
        if (System::getContainer()->getParameter('mailjet_sms.access_token')) {
            return '';
        }

        return $value;
    }
}

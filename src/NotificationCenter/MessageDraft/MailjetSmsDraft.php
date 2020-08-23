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

namespace Richardhj\NotificationCenterMailjetSms\NotificationCenter\MessageDraft;

use Contao\Controller;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\StringUtil as ContaoStringUtil;
use Contao\System;
use Haste\Util\StringUtil as HasteStringUtil;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use NotificationCenter\MessageDraft\MessageDraftInterface;
use NotificationCenter\Model\Language as LanguageModel;
use NotificationCenter\Model\Message as MessageModel;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class MailjetSmsDraft implements MessageDraftInterface
{
    protected MessageModel $messageModel;
    protected LanguageModel $languageModel;
    protected array      $tokens = [];
    private LoggerInterface $logger;

    public function __construct(MessageModel $messageModel, LanguageModel $languageModel, array $tokens)
    {
        /** @var LoggerInterface $logger */
        $logger = System::getContainer()->get('monolog.logger.contao');

        $this->tokens        = $tokens;
        $this->languageModel = $languageModel;
        $this->messageModel  = $messageModel;
        $this->logger        = $logger;
    }

    public function getFrom(): ?string
    {
        return HasteStringUtil::recursiveReplaceTokensAndTags(
            $this->languageModel->sms_sender,
            $this->tokens,
            HasteStringUtil::NO_TAGS | HasteStringUtil::NO_EMAILS | HasteStringUtil::NO_BREAKS
        ) ?: null;
    }

    public function getRecipients(): array
    {
        // Replaces tokens first so that tokens can contain a list of recipients.
        $recipients = HasteStringUtil::recursiveReplaceTokensAndTags(
            $this->languageModel->sms_recipients,
            $this->tokens,
            HasteStringUtil::NO_TAGS | HasteStringUtil::NO_EMAILS | HasteStringUtil::NO_BREAKS
        );

        return array_filter(
            array_map(
                function (string $phoneNumber) {
                    $phoneNumber = HasteStringUtil::recursiveReplaceTokensAndTags(
                        $phoneNumber,
                        $this->tokens,
                        HasteStringUtil::NO_TAGS | HasteStringUtil::NO_EMAILS | HasteStringUtil::NO_BREAKS
                    );

                    try {
                        return $this->normalizePhoneNumber($phoneNumber);
                    } catch (NumberParseException $e) {
                        $this->logger->log(
                            LogLevel::ERROR,
                            sprintf(
                                'Skipping recipient "%s" due to invalid phone number: %s',
                                $phoneNumber,
                                $e->getMessage()
                            ),
                            ['contao' => new ContaoContext(__METHOD__, TL_ERROR)]
                        );
                    }

                    return null;
                },
                ContaoStringUtil::trimsplit(',', $recipients)
            )
        );
    }

    public function getText(): string
    {
        $text = HasteStringUtil::recursiveReplaceTokensAndTags(
            $this->languageModel->sms_text,
            $this->tokens,
            HasteStringUtil::NO_TAGS
        );

        return Controller::convertRelativeUrls($text, '', true);
    }

    public function getTokens()
    {
        return $this->tokens;
    }

    public function getMessage()
    {
        return $this->messageModel;
    }

    public function getLanguage()
    {
        return $this->languageModel->language;
    }

    /**
     * Normalize a phone number and return in E.164 format.
     * When a phone number is present in a local format, use a fallback region (that may be defined in the
     * language model, or inherited from the language).
     *
     * @throws NumberParseException
     */
    protected function normalizePhoneNumber(string $phone): string
    {
        $phoneNumberUtil = PhoneNumberUtil::getInstance();

        // We have to find a default country code as we can not make sure to get a internationalized phone number
        $defaultRegion = HasteStringUtil::recursiveReplaceTokensAndTags(
            $this->languageModel->sms_recipients_region,
            $this->tokens,
            HasteStringUtil::NO_TAGS | HasteStringUtil::NO_EMAILS | HasteStringUtil::NO_BREAKS
        ) ?: $this->languageModel->language;

        $phoneNumber = $phoneNumberUtil->parse($phone, strtoupper($defaultRegion));

        return $phoneNumberUtil->format($phoneNumber, PhoneNumberFormat::E164);
    }
}

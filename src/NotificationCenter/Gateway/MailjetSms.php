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

namespace Richardhj\NotificationCenterMailjetSms\NotificationCenter\Gateway;

use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\FrontendUser;
use Contao\MemberModel;
use Contao\System;
use NotificationCenter\Gateway\Base;
use NotificationCenter\Gateway\GatewayInterface;
use NotificationCenter\Gateway\MessageDraftCheckSendInterface;
use NotificationCenter\MessageDraft\MessageDraftFactoryInterface;
use NotificationCenter\Model\Gateway as GatewayModel;
use NotificationCenter\Model\Language as LanguageModel;
use NotificationCenter\Model\Message as MessageModel;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Richardhj\NotificationCenterMailjetSms\NotificationCenter\MessageDraft\MailjetSmsDraft;
use Symfony\Component\HttpClient\HttpClient;

class MailjetSms extends Base implements GatewayInterface, MessageDraftFactoryInterface, MessageDraftCheckSendInterface
{
    protected $objModel;
    private LoggerInterface $logger;
    private ?string $accessToken;

    public function __construct(GatewayModel $model)
    {
        /** @var LoggerInterface $logger */
        $logger      = System::getContainer()->get('monolog.logger.contao');
        $accessToken = System::getContainer()->getParameter('mailjet_sms.access_token');

        parent::__construct($model);

        $this->logger      = $logger;
        $this->accessToken = $accessToken;
    }

    public function createDraft(MessageModel $messageModel, array $tokens, $language = '')
    {
        if ('' === $language) {
            $language = (string) $GLOBALS['TL_LANGUAGE'];
        }

        if (null === ($languageModel = LanguageModel::findByMessageAndLanguageOrFallback($messageModel, $language))) {
            $this->logger->log(
                LogLevel::ERROR,
                sprintf('No message and no fallback found for message ID "%s" and language "%s".', $messageModel->id, $language),
                ['contao' => new ContaoContext(__METHOD__, TL_ERROR)]
            );

            return null;
        }

        return new MailjetSmsDraft($messageModel, $languageModel, $tokens);
    }

    public function send(MessageModel $message, array $tokens, $language = '')
    {
        $accessToken = $this->getAccessToken();
        if ('' === $accessToken) {
            return false;
        }

        $draft = $this->createDraft($message, $tokens, $language);
        if (null === $draft) {
            return false;
        }

        $client = HttpClient::createForBaseUri('https://api.mailjet.com/v4/', ['auth_bearer' => $accessToken]);

        $success = true;
        foreach ($draft->getRecipients() as $recipient) {
            $response = $client->request(
                'POST',
                'sms-send',
                [
                    'json' => [
                        'From' => $draft->getFrom(),
                        'To'   => $recipient,
                        'Text' => $draft->getText(),
                    ],
                ]
            );

            $content = $response->toArray(false);
            if (isset($content['ErrorMessage'])) {
                $success = false;

                $this->logger->log(
                    LogLevel::ERROR,
                    sprintf('Error sending SMS: %s', $content['ErrorMessage']),
                    ['contao' => new ContaoContext(__METHOD__, TL_ERROR)]
                );
            }
        }

        return $success;
    }

    /**
     * Check whether an exemplary draft can be send by means of a given message and gateway. In most cases this check
     * looks for existing recipients.
     *
     * @param MessageModel $objMessage
     *
     * @throws \LogicException Optional with an error message
     *
     * @return bool
     */
    public function canSendDraft(MessageModel $message)
    {
        // Create a dummy draft
        // All drafts get the member data as tokens with "member_" prefix. We imitate it here

        /** @var MemberModel $memberModel */
        $memberModel = MemberModel::findByPk(FrontendUser::getInstance()->id);

        $draft = $this->createDraft(
            $message,
            array_combine(
                array_map(static fn ($key) => 'member_'.$key, array_keys($memberModel->row())),
                $memberModel->row()
            )
        );

        if (null === $draft || [] === $draft->getRecipients()) {
            throw new \LogicException($GLOBALS['TL_LANG']['ERR']['mailjetsms_draft_unavailable']);
        }

        return true;
    }

    private function getAccessToken(): string
    {
        return $this->accessToken ?: $this->objModel->mailjetsms_accessToken;
    }
}

<?php

namespace Muchacuba\Chuchuchu;

use GuzzleHttp\Client;
use Cubalider\Facebook\PickProfile as PickFacebookProfile;
use Muchacuba\Firebase\PickProfile as PickFirebaseProfile;

/**
 * @di\service({
 *     deductible: true,
 *     internal: true
 * })
 */
class NotifyUser
{
    /**
     * @var PickConversation
     */
    private $pickConversation;

    /**
     * @var PickFacebookProfile
     */
    private $pickFacebookProfile;

    /**
     * @var PickFirebaseProfile
     */
    private $pickFirebaseProfile;

    /**
     * @var string string
     */
    private $apiKey;

    /**
     * @param PickConversation    $pickConversation
     * @param PickFacebookProfile $pickFacebookProfile
     * @param PickFirebaseProfile $pickFirebaseProfile
     * @param string              $apiKey
     *
     * @di\arguments({
     *     apiKey: "%firebase_web_api_key%"
     * })
     */
    public function __construct(
        PickConversation $pickConversation,
        PickFacebookProfile $pickFacebookProfile,
        PickFirebaseProfile $pickFirebaseProfile,
        $apiKey
    ) {
        $this->pickConversation = $pickConversation;
        $this->pickFacebookProfile = $pickFacebookProfile;
        $this->pickFirebaseProfile = $pickFirebaseProfile;
        $this->apiKey = $apiKey;
    }

    /**
     * @param string $conversation
     * @param string $author
     * @param string $user
     * @param string $content
     * @param int    $date
     */
    public function notify(
        $conversation,
        $author,
        $user,
        $content,
        $date
    ) {
        $userFirebaseProfile = $this->pickFirebaseProfile->pick($user);

        if ($userFirebaseProfile->getToken() == null) {
            return;
        }

        $authorFacebookProfile = $this->pickFacebookProfile->pick($author);

        (new Client())->post(
            'https://fcm.googleapis.com/fcm/send',
            [
                'headers' => [
                    'Authorization' => sprintf('key=%s', $this->apiKey)
                ],
                'json' => [
                    'notification' => [
                        'icon' => $authorFacebookProfile->getPicture(),
                        'title' => $authorFacebookProfile->getName(),
                        'body' => $this->buildTruncatedContent($content),
                        'click_action' => sprintf('https://muchacuba.com/#/chuchuchu?conversation=%s', $conversation)
                    ],
                    'to' => $userFirebaseProfile->getToken(),
                    'data' => [
                        'message' => [
                            'conversation' => $conversation,
                            'user' => $user,
                            'content' => $content,
                            'date' => $date
                        ]
                    ]
                ]
            ]
        );
    }

    /**
     * @param string $content
     *
     * @return string
     */
    private function buildTruncatedContent($content)
    {
        $truncatedContent = wordwrap($content, 100, "\n");
        $truncatedContent = substr(
            $content,
            0,
            strpos($truncatedContent, "\n") ?: strlen($content)
        );
        if (strlen($truncatedContent) < strlen($content)) {
            $truncatedContent .= '...';
        }
        
        return $truncatedContent;
    }
}
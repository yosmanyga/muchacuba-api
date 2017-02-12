<?php

namespace Muchacuba\Chuchuchu;

use GuzzleHttp\Client;
use Cubalider\Facebook\PickProfile as PickFacebookProfile;
use Muchacuba\Chuchuchu\Firebase\PickProfile as PickFirebaseProfile;

/**
 * @di\service({
 *     deductible: true,
 *     internal: true
 * })
 */
class NotifyUsers
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
     *     apiKey: "%firebase_api_key%"
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
     * @param string $user
     * @param string $content
     * @param int    $date
     */
    public function notify(
        $conversation,
        $user,
        $content,
        $date
    ) {
        $client = new Client();

        $conversation = $this->pickConversation->pick($conversation);
        foreach ($conversation->getParticipants() as $participant) {
            if ($participant == $user) {
                // Avoid auto notification
                continue;
            }

            $firebaseProfile = $this->pickFirebaseProfile->pick($participant);

            // TODO: Improve this patch, used as a workaround for message on invitation
            if (is_null($firebaseProfile)) {
                return;
            }

            $facebookProfile = $this->pickFacebookProfile->pick($participant);

            $body = wordwrap($content, 100, "\n");
            $body = substr(
                $content,
                0,
                strpos($body, "\n") ?: strlen($content)
            );
            if (strlen($body) < strlen($content)) {
                $body .= '...';
            }

            $client->post(
                'https://fcm.googleapis.com/fcm/send',
                [
                    'headers' => [
                        'Authorization' => sprintf('key=%s', $this->apiKey)
                    ],
                    'json' => [
                        'notification' => [
                            'icon' => $facebookProfile->getPicture(),
                            'title' => $facebookProfile->getName(),
                            'body' => $body,
                            'click_action' => sprintf('https://muchacuba.com/#/chuchuchu?conversation=%s', $conversation->getId())
                        ],
                        'to' => $firebaseProfile->getToken(),
                        'data' => [
                            'message' => [
                                'conversation' => $conversation->getId(),
                                'user' => $user,
                                'content' => $content,
                                'date' => $date
                            ]
                        ]
                    ]
                ]
            );
        }
    }
}
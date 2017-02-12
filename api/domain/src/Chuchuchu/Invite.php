<?php

namespace Muchacuba\Chuchuchu;

use Cubalider\Internet\PickProfile as PickInternetProfile;
use Cubalider\Internet\NonExistentProfileException as NonExistentInternetProfileException;
use Muchacuba\CreateProfiles;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class Invite
{
    /**
     * @var PickInternetProfile
     */
    private $pickInternetProfile;

    /**
     * @var CreateProfiles
     */
    private $createProfiles;

    /**
     * @var AddContact
     */
    private $addContact;

    /**
     * @var ResolveConversation
     */
    private $resolveConversation;

    /**
     * @var InsertMessage
     */
    private $insertMessage;

    /**
     * @param PickInternetProfile    $pickInternetProfile
     * @param CreateProfiles         $createProfiles
     * @param AddContact             $addContact
     * @param ResolveConversation    $resolveConversation
     * @param InsertMessage          $insertMessage
     */
    public function __construct(
        PickInternetProfile $pickInternetProfile,
        CreateProfiles $createProfiles,
        AddContact $addContact,
        ResolveConversation $resolveConversation,
        InsertMessage $insertMessage
    )
    {
        $this->pickInternetProfile = $pickInternetProfile;
        $this->createProfiles = $createProfiles;
        $this->addContact = $addContact;
        $this->resolveConversation = $resolveConversation;
        $this->insertMessage = $insertMessage;
    }

    /**
     * @param string $uniqueness
     * @param string $email
     * @param string $message
     *
     * @throws InvalidDataException
     */
    public function invite($uniqueness, $email, $message)
    {
        $contactUniqueness = $this->resolveContactProfile($email);
        
        $this->addContact->add($uniqueness, $contactUniqueness);
        $this->addContact->add($contactUniqueness, $uniqueness);

        $conversation = $this->resolveConversation->resolve($uniqueness, $contactUniqueness);

        // TODO: Send email with link to conversation

        $this->insertMessage->insert($conversation, $uniqueness, $message);
    }

    /**
     * @param string $email
     *
     * @return string
     *
     * @throws InvalidDataException
     */
    private function resolveContactProfile($email)
    {
        try {
            $internetProfile = $this->pickInternetProfile->pick(null, $email);

            $uniqueness = $internetProfile->getUniqueness();
        } catch (NonExistentInternetProfileException $e) {
            $uniqueness = $this->createProfiles->create(
                null,
                ['email' => $email],
                null,
                null,
                ['contacts' => []],
                null
            );
        }

        return $uniqueness;
    }
}
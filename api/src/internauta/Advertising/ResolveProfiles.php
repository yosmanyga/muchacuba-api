<?php

namespace Muchacuba\Internauta\Advertising;

use Muchacuba\Internauta\Advertising\Profile\ManageStorage;

/**
 * @di\service()
 */
class ResolveProfiles
{
    /**
     * @var ManageStorage
     */
    private $manageStorage;

    /**
     * @param ManageStorage $manageStorage
     */
    public function __construct(
        ManageStorage $manageStorage
    )
    {
        $this->manageStorage = $manageStorage;
    }

    /**
     * Resolves the given amount of profiles for sending the given email.
     * It means that those profiles have never received that email.
     *
     * @param string $email
     * @param int    $amount
     *
     * @return Profile[]
     */
    public function resolve(
        $email,
        $amount
    ) {
        /** @var Profile[] $profiles */
        $profiles = iterator_to_array($this->manageStorage->connect()->find(
            [
                'advertisements' => [
                    '$not' => ['$elemMatch' => ['email' => $email]]
                ]
            ],
            [
                'limit' => $amount
            ]
        ));

        return $profiles;
    }
}
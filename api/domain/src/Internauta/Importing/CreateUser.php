<?php

namespace Muchacuba\Internauta\Importing;

use Muchacuba\Internauta\Advertising\InsertProfile;

/**
 * @di\service({
 *   deductible: true
 * })
 */
class CreateUser
{
    /**
     * @var InsertUser
     */
    private $insertUser;

    /**
     * @var InsertProfile
     */
    private $insertProfile;

    /**
     * @param InsertUser    $insertUser
     * @param InsertProfile $insertProfile
     */
    public function __construct(
        InsertUser $insertUser,
        InsertProfile $insertProfile
    )
    {
        $this->insertUser = $insertUser;
        $this->insertProfile = $insertProfile;
    }

    /**
     * @param string $email
     * @param string $mobile
     */
    public function create($email, $mobile)
    {
        $id = $this->insertUser->insert($email, $mobile);

        $this->insertProfile->insert($id, $email);
    }
}
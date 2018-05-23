<?php

namespace Muchacuba\UpgradeCollectionsTo20180224;

use Yosmy\Unique;
use Yosmy\Facebook;
use Yosmy\Privilege;

/**
 * @di\service()
 */
class UpdateUserProfilesCollections
{
    /**
     * @var Unique\Uniqueness\SelectCollection
     */
    private $selectUniqueUniquenessCollection;

    /**
     * @var Facebook\Profile\SelectCollection
     */
    private $selectFacebookProfileCollection;

    /**
     * @var Privilege\Profile\SelectCollection
     */
    private $selectPrivilegeProfileCollection;

    /**
     * @param Unique\Uniqueness\SelectCollection $selectUniqueUniquenessCollection
     * @param Facebook\Profile\SelectCollection $selectFacebookProfileCollection
     * @param Privilege\Profile\SelectCollection $selectPrivilegeProfileCollection
     */
    public function __construct(
        Unique\Uniqueness\SelectCollection $selectUniqueUniquenessCollection,
        Facebook\Profile\SelectCollection $selectFacebookProfileCollection,
        Privilege\Profile\SelectCollection $selectPrivilegeProfileCollection
    ) {
        $this->selectUniqueUniquenessCollection = $selectUniqueUniquenessCollection;
        $this->selectFacebookProfileCollection = $selectFacebookProfileCollection;
        $this->selectPrivilegeProfileCollection = $selectPrivilegeProfileCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function update()
    {
        $this->updateUniqueUniqueness();
        $this->updateFacebookProfile();
        $this->updatePrivilegeProfile();
    }

    /**
     * {@inheritdoc}
     */
    public function updateUniqueUniqueness()
    {
        $uniquenesses = iterator_to_array($this->selectUniqueUniquenessCollection
            ->select()
            ->find([], [
                'typeMap' => [
                    'root' => 'array',
                    'document' => 'array'
                ],
            ])
        );

        foreach ($uniquenesses as $uniqueness) {
            $this->selectUniqueUniquenessCollection
                ->select()
                ->deleteOne([
                    '_id' => $uniqueness['_id']
                ]);

            $this->selectUniqueUniquenessCollection
                ->select()
                ->insertOne(new Unique\Uniqueness(
                    $uniqueness['_id']
                ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function updateFacebookProfile()
    {
        $profiles = iterator_to_array($this->selectFacebookProfileCollection
            ->select()
            ->find([], [
                'typeMap' => [
                    'root' => 'array',
                    'document' => 'array'
                ],
            ])
        );

        foreach ($profiles as $profile) {
            $this->selectFacebookProfileCollection
                ->select()
                ->deleteOne([
                    '_id' => $profile['_id']
                ]);

            $this->selectFacebookProfileCollection
                ->select()
                ->insertOne(new Facebook\Profile(
                    $profile['_id'],
                    $profile['id'],
                    $profile['name'],
                    $profile['email'],
                    $profile['picture'],
                    time()
                ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function updatePrivilegeProfile()
    {
        $profiles = iterator_to_array($this->selectPrivilegeProfileCollection
            ->select()
            ->find([], [
                'typeMap' => [
                    'root' => 'array',
                    'document' => 'array'
                ],
            ])
        );

        foreach ($profiles as $profile) {
            $this->selectPrivilegeProfileCollection
                ->select()
                ->deleteOne([
                    '_id' => $profile['_id']
                ]);

            $this->selectPrivilegeProfileCollection
                ->select()
                ->insertOne(new Privilege\Profile(
                    $profile['_id'],
                    $profile['roles']
                ));
        }
    }
}
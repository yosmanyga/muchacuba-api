<?php

namespace Muchacuba\Topup;

use Muchacuba\Topup\Product\ManageStorage as ManageServiceStorage;
use MongoDB\UpdateResult;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class UpdateService
{
    /**
     * @var ManageServiceStorage
     */
    private $manageServiceStorage;

    /**
     * @param ManageServiceStorage $manageServiceStorage
     */
    public function __construct(
        ManageServiceStorage $manageServiceStorage
    )
    {
        $this->manageServiceStorage = $manageServiceStorage;
    }

    /**
     * @param string $id
     * @param string $country
     * @param string $name
     * @param string $logo
     *
     * @throws NonExistentServiceException
     */
    public function update($id, $country, $name, $logo)
    {
        /** @var UpdateResult $result */
        $result = $this->manageServiceStorage->connect()->updateOne(
            [
                '_id' => $id,
            ],
            ['$set' => [
                'country' => $country,
                'name' => $name,
                'logo' => $logo
            ]]
        );

        if ($result->getMatchedCount() === 0) {
            throw new NonExistentServiceException();
        }
    }
}
<?php

namespace Muchacuba\Topup;

use Muchacuba\Topup\Provider\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class PickProvider
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
     * @param string $id
     *
     * @return Provider
     *
     * @throws NonExistentProviderException
     */
    public function pick($id)
    {
        /** @var Provider $provider */
        $provider = $this->manageStorage->connect()->findOne([
            '_id' => $id,
        ]);

        if (is_null($provider)) {
            throw new NonExistentProviderException();
        }

        return $provider;
    }
}

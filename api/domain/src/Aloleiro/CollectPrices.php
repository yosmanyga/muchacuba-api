<?php

namespace Muchacuba\Aloleiro;

use Cubalider\Privilege\PickProfile as PickPrivilegeProfile;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class CollectPrices
{
    /**
     * @var PickPrivilegeProfile
     */
    private $pickPrivilegeProfile;

    /**
     * @var CollectPricesAsAdmin
     */
    private $collectPricesAsAdmin;

    /**
     * @var CollectPricesAsSeller
     */
    private $collectPricesAsSeller;

    /**
     * @var CollectPricesAsOperator
     */
    private $collectPricesAsOperator;

    /**
     * @param PickPrivilegeProfile    $pickPrivilegeProfile
     * @param CollectPricesAsAdmin    $collectPricesAsAdmin
     * @param CollectPricesAsSeller   $collectPricesAsSeller
     * @param CollectPricesAsOperator $collectPricesAsOperator
     */
    public function __construct(
        PickPrivilegeProfile $pickPrivilegeProfile,
        CollectPricesAsAdmin $collectPricesAsAdmin,
        CollectPricesAsSeller $collectPricesAsSeller,
        CollectPricesAsOperator $collectPricesAsOperator
    )
    {
        $this->pickPrivilegeProfile = $pickPrivilegeProfile;
        $this->collectPricesAsAdmin = $collectPricesAsAdmin;
        $this->collectPricesAsSeller = $collectPricesAsSeller;
        $this->collectPricesAsOperator = $collectPricesAsOperator;
    }

    /**
     * @param string $uniqueness
     * @param bool   $favorites
     *
     * @return PriceAsAdmin[]|PriceAsSeller[]|PriceAsOperator[]
     *
     * @throws \Exception
     */
    public function collect($uniqueness, $favorites = true)
    {
        $profile = $this->pickPrivilegeProfile->pick($uniqueness);

        if (in_array('admin', $profile->getRoles())) {
            return $this->collectPricesAsAdmin->collect($favorites);
        } elseif (in_array('seller', $profile->getRoles())) {
            return $this->collectPricesAsSeller->collect($uniqueness, $favorites);
        } elseif (in_array('operator', $profile->getRoles())) {
            return $this->collectPricesAsOperator->collect($uniqueness, $favorites);
        }

        throw new \Exception();
    }
}

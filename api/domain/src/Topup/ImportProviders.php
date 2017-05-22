<?php

namespace Muchacuba\Topup;

use Muchacuba\Topup\Payload\ManageStorage as ManagePayloadStorage;
use Muchacuba\Topup\Provider\ManageStorage as ManageProviderStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class ImportProviders
{
    /**
     * @var ManagePayloadStorage
     */
    private $managePayloadStorage;

    /**
     * @var ManageProviderStorage
     */
    private $manageProviderStorage;

    /**
     * @param ManagePayloadStorage  $managePayloadStorage
     * @param ManageProviderStorage $manageProviderStorage
     */
    public function __construct(
        ManagePayloadStorage $managePayloadStorage,
        ManageProviderStorage $manageProviderStorage
    )
    {
        $this->managePayloadStorage = $managePayloadStorage;
        $this->manageProviderStorage = $manageProviderStorage;
    }

    public function import()
    {
        $this->manageProviderStorage->purge();

        /** @var Payload[] $providers */
        $providers = $this->managePayloadStorage->connect()->find([
            'type' => Payload::TYPE_PROVIDER
        ]);

        /** @var Payload[] $providerLogos */
        $providerLogos = $this->managePayloadStorage->connect()->find([
            'type' => Payload::TYPE_PROVIDER_LOGO
        ]);

        foreach ($providers as $provider) {
            $logo = null;
            foreach ($providerLogos as $providerLogo) {
                if ($providerLogo->getData()['ProviderCode'] == $provider->getData()['ProviderCode']) {
                    $logo = $providerLogo->getData()['Logo'];

                    break;
                }
            }

            $this->manageProviderStorage->connect()->insertOne(new Provider(
                $provider['ProviderCode'],
                $provider['CountryIso'],
                $provider['Name'],
                $logo,
                $provider['ValidationRegex']
            ));
        }
    }
}

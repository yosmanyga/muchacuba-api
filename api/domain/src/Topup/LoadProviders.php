<?php

namespace Muchacuba\Topup;

use Aura\Cli\Exception;
use Muchacuba\Topup\Payload\ManageStorage as ManagePayloadStorage;
use Muchacuba\Topup\Provider\ManageStorage as ManageProviderStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class LoadProviders
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

    public function load()
    {
        $this->manageProviderStorage->purge();

        /** @var Payload[] $providers */
        $providers = iterator_to_array($this->managePayloadStorage->connect()->find([
            'type' => Payload::TYPE_PROVIDER
        ]));

        /** @var Payload[] $providerLogos */
        $providerLogos = iterator_to_array($this->managePayloadStorage->connect()->find([
            'type' => Payload::TYPE_PROVIDER_LOGO
        ]));

        foreach ($providers as $provider) {
            $logo = null;
            foreach ($providerLogos as $providerLogo) {
                if ($providerLogo->getData()['ProviderCode'] == $provider->getData()['ProviderCode']) {
                    $logo = $providerLogo->getData()['Logo'];

                    break;
                }
            }

            $validation = $provider->getData()['ValidationRegex'];
            if (strpos($validation, '0-9') !== false) {
                $type = Provider::TYPE_PHONE;
            } elseif (strpos($provider->getData()['ValidationRegex'], '@') !== false) {
                $type = Provider::TYPE_EMAIL;
            } else {
                throw new \Exception(sprintf('Unknown validation "%s"', $validation));
            }

            $this->manageProviderStorage->connect()->insertOne(new Provider(
                $provider->getData()['ProviderCode'],
                $provider->getData()['CountryIso'],
                $provider->getData()['Name'],
                $logo,
                $validation,
                $type
            ));
        }
    }
}

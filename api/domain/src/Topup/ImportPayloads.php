<?php

namespace Muchacuba\Topup;

use Muchacuba\Topup\Payload\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class ImportPayloads
{
    /**
     * @var QueryApi
     */
    private $queryApi;

    /**
     * @var ManageStorage
     */
    private $manageStorage;

    /**
     * @param QueryApi      $queryApi
     * @param ManageStorage $manageStorage
     */
    public function __construct(
        QueryApi $queryApi,
        ManageStorage $manageStorage
    )
    {
        $this->queryApi = $queryApi;
        $this->manageStorage = $manageStorage;
    }

    public function import()
    {
        $this->manageStorage->purge();

        $this->importCurrencies();
        $this->importRegions();
        $this->importCountries();
        $this->importProviders();
        $this->importProducts();
        $this->importProductDescriptions();
        $this->importPromotions();
        $this->importPromotionDescriptions();
    }

    private function importCurrencies()
    {
        $currencies = $this->queryApi->query(
            'GET',
            '/api/EdtsV3/GetCurrencies'
        );

        foreach ($currencies['Items'] as $currencies) {
            $this->manageStorage->connect()->insertOne(new Payload(
                uniqid(),
                Payload::TYPE_CURRENCY,
                $currencies
            ));
        }
    }

    private function importRegions()
    {
        $regions = $this->queryApi->query(
            'GET',
            '/api/EdtsV3/GetRegions'
        );

        foreach ($regions['Items'] as $region) {
            $this->manageStorage->connect()->insertOne(new Payload(
                uniqid(),
                Payload::TYPE_REGION,
                $region
            ));
        }
    }

    private function importCountries()
    {
        $countries = $this->queryApi->query(
            'GET',
            '/api/EdtsV3/GetCountries'
        );

        foreach ($countries['Items'] as $countries) {
            $this->manageStorage->connect()->insertOne(new Payload(
                uniqid(),
                Payload::TYPE_COUNTRY,
                $countries
            ));
        }
    }
    
    private function importProviders()
    {
        $providers = $this->queryApi->query(
            'GET',
            '/api/EdtsV3/GetProviders'
        );

        foreach ($providers['Items'] as $provider) {
            $this->manageStorage->connect()->insertOne(new Payload(
                uniqid(),
                Payload::TYPE_PROVIDER,
                $provider
            ));
        }

        $this->importProviderLogos($providers);
    }

    private function importProviderLogos($providers)
    {
        foreach ($providers['Items'] as $provider) {
            try {
                $logo = base64_encode(file_get_contents(sprintf(
                    'https://imagerepo.ding.com/logo/%s/%s.png?height=34',
                    substr($provider['ProviderCode'], 0, 2),
                    substr($provider['ProviderCode'], 2, 2)
                )));
            } catch (\Exception $e) {
                $logo = null;
            }

            $this->manageStorage->connect()->insertOne(new Payload(
                uniqid(),
                Payload::TYPE_PROVIDER_LOGO,
                [
                    'ProviderCode' => $provider['ProviderCode'],
                    'Logo' => $logo
                ]
            ));
        }
    }
    
    private function importProducts()
    {
        $products = $this->queryApi->query(
            'GET',
            '/api/EdtsV3/GetProducts'
        );

        foreach ($products['Items'] as $product) {
            $this->manageStorage->connect()->insertOne(new Payload(
                uniqid(),
                Payload::TYPE_PRODUCT,
                $product
            ));
        }
    }

    private function importProductDescriptions()
    {
        $descriptions = $this->queryApi->query(
            'GET',
            '/api/EdtsV3/GetProductDescriptions'
        );

        foreach ($descriptions['Items'] as $description) {
            $this->manageStorage->connect()->insertOne(new Payload(
                uniqid(),
                Payload::TYPE_PRODUCT_DESCRIPTION,
                $description
            ));
        }
    }

    private function importPromotions()
    {
        $promotions = $this->queryApi->query(
            'GET',
            '/api/EdtsV3/GetPromotions'
        );

        foreach ($promotions['Items'] as $promotion) {
            $this->manageStorage->connect()->insertOne(new Payload(
                uniqid(),
                Payload::TYPE_PROMOTION,
                $promotion
            ));
        }
    }

    private function importPromotionDescriptions()
    {
        $descriptions = $this->queryApi->query(
            'GET',
            '/api/EdtsV3/GetPromotionDescriptions'
        );

        foreach ($descriptions['Items'] as $description) {
            $this->manageStorage->connect()->insertOne(new Payload(
                uniqid(),
                Payload::TYPE_PROMOTION_DESCRIPTION,
                $description
            ));
        }
    }
}

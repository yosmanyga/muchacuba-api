<?php

namespace Muchacuba\Topup;

use Muchacuba\Topup\Payload\ManageStorage as ManagePayloadStorage;
use Muchacuba\Topup\Product\ManageStorage as ManageProductStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class LoadProducts
{
    /**
     * @var ManagePayloadStorage
     */
    private $managePayloadStorage;

    /**
     * @var ManageProductStorage
     */
    private $manageProductStorage;

    /**
     * @param ManagePayloadStorage $managePayloadStorage
     * @param ManageProductStorage $manageProductStorage
     */
    public function __construct(
        ManagePayloadStorage $managePayloadStorage,
        ManageProductStorage $manageProductStorage
    )
    {
        $this->managePayloadStorage = $managePayloadStorage;
        $this->manageProductStorage = $manageProductStorage;
    }

    public function load()
    {
        $this->manageProductStorage->purge();

        /** @var Payload[] $products */
        $products = iterator_to_array($this->managePayloadStorage->connect()->find([
            'type' => Payload::TYPE_PRODUCT
        ]));

        /** @var Payload[] $productDescriptions */
        $productDescriptions = iterator_to_array($this->managePayloadStorage->connect()->find([
            'type' => Payload::TYPE_PRODUCT_DESCRIPTION
        ]));

        foreach ($products as $product) {
            $description = null;
            foreach ($productDescriptions as $productDescription) {
                if ($productDescription->getData()['LocalizationKey'] == $product->getData()['SkuCode']) {
                    if (
                        (
                            // Spanish?
                            $productDescription->getData()['LanguageCode'] == 'es'
                            // English and no spanish?
                            || (
                                is_null($description)
                                && $productDescription->getData()['LanguageCode'] == 'en'
                            )
                        )
                        // Description not empty?
                        && (
                            $productDescription->getData()['DisplayText'] != null
                            && $productDescription->getData()['DescriptionMarkdown'] != null
                        )
                    ) {
                        $description = sprintf(
                            "%s\r\r%s",
                            $productDescription->getData()['DisplayText'],
                            $productDescription->getData()['DescriptionMarkdown']
                        );

                        break;
                    }
                }
            }

            $this->manageProductStorage->connect()->insertOne(new Product(
                $product->getData()['SkuCode'],
                $product->getData()['ProviderCode'],
                $description
            ));
        }
    }
}

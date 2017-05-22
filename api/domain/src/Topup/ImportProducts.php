<?php

namespace Muchacuba\Topup;

use Muchacuba\Topup\Payload\ManageStorage as ManagepayloadStorage;
use Muchacuba\Topup\Provider\ManageStorage as ManageProductStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class ImportProducts
{
    /**
     * @var ManagepayloadStorage
     */
    private $managepayloadStorage;

    /**
     * @var ManageProductStorage
     */
    private $manageProductStorage;

    /**
     * @param ManagepayloadStorage     $managepayloadStorage
     * @param ManageProductStorage $manageProductStorage
     */
    public function __construct(
        ManagepayloadStorage $managepayloadStorage,
        ManageProductStorage $manageProductStorage
    )
    {
        $this->managepayloadStorage = $managepayloadStorage;
        $this->manageProductStorage = $manageProductStorage;
    }

    public function import()
    {
        $this->manageProductStorage->purge();

        $products = $this->managepayloadStorage->connect()->find([
            'type' => Payload::TYPE_PRODUCT
        ]);

        $productDescriptions = $this->managepayloadStorage->connect()->find([
            'type' => Payload::TYPE_PRODUCT_DESCRIPTION
        ]);

        foreach ($products['Items'] as $product) {
            $description = null;
            foreach ($productDescriptions['Items'] as $productDescription) {
                if ($productDescription['LocalizationKey'] == $product['SkuCode']) {
                    if (
                        (
                            // Spanish?
                            $productDescription['LanguageCode'] == 'es'
                            // English and no spanish?
                            || (
                                is_null($description)
                                && $productDescription['LanguageCode'] == 'en'
                            )
                        )
                        // Description not empty?
                        && (
                            $productDescription['DisplayText'] != null
                            && $productDescription['DescriptionMarkdown'] != null
                        )
                    ) {
                        $description = sprintf(
                            "%s\r\r%s",
                            $productDescription['DisplayText'],
                            $productDescription['DescriptionMarkdown']
                        );

                        break;
                    }
                }
            }

            $this->manageProductStorage->connect()->insertOne(new Product(
                $product['SkuCode'],
                $product['ProviderCode'],
                $description
            ));
        }
    }
}

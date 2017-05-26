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

        $valuesCombinations = [];
        foreach ($products as $j => $product) {
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

            $values = [];
            $min = (string) $product->getData()['Minimum']['SendValue'];
            $max = (string) $product->getData()['Maximum']['SendValue'];

            if ($min != $max) {
                $combinations['1']['12.8'] = [ 1,    3,  5,  8, 10, 12.8];
                $combinations['1']['27.5'] = [ 1,    5, 10, 15, 20, 27.5];
                $combinations['1']['100']  = [ 1,    5, 10, 50, 70,  100];
                $combinations['2']['70']   = [ 2,    5, 10, 20, 50,   70];
                $combinations['3']['70']   = [ 3,    5, 10, 20, 50,   70];
                $combinations['3']['120']  = [ 3,   10, 20, 50, 100, 120];
                $combinations['4']['70']   = [ 4,   10, 20, 30, 50,   70];
                $combinations['4.5']['70'] = [ 4.5, 10, 20, 30, 50,   70];
                $combinations['5']['10']   = [ 5,    6,  7,  8,  9,   10];
                $combinations['5']['14']   = [ 5,    7,  8, 10, 12,   14];
                $combinations['5']['17']   = [ 5,    8, 10, 12, 15,   17];
                $combinations['5']['31']   = [ 5,   10, 15, 20, 25,   31];
                $combinations['5']['35']   = [ 5,   10, 15, 20, 25,   35];
                $combinations['5']['45']   = [ 5,   10, 20, 30, 40,   45];
                $combinations['5']['50']   = [ 5,   10, 20, 30, 40,   50];
                $combinations['5']['60']   = [ 5,   10, 20, 30, 40,   60];
                $combinations['5']['70']   = [ 5,   10, 20, 30, 50,   70];
                $combinations['5']['100']  = [ 5,   10, 20, 40, 80,  100];
                $combinations['6']['70']   = [ 6,   10, 20, 30, 50,   70];
                $combinations['6']['41']   = [ 6,   10, 15, 20, 30,   41];
                $combinations['10']['50']  = [10,   15, 20, 30, 40,   50];
                $combinations['10']['67']  = [10,   15, 20, 30, 50,   67];
                $combinations['10']['99']  = [10,   15, 20, 50, 80,   99];
                $combinations['10']['100'] = [10,   15, 20, 50, 80,  100];
                $combinations['12']['54']  = [12,   15, 20, 30, 40,   54];
                $combinations['15']['100'] = [15,   20, 30, 50, 80,  100];
                $combinations['15']['150'] = [15,   20, 40, 70, 100, 150];

                if (isset($combinations[$min][$max])) {
                    $values = $combinations[$min][$max];
                } else {
                    throw new \Exception();
                }
            } else {
                $values[] = $product->getData()['Minimum']['SendValue'];
            }

            if (
                $values[0] != $min
                || $values[count($values) - 1] != $max
            ) {
                throw new \Exception();
            }

            if (count($values) > 1 && !in_array($values, $valuesCombinations)) {
                $valuesCombinations[] = $values;
            }

            foreach ($values as $value) {
                $this->manageProductStorage->connect()->insertOne(new Product(
                    $product->getData()['SkuCode'],
                    $value,
                    $product->getData()['ProviderCode'],
                    $description
                ));
            }
        }
    }
}

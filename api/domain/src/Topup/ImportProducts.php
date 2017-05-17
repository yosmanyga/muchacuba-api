<?php

namespace Muchacuba\Topup;

use Muchacuba\Topup\Product\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class ImportProducts
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
        $products = $this->queryApi->query(
            'GET',
            '/api/EdtsV3/GetProducts'
        );

        foreach ($products['Items'] as $product) {
            $this->manageStorage->connect()->insertOne(new Product(
                $product['SkuCode'],
                $product
            ));
        }
    }
}

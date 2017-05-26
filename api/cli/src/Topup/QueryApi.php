<?php

namespace Muchacuba\Cli\Topup;

use GuzzleHttp\Exception\RequestException;
use Muchacuba\Topup\QueryApi as DomainQueryApi;

/**
 * @di\command({deductible: true})
 */
class QueryApi
{
    /**
     * @var DomainQueryApi
     */
    private $queryApi;

    /**
     * @param DomainQueryApi $queryApi
     */
    public function __construct(DomainQueryApi $queryApi)
    {
        $this->queryApi = $queryApi;
    }

    /**
     * @cli\resolution({command: "topup.query_api"})
     */
    public function query()
    {
        try {

            $response = $this->queryApi->query(
                'GET',
                '/api/EdtsV3/GetPromotions',
                [
                ]
            );

//            // Filtrar productos por pais
//            $response = $this->queryApi->query(
//                'GET',
//                '/api/EdtsV3/GetProducts?countryIsos=CU',
//                [
//                ]
//            );

//            // Filtrar productos por numero de telefono
//            $response = $this->queryApi->query(
//                'GET',
//                '/api/EdtsV3/GetProducts?accountNumber=5355546837',
//                [
//                ]
//            );

//            $response = $this->queryApi->query(
//                'GET',
//                '/api/EdtsV3/GetAccountLookup?AccountNumber=5354184248',
//                [
//                ]
//            );

            // No pincha
//            $response = $this->queryApi->query(
//                'POST',
//                '/api/EdtsV3/EstimatePrices',
//                [
//                    'SendValue' => 50,
//                    'SendCurrencyIso' => 'USD',
//                    'ReceiveValue' => 0,
//                    'SkuCode' => 'CU_CU_TopUp',
//                    'BatchItemRef' => uniqid()
//                ]
//            );

//            $response = $this->queryApi->query(
//                'POST',
//                '/api/EdtsV3/ListTransferRecords',
//                [
//                    'Take' => 10
//                ]
//            );
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                echo (string) $e->getResponse()->getBody();
            }
        }
    }
}

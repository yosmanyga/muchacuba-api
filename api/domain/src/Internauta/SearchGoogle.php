<?php

namespace Muchacuba\Internauta;

use Cubalider\Navigation\RequestPage;
use GuzzleHttp\Client;

/**
 * @di\service({
 *     deductible: true,
 *     private: true
 * })
 */
class SearchGoogle
{
    /**
     * @var RequestPage
     */
    private $requestPage;

    /**
     * @param RequestPage $requestPage
     */
    public function __construct(
        RequestPage $requestPage
    )
    {
        $this->requestPage = $requestPage;
    }

    /**
     * @param string   $key
     * @param string   $cx
     * @param string   $q
     * @param int|null $num
     * @param int|null $start
     * @param string   $extra
     *
     * @return array
     */
    public function search($key, $cx, $q, $num = 10, $start = null, $extra = null)
    {
        $response = (new Client())->get(sprintf(
            'https://www.googleapis.com/customsearch/v1?key=%s&cx=%s&q=%s&num=%s&start=%s%s',
            $key,
            $cx,
            urlencode($q),
            min((int) $num, 10), // 10 is max allowed by api
            $start ?: 1,
            $extra ? sprintf('&%s', $extra) : ''
        ));

        $data = json_decode($response->getBody()->getContents(), true);

        if (!isset($data['items'])) {
            return [];
        }

        return $data['items'];
    }
}
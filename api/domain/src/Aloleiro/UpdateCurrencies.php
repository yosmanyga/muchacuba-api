<?php

namespace Muchacuba\Aloleiro;

use GuzzleHttp\Client as GuzzleClient;
use Goutte\Client as GoutteClient;
use MongoDB\UpdateResult;
use Muchacuba\Aloleiro\Currency\ManageStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class UpdateCurrencies
{
    /**
     * @var ManageStorage
     */
    private $manageStorage;

    /**
     * @var SendEmail
     */
    private $sendEmail;

    /**
     * @param ManageStorage $manageStorage
     * @param SendEmail     $sendEmail
     */
    public function __construct(
        ManageStorage $manageStorage,
        SendEmail $sendEmail
    )
    {
        $this->manageStorage = $manageStorage;
        $this->sendEmail = $sendEmail;
    }

    /**
     */
    public function update()
    {
        $notifications = array_merge(
            [$this->updateEUR()],
            [$this->updateVEF()]
        );

        $notifications = array_filter($notifications, function($notification) {
            return !empty($notification);
        });

//        $this->notify($notifications);
    }

    /**
     * @return array
     */
    private function updateEUR()
    {
        $value = (new GoutteClient())
            ->request(
                'GET',
                'http://www.xe.com/currencyconverter/convert/?Amount=1&From=EUR&To=USD'
            )
            ->filter('span.uccResultAmount')
            ->first()
            ->getNode(0)
            ->textContent;

        return $this->insertOrUpdate('EUR', (float) $value);
    }

    /**
     * @return array
     */
    private function updateVEF()
    {
        $value = (string) (new GuzzleClient())
            ->request(
                'GET',
                'https://dxj1e0bbbefdtsyig.woldrssl.net/custom/rate.js'
            )
            ->getBody();

        $value = str_replace('var dolartoday =', '', $value);
        $value = json_decode($value, true);
        $value = $value['USD']['transfer_cucuta'];

        return $this->insertOrUpdate('VEF', $value);
    }

    /**
     * @param string $code
     * @param float  $value
     *
     * @return array
     */
    private function insertOrUpdate($code, $value)
    {
        $currency = $this->manageStorage->connect()->findOne(['_id' => $code]);

        /** @var UpdateResult $result */
        $result = $this->manageStorage->connect()->updateOne(
            ['_id' => $code],
            ['$set' => ['value' => $value]],
            ['upsert' => true]
        );

        if ($result->getModifiedCount() > 0) {
            return [
                'code' => $code,
                'old' => $currency->value,
                'new' => $value
            ];
        }

        return [];
    }

    /**
     * @param array $notifications
     */
    private function notify($notifications)
    {
        if (empty($notifications)) {
            return;
        }

        $body = "Las siguientes monedas cambiaron:\n";
        foreach ($notifications as $notification) {
            $body .= sprintf(
                "%s, antes: %s, ahora: %s.\n",
                $notification['code'],
                $notification['old'],
                $notification['new']
            );
        }

        $this->sendEmail->send(
            'yosmanyga@gmail.com, admin@jimenezsolutions.com.ve',
            'Cambio de moneda',
            $body
        );
    }

//    /**
//     * @throws \Exception
//     */
//    private function updateVEF()
//    {
//        $value = (new Client())
//            ->request(
//                'GET',
//                'http://d1fbzr3krofnqb.cloudfront.net/wp-content/themes/Newsmag/indicadores.php'
//            )
//            ->filter('table')
//            ->eq(3)
//            ->filter('tr')
//            ->eq(2)
//            ->filter('td')
//            ->eq(1)
//            ->filter('h2')
//            ->first()
//            ->getNode(0)
//            ->textContent;
//
//        $value = str_replace(['BsF', ',', ' '], [], $value);
//
//        $this->manageStorage->update('Venezuela', $value);
//    }
}
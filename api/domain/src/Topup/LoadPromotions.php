<?php

namespace Muchacuba\Topup;

use Muchacuba\Topup\Payload\ManageStorage as ManagePayloadStorage;
use Muchacuba\Topup\Promotion\ManageStorage as ManagePromotionStorage;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class LoadPromotions
{
    /**
     * @var ManagePayloadStorage
     */
    private $managePayloadStorage;

    /**
     * @var ManagePromotionStorage
     */
    private $managePromotionStorage;

    /**
     * @param ManagePayloadStorage   $managePayloadStorage
     * @param ManagePromotionStorage $managePromotionStorage
     */
    public function __construct(
        ManagePayloadStorage $managePayloadStorage,
        ManagePromotionStorage $managePromotionStorage
    )
    {
        $this->managePayloadStorage = $managePayloadStorage;
        $this->managePromotionStorage = $managePromotionStorage;
    }

    public function load()
    {
        $this->managePromotionStorage->purge();

        /** @var Payload[] $promotions */
        $promotions = iterator_to_array($this->managePayloadStorage->connect()->find([
            'type' => Payload::TYPE_PROMOTION
        ]));

        /** @var Payload[] $promotionDescriptions */
        $promotionDescriptions = iterator_to_array($this->managePayloadStorage->connect()->find([
            'type' => Payload::TYPE_PROMOTION_DESCRIPTION
        ]));

        foreach ($promotions as $promotion) {
            $description = null;
            foreach ($promotionDescriptions as $promotionDescription) {
                if ($promotionDescription->getData()['LocalizationKey'] == $promotion->getData()['LocalizationKey']) {
                    if (
                        (
                            // Spanish?
                            $promotionDescription->getData()['LanguageCode'] == 'es'
                            // English and no spanish?
                            || (
                                is_null($description)
                                && $promotionDescription->getData()['LanguageCode'] == 'en'
                            )
                        )
                    ) {
                        $description = sprintf(
                            "%s\r\r%s\r\r%s\r\r%s",
                            $promotionDescription->getData()['Headline'],
                            $promotionDescription->getData()['TermsAndConditionsMarkDown'],
                            $promotionDescription->getData()['BonusValidity'],
                            $promotionDescription->getData()['PromotionType']
                        );

                        break;
                    }
                }
            }

            $this->managePromotionStorage->connect()->insertOne(new Promotion(
                uniqid(),
                $promotion->getData()['ProviderCode'],
                $description
            ));
        }
    }
}

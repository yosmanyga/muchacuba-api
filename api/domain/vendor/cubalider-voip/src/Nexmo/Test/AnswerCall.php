<?php

namespace Cubalider\Voip\Nexmo\Test;

use Cubalider\Voip\Nexmo\AnswerCall as BaseAnswerCall;
use Faker\Factory;
use Faker\Generator;
use Muchacuba\Aloleiro\NonExistentCallException;
use Muchacuba\Aloleiro\PickCall;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class AnswerCall
{
    /**
     * @var Generator
     */
    private $faker;

    /**
     * @var PickCall
     */
    private $pickCall;
    
    /**
     * @var BaseAnswerCall
     */
    private $answerCall;

    /**
     * @param BaseAnswerCall $answerCall
     * @param PickCall       $pickCall
     */
    public function __construct(
        BaseAnswerCall $answerCall,
        PickCall $pickCall
    )
    {
        $this->faker = Factory::create('es_ES');
        $this->answerCall = $answerCall;
        $this->pickCall = $pickCall;
    }

    /**
     * @param string|null $from
     * @param string|null $to
     *
     * @return array
     */
    public function answer($from = null, $to = null)
    {
        return $this->answerCall->answer([
            'conversation_uuid' => $this->faker->uuid,
            'from' => $from !== null ? $from : $this->generateNumber(),
            'to' => $to !== null ? $to : $this->generateNumber(),
        ]);
    }

    /**
     * @return string
     */
    private function generateNumber()
    {
        do {
            $number = $this->faker->phoneNumber;

            try {
                $this->pickCall->pick($number);

                $ok = false;
            } catch (NonExistentCallException $e) {
                $ok = true;
            }
        } while ($ok == false);

        return $number;
    }
}
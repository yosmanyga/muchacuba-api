<?php

namespace Muchacuba\Aloleiro;

use Muchacuba\Aloleiro\Business\InsufficientBalanceException;
use Muchacuba\Aloleiro\Call\ManageStorage as ManageCallStorage;
use Muchacuba\Aloleiro\Call\InvalidDataException;
use Muchacuba\Aloleiro\Phone\FixNumber;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class PrepareCall
{
    /**
     * @var FixNumber
     */
    private $fixNumber;

    /**
     * @var ManageCallStorage
     */
    private $manageCallStorage;

    /**
     * @param FixNumber         $fixNumber
     * @param ManageCallStorage $manageCallStorage
     */
    public function __construct(
        FixNumber $fixNumber,
        ManageCallStorage $manageCallStorage
    )
    {
        $this->fixNumber = $fixNumber;
        $this->manageCallStorage = $manageCallStorage;
    }

    /**
     * @param Business    $business
     * @param Phone       $phone
     * @param string      $to
     * @param string|null $id
     *
     * @return Call
     *
     * @throws InvalidDataException
     * @throws InsufficientBalanceException
     */
    public function prepare(Business $business, Phone $phone, $to, $id = null)
    {
        try {
            $to = $this->fixNumber->fix($to);
        } catch (InvalidDataException $e) {
            throw $e;
        }

        if ($business->getBalance() <= 0) {
            throw new InsufficientBalanceException();
        }

        $call = new Call(
            $id ?: uniqid(),
            $business->getId(),
            $phone->getNumber(),
            $to,
            []
        );

        $this->manageCallStorage->connect()->insertOne($call);

        return $call;
    }
}
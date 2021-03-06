<?php

namespace Muchacuba\Internauta;

use Muchacuba\Internauta\Request\ManageStorage;
use Muchacuba\Internauta\Delegate\ProcessRequest;
use Email\Parse;

/**
 * @di\service()
 */
class ProcessRequests
{
    /**
     * @var ManageStorage
     */
    private $manageStorage;

    /**
     * @var ProcessRequest
     */
    private $processRequest;

    /**
     * @var SendEmail
     */
    private $sendEmail;

    /**
     * @var InsertLog
     */
    private $insertLog;

    /**
     * @param ManageStorage  $manageStorage
     * @param ProcessRequest $processRequest
     * @param InsertLog      $insertLog
     * @param SendEmail      $sendEmail
     */
    public function __construct(
        ManageStorage $manageStorage,
        ProcessRequest $processRequest,
        InsertLog $insertLog,
        SendEmail $sendEmail
    ) {
        $this->manageStorage = $manageStorage;
        $this->processRequest = $processRequest;
        $this->insertLog = $insertLog;
        $this->sendEmail = $sendEmail;
    }

    /**
     * @cli\resolution({command: "/internauta/process-requests"})
     *
     * @return int
     */
    public function process()
    {
        /** @var Request[] $requests */
        $requests = $this->manageStorage->connect()->find();

        $i = 0;
        foreach ($requests as $request) {
            $to = Parse::getInstance()->parse(
                $request->getTo()
            )['email_addresses'][0]['simple_address'];

            /* Spam */

            if (
                strpos($request->getFrom(), '.date') !== false
                || $to == 'sales@muchacuba.com'
            ) {
                $this->manageStorage->connect()->deleteOne([
                    '_id' => $request->getId(),
                ]);

                continue;
            }

            /** @var Event[] $events */
            $events = [];

            $events[] = new Event(
                $this,
                'BeginProcessing',
                [
                    'id' => $request->getId()
                ]
            );

            try {
                $processResult = $this->processRequest->process(
                    $request->getFrom(),
                    $to,
                    $this->cleanSubject($request->getSubject()),
                    $request->getBody()
                );

                $events = array_merge(
                    $events,
                    $processResult->getEvents()
                );

                foreach ($processResult->getResponses() as $response) {
                    $sendResult = $this->sendEmail->send(
                        $response->getFrom(),
                        $response->getTo(),
                        $response->getSubject(),
                        $response->getBody(),
                        $response->getAttachments()
                    );

                    $events = array_merge(
                        $events,
                        $sendResult->getEvents()
                    );
                }
            } catch (\Exception $e) {
                // Log for future debugging

                $events[] = new Event(
                    $this,
                    'Exception',
                    [
                        'id' => $request->getId(),
                        'exception' => $e->__toString()
                    ]
                );
            }

            $this->manageStorage->connect()->deleteOne([
                '_id' => $request->getId(),
            ]);

            // Event logging
            foreach($events as $event) {
                $payload = $event->getPayload();
                if (!isset($payload['id'])) {
                    $payload['id'] = $request->getId();
                }

                $this->insertLog->insert(
                    $event->getType(),
                    $payload,
                    $event->getDate()
                );
            }

            $i++;
        }

        return $i;
    }

    /**
     * @param string $subject
     *
     * @return string
     */
    private function cleanSubject($subject)
    {
        return trim(str_replace('Re:', '', $subject));
    }
}

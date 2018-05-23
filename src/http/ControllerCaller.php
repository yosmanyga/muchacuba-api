<?php

namespace Muchacuba\Http;

use Symsonte\Http\Server\ControllerCaller as BaseControllerCaller;

/**
 * @di\service({
 *     private: true
 * })
 */
class ControllerCaller implements BaseControllerCaller
{
    /**
     * @var string
     */
    private $raven;

    /**
     * @di\arguments({
     *     raven: "%raven%",
     * })
     *
     * @param string $raven
     */
    public function __construct(string $raven)
    {
        $this->raven = $raven;
    }

    /**
     * {@inheritdoc}
     */
    public function call($controller, $method, $parameters)
    {
        $client = new \Raven_Client($this->raven);

        $error_handler = new \Raven_ErrorHandler($client);
        $error_handler->registerExceptionHandler();
        $error_handler->registerErrorHandler();
        $error_handler->registerShutdownFunction();

        try {
            $result = call_user_func_array([$controller, $method], $parameters);

            $response = [
                'code' => 'success',
                'payload' => $result
            ];
        } catch (\Exception $e) {
            if (strpos($e, 'Muchacuba') === 0) {
                $code = $this->generateKey(
                    str_replace(
                        'Muchacuba\\',
                        '',
                        get_class($e)
                    )
                );

                $response = [
                    'code' => $code,
                    'payload' => []
                ];
            } else {
                throw $e;
            }
        } catch (\Throwable $e) {
            throw $e;
        }

        return $response;
    }

    /**
     * @param string $class
     *
     * @return string
     */
    private function generateKey($class)
    {
        return strtolower(
            strtr(
                preg_replace(
                    '/(?<=[a-zA-Z0-9])[A-Z]/',
                    '-\\0',
                    $class
                ),
                '\\',
                '.'
            )
        );
    }
}

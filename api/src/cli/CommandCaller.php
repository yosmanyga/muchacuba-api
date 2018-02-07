<?php

namespace Muchacuba\Cli;

use Muchacuba\Exception;
use Symsonte\Cli\Server\CommandCaller as BaseCommandCaller;

/**
 * @di\service({
 *     private: true
 * })
 */
class CommandCaller implements BaseCommandCaller
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
    public function call($command, $method, $parameters)
    {
        $client = new \Raven_Client($this->raven);

        $error_handler = new \Raven_ErrorHandler($client);
        $error_handler->registerExceptionHandler();
        $error_handler->registerErrorHandler();
        $error_handler->registerShutdownFunction();

        try {
            $payload = call_user_func_array([$command, $method], $parameters);

            $response = $payload;
        } catch (Exception $e) {
            $response = $this->generateKey($e);
        } catch (\Exception $e) {
            throw $e;
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
                    (new \ReflectionClass($class))->getShortName()
                ),
                '\\',
                '.'
            )
        );
    }
}

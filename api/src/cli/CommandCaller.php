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
     * @var callable
     */
    private $logException;

    /**
     * {@inheritdoc}
     */
    public function call($command, $method, $parameters)
    {
        try {
            $payload = call_user_func_array([$command, $method], $parameters);

            $response = $payload;
        } catch (Exception $e) {
            $response = $this->generateKey($e);
        } catch (\Exception $e) {
            //call_user_func_array([$this->logException, '__invoke'], [$e]);

            $response = $this->generateKey($e);
        } catch (\Throwable $e) {
            throw $e;
            //call_user_func_array([$this->logException, '__invoke'], [$e]);

//            $response = [
//                'code' => 'failure',
//            ];
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

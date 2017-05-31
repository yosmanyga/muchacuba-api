<?php

namespace Muchacuba\Http;

use Muchacuba\Exception;
use Symsonte\Http\Server\ControllerCaller as BaseControllerCaller;

/**
 * @di\service({
 *     private: true
 * })
 */
class ControllerCaller implements BaseControllerCaller
{
    /**
     * @var callable
     */
    private $logException;

    /**
     * {@inheritdoc}
     */
    public function call($controller, $method, $parameters)
    {
        try {
            $result = call_user_func_array([$controller, $method], $parameters);

            $response = [
                'code' => 'success',
                'payload' => $result
            ];
        } catch (Exception $e) {
            $response = [
                'code' => $this->generateKey($e)
            ];
        } catch (\Exception $e) {
            //call_user_func_array([$this->logException, '__invoke'], [$e]);

            $response = [
                'code' => $this->generateKey($e)
            ];
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

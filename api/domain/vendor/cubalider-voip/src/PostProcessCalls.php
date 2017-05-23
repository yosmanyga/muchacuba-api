<?php

namespace Cubalider\Voip;

use Cubalider\Voip\Nexmo\PostProcessCalls as PostProcessNexmoCalls;
use Cubalider\Voip\Sinch\PostProcessCalls as PostProcessSinchCalls;

/**
 * @di\service({
 *     deductible: true
 * })
 */
class PostProcessCalls
{
    /**
     * @var PostProcessNexmoCalls
     */
    private $postProcessNexmoCalls;

    /**
     * @var PostProcessSinchCalls
     */
    private $postProcessSinchCalls;

    /**
     * @param PostProcessNexmoCalls $postProcessNexmoCalls
     * @param PostProcessSinchCalls $postProcessSinchCalls
     */
    public function __construct(
        PostProcessNexmoCalls $postProcessNexmoCalls,
        PostProcessSinchCalls $postProcessSinchCalls
    )
    {
        $this->postProcessNexmoCalls = $postProcessNexmoCalls;
        $this->postProcessSinchCalls = $postProcessSinchCalls;
    }

    /**
     * {@inheritdoc}
     */
    public function postProcess()
    {
        $this->postProcessNexmoCalls->postProcess();
        $this->postProcessSinchCalls->postProcess();
    }
}
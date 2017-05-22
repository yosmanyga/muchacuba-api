<?php

namespace Cubalider\Voip;

use Cubalider\Voip\Nexmo\PostProcessCalls as PostProcessNexmoCalls;

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
     * @param PostProcessNexmoCalls $postProcessNexmoCalls
     */
    public function __construct(
        PostProcessNexmoCalls $postProcessNexmoCalls
    )
    {
        $this->postProcessNexmoCalls = $postProcessNexmoCalls;
    }

    /**
     * {@inheritdoc}
     */
    public function postProcess()
    {
        $this->postProcessNexmoCalls->postProcess();
    }
}
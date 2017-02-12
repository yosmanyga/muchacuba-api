<?php

namespace Muchacuba\Internauta;

use Behat\Behat\Context\Context as BaseContext;
use Behat\Gherkin\Node\PyStringNode;
use Coduo\PHPMatcher\Factory\SimpleFactory;
use Muchacuba\Internauta\Request\ManageStorage;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symsonte\Behat\ContainerAwareContext;
use Symsonte\Service\Container;
use Muchacuba\Internauta\Horoscope\ProcessRequest as HoroscopeProcessRequest;
use Muchacuba\Internauta\Translation\ProcessRequest as TranslationProcessRequest;
use Muchacuba\Internauta\Lyrics\SpanishProcessRequest as SpanishLyricsProcessRequest;
use Muchacuba\Internauta\Lyrics\EnglishProcessRequest as EnglishLyricsProcessRequest;
use Muchacuba\Internauta\Image\ProcessRequest as ImageProcessRequest;

class RequestContext implements BaseContext, ContainerAwareContext
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var ProcessResult
     */
    private $result;

    /**
     * {@inheritdoc}
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @BeforeScenario
     */
    public function purgeStorage()
    {
        /** @var ManageStorage $manageStorage */
        $manageStorage = $this->container->get('muchacuba.internauta.request.manage_storage');

        $manageStorage->purge();
    }

    /**
     * @Given I process this horoscope request:
     *
     * @param PyStringNode $string
     */
    public function iProcessThisHoroscopeRequest(PyStringNode $string)
    {
        $request = json_decode($string->getRaw(), true);

        /** @var HoroscopeProcessRequest $processRequest */
        $processRequest = $this->container->get('muchacuba.internauta.horoscope.process_request');

        $this->result = $processRequest->process(
            $request['sender'],
            $request['recipient'],
            $request['subject'],
            ''
        );
    }

    /**
     * @Given I process this image request:
     *
     * @param PyStringNode $string
     */
    public function iProcessThisImageRequest(PyStringNode $string)
    {
        $request = json_decode($string->getRaw(), true);

        /** @var ImageProcessRequest $processRequest */
        $processRequest = $this->container->get('muchacuba.internauta.image.process_request');

        $this->result = $processRequest->process(
            $request['sender'],
            $request['recipient'],
            $request['subject'],
            ''
        );
    }

    /**
     * @Given I process this translation request:
     *
     * @param PyStringNode $string
     */
    public function iProcessThisTranslationRequest(PyStringNode $string)
    {
        $request = json_decode($string->getRaw(), true);

        /** @var TranslationProcessRequest $processRequest */
        $processRequest = $this->container->get('muchacuba.internauta.translation.process_request');

        $this->result = $processRequest->process(
            $request['sender'],
            $request['recipient'],
            $request['subject'],
            ''
        );
    }

    /**
     * @Given I process this spanish lyrics request:
     *
     * @param PyStringNode $string
     */
    public function iProcessThisSpanishLyricsRequest(PyStringNode $string)
    {
        $request = json_decode($string->getRaw(), true);

        /** @var SpanishLyricsProcessRequest $processRequest */
        $processRequest = $this->container->get('muchacuba.internauta.lyrics.spanish_process_request');

        $this->result = $processRequest->process(
            $request['sender'],
            $request['recipient'],
            $request['subject'],
            ''
        );
    }

    /**
     * @Given I process this english lyrics request:
     *
     * @param PyStringNode $string
     */
    public function iProcessThisEnglishLyricsRequest(PyStringNode $string)
    {
        $request = json_decode($string->getRaw(), true);

        /** @var EnglishLyricsProcessRequest $processRequest */
        $processRequest = $this->container->get('muchacuba.internauta.lyrics.english_process_request');

        $this->result = $processRequest->process(
            $request['sender'],
            $request['recipient'],
            $request['subject'],
            ''
        );
    }
    
    /**
     * @Then I should get this result:
     *
     * @param PyStringNode $string
     *
     * @throws \Exception
     */
    public function iShouldGetThisResult(PyStringNode $string)
    {
        $matcher = (new SimpleFactory())->createMatcher();

        if (!$matcher->match(
            json_decode(json_encode($this->result), true),
            json_decode($string->getRaw(), true)
        )
        ) {
            throw new \Exception($matcher->getError());
        }
    }
}

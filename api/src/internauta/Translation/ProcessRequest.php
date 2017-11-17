<?php

namespace Muchacuba\Internauta\Translation;

use Google\Cloud\Translate\TranslateClient;
use Muchacuba\Internauta\ProcessRequest as BaseProcessRequest;
use Muchacuba\Internauta\Response;
use Muchacuba\Internauta\ProcessResult;
use Muchacuba\Internauta\UnsupportedRequestException;
use EmailReplyParser\Parser\EmailParser;

/**
 * @di\service({
 *     tags: [{name: 'internauta.process_request', key: 'translation'}]
 * })
 */
class ProcessRequest implements BaseProcessRequest
{
    /**
     * @var TranslateClient
     */
    private $client;

    /**
     * @param string $googleServerApi
     *
     * @di\arguments({
     *     googleServerApi: '%google_server_api%'
     * })
     */
    public function __construct(
        $googleServerApi
    )
    {
        $this->client = new TranslateClient([
            'key' => $googleServerApi
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function process($sender, $recipient, $subject, $body)
    {
        if (!in_array(
            current(explode('@', $recipient)),
            ['traduccion', 'traducir', 'traduce', 'translation', 'translate']
        )) {
            throw new UnsupportedRequestException();
        }

        if (empty($subject) && !empty($body)) {
            if ($sender == 'esperanza.ssp@infomed.sld.cu' || $sender == 'yosmanyga@gmail.com') {
                $subject = (new EmailParser())->parse($body)->getVisibleText();
            } else {
                return new ProcessResult([
                    new Response(
                        'Traducción Muchacuba <traduccion@muchacuba.com>',
                        $sender,
                        sprintf('Re: %s...', substr($subject, 0, 10)),
                        "Escribe en el asunto lo que quieras traducir.\nPor el momento no estamos traduciendo lo que venga en cuerpo del mensaje."
                    )
                ], []);
            }
        }

        // Not needed
        unset($body);

        $responses = [];
        $events = [];

        try {
            // es to en; other to es
            $language = $this->detectLanguage($subject) == 'es' ? 'en' : 'es';

            $body = $this->translate($subject, $language);
        } catch (ProcessRequest\UndetectedLanguageException $e) {
            $body = 'No pudimos detectar el idioma de lo que escribiste.';
        }

        $responses[] = new Response(
            'Traducción Muchacuba <traduccion@muchacuba.com>',
            $sender,
            sprintf('Re: %s...', substr($subject, 0, 10)),
            $body
        );

        return new ProcessResult($responses, $events);
    }

    /**
     * {@inheritdoc}
     */
    public function help()
    {
        return <<<EOF
Escribe a traduccion@muchacuba.com para recibir traducciones de un texto.
En el asunto escribe el texto a traducir.
Si el texto está en español, lo traduce a inglés.
Si el texto está en inglés u otro idioma, lo traduce a español.
EOF;
    }

    /**
     * @param string $text
     *
     * @return string
     *
     * @throws ProcessRequest\UndetectedLanguageException
     */
    private function detectLanguage($text)
    {
        $data = $this->client->detectLanguage($text, [
            'model' => 'nmt'
        ]);

        if ($data['languageCode'] === 'und') {
            throw new ProcessRequest\UndetectedLanguageException();
        }

        return $data['languageCode'];
    }

    /**
     * @param string $text
     * @param string $target
     *
     * @return string
     */
    private function translate($text, $target)
    {
        $translation = $this->client->translate($text, [
            'target' => $target,
            'model' => 'nmt'
        ]);

        return $translation['text'];
    }
}
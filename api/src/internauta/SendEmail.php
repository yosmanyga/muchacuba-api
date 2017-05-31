<?php

namespace Muchacuba\Internauta;

use Mailgun\Mailgun;

/**
 * @di\service({
 *     private: true
 * })
 */
class SendEmail
{
    /**
     * @var string
     */
    private $mailgunApiKey;

    /**
     * @param string   $mailgunApiKey
     *
     * @di\arguments({
     *     mailgunApiKey: '%mailgun_api_key%'
     * })
     */
    public function __construct(
        $mailgunApiKey
    )
    {
        $this->mailgunApiKey = $mailgunApiKey;
    }

    /**
     * @param string        $sender
     * @param string        $recipient
     * @param string        $subject
     * @param string        $body
     * @param string[]|null $attachments
     *
     * @return SendResult
     */
    public function send($sender, $recipient, $subject, $body, $attachments = [])
    {
        // TODO: Throw exception if subject, body or attachments are too big

        $events = [];

        $attachmentData = [];
        foreach ($attachments as $i => $attachment) {
            $filename = sprintf(
                "%s/%s",
                sys_get_temp_dir(),
                uniqid()
            );
            file_put_contents($filename, base64_decode($attachment));

            list($width, $height, $type, $attr) = getimagesize($filename);
            unset($width, $height, $attr);

            if (!is_null($type)) {
                $type = image_type_to_mime_type($type);
                list($type, $subtype) = explode('/', $type);
            } else {
                $type = mime_content_type($filename);
                if ($type == 'image/svg+xml') {
                    $subtype = 'svg';
                } else {
                    $events[] = new Event(
                        $this,
                        'UnknownType',
                        [
                            'type' => $type
                        ]
                    );

                    continue;
                }
            }

            $newFilename = sprintf("%s.%s", $filename, $subtype);
            rename($filename, $newFilename);

            $attachmentData[] = [
                'name' => basename($filename),
                'size' => filesize($newFilename),
                'type' => $type,
                'data' => $attachment
            ];

            $attachments[$i] = ['filePath' => $newFilename];
        }

        $events[] = new Event(
            $this,
            '',
            [
                'sender' => $sender,
                'recipient' => $recipient,
                'subject' => $subject,
                'body' => $body,
                'attachments' => $attachmentData
            ]
        );

        $mg = Mailgun::create($this->mailgunApiKey);

        $mg->messages()->send(
            'muchacuba.com',
            [
                'from' => $sender,
                'to' => $recipient,
                'subject' => $subject,
                'text' => sprintf(
                    "%s\n\n%s\n%s\n",
                    $body,
                    "Si quieres enviar dudas, críticas o sugerencias escríbenos a equipo@muchacuba.com",
                    "Para conocer cómo usar todos los servicios envía un correo en blanco a ayuda@muchacuba.com"
                ),
                'attachment' => $attachments
            ]
        );

        foreach ($attachments as $attachment) {
            unlink($attachment['filePath']);
        }

        return new SendResult($events);
    }
}
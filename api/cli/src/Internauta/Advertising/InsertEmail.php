<?php

namespace Muchacuba\Cli\Internauta\Advertising;

use Symsonte\Cli\Server;
use Muchacuba\Internauta\Advertising\InsertEmail as DomainInsertEmail;

/**
 * @di\command({deductible: true})
 */
class InsertEmail
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var DomainInsertEmail
     */
    private $insertEmail;

    /**
     * @param Server            $server
     * @param DomainInsertEmail $insertEmail
     */
    public function __construct(
        Server $server,
        DomainInsertEmail $insertEmail
    )
    {
        $this->server = $server;
        $this->insertEmail = $insertEmail;
    }

    /**
     * @cli\resolution({command: "internauta.advertising.insert-email"})
     */
    public function process()
    {
        $input = $this->server->resolveInput();

//        $this->insertEmail->insert(
//            "Nuevo servicios de internet por correo",
//            "Escribe a letras@muchacuba.com para recibir letras de canciones.\nEn el asunto escribe el artista, título o parte de la letra.\nPara canciones en inglés escribe a lyrics@muchacuba.com\n\nEscribe a horoscopo@muchacuba.com para recibir el horóscopo del día.\nEn el asunto escribe tu signo del zodiaco.\n\nEscribe a traduccion@muchacuba.com para recibir traducciones de un texto.\nEn el asunto escribe el texto a traducir.\n\nEscribe a imagenes@muchacuba.com para recibir imágenes desde internet.\nEn el asunto escribe las palabras claves para buscar las imágenes, ej: josé martí\nPara recibir más de 3 imágenes escribe el número entre corchetes, ej: josé martí [5]\n\nEscribe a revolico@muchacuba.com para recibir anuncios de revolico.\nEn el asunto escribe las palabras a buscar, ej: laptop core i7\nPara recibir más de 3 anuncios escribe el número entre corchetes, ej: laptop core i7 [10]\nPara recibir los anuncios con sus fotos agrega la letra f, ej: laptop core i7 [10f]\n\n"
//        );

        $this->insertEmail->insert(
            $input->get('2'),
            $input->get('3')
        );
    }
}

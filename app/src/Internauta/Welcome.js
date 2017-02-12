import React from 'react';
import Paper from 'material-ui/Paper';
import {Card, CardMedia, CardTitle, CardText} from 'material-ui/Card';
import horoscope from './Image/horoscope.jpg';
import image from './Image/image.jpg';
import lyrics from './Image/lyrics.jpg';
import revolico from './Image/revolico.jpg';
import translation from './Image/translation.jpg';

export default class Welcome extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
    };

    render() {
        const cards = [
            {
                title: "Horóscopo",
                mail: "horoscopo@muchacuba.com",
                image: horoscope,
                description: [
                    "Escribe a horoscopo@muchacuba.com para recibir el horóscopo del día.",
                    "En el asunto escribe tu signo del zodiaco."
                ]
            },
            {
                title: "Traducción",
                mail: "traduccion@muchacuba.com",
                image: translation,
                description: [
                    "Escribe a traduccion@muchacuba.com para recibir traducciones de un texto.",
                    "En el asunto escribe el texto a traducir."
                ]
            },
            {
                title: "Letras",
                mail: "lyrics@muchacuba.com",
                image: lyrics,
                description: [
                    "Escribe a letras@muchacuba.com para recibir letras de canciones.",
                    "En el asunto escribe el artista, título o parte de la letra.",
                    "Para canciones en inglés escribe a lyrics@muchacuba.com"
                ]
            },
            {
                title: "Imágenes",
                mail: "imagenes@muchacuba.com",
                image: image,
                description: [
                    "Escribe a imagenes@muchacuba.com para recibir imágenes desde internet.",
                    "En el asunto escribe las palabras claves para buscar las imágenes, ej: josé martí",
                    "Para recibir más de 3 imágenes escribe el número entre corchetes, ej: josé martí [5]"
                ]
            },
            {
                title: "Revolico",
                mail: "revolico@muchacuba.com",
                image: revolico,
                description: [
                    "Escribe a revolico@muchacuba.com para buscar anuncios en revolico.",
                    "En el asunto escribe las palabras a buscar, ej: laptop core i7",
                    "Para recibir más de 3 anuncios escribe el número entre corchetes, ej: laptop core i7 [10]",
                    "Para recibir los anuncios con sus fotos agrega la letra f, ej: laptop core i7 [10f]"
                ]
            }
        ];

        return (
            <this.props.layout.type {...this.props.layout.props}>
                <Paper
                    zDepth={2}
                    style={{
                        display: "flex",
                        flexWrap: "wrap",
                        alignItems: "center",
                        justifyContent: "center",
                        padding: "10px"
                    }}
                >
                    {cards.map(function(card, i) {
                        return (
                            <Card key={i} style={{marginBottom: "20px", width: "50%"}}>
                                <CardMedia
                                    overlay={
                                        <CardTitle
                                            title={card.title}
                                            subtitle={card.mail}
                                        />
                                    }
                                >
                                    <img src={card.image} role="presentation" />
                                </CardMedia>
                                <CardText>
                                    {card.description.map(function(description, i) {
                                        return (
                                            <p key={i}>{description}</p>
                                        );
                                    })}
                                </CardText>
                            </Card>
                        );
                    })}
                </Paper>
            </this.props.layout.type>
        );
    }
}
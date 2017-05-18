import React from 'react';
import {Card, CardActions, CardHeader, CardText} from 'material-ui/Card';
import {green50, red50, yellow50} from 'material-ui/styles/colors';
import Moment from 'moment';
import {} from 'moment/locale/es';
Moment.locale('es');

import Button from '../Button';
import ConnectToServer from '../ConnectToServer';

import Horoscope_ProcessRequest from './Log/Horoscope_ProcessRequest';
import Horoscope_ProcessRequest_NotFound from './Log/Horoscope_ProcessRequest_NotFound';
import Image_ProcessRequest_Heavy from './Log/Image_ProcessRequest_Heavy';
import Image_ProcessRequest_Invalid from './Log/Image_ProcessRequest_Invalid';
import Lyrics_ProcessRequest_BigBody from './Log/Lyrics_ProcessRequest_BigBody';
import Lyrics_ProcessRequest_Exception from './Log/Lyrics_ProcessRequest_Exception';
import Lyrics_ProcessRequest_Found from './Log/Lyrics_ProcessRequest_Found';
import Lyrics_ProcessRequest_HtmlIncluded from './Log/Lyrics_ProcessRequest_HtmlIncluded';
import Lyrics_ProcessRequest_NotFound from './Log/Lyrics_ProcessRequest_NotFound';
import Lyrics_ProcessRequest_QuestionableParsing from './Log/Lyrics_ProcessRequest_QuestionableParsing';
import Lyrics_ProcessRequest_UnsupportedLink from './Log/Lyrics_ProcessRequest_UnsupportedLink';
import Lyrics_ProcessRequest_UnsupportedLinks from './Log/Lyrics_ProcessRequest_UnsupportedLinks';
import Mailgun_PushRequest from './Log/Mailgun_PushRequest';
import ProcessRequests_BeginProcessing from './Log/ProcessRequests_BeginProcessing';
import ProcessRequests_Exception from './Log/ProcessRequests_Exception';
import Revolico_ProcessRequest_NotFound from './Log/Revolico_ProcessRequest_NotFound';
import SendEmail from './Log/SendEmail';
import SendEmail_UnknownType from './Log/SendEmail_UnknownType';

export default class ViewLogs extends React.Component {
    static propTypes = {
        id: React.PropTypes.string.isRequired,
        logs: React.PropTypes.array.isRequired,
        onDelete: React.PropTypes.func.isRequired
    };

    constructor() {
        super();

        this._components = [
            new Horoscope_ProcessRequest(),
            new Horoscope_ProcessRequest_NotFound(),
            new Image_ProcessRequest_Heavy(),
            new Image_ProcessRequest_Invalid(),
            new Lyrics_ProcessRequest_BigBody(),
            new Lyrics_ProcessRequest_Exception(),
            new Lyrics_ProcessRequest_Found(),
            new Lyrics_ProcessRequest_HtmlIncluded(),
            new Lyrics_ProcessRequest_NotFound(),
            new Lyrics_ProcessRequest_QuestionableParsing(),
            new Lyrics_ProcessRequest_UnsupportedLink(),
            new Lyrics_ProcessRequest_UnsupportedLinks(),
            new Mailgun_PushRequest(),
            new ProcessRequests_BeginProcessing(),
            new ProcessRequests_Exception(),
            new Revolico_ProcessRequest_NotFound(),
            new SendEmail(),
            new SendEmail_UnknownType()
        ];

        this._connectToServer = new ConnectToServer();
    }

    render() {
        let color = null;

        let cards = this.props.logs.map((log) => {
            const component = this._components.find((component) => {
                return component.support(log.type);
            });

            if (typeof component === 'undefined') {
                throw new Error("No component for " + log.type);
            }

            if (component.level() === 'e') {
                color = red50;
            } else if (component.level() === 'w' && color !== 'e') {
                color = yellow50;
            } else if (component.level() === 'd' && color === null) {
                color = green50;
            }

            return component.instantiate({key: log.id, log: log});
        });

        return (
            <Card style={{backgroundColor: color}}>
                <CardHeader
                    title={
                        Moment.unix(this.props.logs[0].date).format('LLLL')
                        + ' :: '
                        + this.props.logs[0].payload.sender
                        + ' :: '
                        + this.props.logs[0].payload.recipient
                    }
                    actAsExpander={true}
                    showExpandableButton={true}
                />
                <CardText expandable={true}>
                    {cards}
                </CardText>
                <CardActions>
                    <Button
                        label="Borrar"
                        labelAfterTouchTap="Borrando..."
                        icon="delete"
                        onTouchTap={(finish) => {
                            this._connectToServer
                                .get('/internauta/delete-log-group/' + this.props.id)
                                .send()
                                .end((err, res) => {
                                    if (err) {
                                        // TODO

                                        return;
                                    }

                                    finish();

                                    this.props.onDelete(res.body);
                                });
                        }}
                    />
                </CardActions>
            </Card>
        );
    }
}

import React from 'react';
import PropTypes from 'prop-types';
import {Card, CardHeader, CardText} from 'material-ui/Card';
import {GridList, GridTile} from 'material-ui/GridList';
import Moment from 'moment';
import {} from 'moment/locale/es';
import {green100} from 'material-ui/styles/colors';

Moment.locale('es');

class SendRequest extends React.Component {
    static propTypes = {
        log: PropTypes.object
    };

    render() {
        const body = this.props.log.payload['body-plain'].replace(/\n/g, '<br/>');

        return (
            <Card style={{backgroundColor: green100}}>
                <CardHeader
                    title="Send email"
                    subtitle={Moment.unix(this.props.log.date).format('LLLL')}
                    actAsExpander={true}
                    showExpandableButton={true}
                />
                <CardText expandable={true}>
                    <p><strong>From</strong>: {this.props.log.payload.sender}</p>
                    <p><strong>To</strong>: {this.props.log.payload.recipient}</p>
                    <p><strong>Subject</strong>: {this.props.log.payload.subject}</p>
                    {body && <p dangerouslySetInnerHTML={{__html: body}}/>}
                    <GridList cellHeight="auto">
                        {this.props.log.payload.attachments.map(function(attachment, i) {
                            return (
                                <GridTile
                                    key={i}
                                    title={Math.floor(attachment.size / 1024) + ' kb'}
                                >
                                    <img
                                        key={i}
                                        src={'data:' + attachment.type + ';base64,' + attachment.data}
                                        alt={attachment.name}
                                    />
                                </GridTile>
                            );
                        })}
                    </GridList>
                </CardText>
            </Card>
        );
    }
}

export default class InstantiateSendRequest
{
    support($type) {
        return $type === 'Muchacuba\\Internauta\\SendEmail'
    }

    instantiate(props) {
        return <SendRequest {...props}/>;
    }

    level() {
        return 'd';
    }
}
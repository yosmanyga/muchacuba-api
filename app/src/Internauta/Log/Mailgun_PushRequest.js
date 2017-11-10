import React from 'react';
import PropTypes from 'prop-types';
import {Card, CardHeader, CardText} from 'material-ui/Card';
import Moment from 'moment';
import {} from 'moment/locale/es';
import {green100} from 'material-ui/styles/colors';

Moment.locale('es');

class MailgunPushRequest extends React.Component {
    static propTypes = {
        log: PropTypes.object
    };

    render() {
        const body = this.props.log.payload['body-plain'].replace(/\n/g, '<br/>');

        return (
            <Card style={{backgroundColor: green100}}>
                <CardHeader
                    title="Mailgun :: Push Request"
                    subtitle={Moment.unix(this.props.log.date).format('LLLL')}
                    actAsExpander={true}
                    showExpandableButton={true}
                />
                <CardText expandable={true}>
                    <p><strong>From</strong>: {this.props.log.payload.sender}</p>
                    <p><strong>To</strong>: {this.props.log.payload.recipient}</p>
                    <p><strong>Subject</strong>: {this.props.log.payload.subject}</p>
                    {body && <p dangerouslySetInnerHTML={{__html: body}}/>}
                </CardText>
            </Card>
        );
    }
}

export default class InstantiateMailgunPushRequest
{
    support($type) {
        return $type === 'Muchacuba\\Internauta\\Mailgun\\PushRequest'
    }

    instantiate(props) {
        return <MailgunPushRequest {...props}/>;
    }

    level() {
        return 'd';
    }
}
import React from 'react';
import {Card, CardHeader, CardText} from 'material-ui/Card';
import Moment from 'moment';
import {} from 'moment/locale/es';
Moment.locale('es');
import {yellow100} from 'material-ui/styles/colors';

class SendEmailUnknownType extends React.Component {
    static propTypes = {
        log: React.PropTypes.object
    };

    render() {
        return (
            <Card style={{backgroundColor: yellow100}}>
                <CardHeader
                    title="Send email :: Unknown Type"
                    subtitle={Moment.unix(this.props.log.date).format('LLLL')}
                    actAsExpander={true}
                    showExpandableButton={true}
                />
                <CardText expandable={true}>
                </CardText>
            </Card>
        );
    }
}

export default class InstantiateSendEmailUnknownType
{
    support($type) {
        return $type === 'Muchacuba\\Internauta\\SendEmail.UnknownType'
    }

    instantiate(props) {
        return <SendEmailUnknownType {...props}/>;
    }

    level() {
        return 'e';
    }
}
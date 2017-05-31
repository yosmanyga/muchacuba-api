import React from 'react';
import PropTypes from 'prop-types';
import {Card, CardHeader, CardText} from 'material-ui/Card';
import Moment from 'moment';
import {} from 'moment/locale/es';
import {red100} from 'material-ui/styles/colors';

Moment.locale('es');

class ProcessRequestsException extends React.Component {
    static propTypes = {
        log: PropTypes.object
    };

    render() {
        return (
            <Card style={{backgroundColor: red100}}>
                <CardHeader
                    title="Process Requests :: Exception"
                    subtitle={Moment.unix(this.props.log.date).format('LLLL')}
                    actAsExpander={true}
                    showExpandableButton={true}
                />
                <CardText expandable={true}>
                    <p><strong>Exception</strong>: {this.props.log.payload.exception}</p>
                </CardText>
            </Card>
        );
    }
}

export default class InstantiateProcessRequestsException
{
    support($type) {
        return $type === 'Muchacuba\\Internauta\\ProcessRequests.Exception'
    }

    instantiate(props) {
        return <ProcessRequestsException {...props}/>;
    }

    level() {
        return 'e';
    }
}
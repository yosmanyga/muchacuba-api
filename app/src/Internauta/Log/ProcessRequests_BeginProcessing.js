import React from 'react';
import PropTypes from 'prop-types';
import {Card, CardHeader, CardText} from 'material-ui/Card';
import Moment from 'moment';
import {} from 'moment/locale/es';
import {green100} from 'material-ui/styles/colors';

Moment.locale('es');

class ProcessRequestsBeginProcessing extends React.Component {
    static propTypes = {
        log: PropTypes.object
    };

    render() {
        return (
            <Card style={{backgroundColor: green100}}>
                <CardHeader
                    title="Process Requests :: Begin processing"
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

export default class InstantiateProcessRequestsBeginProcessing
{
    support($type) {
        return $type === 'Muchacuba\\Internauta\\ProcessRequests.BeginProcessing'
    }

    instantiate(props) {
        return <ProcessRequestsBeginProcessing {...props}/>;
    }

    level() {
        return 'd';
    }
}
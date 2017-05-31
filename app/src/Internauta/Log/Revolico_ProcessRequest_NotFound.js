import React from 'react';
import PropTypes from 'prop-types';
import {Card, CardHeader, CardText} from 'material-ui/Card';
import Moment from 'moment';
import {} from 'moment/locale/es';
import {red100} from 'material-ui/styles/colors';

Moment.locale('es');

class RevolicoProcessRequestNotFound extends React.Component {
    static propTypes = {
        log: PropTypes.object
    };

    render() {
        return (
            <Card style={{backgroundColor: red100}}>
                <CardHeader
                    title="Revolico :: Process Request :: Not Found"
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

export default class InstantiateRevolicoProcessRequestNotFound
{
    support($type) {
        return $type === 'Muchacuba\\Internauta\\Revolico\\ProcessRequest.NotFound'
    }

    instantiate(props) {
        return <RevolicoProcessRequestNotFound {...props}/>;
    }

    level() {
        return 'e';
    }
}
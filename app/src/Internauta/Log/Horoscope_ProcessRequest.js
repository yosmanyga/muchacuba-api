import React from 'react';
import {Card, CardHeader, CardText} from 'material-ui/Card';
import Moment from 'moment';
import {} from 'moment/locale/es';
Moment.locale('es');
import {green100} from 'material-ui/styles/colors';

class HoroscopeProcessRequest extends React.Component {
    static propTypes = {
        log: React.PropTypes.object
    };

    render() {
        return (
            <Card style={{backgroundColor: green100}}>
                <CardHeader
                    title="Horoscope :: Process Request"
                    subtitle={Moment.unix(this.props.log.date).format('LLLL')}
                    actAsExpander={true}
                    showExpandableButton={true}
                />
                <CardText expandable={true}>
                    <p><strong>Source</strong>: {this.props.log.payload.link}</p>
                </CardText>
            </Card>
        );
    }
}

export default class InstantiateHoroscopeProcessRequest
{
    support($type) {
        return $type === 'Muchacuba\\Internauta\\Horoscope\\ProcessRequest'
    }

    instantiate(props) {
        return <HoroscopeProcessRequest {...props}/>;
    }

    level() {
        return 'd';
    }
}
import React from 'react';
import {Card, CardHeader, CardText} from 'material-ui/Card';
import Moment from 'moment';
import {} from 'moment/locale/es';
Moment.locale('es');
import {red100} from 'material-ui/styles/colors';

class HoroscopeProcessRequestNotFound extends React.Component {
    static propTypes = {
        log: React.PropTypes.object
    };

    render() {
        return (
            <Card style={{backgroundColor: red100}}>
                <CardHeader
                    title="Horoscope :: Process Request :: Not Found"
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

export default class InstantiateHoroscopeProcessRequestNotFound
{
    support($type) {
        return $type === 'Muchacuba\\Internauta\\Horoscope\\ProcessRequest.NotFound'
    }

    instantiate(props) {
        return <HoroscopeProcessRequestNotFound {...props}/>;
    }

    level() {
        return 'e';
    }
}
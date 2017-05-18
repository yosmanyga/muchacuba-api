import React from 'react';
import {Card, CardHeader, CardText} from 'material-ui/Card';
import Moment from 'moment';
import {} from 'moment/locale/es';
Moment.locale('es');
import {green100} from 'material-ui/styles/colors';

class LyricsProcessRequestFound extends React.Component {
    static propTypes = {
        log: React.PropTypes.object
    };

    render() {
        return (
            <Card style={{backgroundColor: green100}}>
                <CardHeader
                    title="Lyrics :: Process Request :: Found"
                    subtitle={Moment.unix(this.props.log.date).format('LLLL')}
                    actAsExpander={true}
                    showExpandableButton={true}
                />
                <CardText expandable={true}>
                    <p><strong>Link</strong>: {this.props.log.payload.link}</p>
                </CardText>
            </Card>
        );
    }
}

export default class InstantiateLyricsProcessRequestFound
{
    support($type) {
        return $type === 'Muchacuba\\Internauta\\Lyrics\\ProcessRequest.Found'
    }

    instantiate(props) {
        return <LyricsProcessRequestFound {...props}/>;
    }

    level() {
        return 'd';
    }
}
import React from 'react';
import {Card, CardHeader, CardText} from 'material-ui/Card';
import Moment from 'moment';
import {} from 'moment/locale/es';
Moment.locale('es');
import {red50} from 'material-ui/styles/colors';

class LyricsProcessRequestUnsupportedLinks extends React.Component {
    static propTypes = {
        log: React.PropTypes.object
    };

    render() {
        return (
            <Card style={{backgroundColor: red50}}>
    <CardHeader
        title="Lyrics :: Process Request :: Unsupported Links"
        subtitle={Moment.unix(this.props.log.date).format('LLLL')}
        actAsExpander={true}
        showExpandableButton={true}
            />
            <CardText expandable={true}>
            <p>{this.props.log.payload.link}</p>
        </CardText>
        </Card>
    );
    }
}

export default class InstantiateLyricsProcessRequestUnsupportedLink
{
    support($type) {
        return $type === 'Muchacuba\\Internauta\\Lyrics\\ProcessRequest.UnsupportedLinks'
    }

    instantiate(props) {
        return <LyricsProcessRequestUnsupportedLinks {...props}/>;
    }

    level() {
        return 'e';
    }
}
import React from 'react';
import {Card, CardHeader, CardText} from 'material-ui/Card';
import Moment from 'moment';
import {} from 'moment/locale/es';
Moment.locale('es');
import {red100} from 'material-ui/styles/colors';

class LyricsProcessRequestBigBody extends React.Component {
    static propTypes = {
        log: React.PropTypes.object
    };

    render() {
        return (
            <Card style={{backgroundColor: red100}}>
                <CardHeader
                    title="Lyrics :: Process Request :: Big Body"
                    subtitle={Moment.unix(this.props.log.date).format('LLLL')}
                    actAsExpander={true}
                    showExpandableButton={true}
                />
                <CardText expandable={true}>
                    <p><strong>Body</strong>: {this.props.log.payload.body}</p>
                </CardText>
            </Card>
        );
    }
}

export default class InstantiateLyricsProcessRequestBigBody
{
    support($type) {
        return $type === 'Muchacuba\\Internauta\\Lyrics\\ProcessRequest.BigBody'
    }

    instantiate(props) {
        return <LyricsProcessRequestBigBody {...props}/>;
    }

    level() {
        return 'e';
    }
}
import React from 'react';
import {Card, CardHeader, CardText} from 'material-ui/Card';
import Moment from 'moment';
import {} from 'moment/locale/es';
Moment.locale('es');
import {yellow50} from 'material-ui/styles/colors';

class LyricsProcessRequestQuestionableParsing extends React.Component {
    static propTypes = {
        log: React.PropTypes.object
    };

    render() {
        return (
            <Card style={{backgroundColor: yellow50}}>
                <CardHeader
                    title="Lyrics :: Process Request :: Questionable Parsing"
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

export default class InstantiateLyricsProcessRequestQuestionableParsing
{
    support($type) {
        return $type === 'Muchacuba\\Internauta\\Lyrics\\ProcessRequest.QuestionableParsing'
    }

    instantiate(props) {
        return <LyricsProcessRequestQuestionableParsing {...props}/>;
    }

    level() {
        return 'w';
    }
}
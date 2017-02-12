import React from 'react';
import {Card, CardHeader, CardText} from 'material-ui/Card';
import Moment from 'moment';
import {} from 'moment/locale/es';
Moment.locale('es');
import {yellow50} from 'material-ui/styles/colors';

class LyricsProcessRequestHtmlIncluded extends React.Component {
    static propTypes = {
        log: React.PropTypes.object
    };

    render() {
        return (
            <Card style={{backgroundColor: yellow50}}>
                <CardHeader
                    title="Lyrics :: Process Request :: Html Included"
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

export default class InstantiateLyricsProcessRequestHtmlIncluded
{
    support($type) {
        return $type === 'Muchacuba\\Internauta\\Lyrics\\ProcessRequest.HtmlIncluded'
    }

    instantiate(props) {
        return <LyricsProcessRequestHtmlIncluded {...props}/>;
    }

    level() {
        return 'w';
    }
}
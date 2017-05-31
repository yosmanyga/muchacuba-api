import React from 'react';
import PropTypes from 'prop-types';
import {Card, CardHeader, CardText} from 'material-ui/Card';
import Moment from 'moment';
import {} from 'moment/locale/es';
import {red100} from 'material-ui/styles/colors';

Moment.locale('es');

class LyricsProcessRequestHtmlIncluded extends React.Component {
    static propTypes = {
        log: PropTypes.object
    };

    render() {
        return (
            <Card style={{backgroundColor: red100}}>
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
        return 'e';
    }
}
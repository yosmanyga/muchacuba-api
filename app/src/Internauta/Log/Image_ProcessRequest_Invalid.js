import React from 'react';
import PropTypes from 'prop-types';
import {Card, CardHeader, CardText} from 'material-ui/Card';
import Moment from 'moment';
import {} from 'moment/locale/es';
import {yellow100} from 'material-ui/styles/colors';

Moment.locale('es');

class ImageProcessRequestInvalid extends React.Component {
    static propTypes = {
        log: PropTypes.object
    };

    render() {
        return (
            <Card style={{backgroundColor: yellow100}}>
                <CardHeader
                    title="Image :: Process Request :: Invalid"
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

export default class InstantiateImageProcessRequestInvalid
{
    support($type) {
        return $type === 'Muchacuba\\Internauta\\Image\\ProcessRequest.Invalid'
    }

    instantiate(props) {
        return <ImageProcessRequestInvalid {...props}/>;
    }

    level() {
        return 'w';
    }
}
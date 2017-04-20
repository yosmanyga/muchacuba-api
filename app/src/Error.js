import React from 'react';
import {red500 as errorColor} from 'material-ui/styles/colors';

export default class Error extends React.Component {
    static propTypes = {
        message: React.PropTypes.string.isRequired
    };

    render() {
        return (
            <p style={{
                display: "flex",
                justifyContent: "center",
                paddingTop: "10px",
                color: errorColor
            }}>
                {this.props.message}
            </p>
        );
    }
}

import React from 'react';
import PropTypes from 'prop-types';
import {red500 as color} from 'material-ui/styles/colors';

export default class Error extends React.Component {
    static propTypes = {
        layout: PropTypes.element,
        message: PropTypes.any
    };

    render() {
        if (typeof this.props.message === 'undefined') {
            return null;
        }

        const layout = typeof this.props.layout !== 'undefined'
            ? <this.props.layout {...this.props.layout.props}/>
            : <div style={this.props.style}/>;

        return (
            <layout.type
                {...layout.props}
                style={{
                    color: color,
                    fontSize: "12px",
                    ...layout.props.style
                }}
            >
                {this.props.message}
            </layout.type>
        );
    }
}

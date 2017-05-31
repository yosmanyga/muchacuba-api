import React from 'react';
import PropTypes from 'prop-types';
import FlatButton from 'material-ui/FlatButton';
import FontIcon from 'material-ui/FontIcon';

export default class Button extends React.Component {
    static propTypes = {
        icon: PropTypes.string
    };

    shouldComponentUpdate(nextProps, nextState) {
        if (nextState !== this.state) {
            return true;
        }

        if (
            nextProps.disabled !== this.props.disabled
        ) {
            return true;
        }

        // Ignore any change on props.onChange (anonymous functions)

        return false;
    }

    render() {
        return <FlatButton
            {...this.props}
            icon={this.props.icon
                ? <FontIcon className="material-icons">{this.props.icon}</FontIcon>
                : null
            }
        />;
    }
}

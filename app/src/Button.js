import React from 'react';
import RaisedButton from 'material-ui/RaisedButton';
import FontIcon from 'material-ui/FontIcon';
import _objectWithoutProperties from 'babel-runtime/helpers/objectWithoutProperties';

export default class Button extends React.Component {
    static propTypes = {
        label: React.PropTypes.string.isRequired,
        labelAfterTouchTap: React.PropTypes.string,
        icon: React.PropTypes.string,
        onTouchTap: React.PropTypes.func
    };

    constructor(props) {
        super(props);

        this.state = {
            busy: false
        };

        this._handleTouchTap = this._handleTouchTap.bind(this);
        this._handleFinish = this._handleFinish.bind(this);
    }

    render() {
        let props = _objectWithoutProperties(
            this.props,
            ["labelAfterTouchTap"]
        );

        let icon = this.props.icon
            ? <FontIcon className="material-icons">{this.props.icon}</FontIcon>
            : null;

        let label = this.state.busy && this.props.labelAfterTouchTap
            ? this.props.labelAfterTouchTap
            : this.props.label;

        let disabled = this.state.busy;

        return (
            <RaisedButton
                {...props}
                icon={icon}
                label={label}
                disabled={disabled}
                onTouchTap={this._handleTouchTap}
            />
        );
    }

    _handleTouchTap() {
        this.setState({
            busy: true
        }, () => {
            if (this.props.onTouchTap) {
                this.props.onTouchTap(this._handleFinish)
            }
        });
    }

    _handleFinish() {
        this.setState({
            busy: false
        });
    }
}

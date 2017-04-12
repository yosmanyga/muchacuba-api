import React from 'react';
import RaisedButton from 'material-ui/RaisedButton';
import FontIcon from 'material-ui/FontIcon';
import _objectWithoutProperties from 'babel-runtime/helpers/objectWithoutProperties';

export default class Button extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element,
        label: React.PropTypes.string.isRequired,
        labelAfterTouchTap: React.PropTypes.string,
        icon: React.PropTypes.string,
        // (finish)
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
        const props = _objectWithoutProperties(
            this.props,
            ['layout', 'labelAfterTouchTap']
        );

        const icon = this.props.icon
            ? <FontIcon className="material-icons">{this.props.icon}</FontIcon>
            : null;

        const label = this.state.busy && this.props.labelAfterTouchTap
            ? this.props.labelAfterTouchTap
            : this.props.label;

        const disabled = this.state.busy;

        const button = <RaisedButton
            {...props}
            icon={icon}
            label={label}
            disabled={disabled}
            onTouchTap={typeof this.props.onTouchTap !== 'undefined'
                ? this._handleTouchTap
                : null
            }
        />;

        if (typeof this.props.layout === 'undefined') {
            return <button.type {...button.props}/>;
        }

        return (
            <this.props.layout.type {...this.props.layout.props}>
                <button.type {...button.props}/>
            </this.props.layout.type>
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

    _handleFinish(externalFinish) {
        this.setState({
            busy: false
        }, () => {
            if (typeof externalFinish !== 'undefined') {
                externalFinish();
            }
        });
    }
}

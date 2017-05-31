import React from 'react';
import PropTypes from 'prop-types';
import RaisedButton from 'material-ui/RaisedButton';
import IconButton from 'material-ui/IconButton';
import FontIcon from 'material-ui/FontIcon';

export default class Button extends React.Component {
    static propTypes = {
        /* RaisedButton */
        label: PropTypes.string,
        labelAfterTouchTap: PropTypes.string,
        icon: PropTypes.any,

        /* IconButton */
        name: PropTypes.string,
        tooltip: PropTypes.string,

        /* Common */
        layout: PropTypes.element,
        disabled: PropTypes.bool,
        // (finish)
        onTouchTap: PropTypes.func
    };

    constructor(props) {
        super(props);

        this.state = {
            busy: false
        };
    }

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
        const {layout, icon, ...props} = this.props;

        const disabled = this.state.busy || props.disabled === true;

        const onTouchTap = typeof props.onTouchTap !== 'undefined'
            ? () => {
                this.setState({
                    busy: true
                }, () => {
                    if (props.onTouchTap) {
                        props.onTouchTap((externalFinish) => {
                            this.setState({
                                busy: false
                            }, () => {
                                if (typeof externalFinish !== 'undefined') {
                                    externalFinish();
                                }
                            });
                        })
                    }
                });
            } : null;


        let button  = null;
        if (typeof props.label !== 'undefined') {
            const {label, labelAfterTouchTap, ...raisedProps} = props;

            button = <RaisedButton
                {...raisedProps}
                icon={icon
                    ? <FontIcon className="material-icons">{icon}</FontIcon>
                    : null
                }
                label={this.state.busy && labelAfterTouchTap
                    ? labelAfterTouchTap
                    : label
                }
                disabled={disabled}
                onTouchTap={onTouchTap}
            />
        } else {
            const {tooltip, ...iconProps} = props;

            button = <IconButton
                {...iconProps}
                tooltip={tooltip}
                disabled={disabled}
                onTouchTap={onTouchTap}
            >
                {icon}
            </IconButton>
        }
        
        if (typeof layout === 'undefined') {
            return <button.type {...button.props}/>;
        }

        return (
            <layout.type {...layout.props}>
                <button.type {...button.props}/>
            </layout.type>
        );
    }
}

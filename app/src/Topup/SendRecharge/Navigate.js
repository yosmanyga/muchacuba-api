import React from 'react';

import Button from '../../Button';

export default class Navigate extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
        buttons: React.PropTypes.array
    };

    render() {
        return (
            <this.props.layout.type
                {...this.props.layout.props}
            >
                {this.props.buttons.map((button) => {
                    return <Button
                        key={button.label}
                        label={button.label}
                        labelAfterTouchTap={button.label}
                        disabled={typeof button.disabled !== 'undefined'
                            ? button.disabled
                            : false
                        }
                        icon={button.icon}
                        onTouchTap={(finish) => {
                            button.onTouchTap(finish);
                        }}
                        style={{
                            margin: "16px 4px 4px 4px"
                        }}
                    />;
                })}
            </this.props.layout.type>
        );
    }
}
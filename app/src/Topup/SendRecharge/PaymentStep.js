import React from 'react';

import Navigate from './Navigate';

export default class PaymentStep extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
        stepper: React.PropTypes.element.isRequired,
        // ()
        onBack: React.PropTypes.func.isRequired,
    };

    render() {
        return (
            <this.props.layout.type
                {...this.props.layout.props}
                style={{
                    ...this.props.layout.props.style,
                    padding: "16px",
                    display: "flex",
                    flexDirection: "column",
                    alignItems: "center",
                }}
            >
                <code>Aqui va el formulario de pago...</code>
                <Navigate
                    key="buttons"
                    layout={<div/>}
                    buttons={[
                        {
                            label: "Anterior",
                            icon: "arrow_back",
                            onTouchTap: this.props.onBack
                        },
                        {
                            label: "Siguiente",
                            icon: "arrow_forward",
                            onTouchTap: (finish) => {
                            }
                        }
                    ]}
                />
            </this.props.layout.type>
        );
    }
}
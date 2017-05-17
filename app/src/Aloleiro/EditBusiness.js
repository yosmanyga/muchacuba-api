import React from 'react';
import Dialog from 'material-ui/Dialog';
import FlatButton from 'material-ui/FlatButton';
import TextField from 'material-ui/TextField';

import ConnectToServer from '../ConnectToServer';
import Button from '../Button';
import Wait from '../Wait';

export default class EditBusiness extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
        profile: React.PropTypes.object,
        // (message, finish)
        onNotify: React.PropTypes.func.isRequired,
        // (status, response)
        onError: React.PropTypes.func.isRequired
    };

    constructor(props) {
        super(props);

        this.state = {
            business: null,
            error: null,
            incrementBalanceDialog: false
        };

        this._connectToServer = new ConnectToServer();

        this._pickBusiness = this._pickBusiness.bind(this);
    }

    componentDidMount() {
        if (this.props.profile !== null) {
            this._pickBusiness();
        }
    }

    componentDidUpdate(prevProps, prevState) {
        if (
            prevProps.profile === null
            && this.props.profile !== null
        ) {
            this._pickBusiness();
        }
    }

    _pickBusiness() {
        this._connectToServer
            .get('/aloleiro/pick-business')
            .auth(this.props.profile.token)
            .send()
            .end((err, res) => {
                if (err) {
                    this.props.onError(
                        err.status,
                        JSON.parse(err.response.text)
                    );

                    return;
                }

                this.setState({
                    business: res.body
                });
            });
    }

    render() {
        if (this.state.business === null) {
            return (
                <Wait layout={this.props.layout}/>
            );
        }

        return (
            <this.props.layout.type
                {...this.props.layout.props}
                bar="Mi cuenta"
            >
                <p>Saldo actual. Cada llamada descuenta de este saldo. Si llega a cero no se podrán hacer llamadas.</p>
                <p><strong>{this.state.business.balance + ' Bf'}</strong></p>
                <Button
                    label="Aumentar saldo"
                    icon="add"
                    onTouchTap={(finish) => {this.setState({incrementBalanceDialog: true}, finish)}}
                />
                <br/><br/>
                <p>Este es el número que tus clientes tienen que marcar desde las cabinas.</p>
                <p><strong>02123353020</strong></p>
                <br/>
                <p>Esta casilla permite establecer la ganancia que se quiere obtener con las llamadas.</p>
                <p>Por ejemplo, si el porciento de ganancia es 15 y una llamada cuesta 100 Bf por minuto, entonces se venderá a 115 Bf (costo + % de ganancia).</p>
                <TextField
                    floatingLabelText="Porciento de ganancia, ej: 15"
                    floatingLabelFixed={true}
                    value={this.state.business.profitPercent}
                    errorText={this.state.error === 'profitPercent'
                        ? "Este campo debe ser un número"
                        : null
                    }
                    onChange={(e, value) => this.setState({
                        business: {
                            ...this.state.business,
                            profitPercent: value
                        }
                    })}
                />
                <br/><br/>
                <Button
                    label="Guardar"
                    labelAfterTouchTap="Guardando"
                    icon="save"
                    onTouchTap={(finish) => {
                        this.setState({
                            error: null
                        }, () => {
                            this._connectToServer
                                .post('/aloleiro/update-business')
                                .auth(this.props.profile.token)
                                .send(this.state.business)
                                .end((err, res) => {
                                    if (err) {
                                        const response = JSON.parse(err.response.text);

                                        if (err.status === 422) {
                                            this.setState({
                                                error: response.field
                                            }, finish);
                                        } else {
                                            this.props.onError(
                                                err.status,
                                                response
                                            );
                                        }

                                        return;
                                    }

                                    finish(this.props.onNotify('Los cambios se han guardado'));
                                });
                        });
                    }}
                />
                {this.state.incrementBalanceDialog === true
                    ? <IncrementBalanceDialog
                        onSend={(reference) => {
                            this._connectToServer
                                .post('/aloleiro/notify-payment')
                                .auth(this.props.profile.token)
                                .send({
                                    reference: reference
                                })
                                .end((err) => {
                                    if (err) {
                                        this.props.onError(
                                            err.status,
                                            JSON.parse(err.response.text)
                                        );

                                        return;
                                    }

                                    this.setState({
                                        incrementBalanceDialog: false
                                    }, this.props.onNotify(
                                        "Tu pago será procesado cuanto antes!"
                                    ))
                                });
                        }}
                        onCancel={() => {
                            this.setState({incrementBalanceDialog: false})
                        }}
                    />
                    : null
                }
            </this.props.layout.type>
        );
    }
}

class IncrementBalanceDialog extends React.Component {
    static propTypes = {
        // (reference)
        onSend: React.PropTypes.func.isRequired,
        // ()
        onCancel: React.PropTypes.func.isRequired
    };

    constructor(props) {
        super(props);

        this.state = {
            reference: "",
            busy: false
        };
    }

    render() {
        return(
            <Dialog
                open={true}
                actions={[
                    <FlatButton
                        label="Cancelar"
                        disabled={this.state.busy === true}
                        onTouchTap={this.props.onCancel}
                    />,
                    <FlatButton
                        label={!this.state.busy ? "Enviar" : "Enviando..."}
                        primary={true}
                        disabled={this.state.reference === "" || this.state.busy === true}
                        onTouchTap={() => {
                            this.setState({
                                busy: true
                            }, this.props.onSend(this.state.reference))
                        }}
                    />
                ]}
                modal={true}
                autoScrollBodyContent={true}
            >
                <p><strong>1.</strong> Para aumentar saldo, por favor haga una transferencia a la siguiente cuenta con el monto que desea aumentar.</p>
                <p>
                    <strong>Banco:</strong> Mercantil
                    <br/>
                    <strong>Titular:</strong> Jimenez Solutios CA
                    <br/>
                    <strong>RIF:</strong> J-40251398-4
                    <br/>
                    <strong>C. Corriente:</strong>0105-0046-02-1046849840
                </p>
                <p><strong>2.</strong> Al completar la transferencia escriba aquí el código de la transacción.</p>
                <TextField
                    autoFocus={true}
                    value={this.state.reference}
                    hintText="Referencia de pago."
                    fullWidth={true}
                    onChange={(e, value) => this.setState({reference: value})}
                />
                <p><strong>3.</strong> La transferencia será procesada en el menor tiempo posible y su saldo será aumentado.</p>
            </Dialog>
        );
    }
}

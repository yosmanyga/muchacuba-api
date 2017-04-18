import React from 'react';
import TextField from 'material-ui/TextField';

import ConnectToServer from '../ConnectToServer';
import Button from '../Button';
import Wait from '../Wait';

export default class EditBusiness extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
        profile: React.PropTypes.object,
        // (message, finish)
        onNotify: React.PropTypes.func.isRequired
    };

    constructor(props) {
        super(props);

        this.state = {
            business: null,
            error: null
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
                    // TODO

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
            >
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
                <br/>
                <p>Esta casilla permite actualizar el bolivar respecto al dólar.</p>
                <p>De esta forma los precios de las llamadas siempre estarán ajustados para no tener pérdidas.</p>
                <TextField
                    floatingLabelText="Cambio del bolivar, ej: 4400"
                    floatingLabelFixed={true}
                    value={this.state.business.currencyExchange}
                    errorText={this.state.error === 'currencyExchange'
                        ? "Este campo debe ser un número"
                        : null
                    }
                    onChange={(e, value) => this.setState({
                        business: {
                            ...this.state.business,
                            currencyExchange: value
                        }
                    })}
                />
                <br/>
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
                                        this.setState({
                                            error: JSON.parse(err.response.text).field
                                        }, finish);

                                        return;
                                    }

                                    finish(this.props.onNotify('Los cambios se han guardado'));
                                });
                        });
                    }}
                />
            </this.props.layout.type>
        );
    }
}

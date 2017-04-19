import React from 'react';
import Dialog from 'material-ui/Dialog';
import FlatButton from 'material-ui/FlatButton';
import MenuItem from 'material-ui/MenuItem';
import {Table, TableBody, TableHeader, TableHeaderColumn, TableRow, TableRowColumn} from 'material-ui/Table';
import TextField from 'material-ui/TextField';
import SelectField from 'material-ui/SelectField';

import ConnectToServer from '../ConnectToServer';
import Button from '../Button';
import Wait from '../Wait';

export default class ListClientCalls extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
        profile: React.PropTypes.object,
        // (status, response)
        onError: React.PropTypes.func.isRequired
    };

    constructor(props) {
        super(props);

        this.state = {
            phones: null,
            calls: null,
            add: null,
            remove: null
        };

        this._connectToServer = new ConnectToServer();

        this._collectPhones = this._collectPhones.bind(this);
        this._collectCalls = this._collectCalls.bind(this);
    }

    componentDidMount() {
        if (this.props.profile !== null) {
            this._collectPhones();
        }

        if (this.props.profile !== null) {
            this._collectCalls();
        }
    }

    componentDidUpdate(prevProps, prevState) {
        if (
            this.state.profile !== null
            && this.state.phones === null
        ) {
            this._collectPhones();
        }

        if (
            this.state.profile !== null
            && this.state.calls === null
        ) {
            this._collectCalls();
        }
    }

    _collectPhones() {
        this._connectToServer
            .get('/aloleiro/collect-phones')
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
                    phones: res.body
                });
            });
    }

    _collectCalls() {
        this._connectToServer
            .get('/aloleiro/collect-client-calls')
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
                    calls: res.body
                });
            });
    }

    render() {
        if (
            this.state.phones === null
            || this.state.calls === null
        ) {
            return (
                <Wait layout={this.props.layout}/>
            );
        }

        return (
            <this.props.layout.type
                {...this.props.layout.props}
            >
                <Button
                    label="Preparar llamada"
                    icon="add"
                    onTouchTap={(finish) => {
                        this.setState({add: true}, finish);
                    }}
                />
                <Button
                    label="Refrescar"
                    icon="refresh"
                    onTouchTap={() => {
                        this.setState({calls: null});
                    }}
                />
                {this.state.calls.length !== 0
                    ? <Table style={{background: "transparent"}}>
                        <TableHeader
                            displaySelectAll={false}
                            adjustForCheckbox={false}
                        >
                            <TableRow>
                                <TableHeaderColumn style={{width: "100px"}}>
                                    Desde
                                </TableHeaderColumn>
                                <TableHeaderColumn style={{width: "100px"}}>
                                    Hacia
                                </TableHeaderColumn>
                                <TableHeaderColumn>Llamadas</TableHeaderColumn>
                            </TableRow>
                        </TableHeader>
                        <TableBody displayRowCheckbox={false}>
                            {this.state.calls.map((call, i) => {
                                return (
                                    <TableRow key={i}>
                                        <TableRowColumn
                                            style={{
                                                width: "100px"
                                            }}
                                        >
                                            {this.state.phones.find((phone) => {
                                                return phone.number === call.from
                                            }).name}
                                        </TableRowColumn>
                                        <TableRowColumn
                                            style={{
                                                width: "100px"
                                            }}
                                        >
                                            {call.to}
                                        </TableRowColumn>
                                        <TableRowColumn>
                                            {call.instances.length !== 0
                                                ?
                                                    <Table style={{background: "transparent"}}>
                                                        <TableHeader
                                                            displaySelectAll={false}
                                                            adjustForCheckbox={false}
                                                        >
                                                            <TableRow>
                                                                <TableHeaderColumn
                                                                    style={{width: "200px"}}
                                                                >
                                                                    Duración
                                                                </TableHeaderColumn>
                                                                <TableHeaderColumn
                                                                    style={{
                                                                        width: "100px",
                                                                        textAlign: 'right'
                                                                    }}
                                                                >
                                                                    Costo
                                                                </TableHeaderColumn>
                                                                <TableHeaderColumn/>
                                                            </TableRow>
                                                        </TableHeader>
                                                        <TableBody displayRowCheckbox={false}>
                                                            {call.instances.map((instance, i) => {
                                                                return (
                                                                    <TableRow key={i}>
                                                                        <TableRowColumn
                                                                            style={{
                                                                                width: "200px"
                                                                            }}
                                                                        >
                                                                            {this._buildDuration(instance.duration)}
                                                                        </TableRowColumn>
                                                                        <TableRowColumn
                                                                            style={{
                                                                                width: "100px",
                                                                                textAlign: 'right'
                                                                            }}
                                                                        >
                                                                            {instance.charge} Bf
                                                                        </TableRowColumn>
                                                                        <TableRowColumn/>
                                                                    </TableRow>
                                                                );
                                                            })}
                                                            <TableRow key={i}>
                                                                <TableRowColumn
                                                                    style={{
                                                                        width: "200px"
                                                                    }}
                                                                >
                                                                    <strong>Total</strong>
                                                                </TableRowColumn>
                                                                <TableRowColumn
                                                                    style={{
                                                                        width: "100px",
                                                                        textAlign: 'right'
                                                                    }}
                                                                >
                                                                    <strong>
                                                                        {call.instances.reduce((total, instance) => {
                                                                            return total + instance.charge;
                                                                        }, 0)} Bf
                                                                    </strong>
                                                                </TableRowColumn>
                                                                <TableRowColumn/>
                                                            </TableRow>
                                                        </TableBody>
                                                    </Table>
                                                :
                                                    null
                                            }
                                        </TableRowColumn>
                                    </TableRow>
                                );
                            })}
                        </TableBody>
                    </Table>
                    : <p>No hay llamadas</p>
                }
                {this.state.add === true
                    ? <AddDialog
                        phones={this.state.phones}
                        onAdd={(call, onError) => {
                            this._connectToServer
                                .post('/aloleiro/prepare-call')
                                .auth(this.props.profile.token)
                                .send(call)
                                .end((err, res) => {
                                    if (err) {
                                        const response = JSON.parse(err.response.text);

                                        if (err.status === 422) {
                                            onError(response.field, response.type);
                                        } else {
                                            this.props.onError(
                                                err.status,
                                                response
                                            );
                                        }

                                        return;
                                    }

                                    this.setState({
                                        calls: res.body,
                                        add: false
                                    });
                                });
                        }}
                        onCancel={() => {
                            this.setState({add: false})
                        }}
                    />
                    : null
                }
            </this.props.layout.type>
        );
    }

    _buildDuration(duration) {
        const remainder = duration % 60;

        let string = (duration - remainder) / 60;
        string += string === 1 ? ' minuto' : ' minutos';

        if (remainder !== 0) {
            string += ' y ' + remainder;
            string += remainder === 1 ? ' segundo' : ' segundo';
        }

        return string;
    }
}

class AddDialog extends React.Component {
    static propTypes = {
        phones: React.PropTypes.array,
        // (call, onError(field, type))
        onAdd: React.PropTypes.func.isRequired,
        // ()
        onCancel: React.PropTypes.func.isRequired
    };

    constructor(props) {
        super(props);

        this.state = {
            busy: false,
            focus: 'name',
            call: {
                from: null,
                to: ''
            },
            error: {
                field: null,
                type: null
            }
        };
    }

    render() {
        return(
            <Dialog
                open={true}
                title="Preparar llamada"
                actions={[
                    <FlatButton
                        label="Cancelar"
                        disabled={this.state.busy === true}
                        onTouchTap={this.props.onCancel}
                    />,
                    <FlatButton
                        label={!this.state.busy ? "Preparar" : "Preparando..."}
                        primary={true}
                        disabled={this.state.text === '' || this.state.busy === true}
                        onTouchTap={() => {
                            this.setState({
                                busy: true,
                                error: {
                                    field: null,
                                    type: null
                                }
                            }, () => {
                                this.props.onAdd(
                                    this.state.call,
                                    (field, type) => {
                                        this.setState({
                                            busy: false,
                                            error: {
                                                field: field,
                                                type: type
                                            }
                                        })
                                    }
                                )
                            });
                        }}
                    />
                ]}
                modal={true}
                autoScrollBodyContent={true}
            >
                <SelectField
                    hintText="Desde"
                    value={this.state.call.from}
                    fullWidth={true}
                    errorText={this.state.error.field === 'from'
                        ? 'La cabina seleccionada ya no existe. Por favor refresca la página para actualizar los datos.'
                        : null
                    }
                    onChange={(e, key, value) => {
                        this.setState({
                            call: {
                                ...this.state.call,
                                from: value
                            }
                        });
                    }}
                >
                    {this.props.phones.map((phone) =>
                        <MenuItem
                            key={phone.number}
                            value={phone.number}
                            primaryText={phone.name}
                        />
                    )}
                </SelectField>
                <TextField
                    type="tel"
                    floatingLabelText="Código del país, seguido del número, ej: 17864088134"
                    hintText="Escribe el número de teléfono al que se quiere llamar"
                    value={this.state.call.to}
                    fullWidth={true}
                    errorText={this.state.error.field === 'to'
                        ? "Solo números, comenzando con el prefijo del país, sin espacios, sin guiones u otro símbolo."
                        : null
                    }
                    onChange={(e, value) => this.setState({
                        call: {
                            ...this.state.call,
                            to: value
                        }
                    })}
                />
            </Dialog>
        );
    }
}


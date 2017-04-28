import React from 'react';
import Dialog from 'material-ui/Dialog';
import FlatButton from 'material-ui/FlatButton';
import FontIcon from 'material-ui/FontIcon';
import MenuItem from 'material-ui/MenuItem';
import {Table, TableBody, TableHeader, TableHeaderColumn, TableRow, TableRowColumn} from 'material-ui/Table';
import {Card, CardActions, CardHeader, CardText} from 'material-ui/Card';
import TextField from 'material-ui/TextField';
import SelectField from 'material-ui/SelectField';
import Moment from 'moment';
import {} from 'moment/locale/es';
Moment.updateLocale('es', {
    longDateFormat : {
        LLLL : 'dddd D MMMM YYYY h:mm a'
    },
});

import ConnectToServer from '../ConnectToServer';
import Error from '../Error';
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
            preparedCalls: null,
            calls: null,
            add: null,
            remove: null,
            daily: null
        };

        this._connectToServer = new ConnectToServer();

        this._collectPhones = this._collectPhones.bind(this);
        this._collectPreparedCalls = this._collectPreparedCalls.bind(this);
        this._collectCalls = this._collectCalls.bind(this);
        this._resolvePhoneName = this._resolvePhoneName.bind(this);
    }

    componentDidMount() {
        if (this.props.profile !== null) {
            this._collectPhones();
            this._collectPreparedCalls();
            this._collectCalls();
        }
    }

    componentDidUpdate(prevProps, prevState) {
        if (
            // Authentication occurred?
            this.props !== prevProps
            && this.props.profile !== null
            && prevProps.profile === null
        ) {
            this._collectPhones();
            this._collectPreparedCalls();
            this._collectCalls();
        }

        // Refresh?
        if (
            prevState.preparedCalls !== null
            && this.state.preparedCalls === null
        ) {
            this._collectPreparedCalls();
        }

        // Refresh?
        if (
            prevState.calls !== null
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
            .get('/aloleiro/collect-daily-client-calls')
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

    _collectPreparedCalls() {
        this._connectToServer
            .get('/aloleiro/collect-prepared-calls')
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
                    preparedCalls: res.body
                });
            });
    }

    render() {
        if (
            this.state.phones === null
            || this.state.preparedCalls === null
            || this.state.calls === null
        ) {
            return (
                <Wait layout={this.props.layout}/>
            );
        }

        return (
            <this.props.layout.type
                {...this.props.layout.props}
                bar="Llamadas"
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
                        this.setState({
                            preparedCalls: null,
                            calls: null,
                        });
                    }}
                />
                <Button
                    label="Ver reporte diario"
                    icon="print"
                    onTouchTap={(finish) => {
                        this.setState({daily: true}, finish);
                    }}
                />
                {this.state.preparedCalls.length !== 0
                    ?
                        this.state.preparedCalls.map((preparedCall) => {
                            return (
                                <Card
                                    key={preparedCall.id}
                                    style={{
                                        marginTop: "10px",
                                        background: "transparent"
                                    }}
                                >
                                    <CardHeader
                                        avatar={<FontIcon className="material-icons">phone_in_talk</FontIcon>}
                                        title={'Desde: ' + this._resolvePhoneName(preparedCall.from)}
                                        subtitle={'Hasta: ' + preparedCall.to}
                                        actAsExpander={true}
                                    />
                                    <CardActions>
                                        <Button
                                            label="Cancelar"
                                            labelAfterTouchTap="Cancelando"
                                            icon="delete"
                                            onTouchTap={() => {
                                                this._connectToServer
                                                    .post('/aloleiro/cancel-call')
                                                    .auth(this.props.profile.token)
                                                    .send({
                                                        id: preparedCall.id
                                                    })
                                                    .end((err, res) => {
                                                        if (err) {
                                                            this.props.onError(
                                                                err.status,
                                                                JSON.parse(err.response.text)
                                                            );

                                                            return;
                                                        }

                                                        this.setState({
                                                            preparedCalls: res.body
                                                        });
                                                    });
                                            }}
                                        />
                                    </CardActions>
                                </Card>
                            );
                        })
                    : null
                }
                {this.state.calls.length !== 0
                    ?
                        this.state.calls.map((call, i) => {
                            return (
                                <Card
                                    key={i}
                                    style={{
                                        marginTop: "10px",
                                        background: "transparent"
                                    }}
                                >
                                    <CardHeader
                                        avatar={<FontIcon className="material-icons">phone_in_talk</FontIcon>}
                                        title={'Desde: ' + this._resolvePhoneName(call.from)}
                                        subtitle={'Hasta: ' + call.to}
                                        actAsExpander={true}
                                    />
                                    <CardText expandable={true}>
                                        {call.instances.length !== 0
                                            ?
                                            <Table style={{background: "transparent"}}>
                                                <TableHeader displaySelectAll={false} adjustForCheckbox={false}>
                                                    <TableRow>
                                                        <TableHeaderColumn
                                                            style={{
                                                                textAlign: 'left',
                                                                width: "160px"
                                                            }}
                                                        >
                                                            Duración
                                                        </TableHeaderColumn>
                                                        <TableHeaderColumn
                                                            style={{
                                                                textAlign: 'right',
                                                                width: '60px'
                                                            }}
                                                        >
                                                            Costo
                                                        </TableHeaderColumn>
                                                        <TableHeaderColumn
                                                            style={{
                                                                textAlign: 'left'
                                                            }}
                                                        >
                                                            Fecha y hora
                                                        </TableHeaderColumn>
                                                    </TableRow>
                                                </TableHeader>
                                                <TableBody displayRowCheckbox={false}>
                                                    {call.instances.map((instance, i) => {
                                                        return (
                                                            <TableRow key={i}>
                                                                <TableRowColumn
                                                                    style={{
                                                                        textAlign: 'left',
                                                                        width: "160px"
                                                                    }}
                                                                >
                                                                    {this._buildDuration(instance.duration)}
                                                                </TableRowColumn>
                                                                <TableRowColumn
                                                                    style={{
                                                                        textAlign: 'right',
                                                                        width: '60px'
                                                                    }}
                                                                >
                                                                    {instance.charge} Bf
                                                                </TableRowColumn>
                                                                <TableRowColumn
                                                                    style={{
                                                                        textAlign: 'left'
                                                                    }}
                                                                >
                                                                    {Moment.unix(instance.timestamp).format('LLLL')}
                                                                </TableRowColumn>
                                                            </TableRow>
                                                        );
                                                    })}
                                                    <TableRow>
                                                        <TableRowColumn
                                                            style={{
                                                                textAlign: 'left',
                                                                width: "160px"
                                                            }}
                                                        >
                                                            <strong>Total</strong>
                                                        </TableRowColumn>
                                                        <TableRowColumn
                                                            style={{
                                                                textAlign: 'right',
                                                                width: '60px'
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
                                    </CardText>
                                </Card>
                            );
                        })
                    : null
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

                                        // Invalid data?
                                        if (err.status === 422) {
                                            onError(response.type, response.payload);
                                        } else
                                        // Insufficient balance?
                                        if (err.status === 403) {
                                            onError(response.type, response.payload);
                                        }
                                        // Other
                                        else {
                                            this.props.onError(
                                                err.status,
                                                response
                                            );
                                        }

                                        return;
                                    }

                                    this.setState({
                                        preparedCalls: res.body,
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
                {this.state.daily === true
                    ? <DailyDialog
                        load={(onSuccess) => {
                            this._connectToServer
                                .get('/aloleiro/compute-daily-business-calls')
                                .auth(this.props.profile.token)
                                .end((err, res) => {
                                    if (err) {
                                        this.props.onError(
                                            err.status,
                                            JSON.parse(err.response.text)
                                        );

                                        return;
                                    }

                                    onSuccess(res.body);
                                });
                        }}
                        onClose={() => {
                            this.setState({daily: false})
                        }}
                    />
                        : null
                }
            </this.props.layout.type>
        );
    }

    _resolvePhoneName(number) {
        const phone = this.state.phones.find((phone) => {
            return phone.number === number
        });

        if (typeof phone !== 'undefined') {
            return phone.name;
        }

        return number;
    }

    _buildDuration(duration) {
        const remainder = duration % 60;

        let string = (duration - remainder) / 60;
        string += string === 1 ? ' minuto' : ' minutos';

        if (remainder !== 0) {
            string += ' y ' + remainder;
            string += remainder === 1 ? ' segundo' : ' segundos';
        }

        return string;
    }
}

class AddDialog extends React.Component {
    static propTypes = {
        phones: React.PropTypes.array,
        // (call, onError(type, payload))
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
            error: null
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
                                error: null
                            }, () => {
                                this.props.onAdd(
                                    this.state.call,
                                    (type, payload) => {
                                        if (type === 'invalid-field') {
                                            this.setState({
                                                error: {
                                                    type: type,
                                                    field: payload.field,
                                                }
                                            })
                                        } else if (type === 'insufficient-balance') {
                                            this.setState({
                                                error: {
                                                    type: type
                                                }
                                            })
                                        }

                                        this.setState({
                                            busy: false,
                                        })
                                    }
                                )
                            });
                        }}
                    />
                ]}
                modal={true}
                autoScrollBodyContent={true}
                onRequestClose={this.props.onCancel}
            >
                {this.state.error !== null && this.state.error.type === 'insufficient-balance'
                    ? <Error message="No hay suficiente saldo para hacer la llamada."/>
                    : null
                }
                <SelectField
                    hintText="Desde"
                    value={this.state.call.from}
                    fullWidth={true}
                    errorText={this.state.error !== null && this.state.error.field === 'from'
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
                    errorText={this.state.error !== null && this.state.error.field === 'to'
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

class DailyDialog extends React.Component {
    static propTypes = {
        // (onSuccess(stats))
        load: React.PropTypes.func.isRequired,
        // ()
        onClose: React.PropTypes.func.isRequired
    };

    constructor(props) {
        super(props);

        this.state = {
            daily: null,
        };
    }

    componentDidMount() {
        this.props.load((stats) => {
            this.setState({
                daily: stats
            });
        });
    }

    render() {
        return(
            <Dialog
                open={true}
                title="Reporte diario"
                actions={[
                    <FlatButton
                        label="Cerrar"
                        disabled={this.state.busy === true}
                        onTouchTap={this.props.onClose}
                    />,
                ]}
                modal={true}
                autoScrollBodyContent={true}
                onRequestClose={this.props.onClose}
            >
                {this.state.daily !== null
                    ? [
                        <p key="duration">{this.state.daily.total} {this.state.daily.total === 1 ? 'llamada' : 'llamadas'}</p>,
                        <p key="profit"><strong>{this.state.daily.sale} Bf</strong> en ventas</p>,
                    ]
                    : <Wait />
                }
            </Dialog>
        );
    }
}
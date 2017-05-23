import React from 'react';
import {RadioButton, RadioButtonGroup} from 'material-ui/RadioButton';
import Dialog from 'material-ui/Dialog';
import FlatButton from 'material-ui/FlatButton';
import FontIcon from 'material-ui/FontIcon';
import MenuItem from 'material-ui/MenuItem';
import {Table, TableBody, TableHeader, TableHeaderColumn, TableRow, TableRowColumn} from 'material-ui/Table';
import {Card, CardActions, CardHeader, CardText} from 'material-ui/Card';
import TextField from 'material-ui/TextField';
import SelectField from 'material-ui/SelectField';
import MomentTimezone from 'moment-timezone';
import {} from 'moment/locale/es';

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
            timestamp: null,
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

        this._intervalId = window.setInterval(() => {
            this.setState({
                timestamp: Math.floor(Date.now() / 1000)
            });
        }, 1000);
    }

    componentWillUnmount() {
        window.clearInterval(this._intervalId);
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
                                        avatar={<FontIcon className="material-icons">local_phone</FontIcon>}
                                        title={'Desde: ' + this._resolvePhoneName(preparedCall.from)}
                                        subtitle={'Hasta: ' + preparedCall.to}
                                        actAsExpander={true}
                                    />
                                    <CardActions>
                                        <Button
                                            label="Borrar"
                                            labelAfterTouchTap="Borrando"
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
                        this.state.calls.map((call) => {
                            return (
                                <Card
                                    key={call.id}
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
                                                    <TableHeader
                                                        displaySelectAll={false}
                                                        adjustForCheckbox={false}
                                                    >
                                                        {this._renderHeaderRow(
                                                            'Duración',
                                                            'Costo',
                                                            'Día',
                                                            'Comienzo',
                                                            'Final'
                                                        )}
                                                    </TableHeader>
                                                    <TableBody
                                                        displayRowCheckbox={false}
                                                        stripedRows={true}
                                                    >
                                                        {call.instances.map((instance) => {
                                                            if (instance.duration === null) {
                                                                return (
                                                                    this._renderBodyRow(
                                                                        instance.id,
                                                                        this._buildDuration(
                                                                            this.state.timestamp
                                                                            - instance.start
                                                                        ),
                                                                        <Button
                                                                            label="Solicitar"
                                                                            labelAfterTouchTap="Solicitando"
                                                                            icon="attach_money"
                                                                            onTouchTap={() => {
                                                                                this._connectToServer
                                                                                    .post('/aloleiro/query-call')
                                                                                    .auth(this.props.profile.token)
                                                                                    .send({id: instance.id})
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
                                                                            }}
                                                                        />,
                                                                        null,
                                                                        null,
                                                                        null
                                                                    )
                                                                );
                                                            }

                                                            return (
                                                                this._renderBodyRow(
                                                                    instance.id,
                                                                    this._buildDuration(instance.duration),
                                                                    instance.charge + ' Bf',
                                                                    instance.result,
                                                                    MomentTimezone
                                                                        .unix(instance.start)
                                                                        .tz('America/Caracas')
                                                                        .format('dddd D MMMM YYYY'),
                                                                    MomentTimezone
                                                                        .unix(instance.start)
                                                                        .tz('America/Caracas')
                                                                        .format('h:mm:ss a'),
                                                                    MomentTimezone
                                                                        .unix(instance.end)
                                                                        .tz('America/Caracas')
                                                                        .format('h:mm:ss a'),
                                                                    <RadioButtonGroup
                                                                        name="result"
                                                                        defaultSelected={instance.result}
                                                                        onChange={(e, value) => {
                                                                            this._connectToServer
                                                                                .post(value === 'did_speak'
                                                                                    ? '/aloleiro/call/mark-instance-as-did-speak'
                                                                                    : '/aloleiro/call/mark-instance-as-did-not-speak'
                                                                                )
                                                                                .auth(this.props.profile.token)
                                                                                .send({
                                                                                    call: call.id,
                                                                                    id: instance.id
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
                                                                                        calls: res.body
                                                                                    });
                                                                                });
                                                                        }}
                                                                    >
                                                                        <RadioButton
                                                                            value="did_speak"
                                                                            label="Habló"
                                                                        />
                                                                        <RadioButton
                                                                            value="did_not_speak"
                                                                            label="No habló"
                                                                        />
                                                                    </RadioButtonGroup>
                                                                )
                                                            );
                                                        })}
                                                        {this._renderBodyRow(
                                                            'total',
                                                            'Total',
                                                            call.instances.reduce((total, instance) => {
                                                                if (instance.result === 'did_not_speak') {
                                                                    // Don't sum if client did not speak
                                                                    return total;
                                                                }

                                                                return total + instance.charge;
                                                            }, 0) + ' Bf',
                                                            null,
                                                            null,
                                                            null
                                                        )}
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

    _renderHeaderRow(duration, cost, day, start, end) {
        return this._renderRow(
            <TableHeaderColumn/>,
            'header',
            duration,
            cost,
            null,
            day,
            start,
            end,
            null
        );
    }

    _renderBodyRow(id, duration, cost, result, day, start, end, status) {
        return this._renderRow(
            <TableRowColumn/>,
            id,
            duration,
            cost,
            result,
            day,
            start,
            end,
            status
        );
    }

    _renderRow(component, id, duration, cost, result, day, start, end, status) {
        return (
            <TableRow key={id}>
                <component.type
                    style={{
                        textAlign: 'left',
                        width: "15%"
                    }}
                >
                    {duration}
                </component.type>
                <component.type
                    style={{
                        textAlign: 'right',
                        width: '15%',
                        textDecoration: result === 'did_not_speak'
                            ? 'line-through'
                            : 'none'
                    }}
                >
                    {cost}
                </component.type>
                <component.type
                    style={{
                        width: '20%'
                    }}
                />
                <component.type
                    style={{
                        textAlign: 'left',
                        width: '15%'
                    }}
                >
                    {day}
                </component.type>
                <component.type
                    style={{
                        textAlign: 'right',
                        width: '10%'
                    }}
                >
                    {start}
                </component.type>
                <component.type
                    style={{
                        textAlign: 'right',
                        width: '10%'
                    }}
                >
                    {end}
                </component.type>
                <component.type
                    style={{
                        textAlign: 'left',
                        width: '15%'
                    }}
                >
                    {status}
                </component.type>
            </TableRow>
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
        const seconds = duration % 60;
        const minutes = (duration - seconds) / 60;

        let string = '';

        if (minutes > 0) {
            string += minutes + (minutes === 1 ? ' minuto' : ' minutos');
        }

        if (minutes > 0 && seconds > 0) {
            string += ' y ';
        }

        if (seconds > 0) {
            string += seconds + (seconds === 1 ? ' segundo' : ' segundos');
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
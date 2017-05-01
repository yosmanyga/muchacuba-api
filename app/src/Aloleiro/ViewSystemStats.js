import React from 'react';
import {RadioButton, RadioButtonGroup} from 'material-ui/RadioButton';
import {purple900 as totalColor} from 'material-ui/styles/colors';
import {green900 as profitColor} from 'material-ui/styles/colors';
import {
    CartesianGrid,
    Legend,
    LineChart,
    Line,
    ResponsiveContainer,
    Tooltip,
    XAxis,
    YAxis,
} from 'recharts';
import Moment from 'moment';
import {} from 'moment/locale/es';

import ConnectToServer from '../ConnectToServer';
import Wait from '../Wait';

export default class ViewSystemStats extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
        profile: React.PropTypes.object,
        // (status, response)
        onError: React.PropTypes.func.isRequired
    };

    constructor(props) {
        super(props);

        this.state = {
            interval: null,
            stats: null,
        };

        this._connectToServer = new ConnectToServer();

        this._collectStats = this._collectStats.bind(this);
    }

    componentDidMount() {
        if (this.props.profile !== null) {
            this.setState({
                interval: 'current_month'
            });
        }
    }

    componentDidUpdate(prevProps, prevState) {
        if (this.props.profile !== null) {
            if (
                prevState.interval !== this.state.interval
            ) {
                this.setState({
                    stats: null
                });
            }

            if (this.state.stats === null) {
                let from, to;

                if (this.state.interval === 'last_month') {
                    from = Moment()
                        .startOf('month')
                        .subtract(1, 'month')
                        .unix();
                    to = Moment()
                        .endOf('month')
                        .subtract(1, 'month')
                        .unix();
                }

                if (this.state.interval === 'current_month') {
                    from = Moment()
                        .startOf('month')
                        .unix();
                    to = Moment()
                        .endOf('month')
                        .unix();
                }

                this._collectStats(from, to);
            }
        }
    }

    _collectStats(from, to) {
        this._connectToServer
            .get(
                '/aloleiro/compute-system-calls/'
                + from
                + '/'
                + to
            )
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
                    stats: res.body
                });
            });
    }

    render() {
        return (
            <this.props.layout.type
                {...this.props.layout.props}
                bar="Reportes"
            >
                <RadioButtonGroup
                    name="interval"
                    valueSelected={this.state.interval}
                    onChange={(event, value) => {
                        this.setState({
                            interval: value
                        });
                    }}
                    style={{display: "flex", justifyContent: "center"}}
                >
                    <RadioButton
                        value="last_month"
                        label="Mes anterior"
                        style={{width: "150px"}}
                    />
                    <RadioButton
                        value="current_month"
                        label="Mes actual"
                        style={{width: "150px"}}
                    />
                </RadioButtonGroup>
                {this.state.stats === null
                    ? <Wait/>
                    : this.state.stats.length > 0
                        ?
                            [
                                <p
                                    key="profit_title"
                                    style={{textAlign: "center"}}
                                >
                                    <strong>Ganancias</strong>
                                </p>,
                                <ResponsiveContainer
                                    key="profit_chart"
                                    width="100%"
                                    aspect={2}
                                >
                                    <LineChart
                                        data={this.state.stats}
                                        margin={{top: 5, right: 30, left: 20, bottom: 5}}
                                    >
                                        <XAxis name="Día" dataKey="day"/>
                                        <YAxis/>
                                        <CartesianGrid strokeDasharray="3 3"/>
                                        <Tooltip labelFormatter={(label) => {
                                            return 'Día '+ label;
                                        }}/>
                                        <Legend />
                                        <Line name="Ganancias" dataKey="profit" unit=" USD" type="monotone" stroke={profitColor} activeDot={{r: 8}}/>
                                    </LineChart>
                                </ResponsiveContainer>,
                                <p
                                    key="sale_title"
                                    style={{textAlign: "center"}}
                                >
                                    <strong>Ventas</strong>
                                </p>,
                                <ResponsiveContainer
                                    key="sale_chart"
                                    width="100%"
                                    aspect={2}
                                >
                                    <LineChart
                                        width={600}
                                        height={300}
                                        data={this.state.stats}
                                        margin={{top: 5, right: 30, left: 20, bottom: 5}}
                                    >
                                        <XAxis name="Día" dataKey="day"/>
                                        <YAxis/>
                                        <CartesianGrid strokeDasharray="3 3"/>
                                        <Tooltip labelFormatter={(label) => {
                                            return 'Día '+ label;
                                        }}/>
                                        <Legend />
                                        <Line name="Ventas" dataKey="sale" unit=" USD" type="monotone" stroke={profitColor} activeDot={{r: 8}}/>
                                    </LineChart>
                                </ResponsiveContainer>,
                                <p
                                    key="total_title"
                                    style={{textAlign: "center"}}
                                >
                                    <strong>Total de llamadas</strong>
                                </p>,
                                <ResponsiveContainer
                                    key="total_chart"
                                    width="100%"
                                    aspect={2}
                                >
                                    <LineChart
                                        data={this.state.stats}
                                        margin={{top: 5, right: 30, left: 20, bottom: 5}}
                                    >
                                        <XAxis name="Día" dataKey="day"/>
                                        <YAxis/>
                                        <CartesianGrid strokeDasharray="3 3"/>
                                        <Tooltip labelFormatter={(label) => {
                                            return 'Día '+ label;
                                        }}/>
                                        <Legend />
                                        <Line name="Total de llamadas" dataKey="total" type="monotone" stroke={totalColor} activeDot={{r: 8}}/>
                                    </LineChart>
                                </ResponsiveContainer>
                            ]
                        : <p>No hay datos</p>
                }
            </this.props.layout.type>
        );
    }
}


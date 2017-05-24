import React from 'react';
import {RadioButton, RadioButtonGroup} from 'material-ui/RadioButton';
import {green900 as profitColor} from 'material-ui/styles/colors';
import {red900 as purchaseColor} from 'material-ui/styles/colors';
import {yellow900 as saleColor} from 'material-ui/styles/colors';
import {purple900 as totalColor} from 'material-ui/styles/colors';
import MomentTimezone from 'moment-timezone';
import {} from 'moment/locale/es';

import ConnectToServer from '../ConnectToServer';
import Wait from '../Wait';
import Chart from './Chart';

export default class ViewStats extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
        profile: React.PropTypes.object,
        // (status, response)
        onError: React.PropTypes.func.isRequired,
        url: React.PropTypes.string.isRequired,
        unit: React.PropTypes.string.isRequired,
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
                let from, to, by;

                if (this.state.interval === 'last_month') {
                    from = MomentTimezone()
                        .tz('America/Caracas')
                        .startOf('month')
                        .subtract(1, 'month')
                        .unix();
                    to = MomentTimezone()
                        .tz('America/Caracas')
                        .endOf('month')
                        .subtract(1, 'month')
                        .unix();
                    by = 'by-day';
                }

                if (this.state.interval === 'current_month') {
                    from = MomentTimezone()
                        .tz('America/Caracas')
                        .startOf('month')
                        .unix();
                    to = MomentTimezone()
                        .tz('America/Caracas')
                        .endOf('month')
                        .unix();
                    by = 'by-day';
                }

                if (this.state.interval === 'current_year') {
                    from = MomentTimezone()
                        .tz('America/Caracas')
                        .startOf('year')
                        .unix();
                    to = MomentTimezone()
                        .tz('America/Caracas')
                        .endOf('year')
                        .unix();
                    by = 'by-month';
                }

                this._collectStats(from, to, by);
            }
        }
    }

    _collectStats(from, to, by) {
        this._connectToServer
            .get(
                this.props.url
                + from
                + '/'
                + to
                + '/'
                + by
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
                    <RadioButton
                        value="current_year"
                        label="Año actual"
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
                                <Chart
                                    key="profit_chart"
                                    data={this.state.stats}
                                    xAxis={{
                                        name: this.state.interval !== 'current_year'
                                            ? 'Día' : 'Mes',
                                        dataKey: this.state.interval !== 'current_year'
                                            ? 'day' : 'month',
                                    }}
                                    lines={[{
                                        name: "Ganancias",
                                        dataKey: "profit",
                                        unit: this.props.unit,
                                        stroke: profitColor
                                    }]}
                                />,
                                <p
                                    key="sale_title"
                                    style={{textAlign: "center"}}
                                >
                                    <strong>Ventas</strong>
                                </p>,
                                <Chart
                                    key="sale_chart"
                                    data={this.state.stats}
                                    xAxis={{
                                        name: this.state.interval !== 'current_year'
                                            ? 'Día' : 'Mes',
                                        dataKey: this.state.interval !== 'current_year'
                                            ? 'day' : 'month',
                                    }}
                                    lines={[
                                        {
                                            name: "Ventas",
                                            dataKey: "sale",
                                            unit: this.props.unit,
                                            stroke: saleColor
                                        },
                                        {
                                            name: "Compras",
                                            dataKey: "purchase",
                                            unit: this.props.unit,
                                            stroke: purchaseColor
                                        }
                                    ]}
                                />,
                                <p
                                    key="total_title"
                                    style={{textAlign: "center"}}
                                >
                                    <strong>Total de llamadas</strong>
                                </p>,
                                <Chart
                                    key="total_chart"
                                    data={this.state.stats}
                                    xAxis={{
                                        name: this.state.interval !== 'current_year'
                                            ? 'Día' : 'Mes',
                                        dataKey: this.state.interval !== 'current_year'
                                            ? 'day' : 'month',
                                    }}
                                    lines={[{
                                        name: "Total de llamadas",
                                        dataKey: "total",
                                        unit: this.props.unit,
                                        stroke: totalColor
                                    }]}
                                />
                            ]
                        : <p>No hay datos</p>
                }
            </this.props.layout.type>
        );
    }
}


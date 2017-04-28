import React from 'react';
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
import {purple900 as totalColor} from 'material-ui/styles/colors';
import {green900 as profitColor} from 'material-ui/styles/colors';

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
            stats: null,
        };

        this._connectToServer = new ConnectToServer();

        this._collectStats = this._collectStats.bind(this);
    }

    componentDidMount() {
        if (this.props.profile !== null) {
            this._collectStats();
        }
    }

    componentDidUpdate(prevProps, prevState) {
        if (
            prevProps.profile === null
            && this.props.profile !== null
        ) {
            this._collectStats();
        }
    }

    _collectStats() {
        this._connectToServer
            .get('/aloleiro/compute-monthly-system-calls')
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
        if (this.state.stats === null) {
            return (
                <Wait layout={this.props.layout}/>
            );
        }

        if (this.state.stats.length === 0) {
            return (
                <this.props.layout.type
                    {...this.props.layout.props}
                    bar="Reportes"
                >
                    <p>No hay datos para hacer reporte.</p>
                </this.props.layout.type>
            );
        }

        return (
            <this.props.layout.type
                {...this.props.layout.props}
                bar="Reportes"
                style={{
                    ...this.props.layout.style,
                    textAlign: "center"
                }}
            >
                <p><strong>Ganancias en el mes actual</strong></p>
                <ResponsiveContainer width="100%" aspect={2}>
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
                </ResponsiveContainer>
                <p><strong>Total de llamadas en el mes actual</strong></p>
                <ResponsiveContainer width="100%" aspect={2}>
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
            </this.props.layout.type>
        );
    }
}


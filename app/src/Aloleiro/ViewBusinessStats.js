import React from 'react';
import {
    CartesianGrid,
    Legend,
    LineChart,
    Line,
    Tooltip,
    XAxis,
    YAxis,
} from 'recharts';
import {purple900 as totalColor} from 'material-ui/styles/colors';
import {green900 as profitColor} from 'material-ui/styles/colors';

import ConnectToServer from '../ConnectToServer';
import Wait from '../Wait';

export default class ViewBusinessStats extends React.Component {
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
            .get('/aloleiro/compute-monthly-business-calls')
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
                >
                    <p>No hay datos para hacer reporte.</p>
                </this.props.layout.type>
            );
        }

        return (
            <this.props.layout.type
                {...this.props.layout.props}
            >
                <div
                    style={{
                        display: "flex"
                    }}
                >
                    <div style={{textAlign: "center"}}>
                        <h1>Ganancias en el mes actual</h1>
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
                            <Line name="Ganancias" dataKey="profit" unit=" Bf" type="monotone" stroke={profitColor} activeDot={{r: 8}}/>
                        </LineChart>
                    </div>
                    <div style={{textAlign: "center"}}>
                        <h1>Total de llamadas en el mes actual</h1>
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
                            <Line name="Total de llamadas" dataKey="total" type="monotone" stroke={totalColor} activeDot={{r: 8}}/>
                        </LineChart>
                    </div>
                </div>

            </this.props.layout.type>
        );
    }
}


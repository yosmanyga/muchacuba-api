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

export default class ViewBusinessStats extends React.Component {
    static propTypes = {
        data: React.PropTypes.array,
        xAxis: React.PropTypes.shape({
            name: React.PropTypes.string.isRequired,
            dataKey: React.PropTypes.string.isRequired
        }).isRequired,
        lines: React.PropTypes.arrayOf(React.PropTypes.shape({
            name: React.PropTypes.string.isRequired,
            dataKey: React.PropTypes.string.isRequired,
            unit: React.PropTypes.string.isRequired,
            stroke: React.PropTypes.string.isRequired
        }).isRequired),
    };

    render() {
        return (
            <ResponsiveContainer
                width="100%"
                aspect={2}
            >
                <LineChart
                    data={this.props.data}
                    margin={{top: 5, right: 30, left: 20, bottom: 5}}
                >
                    <XAxis
                        name={this.props.xAxis.name}
                        dataKey={this.props.xAxis.dataKey}
                    />
                    <YAxis/>
                    <CartesianGrid strokeDasharray="3 3"/>
                    <Tooltip labelFormatter={(label) => {
                        return this.props.xAxis.name + ' ' + label;
                    }}/>
                    <Legend />
                    {this.props.lines.map((line) => {
                        return <Line
                            key={line.dataKey}
                            name={line.name}
                            dataKey={line.dataKey}
                            unit={' ' + line.unit}
                            type="monotone"
                            stroke={line.stroke}
                            activeDot={{r: 8}}
                        />
                    })}
                </LineChart>
            </ResponsiveContainer>
        );
    }
}


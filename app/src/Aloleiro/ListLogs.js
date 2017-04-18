import React from 'react';
import {Table, TableBody, TableHeader, TableHeaderColumn, TableRow, TableRowColumn} from 'material-ui/Table';

import ConnectToServer from '../ConnectToServer';
import Wait from '../Wait';

export default class ListLogs extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
        profile: React.PropTypes.object,
    };

    constructor(props) {
        super(props);

        this.state = {
            logs: null,
        };

        this._connectToServer = new ConnectToServer();

        this._collectLogs = this._collectLogs.bind(this);
    }

    componentDidMount() {
        if (this.props.profile !== null) {
            this._collectLogs();
        }
    }

    componentDidUpdate(prevProps, prevState) {
        if (
            prevProps.profile === null
            && this.props.profile !== null
        ) {
            this._collectLogs();
        }
    }

    _collectLogs() {
        this._connectToServer
            .get('/aloleiro/collect-logs')
            .auth(this.props.profile.token)
            .send()
            .end((err, res) => {
                if (err) {
                    // TODO

                    return;
                }

                this.setState({
                    logs: res.body
                });
            });
    }

    render() {
        if (this.state.logs === null) {
            return (
                <Wait layout={this.props.layout}/>
            );
        }

        return (
            <this.props.layout.type
                {...this.props.layout.props}
            >
                {this.state.logs.length !== 0
                    ? <Table style={{background: "transparent"}}>
                        <TableHeader
                            displaySelectAll={false}
                            adjustForCheckbox={false}
                        >
                            <TableRow>
                                <TableHeaderColumn>Tipo</TableHeaderColumn>
                                <TableHeaderColumn>Datos</TableHeaderColumn>
                            </TableRow>
                        </TableHeader>
                        <TableBody displayRowCheckbox={false}>
                            {this.state.logs.map((log) => {
                                return (
                                    <TableRow key={log.id}>
                                        <TableRowColumn>log.type</TableRowColumn>
                                        <TableRowColumn><pre dangerouslySetInnerHTML={{__html: JSON.stringify(log.payload, null, 4)}} /></TableRowColumn>
                                    </TableRow>
                                );
                            })}
                        </TableBody>
                    </Table>
                    : <p>No hay logs</p>
                }
            </this.props.layout.type>
        );
    }
}


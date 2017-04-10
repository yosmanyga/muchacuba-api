import React from 'react';
import {Table, TableBody, TableHeader, TableHeaderColumn, TableRow, TableRowColumn} from 'material-ui/Table';

import ConnectToServer from '../ConnectToServer';
import Wait from '../Wait';

export default class ListEvents extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
        // (onSuccess(token), onError)
        onBackAuth: React.PropTypes.func.isRequired,
        // ()
        onFrontAuth: React.PropTypes.func.isRequired,
        // (message, finish)
        onNotify: React.PropTypes.func.isRequired,
    };

    constructor(props) {
        super(props);

        this.state = {
            token: null,
            events: null,
        };

        this._connectToServer = new ConnectToServer();

        this._collectEvents = this._collectEvents.bind(this);
    }

    componentDidMount() {
        this.props.onBackAuth(
            (token) => {
                if (token === 'null') {
                    this.props.onFrontAuth();

                    return;
                }

                this.setState({
                    token: token
                });
            },
            () => {
                this.props.onFrontAuth();
            }
        );
    }

    componentDidUpdate(prevProps, prevState) {
        if (
            prevState.token === null
            && this.state.token !== null
        ) {
            this._collectEvents();
        }
    }

    _collectEvents() {
        this._connectToServer
            .get('/aloleiro/collect-events')
            .auth(this.state.token)
            .send()
            .end((err, res) => {
                if (err) {
                    // TODO

                    return;
                }

                this.setState({
                    events: res.body
                });
            });
    }

    render() {
        if (this.state.events === null) {
            return (
                <Wait layout={this.props.layout}/>
            );
        }

        return (
            <this.props.layout.type
                {...this.props.layout.props}
            >
                {this.state.events.length !== 0
                    ? <Table>
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
                            {this.state.events.map((event) => {
                                return (
                                    <TableRow key={event.id}>
                                        <TableRowColumn>{event.type}</TableRowColumn>
                                        <TableRowColumn>{JSON.stringify(event.payload, null, 4)}</TableRowColumn>
                                    </TableRow>
                                );
                            })}
                        </TableBody>
                    </Table>
                    : <p>No hay eventos</p>
                }
            </this.props.layout.type>
        );
    }
}


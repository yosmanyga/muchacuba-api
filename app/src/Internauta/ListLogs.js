import React from 'react';
import CircularProgress from 'material-ui/CircularProgress';
import Paper from 'material-ui/Paper';
import _ from 'lodash';

import ConnectToServer from '../ConnectToServer';

import ViewLogs from './ViewLogs';

export default class ListLogs extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
    };

    constructor(props) {
        super(props);

        this.state = {
            logs: null
        };

        this._connectToServer = new ConnectToServer();

        this._collectLogs = this._collectLogs.bind(this);
    }

    componentDidMount() {
        this._collectLogs();
    }

    _collectLogs() {
        this._connectToServer
            .get('/internauta/collect-logs')
            .end((err, res) => {
                if (err) {
                    return;
                }

                this.setState({
                    logs: res.body
                });
            });
    }
    
    render() {
        let body = null;

        if (this.state.logs === null) {
            body = <CircularProgress size={20} style={{marginTop: "10px"}}/>;
        } else {
            let tree = [];

            this.state.logs.forEach((log) => {
                let branch = tree.find((branch) => {
                    return branch.id === log.payload.id;
                });

                if (typeof branch === 'undefined') {
                    tree.unshift({
                        id: log.payload.id,
                        logs: [log]
                    });

                    return;
                }

                branch.logs.push(log);
            });

            body = _.map(tree, (branch, key) => {
                return <ViewLogs
                    key={branch.id}
                    id={branch.id}
                    logs={branch.logs}
                    onDelete={(logs) => {
                        this.setState({
                            logs: logs
                        });
                    }}
                />
            });
        }

        return (
            <this.props.layout.type {...this.props.layout.props}>
                <Paper zDepth={2} style={{padding: "10px"}}>
                    {body}
                </Paper>
            </this.props.layout.type>
        );
    }
}
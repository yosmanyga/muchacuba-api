import React from 'react';
import PropTypes from 'prop-types';
import Paper from 'material-ui/Paper';
import {Card, CardActions, CardHeader, CardText} from 'material-ui/Card';
import {green50, red50, yellow50} from 'material-ui/styles/colors';
import Dialog from 'material-ui/Dialog';
import Moment from 'moment';
import {} from 'moment/locale/es';

import {collectLogs, deleteLogGroup, debugLocally} from './Api';

import {Button, Wait} from '../Base/UI';

import Horoscope_ProcessRequest from './Log/Horoscope_ProcessRequest';
import Horoscope_ProcessRequest_NotFound from './Log/Horoscope_ProcessRequest_NotFound';
import Image_ProcessRequest_Heavy from './Log/Image_ProcessRequest_Heavy';
import Image_ProcessRequest_Invalid from './Log/Image_ProcessRequest_Invalid';
import Lyrics_ProcessRequest_BigBody from './Log/Lyrics_ProcessRequest_BigBody';
import Lyrics_ProcessRequest_Exception from './Log/Lyrics_ProcessRequest_Exception';
import Lyrics_ProcessRequest_Found from './Log/Lyrics_ProcessRequest_Found';
import Lyrics_ProcessRequest_HtmlIncluded from './Log/Lyrics_ProcessRequest_HtmlIncluded';
import Lyrics_ProcessRequest_NotFound from './Log/Lyrics_ProcessRequest_NotFound';
import Lyrics_ProcessRequest_QuestionableParsing from './Log/Lyrics_ProcessRequest_QuestionableParsing';
import Lyrics_ProcessRequest_UnsupportedLink from './Log/Lyrics_ProcessRequest_UnsupportedLink';
import Lyrics_ProcessRequest_UnsupportedLinks from './Log/Lyrics_ProcessRequest_UnsupportedLinks';
import Mailgun_PushRequest from './Log/Mailgun_PushRequest';
import ProcessRequests_BeginProcessing from './Log/ProcessRequests_BeginProcessing';
import ProcessRequests_Exception from './Log/ProcessRequests_Exception';
import Revolico_ProcessRequest_NotFound from './Log/Revolico_ProcessRequest_NotFound';
import SendEmail from './Log/SendEmail';
import SendEmail_UnknownType from './Log/SendEmail_UnknownType';

Moment.locale('es');

export default class ListLogs extends React.Component {
    static propTypes = {
        layout: PropTypes.element.isRequired,
    };

    constructor(props) {
        super(props);

        this.state = {
            logs: null,
            branch: null,
            debugDialog: false
        };
    }

    componentWillMount() {
        collectLogs(
            (logs) => {
                this.setState({
                    logs: logs
                });
            }
        );
    }

    render() {
        if (this.state.logs === null) {
            return <Wait/>;
        }

        const layout=<this.props.layout.type
            {...this.props.layout.props}
            appBar={null}
        />;

        if (this.state.logs === null) {
            return <Wait layout={layout}/>;
        }

        return (
            <layout.type {...layout.props}>
                <Paper style={{padding: "10px"}}>
                    {this._buildGroups(this.state.logs)}
                </Paper>
                {this.state.debugDialog === true ? <DebugDialog
                    branch={this.state.branch}
                    onDebug={() => {
                        this.setState({
                            debugDialog: false
                        });
                    }}
                    onCancel={() => {
                        this.setState({
                            debugDialog: false
                        });
                    }}
                /> : null}
            </layout.type>
        );
    }

    _buildGroups(logs) {
        return this._buildTree(logs).map((branch) => {
            return <GroupBlock
                key={branch.id}
                id={branch.id}
                logs={branch.logs}
                onDelete={() => {
                    deleteLogGroup(
                        branch.id
                    );

                    // Too slow
                    // this.setState({
                    //     logs: this.state.logs.filter((log) => {
                    //         return log.payload.id !== branch.id
                    //     })
                    // }, () => {
                    //     deleteLogGroup(
                    //         branch.id
                    //     );
                    // });
                }}
                onDebug={(finish) => {
                    this.setState({
                        branch: branch,
                        debugDialog: true
                    }, finish);
                }}
            />
        });
    }

    _buildTree(logs) {
        let tree = [];

        logs.forEach((log) => {
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

        return tree;
    }
}

class GroupBlock extends React.Component {
    static propTypes = {
        id: PropTypes.string.isRequired,
        logs: PropTypes.array.isRequired,
        // ()
        onDelete: PropTypes.func.isRequired,
        // ()
        onDebug: PropTypes.func.isRequired
    };

    constructor() {
        super();

        this._components = [
            new Horoscope_ProcessRequest(),
            new Horoscope_ProcessRequest_NotFound(),
            new Image_ProcessRequest_Heavy(),
            new Image_ProcessRequest_Invalid(),
            new Lyrics_ProcessRequest_BigBody(),
            new Lyrics_ProcessRequest_Exception(),
            new Lyrics_ProcessRequest_Found(),
            new Lyrics_ProcessRequest_HtmlIncluded(),
            new Lyrics_ProcessRequest_NotFound(),
            new Lyrics_ProcessRequest_QuestionableParsing(),
            new Lyrics_ProcessRequest_UnsupportedLink(),
            new Lyrics_ProcessRequest_UnsupportedLinks(),
            new Mailgun_PushRequest(),
            new ProcessRequests_BeginProcessing(),
            new ProcessRequests_Exception(),
            new Revolico_ProcessRequest_NotFound(),
            new SendEmail(),
            new SendEmail_UnknownType()
        ];
    }

    render() {
        let color = null;

        let cards = this.props.logs.map((log) => {
            const component = this._components.find((component) => {
                return component.support(log.type);
            });

            if (typeof component === 'undefined') {
                throw new Error("No component for " + log.type);
            }

            if (component.level() === 'e') {
                color = red50;
            } else if (component.level() === 'w' && color !== 'e') {
                color = yellow50;
            } else if (component.level() === 'd' && color === null) {
                color = green50;
            }

            return component.instantiate({key: log.id, log: log});
        });

        return (
            <Card style={{backgroundColor: color}}>
                <CardHeader
                    title={
                        Moment.unix(this.props.logs[0].date).format('LLLL')
                        + ' :: '
                        + this.props.logs[0].payload.sender
                        + ' :: '
                        + this.props.logs[0].payload.recipient
                    }
                    actAsExpander={true}
                    showExpandableButton={true}
                />
                <CardText expandable={true}>
                    {cards}
                </CardText>
                <CardActions>
                    <Button
                        label="Borrar"
                        labelAfterTouchTap="Borrando..."
                        icon="delete"
                        onTouchTap={this.props.onDelete}
                    />
                    <Button
                        label="Debugguear"
                        icon="build"
                        onTouchTap={this.props.onDebug}
                    />
                </CardActions>
            </Card>
        );
    }
}

class DebugDialog extends React.Component {
    static propTypes = {
        branch: PropTypes.object.isRequired,
        // ()
        onDebug: PropTypes.func.isRequired,
        // ()
        onCancel: PropTypes.func.isRequired
    };

    constructor(props) {
        super(props);

        this.state = {
            busy: false,
        };
    }

    render() {
        return(
            <Dialog
                open={true}
                title="Debugguear localmente"
                actions={[
                    <Button
                        label="Cancelar"
                        disabled={this.state.busy === true}
                        onTouchTap={this.props.onCancel}
                    />,
                    <Button
                        label={!this.state.busy ? "Enviar" : "Enviando..."}
                        primary={true}
                        disabled={this.state.busy === true}
                        onTouchTap={() => {
                            this.setState({
                                busy: true
                            }, () => {
                                debugLocally(
                                    'yosmanyga@gmail.com',
                                    this.props.branch.logs[0].payload.recipient,
                                    this.props.branch.logs[0].payload.subject,
                                    this.props.branch.logs[0].payload['body-plain'],
                                    this.props.onDebug
                                );
                            });
                        }}
                        style={{
                            marginLeft: "8px"
                        }}
                    />
                ]}
                modal={true}
                autoScrollBodyContent={true}
                onRequestClose={this.props.onCancel}
            >
                <p>Sender: yosmanyga@gmail.com</p>
                <p>Recipient: {this.props.branch.logs[0].payload.recipient}</p>
                <p>Subject: {this.props.branch.logs[0].payload.subject}</p>
                <p>Body: {this.props.branch.logs[0].payload['body-plain']}</p>
            </Dialog>
        );
    }
}
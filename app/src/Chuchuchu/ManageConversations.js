import React from 'react';
import ReactDOM from 'react-dom';
import * as firebase from 'firebase';
import Avatar from 'material-ui/Avatar';
import CircularProgress from 'material-ui/CircularProgress';
import Divider from 'material-ui/Divider';
import FlatButton from 'material-ui/FlatButton';
import {List, ListItem} from 'material-ui/List';
import Paper from 'material-ui/Paper';
import Subheader from 'material-ui/Subheader';
import {green50} from 'material-ui/styles/colors';
import TextField from 'material-ui/TextField';
import CommunicationChatBubble from 'material-ui/svg-icons/communication/chat-bubble';
// import Infinite from 'react-infinite';
import _ from 'lodash';

import ConnectToServer from '../ConnectToServer';
import Button from '../Button';
import Wait from '../Wait';

import picture from './picture.jpg';

firebase.initializeApp({
    apiKey: "AIzaSyClvnStM8ZWjDNjjU-CaQ5NrjC2Ttd8eTI",
    authDomain: "chuchuchu-2bb11.firebaseapp.com",
    databaseURL: "https://chuchuchu-2bb11.firebaseio.com",
    storageBucket: "chuchuchu-2bb11.appspot.com",
    messagingSenderId: "1003585501404"
});

export default class ManageConversations extends React.Component {
    static propTypes = {
        query: React.PropTypes.object.isRequired,
        layout: React.PropTypes.element.isRequired,
        // (onSuccess, onFailure)
        onUnderAuth: React.PropTypes.func.isRequired,
        // (backUrl)
        onUnauthorized: React.PropTypes.func.isRequired,
    };

    constructor(props) {
        super(props);

        this.state = {
            token: null,
            page: 'list', // list, invite
            contacts: null,
            conversations: null,
            conversation: null
        };

        this._firebaseMessaging = firebase.messaging();
        this._connectToServer = new ConnectToServer();

        this._prepareFirebase = this._prepareFirebase.bind(this);
        this._loadContacts = this._loadContacts.bind(this);
        this._loadConversations = this._loadConversations.bind(this);
    }

    componentDidMount() {
        this.props.onUnderAuth(
            (token) => {
                this.setState({token: token})
            },
            () => {
                this.props.onUnauthorized('/');
            }
        );
    }

    componentDidUpdate(prevProps, prevState) {
        // No authentication yet?
        if (this.state.token === null) {
            return;
        }

        if (prevState.token !== this.state.token) {
            // Token won't change after set for first time
            // It means that this preparation is executed just once

            this._prepareFirebase();
            this._loadContacts();
            this._loadConversations();
        }

        // Open conversation set by query
        if (
            typeof this.props.query.conversation !== 'undefined'
            && this.state.conversation !== this.props.query.conversation
        ) {
            const conversation = _.find(
                this.state.conversations,
                {id: this.props.query.conversation}
            );

            // Conversation not found
            if (typeof conversation === 'undefined') {
                return;
            }

            this.setState({conversation: this.props.query.conversation});
        }

        // Did conversation change?
        if (prevState.conversation !== this.state.conversation) {
            const conversation = _.find(
                this.state.conversations,
                {id: this.state.conversation}
            );

            // Has not loaded messages from server?
            if (typeof conversation.messages === 'undefined') {
                this._connectToServer
                    .get('/chuchuchu/collect-messages/' + this.state.conversation)
                    .auth(this.state.token)
                    .send()
                    .end((err, res) => {
                        if (err) {
                            if (err.status === 401) {
                                this.props.onUnauthorized('');

                                return;
                            }

                            // TODO
                        }

                        const conversations = _.map(
                            this.state.conversations,
                            (conversation) => {
                                if (conversation.id === this.state.conversation) {
                                    return _.set(
                                        conversation,
                                        'messages',
                                        res.body
                                    );
                                }

                                return conversation;
                            }
                        );

                        this.setState({
                            conversations: conversations
                        });
                    });
            }
        }
    }

    render() {
        if (this.state.token === null) {
            return <Wait />;
        }

        if (this.state.contacts === null || this.state.conversations === null) {
            return <Wait layout={this.props.layout}/>;
        }

        if (this.state.page === 'invite') {
            return (
                <Invite
                    layout={this.props.layout}
                    token={this.state.token}
                    onFinish={(conversations, contacts, finish) => {
                        this.setState({
                            page: 'list',
                            conversations: conversations,
                            contacts: contacts
                        });

                        // Don't call finish, because the Invite component was
                        // unmounted at this point
                    }}
                />
            );
        }

        return (
            <this.props.layout.type {...this.props.layout.props}>
                <div
                    style={{
                        display: "flex",
                        height: "calc(100% - 64px)"
                    }}
                >
                    <Paper style={{
                        width: "30%",
                        padding: "10px"
                    }}>
                        <List>
                            <Subheader>Vecin@s</Subheader>
                            {this.state.conversations.map((conversation) => {
                                const contact = _.find(
                                    this.state.contacts,
                                    (contact) => {
                                        return typeof _.find(
                                                conversation.participants,
                                                (participant) => {
                                                    return participant === contact.id;
                                                }
                                            ) !== 'undefined'
                                    }
                                );

                                return (
                                    <ListItem
                                        key={conversation.id}
                                        primaryText={contact.name}
                                        onTouchTap={() => {
                                            this.setState({
                                                conversation: conversation.id
                                            });
                                        }}
                                        leftAvatar={contact.picture !== null
                                            ? <Avatar src={contact.picture} />
                                            : <Avatar src={picture} />
                                        }
                                        rightIcon={typeof conversation.fresh !== 'undefined' ? <CommunicationChatBubble /> : null}
                                        style={{backgroundColor: this.state.conversation === conversation ? green50 : null}}
                                    />
                                );
                            })}
                        </List>
                        <FlatButton
                            label="Invitar"
                            primary={true}
                            onTouchTap={() => {this.setState({page: 'invite'})}}
                        />
                    </Paper>
                    {this.state.conversation !== null
                        ? <Conversation
                            layout={<Paper
                                style={{
                                    flexGrow: 1,
                                    paddingLeft: "10px"
                                }}
                            />}
                            token={this.state.token}
                            messages={_.find(
                                this.state.conversations,
                                {id: this.state.conversation}
                            ).messages}
                            onNewMessage={(text) => {
                                const conversations = _.map(
                                    this.state.conversations,
                                    (conversation) => {
                                        if (conversation.id === this.state.conversation) {
                                            return _.update(
                                                conversation,
                                                'messages',
                                                (messages) => {
                                                    return messages.concat([{
                                                        content: text
                                                    }]);
                                                }
                                            );
                                        }

                                        return conversation;
                                    }
                                );

                                this.setState({
                                    conversations: conversations
                                }, this._connectToServer
                                    .post('/chuchuchu/insert-message')
                                    .auth(this.state.token)
                                    .send({
                                        conversation: this.state.conversation,
                                        content: text
                                    })
                                    .end((err, res) => {
                                        if (err) {
                                            // TODO

                                            return;
                                        }
                                    }));
                            }}
                        />
                        : null
                    }
                </div>
            </this.props.layout.type>
        );
    }

    _prepareFirebase() {
        this._firebaseMessaging.requestPermission()
            .then(() => {
                this._firebaseMessaging.getToken()
                    .then((token) => {
                        this._connectToServer
                            .post('/chuchuchu/firebase/set-presence')
                            .auth(this.state.token)
                            .send({'token': token})
                            .end((err, res) => {
                                if (err) {
                                    if (err.status === 401) {
                                        this.props.onUnauthorized('');

                                        return;
                                    }

                                    // TODO
                                }
                            });
                    })
                    .catch(function(err) {
                        console.log('An error occurred while retrieving token. ', err);
                    });
            })
            .catch(function(err) {
                console.log('Unable to get permission to notify.', err);
            });

        this._firebaseMessaging.onMessage((payload) => {
            const message = JSON.parse(payload.data.message);

            const conversations = _.map(
                this.state.conversations,
                (conversation) => {
                    if (conversation.id === message.conversation) {
                        // Not messages loaded from server yet?
                        if (typeof conversation.messages === 'undefined') {
                            if (typeof conversation.fresh === 'undefined') {
                                return _.set(
                                    conversation,
                                    'fresh',
                                    1
                                );
                            }

                            return _.update(
                                conversation,
                                'fresh',
                                (fresh) => {
                                    return fresh + 1;
                                }
                            );
                        }

                        // Messages were already loaded from server
                        // So then concat message
                        conversation = _.update(
                            conversation,
                            'messages',
                            (messages) => {
                                return messages.concat([message]);
                            }
                        );

                        return conversation;
                    }

                    return conversation
                }
            );

            this.setState({
                conversations: conversations
            });
        });
    }

    _loadContacts() {
        this._connectToServer
            .get('/chuchuchu/collect-contacts')
            .auth(this.state.token)
            .end((err, res) => {
                if (err) {
                    // TODO

                    return;
                }

                this.setState({
                    contacts: res.body
                });
            });
    }

    _loadConversations() {
        this._connectToServer
            .get('/chuchuchu/collect-conversations')
            .auth(this.state.token)
            .end((err, res) => {
                if (err) {
                    // TODO

                    return;
                }

                this.setState({
                    conversations: res.body
                });
            });
    }
}

class Invite extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
        token: React.PropTypes.string.isRequired,
        // (conversations, finish())
        onFinish: React.PropTypes.func.isRequired
    };

    constructor(props) {
        super(props);

        this.state = {
            email: null,
            message: null
        };

        this._connectToServer = new ConnectToServer();
    }

    render() {
        return (
            <this.props.layout.type {...this.props.layout.props}>
                <TextField
                    hintText="Email"
                    autoFocus={true}
                    autoCapitalize="none"
                    onChange={(e, value) => {
                        this.setState({email: value});
                    }}
                />
                <br />
                <TextField
                    hintText="Mensaje"
                    rows={2}
                    rowsMax={4}
                    onChange={(e, value) => {
                        this.setState({message: value});
                    }}
                />
                <br />
                <Button
                    label="Enviar"
                    labelAfterTouchTap="Enviando..."
                    icon="send"
                    onTouchTap={(finish) => {
                        this._connectToServer
                            .post('/chuchuchu/invite')
                            .auth(this.props.token)
                            .send({
                                email: this.state.email,
                                message: this.state.message
                            })
                            .end((err, res) => {
                                if (err) {
                                    // TODO

                                    return;
                                }

                                this.props.onFinish(
                                    res.body.conversations,
                                    res.body.contacts,
                                    finish
                                );
                            });
                    }}
                />
            </this.props.layout.type>
        );
    }
}

class Conversation extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
        token: React.PropTypes.string.isRequired,
        messages: React.PropTypes.array,
        // (text)
        onNewMessage: React.PropTypes.func.isRequired
    };

    componentDidUpdate(prevProps, prevState) {
        if (prevProps.messages !== this.props.messages) {
            /* Scroll to last */
            const list = ReactDOM.findDOMNode(this._listEl);
            const scrollHeight = list.scrollHeight;
            const height = list.clientHeight;
            const maxScrollTop = scrollHeight - height;

            list.scrollTop = maxScrollTop > 0 ? maxScrollTop : 0;
        }
    }

    render() {
        if (typeof this.props.messages === 'undefined') {
            return (
                <this.props.layout.type
                    {...this.props.layout.props}
                    style={{
                        display: "flex",
                        justifyContent: "center",
                        flexGrow: 1,
                        paddingTop: "10px"
                    }}
                >
                    <CircularProgress size={20}/>
                </this.props.layout.type>
            );
        }

        return (
            <this.props.layout.type
                {...this.props.layout.props}
                style={{
                    ...this.props.layout.props.style,
                    display: "flex",
                    flexDirection: "column",
                }}
            >
                <List
                    ref={(el) => {this._listEl = el}}
                    style={{flexGrow: 1, overflowY: "auto"}}
                >
                    {this.props.messages.map((message, i) => {
                        return [
                            i === 0 ? null : <Divider inset={true} />,
                            <ListItem
                                key={message.id}
                                primaryText={message.content}
                                innerDivStyle={{
                                    paddingLeft: 0
                                }}
                            />
                        ];
                    })}
                </List>
                <NewMessage
                    layout={<div style={{display: "flex", padding: "10px 10px 10px 0"}}/>}
                    onSend={this.props.onNewMessage}
                />
            </this.props.layout.type>
        );
    }
}

class NewMessage extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
        onSend: React.PropTypes.func.isRequired
    };

    constructor(props) {
        super(props);

        this.state = {
            text: ''
        };

        this._handleSend = this._handleSend.bind(this);
    }

    render() {
        return (
            <this.props.layout.type {...this.props.layout.props}>
                <TextField
                    value={this.state.text}
                    hintText="Mensaje"
                    multiLine={true}
                    rows={1}
                    rowsMax={3}
                    autoFocus={true}
                    style={{flexGrow: 1}}
                    onChange={(e, value) => {
                        this.setState({text: value});
                    }}
                    onKeyPress={(e) => {
                        if (e.key === 'Enter') {
                            this._handleSend();
                        }
                    }}
                />
                <div style={{marginLeft: "10px"}}>
                    <Button
                        label="Enviar"
                        icon="send"
                        onTouchTap={this._handleSend}
                    />
                </div>
            </this.props.layout.type>
        );
    }

    _handleSend() {
        const text = this.state.text;

        this.setState(
            {text: ''},
            this.props.onSend(text)
        );
    }
}
import React from 'react';
import ReactDOM from 'react-dom';
import * as firebase from 'firebase';
import Avatar from 'material-ui/Avatar';
// import CircularProgress from 'material-ui/CircularProgress';
// import Divider from 'material-ui/Divider';
// import FlatButton from 'material-ui/FlatButton';
import IconButton from 'material-ui/IconButton';
import {List, ListItem} from 'material-ui/List';
import LeftIcon from 'material-ui/svg-icons/hardware/keyboard-arrow-left';
// import Paper from 'material-ui/Paper';
// import Subheader from 'material-ui/Subheader';
// import {gre8en50} from 'material-ui/styles/colors';
import TextField from 'material-ui/TextField';
// import CommunicationChatBubble from 'material-ui/svg-icons/communication/chat-bubble';
// import Infinite from 'react-infinite';
import _ from 'lodash';

import ConnectToServer from '../ConnectToServer';
import Button from '../Button';
import Wait from '../Wait';
import Center from '../Center';

import defaultPicture from './picture.jpg';

export default class ManageConversations extends React.Component {
    static propTypes = {
        query: React.PropTypes.object.isRequired,
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
            selected: null // {id, type}
        };

        this._firebaseMessaging = firebase.messaging();
        this._connectToServer = new ConnectToServer();

        // this._prepareFirebase = this._prepareFirebase.bind(this);
        // this._prepareGeo = this._prepareGeo.bind(this);
        // this._loadContacts = this._loadContacts.bind(this);
        // this._loadPresences = this._loadPresences.bind(this);
        // this._loadConversations = this._loadConversations.bind(this);
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
        // No authentication yet?
        if (
            this.state.token !== null
            && this.state.token !== prevState.token
        ) {
            this._firebaseMessaging.requestPermission()
                .then(() => {
                    this._firebaseMessaging.getToken()
                        .then((token) => {
                            this._connectToServer
                                .post('/firebase/update-profile')
                                .auth(this.state.token)
                                .send({
                                    token: token
                                })
                                .end((err, res) => {
                                    if (err) {
                                        // TODO

                                        return;
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
        }



        /*
        if (prevState.token !== this.state.token) {
            // Token won't change after set for first time
            // It means that this preparation is executed just once

            this._prepareFirebase();
            this._prepareGeo();
            this._loadContacts();
            this._loadConversations();
        }

        if (
            prevState.presence !== this.state.presence
            && this.state.presence.firebaseToken !== null
            && this.state.presence.geoLat !== null
            && this.state.presence.geoLng !== null
        ) {
            this._loadPresences();
            this._setPresence();
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

            }
        }
        */
    }

    render() {
        if (this.state.token === null) {
            return <Wait />;
        }

        if (this.state.selected === null) {
            return (
                <ShowUsers
                    layout={this.props.layout}
                    token={this.state.token}
                    onNotify={this.props.onNotify}
                    onSelectUser={(user) => {
                        this.setState({
                            selected: {
                                type: 'user',
                                user: user
                            }
                        });
                    }}
                    onSelectConversation={(conversation) => {
                        this.setState({
                            selected: {
                                type: 'conversation',
                                conversation: conversation
                            }
                        });
                    }}
                />
            );
        }

        if (this.state.selected.type === 'user') {
            return (
                <InitConversation
                    layout={this.props.layout}
                    token={this.state.token}
                    participants={[this.state.selected.user]}
                    onSend={(conversation) => {
                        this.setState({
                            selected: {
                                type: 'conversation',
                                conversation: conversation
                            }
                        });
                    }}
                    onBack={(conversation) => {
                        this.setState({
                            selected: null
                        });
                    }}
                />
            );
        }

        if (this.state.selected.type === 'conversation') {
            return (
                <KeepConversation
                    layout={this.props.layout}
                    token={this.state.token}
                    conversation={this.state.selected.conversation}
                    onBack={(conversation) => {
                        this.setState({
                            selected: null
                        });
                    }}
                />
            );
        }

        /*

        if (this.state.contacts === null
            || this.state.presences === null
            || this.state.conversations === null
        ) {
            return <Wait layout={this.props.layout}/>;
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
                            <Subheader>Conversaciones</Subheader>
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

                                return this._renderItem(
                                    conversation.id,
                                    contact.name,
                                    contact.picture,
                                    conversation.fresh
                                );
                            })}
                            <Subheader>Vecinos</Subheader>
                            {this.state.presences.map((presence) => {
                                return this._renderItem(
                                    presence.id,
                                    presence.name,
                                    presence.picture
                                );
                            })}
                        </List>
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
                            onCompose={(text) => {
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
        */
    }
    /*
    _prepareFirebase() {
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

    _loadPresences() {
        this._connectToServer
            .post('/chuchuchu/search-presences')
            .auth(this.state.token)
            .send({
                geoLat: this.state.presence.geoLat,
                geoLng: this.state.presence.geoLng,
            })
            .end((err, res) => {
                if (err) {
                    // TODO

                    return;
                }

                this.setState({
                    presences: res.body
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
    */
}

class ShowUsers extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
        token: React.PropTypes.string, // Required, but could be null
        // (message, finish)
        onNotify: React.PropTypes.func.isRequired,
        // (user)
        onSelectUser: React.PropTypes.func.isRequired,
        // (conversation)
        onSelectConversation: React.PropTypes.func.isRequired,
    };

    constructor(props) {
        super(props);

        this.state = {
            geo: false, // Whether user is geo located or not
            users: null,
            conversations: null
        };

        this._connectToServer = new ConnectToServer();
    }

    componentDidMount() {
        this._connectToServer
            .get('/chuchuchu/collect-conversations')
            .auth(this.props.token)
            .send()
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

    render() {
        if (this.state.geo === false) {
            return (
                <ShowResolveGeo
                    layout={<Center layout={this.props.layout}/>}
                    onSuccess={(lat, lng) => {
                        this.setState({
                            geo: true
                        }, () => {
                            this._connectToServer
                                .post('/chuchuchu/set-geo')
                                .auth(this.props.token)
                                .send({
                                    lat: lat,
                                    lng: lng
                                })
                                .end((err, res) => {
                                    if (err) {
                                        // TODO

                                        return;
                                    }

                                    this._connectToServer
                                        .get('/chuchuchu/find-users-by-closeness')
                                        .auth(this.props.token)
                                        .send()
                                        .end((err, res) => {
                                            if (err) {
                                                // TODO

                                                return;
                                            }

                                            this.setState({
                                                users: res.body
                                            });
                                        });
                                });
                        });
                    }}
                    onDenied={() => {
                        this.props.onNotify("Para conocer la gente que tienes cerca, debes permitir conocer tu ubicación");
                    }}
                    onError={() => {
                        this.props.onNotify("No se pudo obtener tu ubicación");
                    }}
                />
            );
        }

        if (this.state.users === null
        ) {
            return <Wait layout={this.props.layout}/>;
        }

        return (
            <this.props.layout.type {...this.props.layout.props}>
                <List>
                    {this.state.users.map((user) => {
                        return (
                            <ListItem
                                key={user.id}
                                primaryText={user.name}
                                leftAvatar={user.picture !== null
                                    ? <Avatar src={user.picture} />
                                    : <Avatar src={defaultPicture} />
                                }
                                onTouchTap={() => {
                                    this.props.onSelectUser(user)
                                }}
                                /*rightIcon={typeof fresh !== 'undefined' ? <CommunicationChatBubble /> : null}*/
                                /*style={{backgroundColor: this.state.conversation === id ? green50 : null}}*/
                            />
                        );
                    })}
                </List>
                <List>
                    {this.state.conversations.map((conversation) => {
                        return (
                            <ListItem
                                key={conversation.id}
                                primaryText={conversation.id}
                                onTouchTap={() => {
                                    this.props.onSelectConversation(conversation)
                                }}
                            />
                        );
                    })}
                </List>
            </this.props.layout.type>
        );
    }
}

class ShowResolveGeo extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
        // (latitude, longitude)
        onSuccess: React.PropTypes.func.isRequired,
        // ()
        onDenied: React.PropTypes.func.isRequired,
        // ()
        onError: React.PropTypes.func.isRequired,
    };

    constructor(props) {
        super(props);

        this.state = {
            busy: true
        };

        this._detectPosition = this._detectPosition.bind(this);
    }

    componentWillMount() {
        // Workaround for when geo position is not working
        this.props.onSuccess(36.1679225, -115.0713089);
        return;

        // TODO
        if (typeof navigator.geolocation === 'undefined') {

        }

        navigator.permissions
            // Is permission API implemented?
            .query({name:'geolocation'})
            .then(result => {
                switch(result.state) {
                    case 'granted':
                        this._detectPosition();

                        break;
                    case 'prompt':
                        this.setState({
                            busy: false
                        });

                        break;
                    case 'denied':
                        this.props.onDenied();

                        break;
                    default:
                        this.props.onDenied();
                }
            });
    }

    render() {
        if (this.state.busy === true) {
            return <Wait layout={this.props.layout}/>;
        }

        return (
            <Button
                layout={this.props.layout}
                label="Detectar mi ubicación"
                icon="my_location"
                onTouchTap={(finish) => {
                    this._detectPosition(finish);
                }}
            />
        );
    }

    _detectPosition(finish) {
        const success = (position) => {
            const success = () => {
                this.props.onSuccess(
                    position.coords.latitude,
                    position.coords.longitude
                )
            };

            if (typeof finish !== 'undefined') {
                finish(success);

                return;
            }

            success();
        };

        const error = (error) => {
            // See https://developer.mozilla.org/en-US/docs/Web/API/PositionError
            switch(error.code) {
                case 1:
                    if (typeof finish !== 'undefined') {
                        finish(this.props.onDenied);

                        return;
                    }

                    this.props.onDenied();

                    break;
                default:
                    if (typeof finish !== 'undefined') {
                        finish(this.props.onError);

                        return;
                    }

                    this.props.onError();
            }
        };

        navigator.geolocation.getCurrentPosition(success, error);
    }
}

class InitConversation extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
        token: React.PropTypes.string.isRequired,
        participants: React.PropTypes.array,
        // (conversation)
        onSend: React.PropTypes.func.isRequired,
        onBack: React.PropTypes.func.isRequired
    };

    constructor(props) {
        super(props);

        this.state = {
            message: null
        };

        this._connectToServer = new ConnectToServer();
    }

    render() {
        return (
            <Conversation
                layout={this.props.layout}
                participants={this.props.participants}
                messages={[this.state.message]}
                onSend={(message) => {
                    this.setState({
                        message: message
                    }, () => {
                        this._connectToServer
                            .post('/chuchuchu/init-conversation')
                            .auth(this.props.token)
                            .send({
                                'participants': this.props.participants,
                                'messages': [message]
                            })
                            .end((err, res) => {
                                if (err) {
                                    // TODO

                                    return;
                                }

                                this.props.onSend(res.body.conversation);
                            });
                    });
                }}
                onBack={this.props.onBack}
            />
        );
    }
}

class KeepConversation extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
        token: React.PropTypes.string.isRequired,
        conversation: React.PropTypes.object.isRequired,
        onBack: React.PropTypes.func.isRequired
    };

    constructor(props) {
        super(props);

        this.state = {
            participants: null,
            messages: null
        };

        this._connectToServer = new ConnectToServer();

        this._conversations = [];

        this._loadConversation = this._loadConversation.bind(this);
    }

    componentDidMount() {
        this._loadConversation();
    }

    componentWillReceiveProps(nextProps) {
        if (this.props.conversation !== nextProps.conversation) {
            this.setState({
                participants: null,
                messages: null
            }, () => {
                this._loadConversation()
            });
        }
    }

    componentDidUpdate(prevProps, prevState) {
        if (this.state.participants !== prevState.participants) {
            /* Upsert cache */

            let found = false;

            this._conversations = _.map(
                this._conversations,
                (conversation) => {
                    if (conversation === this.props.conversation) {
                        found = true;

                        return _.set(
                            conversation,
                            'participants',
                            this.state.participants
                        );
                    }

                    return conversation;
                }
            );

            if (found === false) {
                this._conversations.push({
                    id: this.props.conversation.id,
                    participants: this.state.participants,
                });
            }
        }

        if (this.state.messages !== prevState.messages) {
            /* Upsert cache */

            let found = false;

            this._conversations = _.map(
                this._conversations,
                (conversation) => {
                    if (conversation === this.props.conversation) {
                        found = true;

                        return _.set(
                            conversation,
                            'messages',
                            this.state.messages
                        );
                    }

                    return conversation;
                }
            );

            if (found === false) {
                this._conversations.push({
                    id: this.props.conversation.id,
                    messages: this.state.messages,
                });
            }
        }
    }

    render() {
        if (
            this.state.participants === null
            || this.state.messages === null
        ) {
            return <Wait />;
        }

        return (
            <Conversation
                layout={this.props.layout}
                participants={this.state.participants}
                messages={this.state.messages}
                onSend={(message) => {
                    this.setState({
                        messages: this.state.messages.concat([message])
                    }, this._connectToServer
                        .post('/chuchuchu/keep-conversation')
                        .auth(this.props.token)
                        .send({
                            'conversation': this.props.conversation.id,
                            'messages': [message]
                        })
                        .end((err, res) => {
                            if (err) {
                                // TODO

                                return;
                            }
                        }));
                }}
                onBack={this.props.onBack}
            />
        );
    }

    _loadConversation() {
        // Find conversation in cache
        const conversation = _.find(
            this._conversations,
            {id: this.props.conversation.id}
        );

        // Found it?
        if (typeof conversation !== 'undefined') {
            this.setState({
                participants: conversation.participants,
                messages: conversation.messages
            });

            return;
        }

        this._connectToServer
            .get('/chuchuchu/collect-messages/' + this.props.conversation.id)
            .auth(this.props.token)
            .send()
            .end((err, res) => {
                if (err) {
                    // TODO
                }

                this.setState({
                    messages: res.body,
                });
            });

        this._connectToServer
            .get('/chuchuchu/collect-participants/' + this.props.conversation.id)
            .auth(this.props.token)
            .send()
            .end((err, res) => {
                if (err) {
                    // TODO
                }

                this.setState({
                    participants: res.body,
                });
            });
    }
}

class Conversation extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
        participants: React.PropTypes.arrayOf(
            React.PropTypes.shape({
                name: React.PropTypes.string.isRequired,
                picture: React.PropTypes.string.isRequired
            })
        ),
        messages: React.PropTypes.arrayOf(
            React.PropTypes.shape({
                mime: React.PropTypes.string.isRequired,
                content: React.PropTypes.string.isRequired
            })
        ),
        onSend: React.PropTypes.func.isRequired,
        onBack: React.PropTypes.func.isRequired
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
        return (
            <this.props.layout.type
                {...this.props.layout.props}
                bar={
                    <span>
                        {this.props.participants.map((participant, i) => {
                            return participant.name;
                        }).join(', ')}
                    </span>
                }
                iconElementLeft={<IconButton onTouchTap={this.props.onBack}><LeftIcon/></IconButton>}
                style={{
                    ...this.props.layout.props.style,
                    display: "flex",
                    flexDirection: "column",
                    height: "100%",
                }}
            >
                <List
                    ref={(el) => {this._listEl = el}}
                    style={{
                        flexGrow: 1,
                        overflowY: "auto",
                        padding: "10px"
                    }}
                >
                    {this.props.messages.map((message, i) => {
                        return <ListItem
                            key={i}
                            primaryText={message.content}
                            innerDivStyle={{
                                paddingLeft: 0
                            }}
                        />
                    })}
                </List>
                <Composer
                    layout={<div style={{display: "flex", padding: "10px 10px 10px 0"}}/>}
                    onSend={(content) => {
                        this.props.onSend({
                            content: content,
                            mime: 't'
                        });
                    }}
                />
            </this.props.layout.type>
        );
    }
}

class Composer extends React.Component {
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
import React from 'react';
import ReactDOM from 'react-dom';
import * as firebase from 'firebase';
import Avatar from 'material-ui/Avatar';
import IconButton from 'material-ui/IconButton';
import SendIcon from 'material-ui/svg-icons/content/send';
import {List, ListItem} from 'material-ui/List';
import LeftIcon from 'material-ui/svg-icons/hardware/keyboard-arrow-left';
import TextField from 'material-ui/TextField';
import CommunicationChatBubble from 'material-ui/svg-icons/communication/chat-bubble';
// import Infinite from 'react-infinite';
import _ from 'lodash';

import ConnectToServer from '../ConnectToServer';
import Wait from '../Wait';

import defaultPicture from './picture.jpg';

export default class All extends React.Component {
    static propTypes = {
        query: React.PropTypes.object.isRequired,
        layout: React.PropTypes.element.isRequired,
        profile: React.PropTypes.object,
        // (onSuccess(token), onError)
        onBackAuth: React.PropTypes.func.isRequired,
        // ()
        onFrontAuth: React.PropTypes.func.isRequired,
        // (message, finish)
        onNotify: React.PropTypes.func.isRequired,
        // (status, response)
        onError: React.PropTypes.func.isRequired
    };

    constructor(props) {
        super(props);

        this.state = {
            //geo: false, // Whether user is geo located or not
            touches: null,
            selected: null
        };

        this._firebaseMessaging = firebase.messaging();
        this._connectToServer = new ConnectToServer();

        this._initFirebase = this._initFirebase.bind(this);
        this._resolveTouches = this._resolveTouches.bind(this);

        // this._prepareFirebase = this._prepareFirebase.bind(this);
        // this._prepareGeo = this._prepareGeo.bind(this);
        // this._loadContacts = this._loadContacts.bind(this);
        // this._loadPresences = this._loadPresences.bind(this);
        // this._loadConversations = this._loadConversations.bind(this);
    }

    componentDidMount() {
        if (this.props.profile !== null) {
            this._resolveTouches();
        }
    }

    componentDidUpdate(prevProps, prevState) {
        // Is authenticated?
        if (this.props.profile !== null) {
            // Was the update the authentication process?
            if (prevProps.profile === null) {
                // This will be execute once, just on authentication
                this._initFirebase();
                this._resolveTouches();
            }
        }
    }

    _initFirebase() {
        this._firebaseMessaging.requestPermission()
            .then(() => {
                this._firebaseMessaging.getToken()
                    .then((token) => {
                        this._connectToServer
                            .post('/firebase/update-profile')
                            .auth(this.props.profile.token)
                            .send({
                                token: token
                            })
                            .end((err, res) => {
                                if (err) {
                                    this.props.onError(
                                        err.status,
                                        JSON.parse(err.response.text)
                                    );

                                    return;
                                }
                            });
                    })
                    .catch(function (err) {
                        console.log('An error occurred while retrieving token. ', err);
                    });
            })
            .catch(function (err) {
                console.log('Unable to get permission to notify.', err);
            });

        this._firebaseMessaging.onMessage((payload) => {
            const message = JSON.parse(payload.data.message);

            // Is the conversation currently selected?
            if (
                this.state.selected !== null
                && this.state.selected.type === 'conversation'
                && this.state.selected.data.id === message.conversation
            ) {
                this.setState({
                    selected: {
                        ...this.state.selected,
                        data: _.update(
                            this.state.selected.data,
                            'messages',
                            (messages) => {
                                return messages.concat([message]);
                            }
                        )
                    }
                });
            } else {
                const conversations = _.map(
                    this.state.touches.conversations,
                    (conversation) => {
                        if (conversation.id === message.conversation) {
                            if (typeof conversation.fresh === 'undefined') {
                                conversation = _.set(
                                    conversation,
                                    'fresh',
                                    1
                                );
                            } else {
                                conversation = _.update(
                                    conversation,
                                    'fresh',
                                    (fresh) => {
                                        return fresh + 1;
                                    }
                                );
                            }

                            // Are messages already loaded?
                            if (typeof conversation.messages !== 'undefined') {
                                // Concat message
                                conversation = _.update(
                                    conversation,
                                    'messages',
                                    (messages) => {
                                        return messages.concat([message]);
                                    }
                                );
                            } else {
                                // If messages are not yet loaded from server
                                // then we can't do anything
                            }
                        }

                        return conversation
                    }
                );

                this.setState({
                    touches: {
                        ...this.state.touches,
                        conversations: conversations
                    }
                });
            }
        });
    }

    _resolveTouches() {
        this._connectToServer
            .get('/chuchuchu/me/resolve-touches')
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
                    touches: res.body
                });
            });
    }

    render() {
        if (this.state.profile === null) {
            return <Wait />;
        }

        /*
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
                                .auth(this.props.profile.token)
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
                                        .auth(this.props.profile.token)
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
        */

        if (this.state.selected === null) {
            return (
                <ListTouches
                    layout={this.props.layout}
                    touches={this.state.touches}
                    onSelectTouch={(touch) => {
                        this.setState({
                            selected: touch
                        }, () => {
                            if (touch.type === 'conversation') {
                                // Reset fresh
                                this.setState({
                                    touches: {
                                        ...this.state.touches,
                                        conversations: _.map(
                                            this.state.touches.conversations,
                                            (conversation) => {
                                                if (conversation.id === touch.data.id) {
                                                    conversation = _.set(
                                                        conversation,
                                                        'fresh',
                                                        0
                                                    );
                                                }

                                                return conversation;
                                            }
                                        )
                                    }
                                });

                                if (typeof touch.data.messages === 'undefined') {
                                    // Load messages on first time
                                    this._connectToServer
                                        .get('/chuchuchu/collect-messages/' + touch.data.id)
                                        .auth(this.props.profile.token)
                                        .end((err, res) => {
                                            if (err) {
                                                this.props.onError(
                                                    err.status,
                                                    JSON.parse(err.response.text)
                                                );

                                                return;
                                            }

                                            this.setState({
                                                selected: {
                                                    ...this.state.selected,
                                                    data: {
                                                        ...this.state.selected.data,
                                                        messages: res.body
                                                    }
                                                }
                                            });
                                        });
                                }
                            }
                        });
                    }}
                    onNotify={this.props.onNotify}
                />
            );
        }

        if (this.state.selected.type === 'user') {
            return (
                <ShowConversation
                    layout={this.props.layout}
                    receptors={[this.state.selected.data]}
                    messages={this.state.selected.data.messages}
                    onSend={(message) => {
                        this.setState({
                            selected: {
                                ...this.state.selected,
                                data: {
                                    ...this.state.selected.data,
                                    // This property is temporal, just to update ui
                                    messages: [message]
                                }
                            }
                        }, () => {
                            this._connectToServer
                                .post('/chuchuchu/init-conversation')
                                .auth(this.props.profile.token)
                                .send({
                                    receptors: [this.state.selected.data.id],
                                    messages: [message]
                                })
                                .end((err, res) => {
                                    if (err) {
                                        this.props.onError(
                                            err.status,
                                            JSON.parse(err.response.text)
                                        );

                                        return;
                                    }

                                    const conversation = {
                                        id: res.body.conversation,
                                        receptors: [this.state.selected.data],
                                        messages: [message]
                                    };

                                    this.setState({
                                        touches: {
                                            users: _.filter(
                                                this.state.touches.users,
                                                (user) => {
                                                    return user.id !== this.state.selected.data.id
                                                }
                                            ),
                                            conversations: _.concat(
                                                this.state.touches.conversations,
                                                [conversation]
                                            )
                                        },
                                        selected: {
                                            type: 'conversation',
                                            data: conversation
                                        }
                                    });
                                });
                        });
                    }}
                    onBack={() => {
                        this.setState({
                            selected: null
                        });
                    }}
                />
            );
        }

        if (this.state.selected.type === 'conversation') {
            return (
                <ShowConversation
                    layout={this.props.layout}
                    receptors={this.state.selected.data.receptors}
                    messages={this.state.selected.data.messages}
                    onSend={(message) => {
                        this.setState({
                            selected: {
                                ...this.state.selected,
                                data: {
                                    ...this.state.selected.data,
                                    messages: this.state.selected.data.messages.concat(message)
                                }
                            }
                        }, () => {
                            this._connectToServer
                                .post('/chuchuchu/keep-conversation')
                                .auth(this.props.profile.token)
                                .send({
                                    'conversation': this.state.selected.data.id,
                                    'messages': [message]
                                })
                                .end((err, res) => {
                                    if (err) {
                                        this.props.onError(
                                            err.status,
                                            JSON.parse(err.response.text)
                                        );

                                        return;
                                    }
                                })
                        });
                    }}
                    onBack={() => {
                        // Updated conversation
                        this.setState({
                            touches: {
                                ...this.state.touches,
                                conversations: _.map(
                                    this.state.touches.conversations,
                                    (conversation) => {
                                        if (conversation.id === this.state.selected.data.id) {
                                            return this.state.selected.data
                                        }

                                        return conversation;
                                    }
                                )
                            }
                        }, () => {
                            this.setState({
                                selected: null
                            });
                        });
                    }}
                />
            );
        }
    }

    /*
    _loadContacts() {
        this._connectToServer
            .get('/chuchuchu/collect-contacts')
            .auth(this.props.profile.token)
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
            .auth(this.props.profile.token)
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
            .auth(this.props.profile.token)
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

/*
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
*/

class ListTouches extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
        touches: React.PropTypes.object,
        // (touch)
        onSelectTouch: React.PropTypes.func.isRequired,
    };

    render() {
        if (this.props.touches === null) {
            return <Wait layout={this.props.layout}/>;
        }

        return (
            <this.props.layout.type {...this.props.layout.props}>
                <List>
                    {this.props.touches.conversations.map((conversation) => {
                        const receptor = _.head(conversation.receptors);

                        return (
                            <ListItem
                                key={'c' + conversation.id}
                                primaryText={receptor.name}
                                leftAvatar={receptor.picture !== null
                                    ? <Avatar src={receptor.picture} />
                                    : <Avatar src={defaultPicture} />
                                }
                                onTouchTap={() => {
                                    this.props.onSelectTouch({
                                        type: 'conversation',
                                        data: conversation
                                    });
                                }}
                                rightIcon={
                                    typeof conversation.fresh !== 'undefined'
                                    && conversation.fresh > 0
                                        ? <CommunicationChatBubble />
                                        : null
                                }
                            />
                        );
                    })}
                    {this.props.touches.users.map((user) => {
                        return (
                            <ListItem
                                key={'u' + user.id}
                                primaryText={user.name}
                                leftAvatar={user.picture !== null
                                    ? <Avatar src={user.picture} />
                                    : <Avatar src={defaultPicture} />
                                }
                                onTouchTap={() => {
                                    this.props.onSelectTouch({
                                        type: 'user',
                                        data: {
                                            ...user,
                                            messages: []
                                        }
                                    })
                                }}
                            />
                        );
                    })}
                </List>
            </this.props.layout.type>
        );
    }
}

class ShowConversation extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
        receptors: React.PropTypes.array.isRequired,
        messages: React.PropTypes.array,
        onSend: React.PropTypes.func.isRequired,
        onBack: React.PropTypes.func.isRequired
    };

    componentDidMount() {
        if (typeof this.props.messages !== 'undefined') {
            this._scrollToLast(this._listEl);
        }
    }

    componentDidUpdate(prevProps, prevState) {
        if (prevProps.messages !== this.props.messages) {
            this._scrollToLast(this._listEl);
        }
    }

    _scrollToLast(listEl) {
        /* Scroll to last */
        const list = ReactDOM.findDOMNode(listEl);
        const scrollHeight = list.scrollHeight;
        const height = list.clientHeight;
        const maxScrollTop = scrollHeight - height;

        list.scrollTop = maxScrollTop > 0 ? maxScrollTop : 0;
    }

    render() {
        if (typeof this.props.messages === 'undefined') {
            return <Wait />;
        }

        return (
            <this.props.layout.type
                {...this.props.layout.props}
                bar={
                    <span>
                        {this.props.receptors.map((receptor, i) => {
                            return receptor.name;
                        }).join(', ')}
                    </span>
                }
                iconElementLeft={<IconButton onTouchTap={this.props.onBack}><LeftIcon/></IconButton>}
                style={{
                    ...this.props.layout.props.style,
                    display: "flex",
                    flexDirection: "column",
                }}
            >
                <List
                    ref={(el) => {this._listEl = el}}
                    style={{
                        flexGrow: 1,
                        overflowY: "auto",
                        overflowX: "hidden",
                        padding: "10px",
                        height: "100%"
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
                            e.preventDefault();

                            this._handleSend();
                        }
                    }}
                />
                <div style={{marginLeft: "10px"}}>
                    <IconButton
                        title="Enviar mensaje"
                        onTouchTap={this._handleSend}
                    ><SendIcon/></IconButton>
                </div>
            </this.props.layout.type>
        );
    }

    _handleSend(finish) {
        const text = this.state.text;

        this.setState(
            {
                text: ''
            },
            this.props.onSend(text)
        );
    }
}
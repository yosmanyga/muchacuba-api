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
// import Center from '../Center';

import defaultPicture from './picture.jpg';

export default class All extends React.Component {
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
            //geo: false, // Whether user is geo located or not
            touches: null,
            selected: null
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
        this.setState({
            token: 'eyJhbGciOiJSUzI1NiIsImtpZCI6IjNiYmJiM2VlYTU0NzU1YmJkNWFkM2FlOWY5OWQyNGY0N2IyYTdmODIifQ.eyJpc3MiOiJodHRwczovL3NlY3VyZXRva2VuLmdvb2dsZS5jb20vY3ViYWxpZGVyLW11Y2hhY3ViYSIsIm5hbWUiOiJGaXJlZm94IFVzZXIiLCJwaWN0dXJlIjoiaHR0cHM6Ly9zY29udGVudC54eC5mYmNkbi5uZXQvdi90MS4wLTEvczEwMHgxMDAvMTM3OTg0MV8xMDE1MDAwNDU1MjgwMTkwMV80NjkyMDk0OTY4OTUyMjE3NTdfbi5qcGc_b2g9YjhkMWFjOWQwYjk3MmUzMDljNTM3MmQ2MGNkMjhmODMmb2U9NTk2M0U3OTciLCJhdWQiOiJjdWJhbGlkZXItbXVjaGFjdWJhIiwiYXV0aF90aW1lIjoxNDkxNDMxNzc5LCJ1c2VyX2lkIjoiMXh1clFlM0hjVlR6Zlp0Z0RYT2NmZTdwaFhKMiIsInN1YiI6IjF4dXJRZTNIY1ZUemZadGdEWE9jZmU3cGhYSjIiLCJpYXQiOjE0OTE0MzE3ODAsImV4cCI6MTQ5MTQzNTM4MCwiZW1haWwiOiJmaXJlZm94X2Npc2NndXRfdXNlckB0ZmJudy5uZXQiLCJlbWFpbF92ZXJpZmllZCI6ZmFsc2UsImZpcmViYXNlIjp7ImlkZW50aXRpZXMiOnsiZmFjZWJvb2suY29tIjpbIjQxMDkyMjkxMjYwMzIxNyJdLCJlbWFpbCI6WyJmaXJlZm94X2Npc2NndXRfdXNlckB0ZmJudy5uZXQiXX0sInNpZ25faW5fcHJvdmlkZXIiOiJmYWNlYm9vay5jb20ifX0.eBR3B4dOZz7BM8a9AcqOkULNEgtsa644IduH4r7RX-pQZg2CVC_jYy4-quROh9FFsy8Hvc1CKNcmLnRaEwy9oPwt0iL0Knyoavk1gFcSbSHqp0_TBjjH6XMX1AcwUGiMA8lfTM3-6UE77dPP_HEa5EAw6vK7qc3mKNcdo_bSptIUc10O81F1NtCShW8swkSceOGOEABEmGAwWQvTWvM0eX5Zjk9Efs7pOh1xk8gA1yg7I6OlliB4uUbeXzNncc2viVv_r4YS_Gmafgf5aDD_m2EU2jFAnqV8nrVCFekWJ7t14I912I2tnt4pb7R8UNe9dJ0UxWOigIg9ot-kMeZLtg'
        });

        /*
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
        */
    }

    componentDidUpdate(prevProps, prevState) {
        // Is authenticated?
        if (this.state.token !== null) {
            // Was authentication process?
            if (prevState.token === null) {
                /* This will be executed just at the beginning */

                /*
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
                            .catch(function (err) {
                                console.log('An error occurred while retrieving token. ', err);
                            });
                    })
                    .catch(function (err) {
                        console.log('Unable to get permission to notify.', err);
                    });
                */

                this._connectToServer
                    .get('/chuchuchu/me/resolve-touches')
                    .auth(this.state.token)
                    .send()
                    .end((err, res) => {
                        if (err) {
                            // TODO

                            return;
                        }

                        this.setState({
                            touches: res.body
                        });
                    });
            }

            // Is in list?
            if (this.state.selected === null) {
                // Came from a conversation?
                if (
                    prevState.selected !== null
                    && prevState.selected.type === 'conversation'
                ) {
                    // Update conversation on touches, using selected
                    // conversation, because it contains new messages
                    this.setState({
                        touches: {
                            ...this.state.touches,
                            conversations: _.map(
                                this.state.touches.conversations,
                                (conversation) => {
                                    if (conversation.id === prevState.selected.conversation.id) {
                                        console.log('actualice conversation en touches');

                                        return prevState.selected.conversation
                                    }

                                    return conversation;
                                }
                            )
                        }
                    });
                }
            } else {
                // Is in a conversation?
                if (this.state.selected.type === 'conversation') {
                    console.log('selected conversation');

                    // Came from a new conversation (init to keep)?
                    if (
                        prevState.selected !== null
                        && prevState.selected.type === 'user'
                    ) {
                        // Delete user from touches
                        this.setState({
                            touches: {
                                ...this.state.touches,
                                users: _.filter(
                                    this.state.touches.users,
                                    (user) => {
                                        return user !== prevState.selected.user
                                    }
                                )
                            }
                        }, () => {console.log(this.state.touches)});
                    }

                    if (typeof this.state.selected.conversation.messages === 'undefined') {
                        console.log('conversation messages is undefined');

                        this._connectToServer
                            .get('/chuchuchu/collect-messages/' + this.state.selected.conversation.id)
                            .auth(this.state.token)
                            .end((err, res) => {
                                if (err) {
                                    // TODO

                                    return;
                                }

                                this.setState({
                                    selected: {
                                        ...this.state.selected,
                                        conversation: {
                                            ...this.state.selected.conversation,
                                            messages: res.body
                                        }
                                    }
                                });
                            });
                    }
                } else if (
                    this.state.selected.type === 'user'
                    && typeof this.state.selected.user.messages === 'undefined'
                ) {
                    console.log('user messages is undefined');

                    this.setState({
                        selected: {
                            ...this.state.selected,
                            user: {
                                ...this.state.selected.user,
                                messages: []
                            }
                        }
                    });
                }
            }
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
        */

        if (this.state.selected === null) {
            return (
                <ListTouches
                    layout={this.props.layout}
                    touches={this.state.touches}
                    onSelectTouch={(touch) => {
                        this.setState({
                            selected: touch
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
                    receptors={[this.state.selected.user]}
                    messages={this.state.selected.user.messages}
                    onSend={(message) => {
                        this.setState({
                            selected: {
                                ...this.state.selected,
                                user: {
                                    ...this.state.selected.user,
                                    // This property is temporal, just to update ui
                                    messages: [message]
                                }
                            }
                        }, () => {
                            this._connectToServer
                                .post('/chuchuchu/init-conversation')
                                .auth(this.state.token)
                                .send({
                                    receptors: [this.state.selected.user.id],
                                    messages: [message]
                                })
                                .end((err, res) => {
                                    if (err) {
                                        // TODO

                                        return;
                                    }

                                    this.setState({
                                        selected: {
                                            type: 'conversation',
                                            conversation: {
                                                id: res.body.conversation,
                                                receptors: [this.state.selected.user],
                                                messages: [message]
                                            }
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
                    receptors={this.state.selected.conversation.receptors}
                    messages={this.state.selected.conversation.messages}
                    onSend={(message) => {
                        this.setState({
                            selected: {
                                ...this.state.selected,
                                conversation: {
                                    ...this.state.selected.conversation,
                                    messages: this.state.selected.conversation.messages.concat(message)
                                }
                            }
                        }, () => {
                            this._connectToServer
                                .post('/chuchuchu/keep-conversation')
                                .auth(this.state.token)
                                .send({
                                    'conversation': this.state.selected.conversation.id,
                                    'messages': [message]
                                })
                                .end((err, res) => {
                                    if (err) {
                                        // TODO

                                        return;
                                    }
                                })
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
                                                conversation.receptors,
                                                (receptor) => {
                                                    return receptor === contact.id;
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
                                        conversation: conversation
                                    })
                                }}
                                /*rightIcon={typeof fresh !== 'undefined' ? <CommunicationChatBubble /> : null}*/
                                /*style={{backgroundColor: this.state.conversation === id ? green50 : null}}*/
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
                                        user: user
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
                            e.preventDefault();

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
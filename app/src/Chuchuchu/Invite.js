import React from 'react';
import TextField from 'material-ui/TextField';
import FlatButton from 'material-ui/FlatButton';

export default class Invite extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
        connectToServer: React.PropTypes.object.isRequired,
        firebaseMessaging: React.PropTypes.object.isRequired,
        onRegister: React.PropTypes.func.isRequired
    };

    render() {
        return (
            <RegisterUserController
                layout={this.props.layout}
                connectToServer={this.props.connectToServer}
                firebaseMessaging={this.props.firebaseMessaging}
                onRegister={this.props.onRegister}
            />
        );
    }
}

class RegisterUserController extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
        connectToServer: React.PropTypes.object.isRequired,
        firebaseMessaging: React.PropTypes.object.isRequired,
        onRegister: React.PropTypes.func.isRequired
    };

    constructor(props) {
        super(props);

        this.state = {
            user: null
        };
    }

    render() {
        return (
            <RegisterUserView
                layout={this.props.layout}
                onEmailChange={(email) => {
                    this.setState({
                        user: {
                            ...this.state.user,
                            email: email
                        }
                    });
                }}
                onRegister={() => {
                    this.props.connectToServer
                        .post('/register-user')
                        .send(this.state.user)
                        .end(function(err, res) {
                            if (err) {
                                // TODO

                                return;
                            }

                            this.props.onRegister(res.body);
                        }.bind(this));
                }}
            />
        );
    }

    componentDidMount() {

    }
}


import React from 'react';
import injectTapEventPlugin from 'react-tap-event-plugin';
import History from 'history/createHashHistory';
import {Wait} from './Base/UI';
import {initialize, verify} from './Base/Firebase';
import {route} from './Base/Route';

import InternautaFront from "./Internauta/Front";

import {initializeUser} from './Api';

injectTapEventPlugin();

const history = new History();

export default class Front extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            userVerified: false,
            user: null,
            location: null
        };
    }

    componentWillMount() {
        initialize({
            apiKey: "AIzaSyApFrRpHVKRK1pvBchd0rcC_ycUa0H-5AU",
            authDomain: "cubalider-muchacuba.firebaseapp.com",
            databaseURL: "https://cubalider-muchacuba.firebaseio.com",
            projectId: "cubalider-muchacuba",
            storageBucket: "cubalider-muchacuba.appspot.com",
            messagingSenderId: "43324202525"
        });

        /* Authentication */

        // Need to verify user to decide routing

        verify(
            () => {
                initializeUser((user) => {
                    this.setState({
                        userVerified: true,
                        user: user
                    });
                });
            },
            () => {
                this.setState({
                    userVerified: true
                });
            }
        );

        // firebase.auth().onAuthStateChanged((user) => {
        //     if (user) {
        //
        //     }
        // });

        // var connectedRef = firebase.database().ref(".info/connected");
        // connectedRef.on("value", function(snap) {
        //     if (snap.val() === true) {
        //         alert("connected");
        //     } else {
        //         alert("not connected");
        //     }
        // });

        /* Resolution */

        history.listen((location) => {
            this.setState({
                location: location
            });
        });

        this.setState({
            location: history.location
        });
    }

    render() {
        if (
            this.state.location === null
            || this.state.userVerified === false
        ) {
            return (
                <Wait/>
            );
        }

        return route(
            this.state.location.pathname,
            [
                {
                    url: '/internauta',
                    element: () => {
                        return <InternautaFront
                            url={this.state.location.pathname.replace('/internauta', '')}
                            onNavigate={(url, finish) => this._handleNavigate(
                                '/internauta' + url,
                                finish
                            )}
                        />
                    },
                    def: true
                }
            ]
        );
    }

    _handleNavigate = (url, finish = null) => {
        if (url === this.state.location.pathname) {
            return;
        }

        history.push(url);

        if (finish) {
            finish();
        }
    };
}

import React from 'react';

import ResolveElement from '../ResolveElement';

import All from './All';
// import RegisterUser from './RegisterUser';
// import ViewConversation from './ViewConversation';

export default class Front extends React.Component {
    static propTypes = {
        url: React.PropTypes.string.isRequired,
        query: React.PropTypes.object.isRequired,
        layout: React.PropTypes.element.isRequired,
        // (onSuccess, onError)
        onBackAuth: React.PropTypes.func.isRequired,
        // ()
        onFrontAuth: React.PropTypes.func.isRequired,
        // (url)
        onNavigate: React.PropTypes.func.isRequired,
        // (message, finish)
        onNotify: React.PropTypes.func.isRequired,
        // (status, response)
        onError: React.PropTypes.func.isRequired,
    };

    constructor(props) {
        super(props);

        this.state = {
            profile: null
        };

        this._resolveElement = new ResolveElement();
    }

    componentDidMount() {
        this.props.onBackAuth(
            (profile) => {
                if (profile.token === 'null') {
                    this.props.onFrontAuth();

                    return;
                }

                this.setState({
                    profile: profile
                });
            },
            () => {
                this.props.onFrontAuth();
            }
        );
    }

    render() {
        const layout = <Layout
            layout={this.props.layout}
            onNavigate={this.props.onNavigate}
        />;

        return this._resolveElement.resolve(
            this.props.url,
            [
                {
                    url: '/',
                    element: <All
                        query={this.props.query}
                        layout={layout}
                        profile={this.state.profile}
                        onBackAuth={this.props.onBackAuth}
                        onFrontAuth={this.props.onFrontAuth}
                        onNotify={this.props.onNotify}
                        onError={this.props.onError}
                    />,
                    def: true
                },
            ]
        );
    }
}

class Layout extends React.Component {
    static propTypes = {
        layout: React.PropTypes.element.isRequired,
        onNavigate: React.PropTypes.func.isRequired,
    };

    render() {
        return (
            <this.props.layout.type
                {...this.props.layout.props}
                title="Chuchuchu"
                onTitleTouchTap={() => {this.props.onNavigate('/')}}
                style={{
                    ...this.props.layout.props.style,
                    height: "100%"
                }}
                {...this.props}
            >
                {this.props.children}
            </this.props.layout.type>
        );
    }
}

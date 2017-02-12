import React from 'react';

import ResolveElement from '../ResolveElement';

import Welcome from './Welcome';
import ListLogs from './ListLogs';
// import RegisterUser from './RegisterUser';
// import ViewConversation from './ViewConversation';

export default class Front extends React.Component {
    static propTypes = {
        url: React.PropTypes.string.isRequired,
        query: React.PropTypes.object.isRequired,
        layout: React.PropTypes.element.isRequired,
        // (onSuccess, backUrl)
        onUnderAuth: React.PropTypes.func.isRequired,
        // (backUrl)
        onUnauthorized: React.PropTypes.func.isRequired,
        // (url)
        onNavigate: React.PropTypes.func.isRequired,
        // (message, finish)
        onNotify: React.PropTypes.func.isRequired
    };

    constructor(props) {
        super(props);

        this._resolveElement = new ResolveElement();
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
                    'url': '/welcome',
                    'element': <Welcome
                        layout={layout}
                    />,
                    'def': true
                },
                {
                    'url': '/list-logs',
                    'element': <ListLogs
                        layout={layout}
                    />
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
                title="Internauta"
                onTitleTouchTap={() => {this.props.onNavigate('/')}}
                style={{
                    ...this.props.layout.props.style,
                    height: "100%"
                }}
            >
                {this.props.children}
            </this.props.layout.type>
        );
    }
}

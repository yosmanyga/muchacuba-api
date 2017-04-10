import React from 'react';
import Drawer from 'material-ui/Drawer';
import FontIcon from 'material-ui/FontIcon';
import MenuItem from 'material-ui/MenuItem';

import ResolveElement from '../ResolveElement';

import ListPhones from './ListPhones';
import ListCalls from './ListCalls';

export default class Front extends React.Component {
    static propTypes = {
        url: React.PropTypes.string.isRequired,
        layout: React.PropTypes.element.isRequired,
        // (onSuccess, onError)
        onBackAuth: React.PropTypes.func.isRequired,
        // ()
        onFrontAuth: React.PropTypes.func.isRequired,
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
            url={this.props.url}
            layout={this.props.layout}
            onBackAuth={this.props.onBackAuth}
            onFrontAuth={this.props.onFrontAuth}
            onNavigate={this.props.onNavigate}
            onNotify={this.props.onNotify}
        />;

        return this._resolveElement.resolve(
            this.props.url,
            [
                {
                    'url': '/list-phones',
                    'element': <ListPhones
                        layout={layout}
                        onBackAuth={this.props.onBackAuth}
                        onFrontAuth={this.props.onFrontAuth}
                        onNotify={this.props.onNotify}
                    />,
                    'def': true
                },
                {
                    'url': '/list-calls',
                    'element': <ListCalls
                        layout={layout}
                        onBackAuth={this.props.onBackAuth}
                        onFrontAuth={this.props.onFrontAuth}
                        onNotify={this.props.onNotify}
                    />
                }
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
                title="Aloleiro"
                onTitleTouchTap={() => {this.props.onNavigate('/')}}
                drawer={
                    <Drawer>
                        <MenuItem
                            onTouchTap={() => {this.props.onNavigate('/list-calls')}}
                            leftIcon={<FontIcon className="material-icons">phone_in_talk</FontIcon>}
                        >
                            Llamadas
                        </MenuItem>
                        <MenuItem
                            onTouchTap={() => {this.props.onNavigate('/list-phones')}}
                            leftIcon={<FontIcon className="material-icons">picture_in_picture</FontIcon>}
                        >
                            Cabinas
                        </MenuItem>
                    </Drawer>
                }
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

import React from 'react';
import PropTypes from 'prop-types';
import AppBar from 'material-ui/AppBar';
import Snackbar from 'material-ui/Snackbar';
import DocumentTitle from 'react-document-title';

export default class BackendLayout extends React.Component {
    static propTypes = {
        title: PropTypes.string,
        appBar: PropTypes.element,
        bar: PropTypes.node,
        drawer: PropTypes.element,
        iconElementLeft: PropTypes.element,
        iconElementRight: PropTypes.element,
        onTitleTouchTap: PropTypes.func,
        notification: PropTypes.string,
        onNotificationClose: PropTypes.func,
        style: PropTypes.object
    };

    constructor(props) {
        super(props);

        this.state = {
            drawer: false
        };
    }

    // shouldComponentUpdate(nextProps, nextState) {
    //     if (
    //         nextProps.notification !== this.props.notification
    //         || nextProps.children !== this.props.children
    //         || nextState !== this.state
    //     ) {
    //         return true;
    //     }
    //
    //     // Ignore any change on anonymous functions
    //
    //     return false;
    // }

    render() {
        return (
            <DocumentTitle title={this.props.title ? this.props.title : ''}>
                <div style={{height: "100%"}}>
                    {typeof this.props.appBar !== 'undefined'
                        ? this.props.appBar
                        : <AppBar
                            title={this.props.bar}
                            onTitleTouchTap={this.props.onTitleTouchTap}
                            iconElementLeft={this.props.iconElementLeft}
                            iconElementRight={this.props.iconElementRight}
                            onLeftIconButtonTouchTap={() => {
                                this.setState({drawer: true});
                            }}
                        />
                    }

                    <div style={{
                        height: "calc(100% - 64px)",
                        ...this.props.style
                    }}>
                        {this.props.children}
                    </div>
                    {typeof this.props.drawer !== 'undefined'
                        ? <this.props.drawer.type
                            {...this.props.drawer.props}
                            docked={false}
                            open={this.state.drawer}
                            onRequestChange={(open) => this.setState({drawer: open})}
                        >
                            {this.props.drawer.props.children}
                        </this.props.drawer.type>
                        : null
                    }
                    {this.props.notification !== null ? <Snackbar
                        open={true}
                        message={this.props.notification}
                        autoHideDuration={4000}
                        onRequestClose={this.props.onNotificationClose}
                    /> : null}
                </div>
            </DocumentTitle>
        );
    }
}
import React from 'react';
import PropTypes from 'prop-types';

import {route} from '../Base/Route';

import BackendLayout from '../BackendLayout';
import Welcome from './Welcome';
import ListLogs from './ListLogs';

export default class Front extends React.Component {
    static propTypes = {
        url: PropTypes.string.isRequired,
        // (url)
        onNavigate: PropTypes.func.isRequired,
    };

    render() {
        const layout = <BackendLayout
            title="Internauta"
            notification={null}
        />;

        return route(
            this.props.url,
            [
                {
                    url: '/welcome',
                    element: () => {
                        return <Welcome
                            layout={layout}
                        />;
                    },
                    enabled: true,
                    def: true
                },
                {
                    url: '/list-logs',
                    element: () => {
                        return <ListLogs
                            layout={layout}
                        />;
                    },
                    enabled: true
                },
            ]
        );
    }
}


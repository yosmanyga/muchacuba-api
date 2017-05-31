import React from 'react';
import ReactDOM from 'react-dom';
import registerServiceWorker from './registerServiceWorker';
import './index.css';
import getMuiTheme from 'material-ui/styles/getMuiTheme';
import {cyan800} from 'material-ui/styles/colors';
import MuiThemeProvider from 'material-ui/styles/MuiThemeProvider';

import Front from './Front';

const muiTheme = getMuiTheme({
    palette: {
        textColor: cyan800,
    }
});

ReactDOM.render(
    <MuiThemeProvider
        muiTheme={muiTheme}
    >
        <Front />
    </MuiThemeProvider>,
    document.getElementById('root')
);
registerServiceWorker();

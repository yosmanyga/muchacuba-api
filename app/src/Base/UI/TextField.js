import React from 'react';
import {default as BaseTextField} from 'material-ui/TextField';

export default class TextField extends React.Component {
    shouldComponentUpdate(nextProps, nextState) {
        if (nextState !== this.state) {
            return true;
        }

        if (
            nextProps.value !== this.props.value
            || nextProps.errorText !== this.props.errorText
            || nextProps.disabled !== this.props.disabled
        ) {
            return true;
        }

        // Ignore any change on props.onChange (anonymous functions)

        return false;
    }

    render() {
        return <BaseTextField {...this.props}/>
    }
}

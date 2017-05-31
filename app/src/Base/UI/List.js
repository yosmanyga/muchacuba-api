import React from 'react';
import {List as BaseList} from 'material-ui/List';

export default class List extends React.Component {
    render() {
        return <BaseList {...this.props}/>
    }
}
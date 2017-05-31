import React from 'react';

import Button from './Button';

export default class BackButton extends React.Component {
    render() {
        return <Button
            {...this.props}
            icon="arrow_back"
        />
    }
}
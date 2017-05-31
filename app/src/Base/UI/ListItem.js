import React from 'react';
import PropTypes from 'prop-types';
import IconButton from 'material-ui/IconButton';
import IconMenu from 'material-ui/IconMenu';
import {ListItem as BaseListItem} from 'material-ui/List';
import MenuItem from 'material-ui/MenuItem';
import MoreIcon from 'material-ui/svg-icons/navigation/more-vert';

export default class ListItem extends React.Component {
    static propTypes = {
        rightIcons: PropTypes.array
    };

    render() {
        const {rightIcons, ...props} = this.props;

        return <BaseListItem
            {...props}
            rightIconButton={typeof rightIcons !== 'undefined' ? <IconMenu
                iconButtonElement={<IconButton
                    touch={true}
                    tooltip="Acciones"
                    tooltipPosition="bottom-left"
                >
                    <MoreIcon/>
                </IconButton>}
            >
                {rightIcons.map((rightIcon, i) => {
                    return <MenuItem
                        key={i}
                        primaryText={rightIcon.label}
                        leftIcon={rightIcon.icon}
                        onTouchTap={rightIcon.onTouchTap}
                    />;
                })}
            </IconMenu> : null}
        />
    }
}
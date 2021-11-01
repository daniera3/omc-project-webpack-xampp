import React from "react";
import Text from "../../components/Text";
import Spinner from "../../components/Spinner";
import CheckBox from "../../components/CheckBox";
import IconButton from "@material-ui/core/IconButton";
import FavoriteIcon from "@material-ui/icons/Favorite";
import {changeFavorite} from "../../actions/favoriteActions";
import * as S from "./style";
import PropTypes from 'prop-types';


class UserList extends React.Component {
    constructor(props) {
        super(props);
        this.state = {hoveredUserId: undefined, selectCountrys: [], selectedUsers: this.props.users, countrys: []};
        this.handleClick = this.handleClick.bind(this);
        this.handleMouseEnter = this.handleMouseEnter.bind(this);
        this.handleMouseLeave = this.handleMouseLeave.bind(this);
        this.handleCheck = this.handleCheck.bind(this);
        this.setHoveredUserId = this.setHoveredUserId.bind(this);
        this.setSelectCountrys = this.setSelectCountrys.bind(this);
        this.setSelectedUsers = this.setSelectedUsers.bind(this);

    }

    setHoveredUserId(hoveredUserId) {
        this.setState({hoveredUserId})
    }

    setSelectCountrys(selectCountrys) {
        this.setState({selectCountrys})
    }

    setSelectedUsers(selectedUsers) {
        this.setState({selectedUsers})
    }


    handleMouseEnter = (index) => {
        this.setHoveredUserId(index);
    };

    handleMouseLeave = () => {
        this.setHoveredUserId();
    };

    handleCheck = country => {
        if (this.state.selectCountrys.indexOf(country) !== -1)
            this.setSelectCountrys(this.state.selectCountrys.filter(selectCountry => selectCountry !== country));
        else
            this.setSelectCountrys([...this.state.selectCountrys, country]);
    }

    handleClick = (index) => {
        changeFavorite(this.state.selectedUsers[index]);
    }


    componentDidUpdate(prevProp, prevState) {
        if (prevProp.users !== this.props.users || prevState.selectCountrys !== this.state.selectCountrys) {
            if (this.props.users && !this.state.selectCountrys.length) {
                this.setSelectedUsers(this.props.users);
            } else {
                this.setSelectedUsers(this.props.users.filter(user => this.state.selectCountrys.includes(user.location.country)))
            }
            this.setState({countrys: Array.from(new Set(this.props.users.map(user => user.location.country)))});
        }
    }

    render() {
        return (
            <S.UserList>
                <S.Filters>
                    {/* <CheckBox value="BR" label="Brazil" />
        <CheckBox value="AU" label="Australia" />
        <CheckBox value="CA" label="Canada" />
        <CheckBox value="DE" label="Germany" /> */}
                    {this.state.countrys.map((country, index) => {
                        return (<CheckBox key={index} value={country} label={country} onChange={this.handleCheck}/>)
                    })
                    }
                </S.Filters>
                <S.List onScroll={this.props.handleScroll}>
                    {this.state.selectedUsers.map((user, index) => {
                        return (
                            <S.User
                                key={index}
                                onMouseEnter={() => this.handleMouseEnter(index)}
                                onMouseLeave={this.handleMouseLeave}
                            >
                                <S.UserPicture src={user.picture.large} alt=""/>
                                <S.UserInfo>
                                    <Text size="22px" bold>
                                        {user.name.title} {user.name.first} {user.name.last}
                                    </Text>
                                    <Text size="14px">{user.email}</Text>
                                    <Text size="14px">
                                        {user.location.street.number} {user.location.street.name}
                                    </Text>
                                    <Text size="14px">
                                        {user.location.city} {user.location.country}
                                    </Text>
                                </S.UserInfo>
                                <S.IconButtonWrapper onClick={() => {
                                    this.handleClick(index)
                                }}
                                                     isVisible={index === this.state.hoveredUserId || (this.props.favorites  && this.props.favorites.indexOf(user) !== -1)}>
                                    <IconButton>
                                        <FavoriteIcon color="error"/>
                                    </IconButton>
                                </S.IconButtonWrapper>
                            </S.User>
                        );
                    })}
                    {this.props.isLoading && (
                        <S.SpinnerWrapper>
                            <Spinner color="primary" size="45px" thickness={6} variant="indeterminate"/>
                        </S.SpinnerWrapper>
                    )}
                </S.List>
            </S.UserList>
        );
    }
};

UserList.propTypes = {
    isLoading: PropTypes.bool.isRequired,
    handleScroll: PropTypes.func,
    users: PropTypes.array.isRequired,
    favorites: PropTypes.array,
};


export default UserList;

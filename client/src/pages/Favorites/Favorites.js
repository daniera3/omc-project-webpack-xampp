import React from "react";
import Text from "../../components/Text";
import UserList from "../../components/UserList";
import {favoriteStore} from "../../stores";


import * as S from "./style";

class Favorites extends React.Component {

    constructor(props) {
        super(props);
        console.log(1.15);
        this.state = {favorites: favoriteStore.getFavoritesArray()}
        this.onChange = this.onChange.bind(this);
    }


    onChange = () => {
        this.setState({favorites: favoriteStore.getFavoritesArray()});
    }

    componentDidMount() {

        favoriteStore.addChangeListener(this.onChange);
    }

    componentWillUnmount() {
        favoriteStore.removeChangeListener(this.onChange);
    }

    render() {
        return (
            <S.Home>
                <S.Content>
                    <S.Header>
                        <Text size="64px" bold>
                            Favorites
                        </Text>
                    </S.Header>
                    <UserList users={this.state.favorites || []} isLoading={false}
                              favorites={this.state.favorites || []}/>
                </S.Content>
            </S.Home>
        );
    }
};

export default Favorites;

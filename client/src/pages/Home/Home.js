import React from "react";
import Text from "../../components/Text";
import UserList from "../../components/UserList";
import {favoriteStore} from "../../stores";
import {PeopleFetch} from "../../hooks/usePeopleFetch";
import * as S from "./style";


class Home extends React.Component {

    constructor() {
        super();
        this.state = {favorites: favoriteStore.getFavoritesArray(), users: []};
        this.onChange = this.onChange.bind(this);
    }


    onChange = () => {
        this.setState({favorites: favoriteStore.getFavoritesArray()});
    }

    componentDidMount() {
        PeopleFetch().then(res => {

            this.setState({users: res.data.results});
        })
        favoriteStore.addChangeListener(this.onChange);
    }

    componentWillUnmount() {
        favoriteStore.removeChangeListener(this.onChange);
    }


    handleScroll(e) {
        if (e.target.scrollHeight - e.target.scrollTop === e.target.clientHeight) {
            PeopleFetch().then(res => {
                this.setState({users: [...this.state.users, ...res.data.results]})
            });
        }
    }

    render() {
        return (
            <S.Home>
                <S.Content>
                    <S.Header>
                        <Text size="64px" bold>
                            PplFinder
                        </Text>
                    </S.Header>
                    <UserList users={this.state.users} isLoading={!this.state.users.length}
                              favorites={this.state.favorites} handleScroll={this.handleScroll.bind(this)}/>
                </S.Content>
            </S.Home>
        );
    }
};

export default Home;


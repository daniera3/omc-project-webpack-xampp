/* eslint-disable react-hooks/exhaustive-deps */
import React from "react";
import Select from "../../components/Select";
import Text from "../../components/Text";
import * as S from "./style";
import Button from "../../components/Button";
import {getAllUsers, updateRole} from "../../hooks/useUserFetch";


class UserPanel extends React.Component {

    constructor(props) {
        super(props);

        this.state = {massage: "", userList: undefined, parmission: "", selectedUser: ""};

        this.permissionsOptions = [{'value': 'admin', 'label': 'Admin'}, {'value': 'user', 'label': 'Regular'}];
        this.handleClick = this.handleClick.bind(this);
    }

    componentDidMount() {
        getAllUsers(this.props.token).then(response => {
                if (response.status === 200) {
                    let data = response.data;
                    let userList = data.map(e => {
                        e['value'] = e['id'];
                        e['label'] = e['username']
                        return e;

                    })
                    this.setState({
                        massage: '', userList
                    });
                } else {
                    this.setState({massage: response.data, userList: undefined});
                }
            }
        ).catch(() => {
            this.setState({massage: '', userList: undefined});
        })
    }


    componentDidUpdate(_prevProp, prevState) {
        if (this.state.selectedUser !== prevState.selectedUser) {
            if (this.state.userList) {
                let temp = this.state.userList.filter((user) => user['id'] === this.state.selectedUser)[0];
                if (temp) {
                    this.setState({parmission: temp['role']});
                }
            }
        }
    }


    handleClick() {
        const data = {'id': this.state.selectedUser, 'role': this.state.parmission, 'token': this.props.token}
        updateRole(data).then(response => {
            if (response.status === 200) {
                const userList = this.state.userList.map(user => {
                    if (user['id'] === this.state.selectedUser) {
                        user['role'] = this.state.parmission;
                        return user
                    } else {
                        return user
                    }
                });

                this.setState({massage: 'save', userList});
            } else {
                this.setState({massage: response.data['error']});
            }
        }).catch(() => {
            this.setState({massage: 'Sorry, we cant save you`r changes'});
        })
    }

    setSelectedUser(user) {
        this.setState({selectedUser: user})
    }

    setParmission(parm) {
        this.setState({parmission: parm})
    }

    render() {

        return (

            <S.Content>

                <S.Header>
                    <Text size="64px" color="#84ffff" bold>
                        User Panel control
                    </Text>
                </S.Header>
                <S.Form className='form-group'>
                    <Text color='red' bold>{this.state.massage} </Text>
                    <Select label='Users' options={this.state.userList} value={this.state.selectedUser}
                            setValue={this.setSelectedUser.bind(this)} def={this.state.selectedUser}/>
                    <Select label='Permissions' options={this.permissionsOptions} value={this.state.parmission}
                            setValue={this.setParmission.bind(this)} def={this.state.parmission}/>
                    <Button label="UserPanel" color="primary" onClick={this.handleClick}/>
                </S.Form>

            </S.Content>
        );
    }
};


export default UserPanel;
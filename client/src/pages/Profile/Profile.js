import React from "react";
import Select from "../../components/Select";
import Text from "../../components/Text";
import * as S from "./style";
import Button from "../../components/Button";

import {getProfile, updateEmail} from "../../hooks/useUserFetch";
import InputText from "../../components/InputText";


class Profile extends React.Component {

    constructor(props) {
        super(props);
        this.handleClick = this.handleClick.bind(this);
        this.setMassage = this.setMassage.bind(this);
        this.state = {massage: '', userList: undefined, email: "", selectedUser: ""}
    }


    componentDidMount() {
        getProfile().then(response => {
                if (response.status === 200) {
                    let data = response.data;
                    data['value'] = data['id'];
                    data['label'] = data['username']

                    this.setState({userList: [data], selectedUser: data['value'], email: data['email'], massage: ''})
                } else {
                    this.setState({userList: undefined, massage: response.data});
                }
            }
        ).catch(() => {
            this.setState({userList: undefined, massage: 'Error'});
        })
    }

    handleClick() {
        const data = {'id': this.state.selectedUser, 'email': this.state.email}
        updateEmail(data).then(response => {
            if (response.status === 200) {
                this.setMassage('save');
            } else {
                this.setMassage(response.data['error'])
            }
        }).catch(() => {
            this.setMassage('Sorry, we cant save you`r changes');
        })
    }

    setMassage(msg) {
        this.setState({massage: msg});
    }

    setEmail(email) {
        this.setState({email});
    }

    render() {
        return (

            <S.Content>
                <S.Header>
                    <Text size="64px" bold>
                        profile
                    </Text>
                </S.Header>
                <S.Form className='form-group'>
                    <Text color='red' bold>{this.state.massage} </Text>
                    <Select label='Users' options={this.state.userList} value={this.state.selectedUser}
                            def={this.state.selectedUser}/>
                    <InputText onChange={this.setEmail.bind(this)} label='Email' val={this.state.email} type='email'/>
                    <Button label="Edit" color="primary" onClick={this.handleClick}/>
                </S.Form>
            </S.Content>
        );
    }
};

export default Profile;
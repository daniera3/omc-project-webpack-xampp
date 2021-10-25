/* eslint-disable react-hooks/exhaustive-deps */
import React from "react";
import InputText from "../../components/InputText";
import InputPassword from "../../components/InputPassword";
import Text from "../../components/Text";
import * as S from "./style";
import Button from "../../components/Button";
import { userStore } from "../../stores";
import { userRegister } from "../../actions/userActions";


class Register extends React.Component {
  constructor() {
    super();
    this.state = { username: '', password: '', massage: '', confirmPassword: '' };

    this.onChange = this.onChange.bind(this);
    this.handelClick = this.handelClick.bind(this);
  }



  onChange = () => {
    this.setState({ username: '', password: '', confirmPassword: '', massage: userStore.getMassege() })
    if (userStore.getUser() !== null) {
     this.props.history.push('/');
    }
  }



  componentDidMount() {
    userStore.addChangeListener(this.onChange);
    if (userStore.getUser() !== null) {
      this.props.history.push('/');
    }
  }

  componentWillUnmount() {
    userStore.removeChangeListener(this.onChange);
  }



  handelClick = (e) => {
    e.preventDefault();
    if (this.state.password !== this.state.confirmPassword) {
      this.setState({ password: '', confirmPassword: '', massage: "Password not equile to confirm password" })
    }
    else if (this.state.username === '') {
      this.setState({ massage: "Username field cannot be empty" });
    } else if (this.state.password.length < 7) {
      this.setState({ massage: "Password length can not be less than 7 " });
    }
    else {
      const data = { 'username': this.state.username, 'password': this.state.password };
      userRegister(data);

    }
  }


  setUsername(user){
    this.setState({username:user})
  }

  setPassword(pass){
    this.setState({password:pass})
  }
  setConfirmPassword(conf){
    this.setState({confirmPassword:conf})
  }
  render(){
  return(

    <S.Content>
      <S.Header>
        <Text size="64px" bold>
          Register
        </Text>
      </S.Header>
      <S.Form className='form-group' onSubmit={this.handelClick}>
        <Text color='red' bold>{this.state.massage} </Text>
        <InputText label='Username' val={this.state.username} onChange={this.setUsername.bind(this)} />
        <InputPassword value={this.state.password} onChange={this.setPassword.bind(this)} id='password' />
        <InputPassword value={this.state.confirmPassword} onChange={this.setConfirmPassword.bind(this)} label="Confirm Password" id='cPassword' />
        <Button label="Register" color="primary" onClick={this.handelClick} />
      </S.Form>
    </S.Content >
  );
  }
};

export default Register;
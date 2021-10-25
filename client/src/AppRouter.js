import React from "react";
import { BrowserRouter as Router, Switch, Route, Redirect } from "react-router-dom";
import { Home, Favorites, Login, Register, NotFound, Profile, UserPanel } from "./pages";
import { ThemeProvider } from "./theme";
import NavBar from "./components/NavBar";
import { userStore } from "./stores";
import { loginUserSessien } from "./actions/userActions";
import { isAdmin } from "./hooks/useUserFetch";
import Spinner from "./components/Spinner";
import * as history from "./utils/History"




/**
 * if user not login then redirect to login page for login else rander component by path 
 *  @param setValue func for change in nevBar active tab
 *  @param component Component for render if user login 
 */
class PrivateRoute extends React.Component {

  render() {
    return (
      <Route render={props => {
        userStore.getUser() !== null
          ? this.props.setValue()
          : this.props.setValue(0)
        return (userStore.getUser() !== null
          ? (<this.props.component  {...props} />)
          : <Redirect to={{ pathname: '/login' }} />)
      }
      }
      />
    )
  }

}


/**
 * if user  login then redirect to Home page  else rander component 
 *  @param setValue func for change in nevBar active tab
 *  @param component Component for render if user login 
 */
class UserRoute extends React.Component {
  render() {
    return (
      <Route render={props => {
        
        userStore.getUser() === null
          ? this.props.setValue()
          : this.props.setValue(0)
        return (userStore.getUser() === null
          ? (<this.props.component  {...props} />)
          : <Redirect to={{ pathname: '/' }} />)
      }
      }
      />
    )
  }
}


class AdminRoute extends React.Component {
  constructor(props) {
    super(props);
    this.state = { admin: <Spinner color="primary" size="45px" thickness={6} variant="indeterminate" /> };
  }

  componentDidMount() {
    isAdmin().then(
      response => {
        if (response.data['success'] === true) {

          this.setState({ admin: <this.props.component {...(response.data)} /> })
        }
        else {
          alert('you not un admin goodbye :P');
          this.setState({ admin: <Redirect to={{ pathname: '/' }} /> })
        }
      })
      .catch((e) => {
        alert('you not un admin goodbye :P');

        this.setState({ admin: <Redirect to={{ pathname: '/' }} /> })
      }
      )
  }

  render() {
    return (
      <Route render={() => {
        this.state.admin === <Redirect to={{ pathname: '/' }} />
          ? this.props.setValue(0)
          : this.props.setValue()
        return this.state.admin;
      }
      }
      />
    )
  }
}


class AppRouter extends React.Component {


  constructor() {
    super();
    this.state = { value: 0 };
    this.setValue = this.setValue.bind(this);
  }

  setValue(value) {
    if (this.state.value !== value)
      this.setState({ value })
  }

  urlSwitch = () => {
    switch (window.location.pathname) {
      case '/':
        return 0;
      case '/favorites':
        return 1;
      case '/profile':
        return 2;
      case '/user':
        return 3;
      case '/register':
        return 2;
      case '/login':
        return 3;
      default:
        return 0;
    }
  }

  componentDidMount() {
    this.setValue(this.urlSwitch());
    if (!userStore.getUser()) {
      loginUserSessien();
    }
  }


  render() {
    return (
      <ThemeProvider >
        <Router history={history}>
          <NavBar value={this.state.value} />
          <Switch  >
            <Route exact path="/" render={() => { this.setValue(0); return <Home /> }} />
            <PrivateRoute path="/favorites" setValue={(e) => this.setValue(e || 1)} component={Favorites} />
            <PrivateRoute path="/profile" setValue={(e) => this.setValue(e || 2)} component={Profile} />
            <AdminRoute path="/user" setValue={(e) => this.setValue(e || 3)} component={UserPanel} />
            <UserRoute path="/register" setValue={(e) => this.setValue(e || 2)} component={Register} />
            <UserRoute path="/login" setValue={(e) => this.setValue(e || 3)} component={Login} />
            <Route render={() => { this.setValue(0); return <NotFound /> }} />
          </Switch>
        </Router>
      </ThemeProvider>
    );
  }
};

export default AppRouter;

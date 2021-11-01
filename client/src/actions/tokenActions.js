import dispatcher from "../utils/appDispatcher"
import actionTypes from "./actionTypes";




export function setToken(token) {
    dispatcher.dispatch({
        actionTypes: actionTypes.SET_TOKEN,
        token: token,
    });
}

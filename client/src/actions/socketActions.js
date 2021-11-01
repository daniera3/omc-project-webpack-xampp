import dispatcher from "../utils/appDispatcher"
import actionTypes from "./actionTypes";




export function setConnect(con) {
    dispatcher.dispatch({
        actionTypes: actionTypes.SET_CONNECT,
        con: con,
    });
}


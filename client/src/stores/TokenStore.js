import {EventEmitter} from "events";
import dispatcher from "../utils/appDispatcher"
import actionTypes from "../actions/actionTypes";



const CHANGE_EVENT = "change";
let _token = null;



class TokenStore extends EventEmitter {
    addChangeListener(callback) {
        this.on(CHANGE_EVENT, callback);
    }

    removeChangeListener(callback) {
        this.removeListener(CHANGE_EVENT, callback);
    }

    emitChange() {
        this.emit(CHANGE_EVENT);
    }

    getToken() {
        return _token;
    }


}

const store = new TokenStore();

dispatcher.register((action) => {
    switch (action.actionTypes) {
        case actionTypes.SET_TOKEN:
                _token = action.token;
                store.emitChange();

            break;
        default:
    }
});


export default store;





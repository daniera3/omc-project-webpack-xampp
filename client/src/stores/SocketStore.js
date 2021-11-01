import {EventEmitter} from "events";
import dispatcher from "../utils/appDispatcher"
import actionTypes from "../actions/actionTypes";



const CHANGE_EVENT = "change";
let _con = null;
const _rand = Math.floor(Math.random() * 9999998251599);



class SocketStore extends EventEmitter {
    addChangeListener(callback) {
        this.on(CHANGE_EVENT, callback);
    }

    removeChangeListener(callback) {
        this.removeListener(CHANGE_EVENT, callback);
    }

    emitChange() {
        this.emit(CHANGE_EVENT);
    }

    getSocket() {
        return _con;
    }
    getRandom() {
        return _rand;
    }


}

const store = new SocketStore();

dispatcher.register((action) => {
    switch (action.actionTypes) {
        case actionTypes.SET_CONNECT:
                _con = action.con;
                store.emitChange();

            break;
        default:
    }
});


export default store;




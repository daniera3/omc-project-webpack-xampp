import axios from "axios";


axios.defaults.baseURL = '/api/'

export function register(user) {
    return axios.post('user/register', (user), {withCredentials: true});
}

export const login = (user) => {
    return axios.post('user/login', (user), {withCredentials: true});

}


export const logout = () => {
    return axios.post('user/logout', {withCredentials: true});

}


export const getSession = () => {
    return axios.get('user/session', {withCredentials: true});

}

export const isAdmin = () => {
    return axios.post('user/isAdmin', {withCredentials: true});

}

export const getProfile = () => {
    return axios.get('user/details', {withCredentials: true});

}

export const getAllUsers = () => {
    return axios.post('user/getAllUsers', {withCredentials: true});

}


export const updateRole = (data) => {

    return axios.put('user/updateRole', data, {withCredentials: true});

}

export const updateEmail = (data) => {

    return axios.put('user/update', data, {withCredentials: true});

}




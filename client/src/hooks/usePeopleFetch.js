import axios from "axios";


export function PeopleFetch() {

    return axios.get(`https://randomuser.me/api/?results=25&page=1`);
}



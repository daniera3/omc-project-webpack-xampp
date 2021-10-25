
import axios from "axios";



export function PeopleFetch() {
  
    return axios.get(`https://randomuser.me/api/?results=25&page=1`);
  }

  // const fetchUsersAdd = async () => {
  //   const response = await axios.get(`https://randomuser.me/api/?results=25&page=1`);
  //   return (Array.from(new Set([...users, ...response.data.results])));
  // }



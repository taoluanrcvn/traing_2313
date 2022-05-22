import axios from 'axios'
axios.defaults.withCredentials = true;
export const callApi = {
  async postRequest (url, body, isAuth = true) {
    try {
      if (isAuth) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${localStorage.getItem("token")}`;
      }
      const response = await axios.post(url, body)
      return response.data
    } catch (e) {
      throw e.response
    }
  },

  async getRequest (url, param, isAuth = true) {
    try {
      if (isAuth) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${localStorage.getItem("token")}`;
      }
      const response = await axios.get(url, { params: param})
      return response.data
    } catch (e) {
      // if (e.response && e.response.status === 401) {
      //   localStorage.removeItem("token");
      //   localStorage.removeItem("user");
      // }
      throw e.response
    }
  },

  async deleteRequest(url, param, isAuth = true) {
    try {
      if (isAuth) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${localStorage.getItem("token")}`;
      }
      const response = await axios.delete(url, {params: param})
      return response.data
    } catch (e) {
      throw e.response
    }
  },

  async putRequest(url, param, isAuth = true) {
    try {
      if (isAuth) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${localStorage.getItem("token")}`;
      }
      const response = await axios.put(url, {params: param})
      return response.data
    } catch (e) {
      throw e.response
    }
  }
}

import axios from 'axios'
axios.defaults.withCredentials = true;
axios.defaults.headers.common['Authorization'] = `Bearer ${localStorage.getItem("token")}`;
export const callApi = {
  async postRequest (url, body) {
    try {
      const response = await axios.post(url, body)
      return response.data
    } catch (e) {
      throw e.response
    }
  },

  async getRequest (url, param) {
    try {
      axios.defaults.headers.common['Authorization'] = `Bearer ${localStorage.getItem("token")}`;
      const response = await axios.get(url, { params: param})
      return response.data
    } catch (e) {
      throw e.response
    }
  },

  async deleteRequest(url, param) {
    try {
      axios.defaults.headers.common['Authorization'] = `Bearer ${localStorage.getItem("token")}`;
      const response = await axios.delete(url, {params: param})
      return response.data
    } catch (e) {
      throw e.response
    }
  },

  async putRequest(url, param) {
    try {
      const response = await axios.put(url, param)
      return response.data
    } catch (e) {
      throw e.response
    }
  },

  async verifyToken (url,  body = {}, token) {
    try {
      const response = await axios.post(url, body, {
        headers: {
          'Authorization': `Bearer ${token}`
        }
      })
      return response.data
    } catch (e) {
      throw e.response
    }
  },

}

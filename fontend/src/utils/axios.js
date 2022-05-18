import axios from 'axios'

export const Axios = {
  async postRequest (url, body) {
    try {
      // const headerRequest = { 'Content-Type': 'multipart/form-data'};
      const response = await axios.post(url, body)
      return response.data
    } catch (e) {
      return e
    }
  },
  async getRequest (url, param) {
    try {
      const response = await axios.get(url, { params: param })
      return response.data
    } catch (e) {
      return e.response
    }
  }
}

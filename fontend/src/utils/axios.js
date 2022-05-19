import axios from 'axios'
axios.defaults.withCredentials = true;
axios.defaults.headers.common = {
  "X-Referer": window.location.href
};
axios.defaults.headers.common['Access-Control-Allow-Origin'] = '*';


export const callApi = {
  async postRequest (url, body) {
    try {
      const response = await axios.post(url, body)
      return response.data
    } catch (e) {
      return e
    }
  },

  async getRequest (url, param) {
    try {
      const response = await axios.get(url, {
        params: param
      })
      return response.data
    } catch (e) {
      return e.response
    }
  }
}

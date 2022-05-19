import axios from 'axios'
axios.defaults.withCredentials = true;

export const callApi = {
  async postRequest (url, body, isAuth = true) {
    try {
      if (isAuth) {
        axios.defaults.headers.common['Authorization'] = `Bearer 0270f557de44e52a5c88159454df99fc7281dd62213f9ae45aa134fa890f90f6`;
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
        axios.defaults.headers.common['Authorization'] = `Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNjUyOTcyMzA5LCJleHAiOjE2NTM1NzcxMDksIm5iZiI6MTY1Mjk3MjMwOSwianRpIjoidG5uTTA5U2l5RnJSdzQzeSIsInN1YiI6IjEiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.u2yf7t-wSLL-ZBMDJG7E-7cUWYDvv-IcoPNMwfROEdg`;
      }
      const response = await axios.get(url, {
        params: param
      })
      return response.data
    } catch (e) {
      throw e.response
    }
  }
}

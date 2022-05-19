const DOMAIN = 'http://127.0.0.1:8000' + '/api/'
const PATH = {
  PRODUCT: DOMAIN + 'product/',
  USER: DOMAIN + 'user/'
}
export const API_CONSTANT = {
  LOGIN: DOMAIN + 'login',

  GET_USERS: PATH.USER,
  BLOCK_USER: PATH.USER + 'block' ,
  DELETE_USER: PATH.USER + 'delete',
  UPDATE_USER: PATH.USER

}

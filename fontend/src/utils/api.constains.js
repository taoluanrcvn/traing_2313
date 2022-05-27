const DOMAIN = 'http://127.0.0.1:8000/api/';
const PATH = {
  PRODUCT: DOMAIN + 'product/',
  USER: DOMAIN + 'user/',
  CUSTOMER: DOMAIN + 'customer/',
}
export const API_CONSTANT = {
  LOGIN: DOMAIN + 'login',
  VERIFY_TOKEN: DOMAIN + 'verify-token',
  USERS: PATH.USER,
  LOCK_OR_UNBLOCK_USER: PATH.USER + 'lock-or-unlock' ,
  UPDATE_USER: PATH.USER,

  CUSTOMERS: PATH.CUSTOMER,

  PRODUCTS: PATH.PRODUCT
}

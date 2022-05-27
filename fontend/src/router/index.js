import Vue from 'vue'
import VueRouter from 'vue-router'
import HomeView from '../views/HomeView.vue'
import Login from '../views/Login'
import Customer from '../views/customer/Customer'
import Product from '../views/product/Product'
import User from '../views/user/User'
import {callApi} from "@/utils/axios";
import {API_CONSTANT} from "@/utils/api.constains";
Vue.use(VueRouter)

const routes = [
  {
    path: '/',
    name: 'home',
    component: HomeView
  },
  {
    path: '/login',
    component: Login,
    name: 'login',
    beforeEnter: (to, from, next) => {
      if (from.path === '/') {
        guard(to, from, next)
      }
      next()
    }
  },
  {
    path: '/products',
    component: Product,
    beforeEnter: (to, from, next) => {
      guard(to, from, next)
    }
  },
  {
    path: '/customers',
    component: Customer,
    beforeEnter: (to, from, next) => {
      guard(to, from, next)
    }
  },
  {
    path: '/users',
    component: User,
    beforeEnter: (to, from, next) => {
      guard(to, from, next)
    }
  },
]

const guard = function (to, from, next) {
  if (!localStorage.getItem('user') || !localStorage.getItem('token')) {
    if (to.path === '/login') {
      next()
    }
    next('login')
  }
  const token = localStorage.getItem("token")
  callApi.verifyToken(API_CONSTANT.VERIFY_TOKEN, null, token)
      .then((response) => {
          if (response.statusCode) {
            localStorage.setItem('user', JSON.stringify(response.data))
            if (to.path === '/login') {
              next('users')
            }
            next()
          }
      }).catch(function (error) {
          if (error && error.status === 401) {
            next('login')
            localStorage.removeItem('token')
            localStorage.removeItem('user')
          }
        })
}
const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes
})

export default router

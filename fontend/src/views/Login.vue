<template>
    <v-app id="inspire">
        <v-container fluid >
            <v-row class="w-100 d-flex justify-center">
                <v-col lg="4" md="12">
                    <v-card elevation="4" light tag="section">
                        <v-card-title>
                            <v-col cols="4">
                              <h3 class="headline">
                                Login
                              </h3>
                            </v-col>
                          <v-col cols="8">
                            <v-flex>
                              <v-img :alt="platformName"  class="ml-3 float-right" contain width="400" height="82px" position="center right" src="../assets/logo_rivercrane.png"></v-img>
                            </v-flex>
                          </v-col>
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-text>
                            <v-form
                                    ref="form"
                                    v-model="valid"
                                    lazy-validation>
                                <v-text-field
                                    outlined
                                    label="Email"
                                    type="email"
                                    :rules="emailRules"
                                    :error-messages="errorsEmail"
                                    class="mb-2"
                                    v-model.trim="email"
                                    v-on:keyup.enter="login()"
                                ></v-text-field>
                                <v-text-field
                                    outlined
                                    label="Mật khẩu"
                                    type="password"
                                    :rules="passwordRules"
                                    :error-messages="errorsPassword"
                                    v-model.trim="password"
                                    v-on:keyup.enter="login()"
                                >

                                </v-text-field>
                            </v-form>
                        </v-card-text>
                        <v-divider></v-divider>
                        <v-card-actions >
                            <v-checkbox v-model="remember" label="Remember"></v-checkbox>
                            <v-spacer></v-spacer>
                            <v-btn
                                   :disabled="!valid || btnLogin"
                                   color="success"
                                   class="mr-4"
                                   @click="login()"
                                   :loading="btnLogin"
                            >
                                Đăng nhập
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </v-app>
</template>

<script>
import { callApi } from '../utils/axios.js'
import { API_CONSTANT } from '../utils/api.constains'
import {Toast} from "@/utils/toast";
export default {
  data() {
        return {
            btnLogin: false,
            valid: true,
            platformName: 'River Crane',
            password: null,
            email: null,
            remember: false,
            emailRules: [
                v => !!v || 'E-mail không được để trống!',
                v => /.+@.+\..+/.test(v) || 'E-mail không hợp lệ!',
            ],
            passwordRules: [
                v => !!v || 'Mật khậu không được để trống!',
                v => (v && v.length > 5) || 'Mật khẩu phải hơn 5 ký tự!'
            ],
            errorsEmail: null,
            errorsPassword: null,
        }
    },
    watch: {
        email() {
          if (this.errorsEmail) {
              this.errorsEmail = null;
          }

        },
        password() {
          if (this.errorsPassword) {
              this.errorsPassword = null;
          }

        }
    },
  mounted() {
    const email = localStorage.getItem('email');
    const pws = localStorage.getItem('pws')
    if (email && pws) {
        this.email = email;
        this.password = pws;
    }
  },
  methods: {
        login: async function () {
            const isValid = this.$refs.form.validate()
            if (isValid) {
                try {
                    this.btnLogin = true;
                    const data = new FormData();
                    data.append('email', this.email);
                    data.append('password', this.password);
                    data.append('remember', JSON.stringify(this.remember));
                    const response = await callApi.postRequest(API_CONSTANT.LOGIN, data, false);
                    if (response.statusCode) {
                      if (this.remember) {
                        localStorage.setItem('email', this.email);
                        localStorage.setItem('pws', this.password)
                      }
                      localStorage.setItem('token', response.data.access_token)
                      localStorage.setItem('user', JSON.stringify(response.data.user))
                      this.$router.push('users')
                    }
                } catch (e) {
                  if (e.status && e.status === 422) {
                    const errors = e.data.messages;
                    if (errors.password) {
                      this.errorsPassword = errors.password;
                    }
                    if (errors.email) {
                      this.errorsEmail = errors.email;
                    }
                    console.log(this.errorsEmail)
                    console.log(this.errorsPassword)
                    if (errors.other) {
                      await Toast.show('error', errors.other);
                    }
                  }
                }
                finally {
                  this.btnLogin = false;
                }
            }

        },
    }
}
</script>

<style scoped>

</style>

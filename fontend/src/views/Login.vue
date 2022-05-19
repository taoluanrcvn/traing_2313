<template>
    <v-app id="inspire">
        <v-container fluid >
            <v-row class="w-100 d-flex justify-center">
                <v-col lg="6" md="12">
                    <v-card elevation="4" light tag="section">
                        <v-card-title>
                            <v-layout align-center justify-space-between>
                                <h3 class="headline">
                                    Login
                                </h3>
<!--                                <v-flex>-->
<!--                                    <v-img :alt="platformName" class="ml-3" contain width="400" height="82px" position="center right" src="https://vieclamvui.com/upload/img/700/2021/06/16/a_1623828860539.png"></v-img>-->
<!--                                </v-flex>-->
                            </v-layout>
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-text>
                            <v-form
                                    ref="form"
                                    v-model="valid"
                                    lazy-validation>
                                <v-text-field
                                    outline
                                    label="Email"
                                    type="email"
                                    :rules="emailRules"
                                    :error-messages="errorsEmail"
                                    class="mb-2"
                                    v-model="email"></v-text-field>
                                <v-text-field
                                    outlin
                                    label="Mật khẩu"
                                    type="password"
                                    :rules="passwordRules"
                                    :error-messages="errorsPassword"
                                    v-model="password"></v-text-field>
                            </v-form>
                        </v-card-text>
                        <v-divider></v-divider>
                        <v-card-actions >
                            <v-checkbox v-model="remember" label="Remember"></v-checkbox>
                            <v-spacer></v-spacer>
                            <v-btn
                                   :disabled="!valid"
                                   color="success"
                                   class="mr-4"
                                   @click="login()">
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
export default {
  data() {
        return {
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
                v => (v && v.length > 5) || 'Mật khẩu phải hơn 5 ký tự!',
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
    methods: {
        login: async function () {
            const isValid = this.$refs.form.validate()
            if (isValid) {
                try {
                    const data = new FormData();
                    data.append('email', this.email);
                    data.append('password', this.password);
                    data.append('remember', JSON.stringify(this.remember));
                    const response = await callApi.postRequest(API_CONSTANT.LOGIN, data);
                    if (!response.status && response.messages) {
                        const errors = response.messages;
                        console.log(errors)
                        if (errors.password) {
                            this.errorsPassword = errors.password;
                        }
                        if (errors.email) {
                            this.errorsEmail = errors.email;
                        }
                        return;
                    }
                  localStorage.setItem('token', response.access_token)
                  localStorage.setItem('email', this.email)
                  // this.$cookies.set("token", response.access_token, Infinity);
                    // window.location.href = window.location.origin + '/products'
                    this.$router.push('users')
                } catch (e) {
                    console.log(e)
                }
                // console.log(this.$refs.form)

            }

        },
    }
}
</script>

<style scoped>

</style>

<template>
    <div class="login" style="margin-top: -56px">
        <div class="align-items-center container d-flex justify-content-center min-vh-100">
            <div class="col-11 col-lg-5 col-md-7">
                <div class="card shadow position-relative" :class="{ 'show-loader': processing }">
                    <div class="card-body p-4 p-md-5">
                        <h2 class="card-title text-center">Smart School</h2>
                        <hr class="my-4" />
                        <form class="px-2" @submit.prevent="login">
                            <div class="form-floating mb-3">
                                <input
                                    type="text"
                                    :class="
                                        'form-control' +
                                            (validation.school_name ? ' is-invalid' : '')
                                    "
                                    id="school_name"
                                    list="school_list"
                                    placeholder="School name"
                                    v-model="user.school_name"
                                    :readonly="processing"
                                />
                                <label for="schoolName">School Name</label>
                                <datalist id="school_list">
                                    <option
                                        v-for="item in school"
                                        :key="item.id"
                                        :value="item.name"
                                    >
                                        {{ item.address }}
                                    </option>
                                </datalist>
                            </div>
                            <div class="form-floating mb-3">
                                <input
                                    type="email"
                                    :class="
                                        'form-control' + (validation.email ? ' is-invalid' : '')
                                    "
                                    id="email"
                                    placeholder="example@email.com"
                                    v-model="user.email"
                                    :readonly="processing"
                                />
                                <label for="email">Email</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input
                                    type="password"
                                    :class="
                                        'form-control' + (validation.password ? ' is-invalid' : '')
                                    "
                                    id="password"
                                    placeholder="Password"
                                    v-model="user.password"
                                    :readonly="processing"
                                />
                                <label for="password">Password</label>
                            </div>
                            <button
                                type="submit"
                                class="btn btn-primary d-block mx-auto"
                                :disabled="processing"
                            >
                                LOGIN
                            </button>
                        </form>
                        <hr class="my-4" />
                        <small class="text-muted"
                            >Don't have an account?
                            <a href="/" :tabindex="processing ? '-1' : ''">Signup</a></small
                        >
                    </div>
                    <div id="loader">
                        <div class="spinner spinner-border" role="status"></div>
                        <div class="spinner-text">Processing...</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="position-fixed top-0 end-0 p-3 mt-5" style="z-index: 11">
            <div id="liveToast" ref="toaster" class="toast hide">
                <div class="toast-header">
                    <svg
                        class="bd-placeholder-img rounded me-2"
                        width="20"
                        height="20"
                        xmlns="http://www.w3.org/2000/svg"
                        aria-hidden="true"
                        preserveAspectRatio="xMidYMid slice"
                        focusable="false"
                    >
                        <rect
                            width="100%"
                            height="100%"
                            :fill="
                                toastData.type == 'error'
                                    ? '#dc3545'
                                    : toastData.type == 'success'
                                    ? '#198754'
                                    : '#0dcaf0'
                            "
                        ></rect>
                    </svg>
                    <strong class="me-auto">{{ toastData.title }}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">
                    {{ toastData.text }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios'
import { Toast } from 'bootstrap'

export default {
    name: 'Login',

    data() {
        return {
            loggedIn: localStorage.getItem('loggedIn'),
            token: localStorage.getItem('token'),
            user: [],
            school: [],
            validation: [],
            loginFailed: null,
            toast: null,
            toastData: {},
            processing: false
        }
    },
    methods: {
        login() {
            this.processing = true
            this.validation = []
            this.validation.success = true
            if (!this.user.school_name) {
                this.validation.school_name = true
                this.validation.success = false
            }
            if (!this.user.email) {
                this.validation.email = true
                this.validation.success = false
            }
            if (!this.user.password) {
                this.validation.password = true
                this.validation.success = false
            }

            if (this.validation.success) {
                axios.get('http://localhost:8000/sanctum/csrf-cookie').then(response => {
                    console.log(response.data)
                    axios
                        .post('http://localhost:8000/api/login', {
                            school_name: this.user.school_name,
                            email: this.user.email,
                            password: this.user.password
                        })
                        .then(res => {
                            if (res.data.success) {
                                localStorage.setItem('loggedIn', 'true')
                                localStorage.setItem('token', res.data.data.token)
                                localStorage.setItem('user_name', res.data.data.name)
                                this.loggedIn = true
                                return this.$router.push({ name: 'dashboard' })
                            }
                        })
                        .catch(error => {
                            if (error.response.data.success == 'false') {
                                this.loginFailed = true
                                this.toastData = {
                                    type: 'error',
                                    title: 'Login Error',
                                    text:
                                        'Please check again your school name, email address, or password'
                                }
                                this.toast.show()
                                this.processing = false
                            }
                        })
                })
            } else {
                this.processing = false
            }
        },
        getSchoolsList() {
            axios
                .get('http://localhost:8000/api/school/get_all_school')
                .then(res => {
                    if (res.data.success) {
                        this.school = res.data.data
                    }
                })
                .catch(error => {
                    console.log(error.response)
                })
        }
    },
    mounted() {
        if (this.loggedIn) {
            return this.$router.push({ name: 'dashboard' })
        }
        this.toast = new Toast(this.$refs.toaster)
        this.getSchoolsList()
    }
}
</script>

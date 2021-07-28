<template>
    <div class="signup" style="margin-top: -56px">
        <div class="container col-xl-10 col-xxl-8 px-4 py-5 py-lg-0 d-flex min-vh-100">
            <div class="row align-items-center g-lg-5 py-5">
                <div class="col-lg-7 text-center text-lg-start">
                    <h1 class="display-1 fw-bold lh-1 mb-3">Smart School</h1>
                    <p class="col-lg-10 fs-4">
                        Bring your school to the world, bring the world to your school. Join us
                        now!!
                    </p>
                </div>
                <div class="col-md-10 mx-auto col-lg-5">
                    <form
                        class="p-4 p-md-5 border rounded-3 bg-light position-relative"
                        :class="{ 'show-loader': processing }"
                        @submit.prevent="signup"
                    >
                        <div class="form-floating mb-3">
                            <input
                                type="text"
                                :class="
                                    'form-control' + (validation.school_name ? ' is-invalid' : '')
                                "
                                id="school_name"
                                placeholder="School name"
                                v-model="user.school_name"
                                :readonly="processing"
                            />
                            <label for="school_name">School name</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input
                                type="text"
                                :class="'form-control' + (validation.name ? ' is-invalid' : '')"
                                id="name"
                                placeholder="Your name"
                                v-model="user.name"
                                :readonly="processing"
                            />
                            <label for="name">Your name</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input
                                type="email"
                                :class="'form-control' + (validation.email ? ' is-invalid' : '')"
                                id="email"
                                placeholder="name@example.com"
                                v-model="user.email"
                                :readonly="processing"
                            />
                            <label for="email">Email address</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input
                                type="password"
                                :class="'form-control' + (validation.password ? ' is-invalid' : '')"
                                id="password"
                                placeholder="Password"
                                v-model="user.password"
                                :readonly="processing"
                            />
                            <label for="password">Password</label>
                            <div class="form-text">* Minimum 6 characters</div>
                        </div>
                        <button
                            class="w-100 btn btn-lg btn-primary"
                            type="submit"
                            :disabled="processing"
                        >
                            Sign up
                        </button>
                        <hr class="my-4" />
                        <small class="text-muted">
                            Already have an account?
                            <a href="/login" :tabindex="processing ? '-1' : ''">Login</a></small
                        >
                        <div id="loader">
                            <div class="spinner spinner-border" role="status"></div>
                            <div class="spinner-text">Processing...</div>
                        </div>
                    </form>
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
    name: 'Home',

    data() {
        return {
            loggedIn: localStorage.getItem('loggedIn'),
            token: localStorage.getItem('token'),
            user: [],
            school: [],
            validation: [],
            loginFailed: null,
            signupFailed: null,
            toast: null,
            toastData: {},
            processing: false
        }
    },
    methods: {
        signup() {
            this.processing = true
            this.validation = []
            this.validation.success = true
            if (!this.user.school_name) {
                this.validation.school_name = true
                this.validation.success = false
            }
            if (!this.user.name) {
                this.validation.name = true
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
                axios
                    .post('http://localhost:8000/api/signup', {
                        school_name: this.user.school_name,
                        user_name: this.user.name,
                        user_email: this.user.email,
                        user_password: this.user.password,
                        user_role: 1
                    })
                    .then(res => {
                        if (res.data.success) {
                            this.toastData = {
                                type: 'success',
                                title: 'Signup Success',
                                text:
                                    'Your account has been created. Redirecting to your Dashboard.'
                            }
                            this.toast.show()
                            axios
                                .get('http://localhost:8000/sanctum/csrf-cookie')
                                .then(response => {
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
                                                this.loggedIn = true
                                                return this.$router.push({
                                                    name: 'dashboard'
                                                })
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
                        }
                    })
                    .catch(error => {
                        if (error.response.data.success == 'false') {
                            this.signupFailed = true
                            this.toastData = {
                                type: 'error',
                                title: 'Signup Error',
                                text:
                                    'Please check again your school name, your name, email address or password'
                            }
                            this.toast.show()
                            this.processing = false
                        }
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
        // this.getSchoolsList();
    }
}
</script>

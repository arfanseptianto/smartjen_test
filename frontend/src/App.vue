<template>
    <div id="app">
        <nav class="navbar navbar-expand navbar-dark fixed-top bg-dark px-3" v-show="loggedIn">
            <router-link :to="{ name: 'home' }" class="navbar-brand">SMART SCHOOL</router-link>
            <ul class="navbar-nav ms-auto me-3">
                <li class="nav-item dropdown" v-show="loggedIn">
                    <a
                        class="nav-link dropdown-toggle icon-only"
                        href="#"
                        @click.prevent="dropdown"
                        data-bs-toggle="dropdown"
                        ref="dropdownToggle"
                    >
                        <i class="bi-person-circle"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark">
                        <li>
                            <h6 class="dropdown-header">Hi, {{ user_name }}!</h6>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#" @click.prevent="logout"><i class="bi-power"></i> Logout</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
        <router-view></router-view>
    </div>
</template>

<script>
import axios from 'axios'

export default {
    name: 'App',

    data() {
        return {
            loggedIn: null,
            user_name: null,
            token: null
        }
    },

    methods: {
        getLoggedIn() {
            this.loggedIn = localStorage.getItem('loggedIn')
            this.user_name = localStorage.getItem('user_name')
            this.token = localStorage.getItem('token')
        },
        logout() {
            axios
                .get('http://localhost:8000/api/logout', {
                    headers: { Authorization: 'Bearer ' + this.token }
                })
                .then(() => {
                    //remove localStorage
                    localStorage.removeItem('loggedIn')

                    //redirect
                    return this.$router.push({ name: 'home' })
                })
        },
        dropdown() {
            var target = this.$refs.dropdownToggle
            if (target.classList.contains('show')) {
                target.classList.remove('show')
                target.nextSibling.classList.remove('show')
            } else {
                target.classList.add('show')
                target.nextSibling.classList.add('show')
            }
        }
    },

    watch: {
        $route: {
            immediate: true,
            handler() {
                this.getLoggedIn()
            }
        }
    }
}
</script>

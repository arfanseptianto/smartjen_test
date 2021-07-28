<template>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark px-3" v-show="loggedIn">
            <router-link :to="{ name: 'home' }" class="navbar-brand">SMART SCHOOL</router-link>
            <button
                class="navbar-toggler"
                type="button"
                data-toggle="collapse"
                data-target="#navbarsExampleDefault"
                aria-controls="navbarsExampleDefault"
                aria-expanded="false"
                aria-label="Toggle navigation"
            >
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarsExampleDefault">
                <ul class="navbar-nav ms-auto me-3">
                    <li class="nav-item dropdown" v-show="loggedIn">
                        <a
                            class="nav-link dropdown-toggle"
                            href="#"
                            @click.prevent="dropdown"
                            data-bs-toggle="dropdown"
                        >
                            {{ user_name }}
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="#" @click.prevent="logout">Logout</a>
                            </li>
                        </ul>
                    </li>
                </ul>
                <div class="form-inline my-2 my-lg-0">
                    <router-link
                        :to="{ name: 'login' }"
                        v-if="!loggedIn"
                        class="btn btn-primary my-2 my-sm-0"
                        >LOGIN</router-link
                    >
                </div>
            </div>
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
        dropdown: function(event) {
            if (event) {
                event.target.classList.add('show')
                event.target.nextSibling.classList.add('show')
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

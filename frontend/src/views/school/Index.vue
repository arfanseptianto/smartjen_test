<template>
    <div class="dashboard">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1 class="border-secondary display-4 my-4 text-center text-uppercase">
                        WELCOME TO<br />{{ user.school_name }}
                    </h1>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <nav class="overflow-auto">
                        <div class="nav nav-tabs flex-nowrap" id="nav-tab" role="tablist">
                            <button
                                class="nav-link text-nowrap"
                                id="teacherTab"
                                data-bs-toggle="tab"
                                data-bs-target="#teacherTabContent"
                                type="button"
                                role="tab"
                                tabindex="-1"
                                aria-controls="teacherTabContent"
                                aria-selected="true"
                                :class="user.role > 2 ? 'd-none' : 'active'"
                                :disabled="fetching"
                            >
                                <h4 class="mb-0">Teachers Board</h4>
                            </button>
                            <button
                                class="nav-link text-nowrap"
                                id="studentTab"
                                data-bs-toggle="tab"
                                data-bs-target="#studentTabContent"
                                type="button"
                                role="tab"
                                tabindex="-1"
                                aria-controls="studentTabContent"
                                aria-selected="false"
                                :class="user.role == 3 ? 'active' : ''"
                                :disabled="fetching"
                            >
                                <h4 class="mb-0">Student's board</h4>
                            </button>
                        </div>
                    </nav>
                    <div class="card position-relative" :class="{ 'show-loader': fetching }">
                        <div class="card-body tab-content" id="nav-tabContent">
                            <div
                                class="tab-pane fade"
                                id="teacherTabContent"
                                role="tabpanel"
                                aria-labelledby="teacherTabContent"
                                :class="user.role > 2 ? 'd-none' : 'show active'"
                            >
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead class="bg-info text-center">
                                            <tr>
                                                <th>Name</th>
                                                <th>Email</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-if="!teachers.length">
                                                <td colspan="2" class="text-center">
                                                    <span class="display-6">No teachers found!</span
                                                    ><br />Please contact your school's
                                                    administrator to invite a teacher.
                                                </td>
                                            </tr>
                                            <tr v-for="teacher in teachers" :key="teacher.id">
                                                <td data-caption="Name">{{ teacher.name }}</td>
                                                <td data-caption="Email">{{ teacher.email }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div
                                class="tab-pane fade"
                                id="studentTabContent"
                                role="tabpanel"
                                aria-labelledby="studentTabContent"
                                :class="user.role == 3 ? 'show active' : ''"
                            >
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead class="bg-info text-center">
                                            <tr>
                                                <th>Name</th>
                                                <th>Email</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-if="!students.length">
                                                <td colspan="2" class="text-center">
                                                    <span class="display-6">No students found!</span
                                                    ><br />Please contact your school's
                                                    administrator to invite a student.
                                                </td>
                                            </tr>
                                            <tr v-for="student in students" :key="student.id">
                                                <td data-caption="Name">{{ student.name }}</td>
                                                <td data-caption="Email">{{ student.email }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div id="loader">
                            <div class="spinner spinner-border" role="status"></div>
                            <div class="spinner-text">Please wait...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios'

export default {
    name: 'Dashboard',

    data() {
        return {
            loggedIn: localStorage.getItem('loggedIn'),
            token: localStorage.getItem('token'),
            users: [],
            teachers: [],
            students: [],
            user: {
                name: localStorage.getItem('user_name'),
                role: localStorage.getItem('user_role'),
                school_name: localStorage.getItem('user_school_name')
            },
            fetching: false
        }
    },

    created() {},

    methods: {
        getUsers() {
            this.fetching = true
            axios
                .get('http://localhost:8000/api/user/get_all_user', {
                    headers: { Authorization: 'Bearer ' + this.token }
                })
                .then(response => {
                    if (response.data.success) {
                        this.users = response.data.data // assign response to state user
                    }
                    this.fetching = false
                })
                .catch(error => {
                    if (error.response) {
                        if (error.response.data.success == 'false') {
                            //set state login failed
                            this.loginFailed = true
                            this.loginFailedMessage =
                                'Please check again your school name, email address, or password'
                            this.toast.show()
                        }
                    } else {
                        console.log(error.response)
                    }
                    this.fetching = false
                })
        }
    },

    //check user logged in or not
    mounted() {
        if (!this.loggedIn) {
            return this.$router.push({ name: 'home' })
        } else if (this.loggedIn && this.user.role == 1) {
            return this.$router.push({ name: 'dashboard' })
        }
        this.getUsers()
    },
    watch: {
        users: function() {
            this.teachers = this.users.filter(function(user) {
                return user.role == 2
            })
            this.students = this.users.filter(function(user) {
                return user.role == 3
            })
        }
    }
}
</script>

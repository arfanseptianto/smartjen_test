<template>
    <div class="dashboard" style="margin-top: 80px">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            DASHBOARD
                            <hr />
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="bg-info text-center">
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="user in users" :key="user.id">
                                            <td data-caption="Name">{{ user.name }}</td>
                                            <td data-caption="Email">{{ user.email }}</td>
                                            <td data-caption="Role">
                                                {{
                                                    user.role == 1
                                                        ? 'Admin'
                                                        : user.role == 2
                                                        ? 'Teacher'
                                                        : user.role == 3
                                                        ? 'Student'
                                                        : '-'
                                                }}
                                            </td>
                                            <td data-caption="Action" class="text-center">
                                                <div class="d-flex justify-content-center">
                                                    <button
                                                        class="btn btn-primary btn-sm me-2"
                                                        v-bind:data-user="JSON.stringify(user)"
                                                        @click="editUser"
                                                    >
                                                        <i class="bi-pencil-square"></i></button
                                                    ><button
                                                        class="btn btn-warning btn-sm"
                                                        v-bind:data-user="JSON.stringify(user)"
                                                        @click="deleteUser"
                                                    >
                                                        <i class="bi-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div
            class="modal fade"
            id="modalUserEdit"
            ref="modalUserEdit"
            data-bs-backdrop="static"
            data-bs-keyboard="false"
            tabindex="-1"
            aria-labelledby="modalUserEditLabel"
            aria-hidden="true"
        >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalUserEditLabel">Edit User</h5>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        ></button>
                    </div>
                    <div class="modal-body">
                        <form
                            class="p-4 p-md-5 border rounded-3 bg-light position-relative"
                            :class="{ 'show-loader': processing }"
                            @submit.prevent="signup"
                        >
                            <div class="form-floating mb-3">
                                <input
                                    type="text"
                                    :class="
                                        'form-control' +
                                            (validation.school_name ? ' is-invalid' : '')
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
                                    :class="
                                        'form-control' + (validation.email ? ' is-invalid' : '')
                                    "
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
                                    :class="
                                        'form-control' + (validation.password ? ' is-invalid' : '')
                                    "
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
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="button" class="btn btn-primary">Understood</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios'
import { Modal } from 'bootstrap'

export default {
    name: 'Dashboard',

    data() {
        return {
            loggedIn: localStorage.getItem('loggedIn'),
            token: localStorage.getItem('token'),
            user_name: localStorage.getItem('user_name'),
            users: [],
            modalAdd: null,
            modalEdit: null,
            modalEditData: {}
        }
    },

    created() {
        axios
            .get('http://localhost:8000/api/admin/get_all_user', {
                headers: { Authorization: 'Bearer ' + this.token }
            })
            .then(response => {
                console.log(response.data.data)
                if (response.data.success) {
                    this.users = response.data.data // assign response to state user
                }
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
            })
    },

    methods: {
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
        editUser: function(event) {
            this.modalEditData = null
            if (event.target.tagName == 'I') {
                this.modalEditData = JSON.parse(event.target.parentElement.dataset.user)
            } else {
                this.modalEditData = JSON.parse(event.target.dataset.user)
            }
            console.log(this.modalEditData)
            this.modalEdit.show()
        }
    },

    //check user logged in or not
    mounted() {
        if (!this.loggedIn) {
            return this.$router.push({ name: 'home' })
        }
        this.modalEdit = new Modal(this.$refs.modalUserEdit)
    }
}
</script>

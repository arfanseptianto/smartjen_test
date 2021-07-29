<template>
    <div class="dashboard">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1 class="border-secondary display-4 my-4 text-center text-uppercase">
                        {{ user.school_name }}<br />DASHBOARD
                    </h1>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card position-relative" :class="{ 'show-loader': fetching }">
                        <div class="card-header d-flex justify-content-between">
                            <h3 class="card-title">Members Board</h3>
                            <button
                                class="align-self-center btn btn-sm btn-success text-nowrap"
                                @click="showAddModal"
                            >
                                <i class="bi-person-plus-fill"></i> Invite new member
                            </button>
                        </div>
                        <div class="card-body">
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
                                        <tr v-if="!users.length">
                                            <td colspan="4" class="text-center display-6">
                                                No members found!
                                            </td>
                                        </tr>
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
                                                        @click="showEditModal(user.id)"
                                                        :ref="'editModalToggle' + user.id"
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
                        <div id="loader">
                            <div class="spinner spinner-border" role="status"></div>
                            <div class="spinner-text">Please wait...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <!-- Modal Add -->
        <div
            class="modal fade"
            id="modalUserAdd"
            ref="modalUserAdd"
            data-bs-backdrop="static"
            data-bs-keyboard="false"
            tabindex="-1"
            aria-labelledby="modalUserAddLabel"
            aria-hidden="true"
        >
            <div class="modal-dialog">
                <div class="modal-content">
                    <form
                        class="position-relative"
                        :class="{ 'show-loader': processing }"
                        @submit.prevent="addUser"
                    >
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalUserAddLabel">Invite New Member</h5>
                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Close"
                                :disabled="processing"
                            ></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3 row">
                                <div class="align-self-center col-4">Member role</div>
                                <div class="col-8 d-grid">
                                    <div class="btn-group" role="group">
                                        <input
                                            type="radio"
                                            class="btn-check"
                                            name="role"
                                            id="newRoleTeacher"
                                            autocomplete="off"
                                            value="2"
                                            v-model="modalAddData.role"
                                            :disabled="processing"
                                        />
                                        <label
                                            class="btn"
                                            :class="
                                                'btn-outline-' +
                                                    (validation.role ? 'danger' : 'primary')
                                            "
                                            for="newRoleTeacher"
                                            >Teacher</label
                                        >

                                        <input
                                            type="radio"
                                            class="btn-check"
                                            name="role"
                                            id="newRoleStudent"
                                            autocomplete="off"
                                            value="3"
                                            v-model="modalAddData.role"
                                            :disabled="processing"
                                        />
                                        <label
                                            class="btn"
                                            :class="
                                                'btn-outline-' +
                                                    (validation.role ? 'danger' : 'primary')
                                            "
                                            for="newRoleStudent"
                                            >Student</label
                                        >
                                    </div>
                                </div>
                            </div>
                            <div class="form-floating mb-3">
                                <input
                                    type="text"
                                    :class="'form-control' + (validation.name ? ' is-invalid' : '')"
                                    id="newName"
                                    placeholder="Your name"
                                    v-model="modalAddData.name"
                                    :readonly="processing"
                                    :disabled="!modalAddData.role"
                                />
                                <label for="name">Full name</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input
                                    type="email"
                                    :class="
                                        'form-control' + (validation.email ? ' is-invalid' : '')
                                    "
                                    id="newEmail"
                                    placeholder="name@example.com"
                                    v-model="modalAddData.email"
                                    :readonly="processing"
                                    :disabled="!modalAddData.role"
                                />
                                <label for="email">Email address</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input
                                    type="password"
                                    :class="
                                        'form-control' + (validation.password ? ' is-invalid' : '')
                                    "
                                    id="newPassword"
                                    placeholder="Password"
                                    v-model="modalAddData.password"
                                    :readonly="processing"
                                    :disabled="!modalAddData.role"
                                />
                                <label for="password">Password</label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button
                                type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal"
                                :disabled="processing"
                            >
                                Cancel
                            </button>
                            <button class="btn btn-primary" type="submit" :disabled="processing">
                                Invite
                            </button>
                        </div>
                        <div id="loader">
                            <div class="spinner spinner-border" role="status"></div>
                            <div class="spinner-text">Processing...</div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Edit -->
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
                    <form
                        class="position-relative"
                        :class="{ 'show-loader': processing }"
                        @submit.prevent="editUser"
                    >
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalUserEditLabel">Edit Member Info</h5>
                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Close"
                                :disabled="processing"
                            ></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-floating mb-3">
                                <input
                                    type="text"
                                    :class="'form-control' + (validation.role ? ' is-invalid' : '')"
                                    id="editRole"
                                    placeholder="Role"
                                    :value="
                                        modalEditData.role == 1
                                            ? 'Admin'
                                            : modalEditData.role == 2
                                            ? 'Teacher'
                                            : modalEditData.role == 3
                                            ? 'Student'
                                            : '-'
                                    "
                                    disabled
                                />
                                <label for="name">Role</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input
                                    type="text"
                                    :class="'form-control' + (validation.name ? ' is-invalid' : '')"
                                    id="editName"
                                    placeholder="Your name"
                                    v-model="modalEditData.name"
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
                                    id="editEmail"
                                    placeholder="name@example.com"
                                    v-model="modalEditData.email"
                                    disabled
                                />
                                <label for="email">Email address</label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button
                                type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal"
                                :disabled="processing"
                            >
                                Cancel
                            </button>
                            <button class="btn btn-primary" type="submit" :disabled="processing">
                                Update
                            </button>
                        </div>
                        <div id="loader">
                            <div class="spinner spinner-border" role="status"></div>
                            <div class="spinner-text">Processing...</div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Toast -->
        <div class="position-fixed top-0 end-0 p-3 mt-5" style="z-index: 1070">
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
import { Modal, Toast } from 'bootstrap'

export default {
    name: 'Dashboard',

    data() {
        return {
            loggedIn: localStorage.getItem('loggedIn'),
            token: localStorage.getItem('token'),
            users: [],
            user: {
                name: localStorage.getItem('user_name'),
                role: localStorage.getItem('user_role'),
                school_name: localStorage.getItem('user_school_name')
            },
            modalAdd: null,
            modalAddData: [],
            modalEdit: null,
            modalEditData: [],
            validation: [],
            processing: false,
            fetching: false,
            toast: null,
            toastData: {}
        }
    },

    created() {},

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
        getUsers() {
            this.fetching = true
            axios
                .get('http://localhost:8000/api/admin/get_all_user', {
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
        },
        showAddModal() {
            this.validation = []
            this.modalAddData = []
            this.modalAdd.show()
        },
        showEditModal(id) {
            this.validation = []
            this.modalEditData = []
            var target = this.$refs['editModalToggle' + id]
            this.modalEditData = JSON.parse(target.dataset.user)
            this.modalEdit.show()
        },
        addUser() {
            this.processing = true
            this.validation = []
            this.validation.success = true
            if (!this.modalAddData.role) {
                this.validation.role = true
                this.validation.success = false
            }
            if (!this.modalAddData.name) {
                this.validation.name = true
                this.validation.success = false
            }
            if (!this.modalAddData.email) {
                this.validation.email = true
                this.validation.success = false
            }
            if (!this.modalAddData.password) {
                this.validation.password = true
                this.validation.success = false
            }

            if (this.validation.success) {
                axios
                    .post(
                        'http://localhost:8000/api/admin/add_new_user',
                        {
                            name: this.modalAddData.name,
                            email: this.modalAddData.email,
                            password: this.modalAddData.password,
                            role: this.modalAddData.role
                        },
                        {
                            headers: { Authorization: 'Bearer ' + this.token }
                        }
                    )
                    .then(res => {
                        if (res.data.success) {
                            this.toastData = {
                                type: 'success',
                                title: 'Member Invited',
                                text: 'Successfully add new member to your school'
                            }
                            this.toast.show()
                            this.modalAdd.hide()
                            this.processing = false
                            this.getUsers()
                        }
                    })
                    .catch(error => {
                        // if (error.response.data.success == 'false') {
                        // }
                        console.log(error.response.data)
                        this.toastData = {
                            type: 'error',
                            title: 'Invite Error',
                            text:
                                'We could not process your invitation. Please check again the role, name, email address or password.'
                        }
                        this.toast.show()
                        this.processing = false
                    })
            } else {
                this.processing = false
            }
        },
        editUser() {
            this.processing = true
            this.validation = []
            this.validation.success = true
            if (!this.modalEditData.role) {
                this.validation.role = true
                this.validation.success = false
            }
            if (!this.modalEditData.name) {
                this.validation.name = true
                this.validation.success = false
            }
            if (!this.modalEditData.email) {
                this.validation.email = true
                this.validation.success = false
            }

            console.log(this.modalEditData)

            if (this.validation.success) {
                axios
                    .post(
                        'http://localhost:8000/api/admin/edit_user/' + this.modalEditData.id,
                        {
                            name: this.modalEditData.name,
                            email: this.modalEditData.email,
                            password: this.modalEditData.password,
                            role: this.modalEditData.role
                        },
                        {
                            headers: { Authorization: 'Bearer ' + this.token }
                        }
                    )
                    .then(res => {
                        if (res.data.success) {
                            this.toastData = {
                                type: 'success',
                                title: 'Member Updated',
                                text: 'Successfully updated member information'
                            }
                            this.toast.show()
                            this.modalEdit.hide()
                            this.processing = false
                            this.getUsers()
                        }
                    })
                    .catch(error => {
                        // if (error.response.data.success == 'false') {
                        // }
                        console.log(error.response.data)
                        this.toastData = {
                            type: 'error',
                            title: 'Update Error',
                            text:
                                'Please check again the role, name, email address or password.'
                        }
                        this.toast.show()
                        this.processing = false
                    })
            } else {
                this.processing = false
            }
        },
        deleteUser() {
            //
        }
    },

    //check user logged in or not
    mounted() {
        if (!this.loggedIn) {
            return this.$router.push({ name: 'home' })
        }
        this.getUsers()
        this.toast = new Toast(this.$refs.toaster)
        this.modalAdd = new Modal(this.$refs.modalUserAdd)
        this.modalEdit = new Modal(this.$refs.modalUserEdit)
    }
}
</script>

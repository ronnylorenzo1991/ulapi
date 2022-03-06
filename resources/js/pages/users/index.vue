<template>
    <div>
        <loader :show="isLoading"/>
        <div class="header bg-gradient-primary pb-8 pt-5 pt-md-8">
            <div class="container-fluid">
                <div class="header-body">
                </div>
            </div>
        </div>
        <div class="container-fluid mt--6">
            <div class="row">
                <div class="col">
                    <div class="card shadow">
                        <div class="card-header bg-transparent">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h2 class="mb-0">Usuarios</h2>
                                </div>
                                <div class="col">
                                    <ul class="nav nav-pills justify-content-end">
                                        <li class="nav-item mr-2 mr-md-0">
                                            <a @click.prevent="openCreateEditUserModal" href="#"
                                               class="nav-link py-2 px-3 active">
                                                <span class="d-none d-md-block">+ Nuevo Usuario</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <simple-table reference="userVueTable"
                                          :key="userTableKey"
                                          ref="userTable"
                                          :api-url="usersUrl"
                                          :has-settings="false"
                                          :has-header="false"
                                          :fields="usersFields"
                                          :per-page="5"
                                          paginationFontSize="small"
                                          :hasCustomActions="true">
                                <template slot="custom-actions" slot-scope="props">
                                    <button type="button" class="btn btn-secondary btn-icon-only rounded-circle"
                                            @click="openCreateEditUserModal(props.props.rowData)">
                                        <span class="btn-inner--icon"><i class="fa fa-edit"></i></span>
                                    </button>
                                    <button type="button" class="btn btn-primary btn-icon-only rounded-circle"
                                            @click="deleteUser(props.props.rowData.id)">
                                        <span class="btn-inner--icon"><i class="fa fa-trash"></i></span>
                                    </button>
                                </template>
                            </simple-table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <modal title="Añadir Usuario"
               :show="showNewUserModal"
               @accept="saveUserData"
               @cancel="closeCreateUserModal">
            <div class="container">
                <div class="form-group mb-3">
                    <div class="input-group input-group-merge input-group-alternative">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="ni ni-single-02"></i></span>
                        </div>
                        <input v-model="newUser.name" class="form-control" placeholder="Inserte Nombre"
                               type="text">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group input-group-merge input-group-alternative">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="ni ni-email-83"></i></span>
                        </div>
                        <input v-model="newUser.email" class="form-control" placeholder="Inserte Dirección Email"
                               type="text">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group input-group-merge input-group-alternative">
                        <multi_select v-model="newUser.role" :options="lists.roles"
                                      label="name" track-by="id" placeholder="Seleccione el rol"></multi_select>
                    </div>
                </div>
            </div>
        </modal>
    </div>
</template>

<script>
import Modal from '../../components/utils/modal'
import simpleTable from "../../components/utils/simpleTable/simpleTable";
import simpleTableSwitchField from "../../components/utils/simpleTable/simpleTableSwitchField";
import dialog from "../../libs/custom/dialog";
import Multi_select from "../../components/utils/multiselect";

const baseApiUrl = process.env.API_BASE_URL

export default {
    name: 'Users',
    components: {
        Modal,
        simpleTable,
        simpleTableSwitchField,
        Multi_select,
    },

    data() {
        return {
            isLoading: false,
            userTableKey: 0,
            newUser: {
                name: null,
                email: null,
                role: null,
            },
            lists: {
                roles: []
            },
            usersFields: [
                {
                    name: 'name',
                    title: 'Nombre',
                    sortField: 'name',
                    titleClass: "text-left",
                    dataClass: "text-left",
                },
                {
                    name: 'email',
                    title: 'Email',
                    sortField: 'email',
                    titleClass: "text-left",
                    dataClass: "text-left",
                },
                {
                    name: 'roles',
                    title: 'Roles',
                    titleClass: "text-left",
                    dataClass: "text-left",
                    formatter: (value) => {
                        let span = ''
                        value.forEach(item => {
                            span += '<span class="badge badge-default badge-md ml-1 mr-1 text-white">' + item['name'] + '</span>'
                        })

                        return span
                    }
                },
                {
                    name: "actions-slot",
                    title: 'Actions',
                    titleClass: "text-center",
                    dataClass: "text-center",
                },
            ],
            showNewUserModal: false,
        }
    },

    computed: {
        usersUrl() {
            return route('users.all')
        },
    },

    watch: {
    },

    methods: {
        reloadTable(tableReference, vuetableReference) {
            this.$refs[tableReference].$refs[vuetableReference].reload()
        },

        resetUserData() {
            this.newUser = {
                name: null,
                email: null,
                password: null,
                role: null,
            }
        },

        closeCreateUserModal() {
            this.showNewUserModal = false
            this.resetUserData()
        },

        openCreateEditUserModal(userData) {
            if (userData.id) {
                this.newUser.id = userData.id
                this.newUser.name = userData.name
                this.newUser.email = userData.email
                this.newUser.role = userData.roles[0].id
            }
            this.showNewUserModal = true
        },

        saveUserData() {
            this.isLoading = true
            let url = this.newUser.id ? route('users.edit', this.newUser.id) : route('users.create')
            axios.post(url, this.newUser)
                .then(response => {
                    if (response.status === 200) {
                        this.isLoading = false
                        dialog.success(response.data.message)
                        this.closeCreateUserModal()
                        this.reloadTable('userTable', 'userVueTable')
                        this.getLists(['roles'])
                    } else {
                        console.log(response.data)
                        dialog.error()
                    }
                }).catch(error => {
                this.isLoading = false
                if (!error.response) {
                    // network error
                    this.errorStatus = 'Error: Problemas de Conexión';
                    dialog.error(this.errorStatus)
                } else {
                    this.errorStatus = error.response.data.message;
                    dialog.error(this.errorStatus, error.response.data.errors)
                }
            })
        },

        deleteUser(id) {
            this.$swal({
                icon: 'warning',
                title: 'Está seguro de que desea eliminar este usuario?',
                showCancelButton: true,
                confirmButtonText: `Eliminar`,
                cancelButtonText: `Cancelar`,
            }).then((result) => {
                if (result.isConfirmed) {
                    this.isLoading = true
                    axios.delete(route('users.remove', id))
                        .then(response => {
                            if (response.status === 200) {
                                this.isLoading = false
                                dialog.success(response.data.message)
                                this.reloadTable('userTable', 'userVueTable')
                            } else {
                                this.isLoading = false
                                dialog.error()
                            }
                        }).catch(error => {
                        this.isLoading = false
                        if (!error.response) {
                            // network error
                            this.errorStatus = 'Error: Problemas de Conexión';
                            dialog.error(this.errorStatus)
                        } else {
                            this.errorStatus = error.response.data.message;
                            dialog.error(this.errorStatus)
                        }
                    })
                }
            })
        },

        getLists(list) {
            let url = route('defaults.lists')
            axios.get(url, {params: {lists: JSON.stringify(list)}})
                .then(response => {
                    if (response.status === 200) {
                        this.lists = response.data.lists
                    } else {
                        dialog.error(response.data.message)
                    }
                }).catch(error => {
                this.isLoading = false
                if (!error.response) {
                    // network error
                    this.errorStatus = 'Error: Problemas de Conexión';
                    dialog.error(this.errorStatus)
                } else {
                    this.errorStatus = error.response.data.message;
                    dialog.error(this.errorStatus)
                }
            })
        },
    },

    created() {

    },

    mounted() {
        this.getLists(['roles'])
    }
}
</script>

<style scoped>
.is_disabled {
    pointer-events: none;
    cursor: default;
    opacity: 0.3;
}
</style>

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
                                    <h2 class="mb-0">Roles</h2>
                                </div>
                                <div class="col">
                                    <ul class="nav nav-pills justify-content-end">
                                        <li class="nav-item mr-2 mr-md-0">
                                            <a @click.prevent="openCreateEditRoleModal" href="#"
                                               class="nav-link py-2 px-3 active">
                                                <span class="d-none d-md-block">+ Nuevo Rol</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <simple-table reference="roleVueTable"
                                          :key="roleTableKey"
                                          ref="roleTable"
                                          :api-url="rolesUrl"
                                          :has-settings="false"
                                          :has-header="false"
                                          :fields="rolesFields"
                                          :per-page="5"
                                          paginationFontSize="small"
                                          :hasCustomActions="true">
                                <template slot="custom-actions" slot-scope="props">
                                    <button type="button" class="btn btn-secondary btn-icon-only rounded-circle"
                                            @click="openCreateEditRoleModal(props.props.rowData)">
                                        <span class="btn-inner--icon"><i class="fa fa-edit"></i></span>
                                    </button>
                                    <button type="button" class="btn btn-primary btn-icon-only rounded-circle"
                                            @click="deleteRole(props.props.rowData.id)">
                                        <span class="btn-inner--icon"><i class="fa fa-trash"></i></span>
                                    </button>
                                </template>
                            </simple-table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <modal title="Añadir Rol"
               :show="showNewRoleModal"
               @accept="saveRoleData"
               @cancel="closeCreateRoleModal">
            <div class="container">
                <div class="form-group mb-3">
                    <div class="input-group input-group-merge input-group-alternative">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="ni ni-single-02"></i></span>
                        </div>
                        <input v-model="newRole.name" class="form-control" placeholder="Inserte Nombre"
                               type="text">
                    </div>
                </div>
                <div class="row" v-if="newRole.id">
                    <div class="col-6">
                        <div class="form-group mb-3">
                            <div class="input-group input-group-merge input-group-alternative">
                                <multi_select v-model="moduleSelected" :options="permission.modules"
                                              label="filename" track-by="id" placeholder="Modulo"></multi_select>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group mb-3">
                            <div class="input-group input-group-merge input-group-alternative">
                                <multi_select v-model="subModuleSelected" :options="permission.submodules"
                                              placeholder="Sub-modulo"></multi_select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="container" :key="permissionListKey" v-if="newRole.id">
                    <div class="row">
                        <div class="col" v-for="item in permission.actions">
                            <div class="form-group mb-3">
                                <button type="button" class="btn btn-outline-primary"
                                        @click="toggleActivatePermission($event)"
                                        :class="{'active': newRole.id ? hasPermission(item) : false}"
                                        data-toggle="button" v-text="item" :value="getPermission(item)"></button>
                            </div>
                        </div>
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

export default {
    name: 'Roles',
    components: {
        Modal,
        simpleTable,
        simpleTableSwitchField,
        Multi_select,
    },

    data() {
        return {
            permissionListKey: 0,
            hasConnection: false,
            moduleSelected: null,
            subModuleSelected: null,
            isLoading: false,
            roleTableKey: 0,
            permission: {
                modules: [],
                submodules: [],
                actions: [],
            },
            newRole: {
                name: null,
                permissions: [],
            },
            lists: {
                permissions: []
            },
            rolesFields: [
                {
                    name: 'name',
                    title: 'Nombre',
                    sortField: 'name',
                    titleClass: "text-left",
                    dataClass: "text-left",
                },
                {
                    name: 'guard_name',
                    title: 'Guard',
                    sortField: 'guard_name',
                    titleClass: "text-left",
                    dataClass: "text-left",
                },
                {
                    name: "actions-slot",
                    title: 'Actions',
                    titleClass: "text-center",
                    dataClass: "text-center",
                },
            ],
            showNewRoleModal: false,
        }
    },

    computed: {
        rolesUrl() {
            return route('roles.all')
        },
    },

    watch: {
        'lists.permissions'(value) {
            if (value.length) {
                this.preparePermissionModules(value)
            }
        },

        moduleSelected(value) {
            if (value) {
                this.preparePermissionSubmodule(this.permission.modules[value])
            }

            this.permissionListKey++
        },

        subModuleSelected(value) {
            if (value) {
                this.preparePermissionActions(this.permission.submodules[value])
            }

            this.permissionListKey++
        },

        'permission.submodules'(value) {
            if (value.length) {
                this.subModuleSelected = 0
                this.preparePermissionActions(this.permission.submodules[0])
            }
        }
    },

    methods: {
        getPermission(action) {
            return `${this.permission.modules[this.moduleSelected]}.${this.permission.submodules[this.subModuleSelected]}.${action}`
        },

        toggleActivatePermission($event) {
            let activate = !$event.target.classList.contains('active')
            let permissionName = $event.target.value
            axios.post(route('roles.add_permission', this.newRole.id), {'activate': activate, 'permissionName': permissionName})
                .then(response => {
                    if (response.status === 200) {
                        this.isLoading = false
                        dialog.success(response.data.message)
                        this.getLists(['permissions'])
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

        hasPermission(action) {
            let permissionString = `${this.permission.modules[this.moduleSelected]}.${this.permission.submodules[this.subModuleSelected]}.${action}`
            const asserting = this.newRole.permissions.filter(function (item) {
                return item.name == permissionString
            })

            return asserting.length > 0
        },

        preparePermissionModules(value) {
            let permissionModules = []
            value.forEach(function (item) {
                let explode = item.name.split('.')
                if (!permissionModules.includes(explode[0])) {
                    permissionModules.push(explode[0])
                }
            })

            this.permission.modules = permissionModules
        },

        preparePermissionSubmodule(value) {
            let permissionSubModules = []
            this.lists.permissions.forEach(function (item) {
                let explode = item.name.split('.')
                if (explode[0] == value && !permissionSubModules.includes(explode[1])) {
                    permissionSubModules.push(explode[1])
                }
            })

            this.permission.submodules = permissionSubModules
        },

        preparePermissionActions(value) {
            let permissionActions = []
            let permissionModule = this.permission.modules[this.moduleSelected];
            this.lists.permissions.forEach(function (item) {
                let explode = item.name.split('.')
                if (explode[0] == permissionModule && explode[1] == value) {
                    permissionActions.push(explode[2])
                }
            })

            this.permission.actions = permissionActions
        },

        reloadTable(tableReference, vuetableReference) {
            this.$refs[tableReference].$refs[vuetableReference].reload()
        },

        resetRoleData() {
            this.newRole = {
                name: null,
                permissions: [],
            }
            this.subModuleSelected = null
            this.moduleSelected = null
        },

        closeCreateRoleModal() {
            this.showNewRoleModal = false
            this.resetRoleData()
        },

        openCreateEditRoleModal(roleData) {
            if (roleData.id) {
                this.newRole.id = roleData.id
                this.newRole.name = roleData.name
                this.newRole.permissions = roleData.permissions
            }
            this.showNewRoleModal = true
        },

        saveRoleData() {
            this.isLoading = true
            let url = this.newRole.id ? route('roles.edit', this.newRole.id) : route('roles.create')
            axios.post(url, this.newRole)
                .then(response => {
                    if (response.status === 200) {
                        this.isLoading = false
                        dialog.success(response.data.message)
                        this.closeCreateRoleModal()
                        this.reloadTable('roleTable', 'roleVueTable')
                        this.getLists(['permissions'])
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

        deleteRole(id) {
            this.$swal({
                icon: 'warning',
                title: 'Está seguro de que desea eliminar este rol?',
                showCancelButton: true,
                confirmButtonText: `Eliminar`,
                cancelButtonText: `Cancelar`,
            }).then((result) => {
                if (result.isConfirmed) {
                    this.isLoading = true
                    axios.delete(route('roles.remove', id))
                        .then(response => {
                            if (response.status === 200) {
                                this.isLoading = false
                                dialog.success(response.data.message)
                                this.reloadTable('roleTable', 'roleVueTable')
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
        this.getLists(['permissions'])
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

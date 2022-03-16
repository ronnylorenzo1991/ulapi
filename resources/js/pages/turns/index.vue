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
                                    <h2 class="mb-0">Turnos</h2>
                                </div>
                                <div class="col">
                                    <ul class="nav nav-pills justify-content-end">
                                        <li class="nav-item mr-2 mr-md-0">
                                            <a @click.prevent="openCreateEditTurnModal" href="#"
                                               class="nav-link py-2 px-3 active">
                                                <span class="d-none d-md-block">+ Nuevo Turno</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <simple-table reference="turnVueTable"
                                          :key="turnTableKey"
                                          ref="turnTable"
                                          :api-url="turnsUrl"
                                          :has-settings="false"
                                          :has-header="false"
                                          :fields="turnsFields"
                                          :per-page="5"
                                          paginationFontSize="small"
                                          :hasCustomActions="true">
                                <template slot="custom-actions" slot-scope="props">
                                    <button type="button" title="Editar" class="btn btn-secondary btn-icon-only rounded-circle"
                                            @click="openCreateEditTurnModal(props.props.rowData)">
                                        <span class="btn-inner--icon"><i class="fa fa-edit"></i></span>
                                    </button>
                                    <button type="button" title="Confirmar" v-if="props.props.rowData.status_id === 1" class="btn btn-secondary btn-icon-only rounded-circle"
                                            @click="confirmTurn(props.props.rowData)">
                                        <span class="btn-inner--icon"><i class="fa fa-check-circle"></i></span>
                                    </button>
                                    <button type="button" title="Pasar a Pendiente" v-else class="btn btn-secondary btn-icon-only rounded-circle"
                                            @click="pendingTurn(props.props.rowData)">
                                        <span class="btn-inner--icon"><i class="fa fa-clock"></i></span>
                                    </button>
                                    <button type="button" title="Cotizar" v-if="props.props.rowData.status_id >= 2" class="btn btn-secondary btn-icon-only rounded-circle"
                                            @click="addTurnPayment(props.props.rowData)">
                                        <span class="btn-inner--icon"><i class="fa fa-money-bill"></i></span>
                                    </button>
                                    <button type="button" title="Eliminar" class="btn btn-primary btn-icon-only rounded-circle"
                                            @click="deleteTurn(props.props.rowData.id)">
                                        <span class="btn-inner--icon"><i class="fa fa-trash"></i></span>
                                    </button>
                                </template>
                            </simple-table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <modal :show="showNewTurnModal"
               @accept="saveTurnData"
               @cancel="closeCreateTurnModal">
            <div class="container" v-show="!forPayment">
                <div class="form-group mb-3">
                    <div class="input-group input-group-merge input-group-alternative">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="ni ni-calendar-grid-58"></i></span>
                        </div>
                        <flatPicker class="form-control datepicker pl-2" placeholder="Seleccionar fecha"
                                    :config="{ dateFormat: 'Y-m-d' }"
                                    v-model="newTurn.date"/>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <div class="input-group input-group-merge input-group-alternative">
                        <multi_select v-model="newTurn.user_id" :options="lists.clients"
                                      label="name" track-by="id" placeholder="Seleccione una Clienta"></multi_select>
                    </div>
                </div>

                <div class="m-3">
                    <div class="custom-control custom-radio mb-3 pt-3" v-for="(item,key) in availableTimes">
                        <input type="radio" :id="'available_time_radio_' + key" name="available_time_radio"
                               class="custom-control-input" :value="item" v-model="newTurn.time">
                        <label class="custom-control-label"
                               :for="'available_time_radio_' + key"
                               v-text="item"></label>
                    </div>
                </div>
            </div>
            <div class="container" v-show="forPayment">
                <div class="form-group mb-3">
                    <div class="input-group input-group-merge input-group-alternative">
                        <input type="text" class="form-control" v-model="newTurn.payment" placeholder="Añadir pago">
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
import TurnModal from "../dashboard/partials/TurnModal";
import flatPicker from "vue-flatpickr-component";

export default {
    name: 'Turns',
    components: {
        Modal,
        simpleTable,
        simpleTableSwitchField,
        Multi_select,
        TurnModal,
        flatPicker,
    },

    data() {
        return {
            forPayment: false,
            isLoading: false,
            turnTableKey: 0,
            newTurn: {
                time: null,
                date: null,
                user_id: false,
            },
            lists: {
                clients: []
            },
            availableTimes: [
                '08:00:00',
                '10:00:00',
                '14:00:00',
                '16:00:00'
            ],
            turnsFields: [
                {
                    name: 'date',
                    title: 'Fecha',
                    sortField: 'date',
                    titleClass: "text-left",
                    dataClass: "text-left",
                    formatter: (value) => {
                        return value.slice(0, 10)
                    }
                },
                {
                    name: 'time',
                    title: 'Horario',
                    sortField: 'time',
                    titleClass: "text-left",
                    dataClass: "text-left",
                },
                {
                    name: 'user',
                    title: 'Clienta',
                    sortField: 'users.name',
                    titleClass: "text-left",
                    dataClass: "text-left",
                    formatter: (value) => {
                        return '<span class="badge badge-default badge-md ml-1 mr-1 text-white">' + value['name'] + '</span>'
                    }
                },
                {
                    name: 'status',
                    title: 'Estado',
                    sortField: 'turn_statuses.name',
                    titleClass: "text-left",
                    dataClass: "text-left",
                    formatter: (value) => {
                        return this.getStatusBadge(value)
                    }
                },
                {
                    name: 'payment',
                    title: 'Pagado',
                    sortField: 'payment',
                    titleClass: "text-center",
                    dataClass: "text-center",
                    formatter: (value) => {
                        return value != null ? '<span>$' + value + '</span>' : "$0"
                    }
                },
                {
                    name: "actions-slot",
                    title: 'Actions',
                    titleClass: "text-right",
                    dataClass: "text-right",
                },
            ],
            showNewTurnModal: false,
        }
    },

    computed: {
        turnsUrl() {
            return route('turns.all')
        },

    },

    watch: {
    },

    methods: {
        getStatusBadge(value) {
            switch (value.id) {
                case 1:
                    return '<span class="badge badge-warning badge-md ml-1 mr-1 text-black">' + value['name'] + '</span>';
                case 2:
                    return '<span class="badge badge-info badge-md ml-1 mr-1 text-blue">' + value['name'] + '</span>';
                case 3:
                    return '<span class="badge badge-success badge-md ml-1 mr-1 text-black">' + value['name'] + '</span>';
            }
        },

        reloadTable(tableReference, vuetableReference) {
            this.$refs[tableReference].$refs[vuetableReference].reload()
        },

        resetTurnData() {
            this.newTurn = {
                time: null,
                date: null,
                turn_id: false,
            }
        },

        closeCreateTurnModal() {
            this.showNewTurnModal = false
            this.forPayment = false
            this.resetTurnData()
        },

        openCreateEditTurnModal(data) {
            if (data.id) {
                this.newTurn.id = data.id
                this.newTurn.time = data.time
                this.newTurn.date = data.date
                this.newTurn.user_id = data.user_id
            }
            this.showNewTurnModal = true
        },

        saveTurnData() {
            this.isLoading = true
            let url = this.newTurn.id ? route('turns.edit', this.newTurn.id) : route('turns.create')
            if (this.forPayment) {
                this.newTurn.status_id = 3
            }
            axios.post(url, this.newTurn)
                .then(response => {
                    if (response.status === 200) {
                        this.isLoading = false
                        dialog.success(response.data.message)
                        this.closeCreateTurnModal()
                        this.reloadTable('turnTable', 'turnVueTable')
                        this.getLists(['clients'])
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

        deleteTurn(id) {
            this.$swal({
                icon: 'warning',
                title: 'Está seguro de que desea eliminar este turno?',
                showCancelButton: true,
                confirmButtonText: `Eliminar`,
                cancelButtonText: `Cancelar`,
            }).then((result) => {
                if (result.isConfirmed) {
                    this.isLoading = true
                    axios.delete(route('turns.remove', id))
                        .then(response => {
                            if (response.status === 200) {
                                this.isLoading = false
                                dialog.success(response.data.message)
                                this.reloadTable('turnTable', 'turnVueTable')
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

        changeTurnStatus(turn, status) {
            this.isLoading = true
            axios.post(route('turns.edit', turn.id),
                {
                    time: turn.time,
                    user_id: turn.user_id,
                    status_id: status,
                    date: turn.date,
                })
                .then(response => {
                    if (response.status === 200) {
                        this.isLoading = false
                        dialog.success(response.data.message)
                        this.reloadTable('turnTable', 'turnVueTable')
                    } else {
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

        confirmTurn(turn) {
            this.changeTurnStatus(turn, 2)
        },

        pendingTurn(turn) {
            this.changeTurnStatus(turn, 1)
        },

        addTurnPayment(turn) {
            this.forPayment = true
            this.openCreateEditTurnModal(turn)
        },
    },

    created() {

    },

    mounted() {
        this.getLists(['clients'])
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

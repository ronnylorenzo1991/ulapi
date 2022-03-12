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
                                    <h2 class="mb-0">Calendario</h2>
                                </div>
                                <div class="col">
                                    <ul class="nav nav-pills justify-content-end">
                                        <li class="nav-item mr-2 mr-md-0">
                                            <a @click.prevent="openTurnModal()" href="#"
                                               class="nav-link py-2 px-3 active">
                                                <span class="d-none d-md-block">+ Nuevo Turno</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <Calendar :show-header-menu="true"
                                  :url-events="turnsUrl"
                                  @removeEvent="removeTurn"
                                  @eventDrop="updateTurnDate"
                                  @editEvent="editTurn"
                                  @addEvent="addTurn"
                                  @addPayment="addTurnPayment"
                                  @confirmEvent="confirmTurn"
                                  @pendingEvent="pendingTurn"
                                  ref="calendar"
                        />
                    </div>
                </div>
            </div>
        </div>
        <turn-modal :current-turn="currentTurn"
                    :lists="lists"
                    :available-times="availableTimes"
                    :show="showTurnModal"
                    :forPayment="forPayment"
                    @close="closeTurnModal"
                    :key="turnModalKey"/>

    </div>
</template>
<script>
import Modal from '../../components/utils/modal'
import Calendar from '../../components/Calendar/Calendar'
import flatPicker from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import dialog from "../../libs/custom/dialog";
import TurnModal from "./partials/TurnModal";
import format from "date-fns/format";


export default {
    name: "dashboard",

    components: {
        TurnModal,
        Modal,
        Calendar,
        flatPicker
    },

    data: function () {
        return {
            turnModalKey: 0,
            forPayment: false,
            turnsUrl: route('calendar.all'),
            lists: {},
            isLoading: false,
            showTurnModal: false,
            currentTurn: {},
            defaultTurn: {
                time: null,
                date: null,
                request: false,
                user_id: false,
            },
            availableTimes: [
                '08:00:00',
                '10:00:00',
                '14:00:00',
                '16:00:00'
            ]
        }
    },

    methods: {
        removeTurn(turn) {
            dialog.confirm(null, 'Seguro que desea eliminar el turno?').then(confirmed => {
                if (!confirmed) {
                    return
                }

                this.isLoading = true
                axios.delete(route('turns.remove', turn.extendedProps.db_id))
                    .then(response => {
                        if (response.status === 200) {
                            this.isLoading = false
                            dialog.success(response.data.message)
                            this.reloadCalendar()
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
            })
        },

        updateTurnDate(turn) {
            this.isLoading = true
            axios.post(route('turns.edit', turn.extendedProps.db_id),
                {
                    time: turn.extendedProps.time,
                    user_id: turn.extendedProps.user_id,
                    date: this.getDateFormatted(turn.start),
                })
                .then(response => {
                    if (response.status === 200) {
                        this.isLoading = false
                        this.reloadCalendar()
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

        changeTurnStatus(turn, status) {
            this.isLoading = true
            axios.post(route('turns.edit', turn.extendedProps.db_id),
                {
                    time: turn.extendedProps.time,
                    user_id: turn.extendedProps.user_id,
                    status_id: status,
                    date: this.getDateFormatted(turn.start),
                })
                .then(response => {
                    if (response.status === 200) {
                        this.isLoading = false
                        this.reloadCalendar()
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

        addTurnPayment(turn) {
            this.forPayment = true
            this.editTurn(turn)
        },

        confirmTurn(turn) {
            this.changeTurnStatus(turn, 2)
        },

        pendingTurn(turn) {
            this.changeTurnStatus(turn, 1)
        },

        editTurn(turn) {
            this.openTurnModal(false, turn.extendedProps.db_id)
        },

        reloadCalendar() {
            this.$refs.calendar.refreshEvents()
        },

        addTurn(pointerDate) {
            this.isLoading = true
            this.currentTurn = this.defaultTurn
            this.currentTurn.date = pointerDate
            this.showTurnModal = true
            this.isLoading = false
        },

        openTurnModal(create = true, turnId) {
            this.isLoading = true
            if (create) {
                this.currentTurn = this.defaultTurn
                this.showTurnModal = true
                this.isLoading = false
                return
            }

            axios.get(route('turns.show', turnId))
                .then(response => {
                    if (response.status === 200) {
                        this.isLoading = false
                        this.currentTurn = {
                            id: response.data.turn.id,
                            time: response.data.turn.time,
                            date: this.getDateFormatted(response.data.turn.date),
                            request: response.data.turn.request,
                            user_id: response.data.turn.user_id,
                        }
                        if (this.forPayment) {
                            this.currentTurn.payment = response.data.turn.payment
                        }
                        this.showTurnModal = true
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


        },

        getDateFormatted(date) {
           return format(new Date(new Date(date).toISOString().slice(0, 10) + 'T00:00'),'yyyy-MM-dd')
        },

        closeTurnModal() {
            this.turnModalKey++
            this.showTurnModal = false
            this.forPayment = false
            this.reloadCalendar()
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
    mounted() {
        this.getLists(['permissions', 'clients'])
    }
}
</script>

<style scoped>

</style>

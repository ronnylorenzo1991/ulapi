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
                                            <a @click.prevent="toggleShowMenuModal(true)" href="#"
                                               class="nav-link py-2 px-3 active">
                                                <span class="d-none d-md-block">+ Nuevo Turno</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <Calendar :show-header-menu="false"
                                  :url-events="turnsUrl"></Calendar>
                    </div>
                </div>
            </div>
        </div>
        <modal title="Añadir Turno"
               :show="showNewTurnModal"
               @accept="saveTurnData"
               @cancel="toggleShowMenuModal(false)">
            <div class="container">
                <div class="form-group mb-3">
                    <div class="input-group input-group-merge input-group-alternative">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="ni ni-calendar-grid-58"></i></span>
                        </div>
                        <flatPicker class="form-control datepicker pl-2" placeholder="Seleccionar fecha" v-model="newTurn.date"/>
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
            </div>
        </modal>
    </div>
</template>
<script>
import Modal from '../../components/utils/modal'
import Calendar from '../../components/Calendar/calendar'
import flatPicker from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import dialog from "../../libs/custom/dialog";


export default {
    name: "dashboard",

    components: {
        Modal,
        Calendar,
        flatPicker
    },

    data: function () {
        return {
            turnsUrl: route('calendar.all'),
            lists: {},
            isLoading: false,
            showNewTurnModal: false,
            newTurn: {
                time: null,
                date: null,
                request: false,
            },
            availableTimes: [
                '8:00',
                '10:00',
                '14:00',
                '16:00'
            ]
        }
    },

    methods: {
        toggleShowMenuModal($force = null) {
            this.showNewTurnModal = $force != null ? $force : !this.showNewTurnModal
        },

        saveTurnData() {
            this.isLoading = true
            let url = this.newTurn.id ? route('turns.edit', this.newTurn.id) : route('turns.create')
            axios.post(url, this.newTurn)
                .then(response => {
                    if (response.status === 200) {
                        this.isLoading = false
                        dialog.success(response.data.message)
                        this.toggleShowMenuModal()
                        this.reloadCalendar()
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

        reloadCalendar() {

        }
    }
}
</script>

<style scoped>

</style>

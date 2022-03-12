<template>
    <modal :title="getTitle"
           :show="show"
           v-if="show"
           @accept="saveTurnData"
           @cancel="closeModal">
        <div class="container" v-show="!forPayment">
            <div class="form-group mb-3">
                <div class="input-group input-group-merge input-group-alternative">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="ni ni-calendar-grid-58"></i></span>
                    </div>
                    <flatPicker class="form-control datepicker pl-2" placeholder="Seleccionar fecha" :config="{ dateFormat: 'Y-m-d' }"
                                v-model="turn.date"/>
                </div>
            </div>

            <div class="form-group mb-3">
                <div class="input-group input-group-merge input-group-alternative">
                    <multi_select v-model="turn.user_id" :options="lists.clients"
                                  label="name" track-by="id" placeholder="Seleccione una Clienta"></multi_select>
                </div>
            </div>

            <div class="m-3">
                <div class="custom-control custom-radio mb-3 pt-3" v-for="(item,key) in availableTimes">
                    <input type="radio" :id="'available_time_radio_' + key" name="available_time_radio"
                           class="custom-control-input" :value="item" v-model="turn.time">
                    <label class="custom-control-label"
                           :for="'available_time_radio_' + key"
                           v-text="item"></label>
                </div>
            </div>
        </div>
        <div class="container" v-show="forPayment">
            <div class="form-group mb-3">
                <div class="input-group input-group-merge input-group-alternative">
                    <input type="text" class="form-control" v-model="turn.payment"  placeholder="Añadir pago">
                </div>
            </div>
        </div>
    </modal>
</template>

<script>
import Modal from '../../../components/utils/modal'
import dialog from "../../../libs/custom/dialog";
import flatPicker from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";

export default {
    name: "TurnModal",

    components: {
        Modal,
        flatPicker
    },

    props: {
        show: {
            type: Boolean,
            default: false,
        },
        forPayment: {
            type: Boolean,
            default: false,
        },
        lists: {
            required: true
        },
        currentTurn: {
            required: true,
            default: {
                time: null,
                date: null,
                request: false,
                user_id: false,
            }
        },
        availableTimes: {
            required: true
        }
    },

    data() {
        return {
            turn: {},
        }
    },
    computed: {
        getTitle() {
            return this.turn.hasOwnProperty('id') ? 'Editar Turno' : 'Añadir Turno'
        }
    },

    watch: {
        currentTurn(value) {
            this.turn = value
        }
    },

    methods: {
        saveTurnData() {
            this.isLoading = true
            let url = this.turn.id ? route('turns.edit', this.turn.id) : route('turns.create')
            if (this.forPayment) {
                this.turn.status_id = 3
            }
            axios.post(url, this.turn)
                .then(response => {
                    if (response.status === 200) {
                        this.isLoading = false
                        dialog.success(response.data.message)
                        this.$emit('close')
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

        closeModal() {
            this.$emit('close')
        }
    }
}
</script>

<style scoped>

</style>

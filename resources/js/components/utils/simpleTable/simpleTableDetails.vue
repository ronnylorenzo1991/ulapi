<template>
    <div>
        <table class="table align-items-center table-flush">
            <thead class="thead-medium-dark">
            <tr>
                <th scope="col" class="sort">Estado</th>
                <th scope="col" class="sort">Inicio</th>
                <th scope="col" class="sort">Fin</th>
                <th scope="col" class="sort">Modelo</th>
                <th scope="col" class="sort">Actividad</th>
                <th scope="col">Acciones</th>
            </tr>
            </thead>
            <tbody class="list">
            <tr v-for="item in rowData.tasks">
                <td>
                      <span class="badge badge-dot mr-4">
                        <i class="bg-warning"></i>
                        <span class="status">{{ getStatusLabel(item.status) }}</span>
                      </span>
                </td>
                <td class="budget">
                    {{ item.start }}
                </td>
                <td class="budget">
                    {{ item.end }}
                </td>
                <td class="budget">
                    {{ item.weight.filename }}
                </td>
                <td>
                    <div class="ui toggle checkbox">
                        <label class="custom-toggle">
                            <input type="checkbox" @change="toggleSwitch($event, item.id, vuetable)" :checked="item.status == 2">
                            <span class="custom-toggle-slider rounded-circle"></span>
                        </label>
                    </div>
                </td>
                <td>
                    <button type="button" class="btn btn-secondary btn-icon-only rounded-circle"
                            @click="openCreateEditCameraModal(props.props)">
                        <span class="btn-inner--icon"><i class="fa fa-edit"></i></span>
                    </button>
                    <button type="button" class="btn btn-primary btn-icon-only rounded-circle"
                            @click="deleteCamera(props.props.id)">
                        <span class="btn-inner--icon"><i class="fa fa-trash"></i></span>
                    </button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
import VuetableFieldMixin from 'vuetable-2/src/components/VuetableFieldMixin'

export default {
    name: "simpleTableDetails",

    mixins: [VuetableFieldMixin],

    props: {
        rowData: {
            type: Object,
        },
        rowIndex: {
            type: Number
        },
        options: {
            type: Object,
        }
    },

    methods: {
        getStatusLabel(statusId) {
            const statuses = [
                'Detenido',
                'Pendiente',
                'En Proceso',
            ]

            return statuses[statusId]
        },

        toggleSwitch(event, id, vuetable) {
            console.log(vuetable)
            vuetable.$emit('toggleSwitchActivate', {'id': id,'value': event.target.checked, 'field': 'task'})
        },
    },
}
</script>

<style scoped>
.thead-medium-dark {
    color: #252f41;
    background-color: #98b2de;
    border-color: #1f3a68;
}
</style>

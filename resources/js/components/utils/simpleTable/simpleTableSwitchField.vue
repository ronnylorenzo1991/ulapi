<template>
    <th v-if="isHeader"
        class="vuetable-th-component-switch"
        v-html="title">
    </th>
    <td v-else
        class="vuetable-td-component-switch">
        <div class="ui toggle checkbox">
            <label class="custom-toggle">
                <input type="checkbox" @change="toggleSwitch(rowData, rowField, $event, vuetable)" :checked="isRowChecked(rowData)">
                <span class="custom-toggle-slider rounded-circle"></span>
            </label>
            <label v-if="rowField.switch.label">{{ label(rowData, rowField) }}</label>
        </div>
    </td>
</template>
<script>
import VuetableFieldMixin from 'vuetable-2/src/components/VuetableFieldMixin'

export default {
    name: 'vuetable-field-switch',

    mixins: [VuetableFieldMixin],

    methods: {
        isChecked(event) {
            return event.target.checked
        },

        isRowChecked(rowData) {
            return rowData.activated || rowData.status == '2' || rowData.status == '1'
        },

        toggleSwitch(data, rowField, event, vuetable) {
            vuetable.$emit('toggleSwitchActivate', {'id': data.id,'value': event.target.checked, 'field': rowField.id})
        },

        label(rowData, rowField) {
            return typeof (rowField.switch.label) === 'function'
                ? rowField.switch.label(rowData)
                : rowField.switch.label
        },

        checkValue(rowData, rowField) {
            return typeof (rowField.switch.field) === 'function'
                ? rowField.switch.field(rowData)
                : rowData[rowField.switch.field]
        }
    }
}
</script>

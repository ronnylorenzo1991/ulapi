<template>
    <th v-if="isHeader"
        class="vuetable-th-component-switch"
        v-html="title">
    </th>
    <td v-else
        class="vuetable-td-component-switch">
        <div class="ui toggle checkbox">
            <label class="custom-toggle detail-btn">
                <input v-model="checked" type="checkbox" @change="toggleSwitch(rowData, rowField, $event, vuetable)">
                <span><i :class="arrowClass"></i></span>
            </label>
        </div>
    </td>
</template>
<script>
import VuetableFieldMixin from 'vuetable-2/src/components/VuetableFieldMixin'

export default {
    name: 'vuetable-detail-field',

    mixins: [VuetableFieldMixin],

    data() {
        return {
            arrowClass: 'fa fa-chevron-right',
            checked: false,
        }
    },

    methods: {
        isChecked(event) {
            return event.target.checked
        },

        toggleSwitch(data, rowField, event, vuetable) {
            vuetable.$emit('toggleDetail', {'id': data.id,'value': event.target.checked, 'field': rowField.id})
            this.iconToggleClass()
        },

        iconToggleClass() {
            this.arrowClass = this.checked ? 'fa fa-chevron-down' : 'fa fa-chevron-right'
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
<style scoped>
.detail-btn {
    cursor: pointer !important;
}
</style>

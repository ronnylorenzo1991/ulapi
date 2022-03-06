<template>
    <multiselect :value="valueAsObject"
                 @input="inputValue($event)"
                 :options="optionsAsArray"
                 :label="label"
                 :track-by="trackBy"
                 :multiple="multiple"
                 :searchable="isSearchable"
                 :show-labels="false"
                 :allow-empty="false"
                 :data-curated-value="curatedValue"
                 :show-no-options="false"
                 :placeholder="localPlaceholder"
                 :open-direction="openDirection"
                 :disabled="localDisabled"
                 :class="className"
                 @search-change="onSearchValue($event)"
                 @close="onSelectClose" :style="'width:' + width">
        <span slot="noResult">No elements found.</span>
    </multiselect>
</template>

<script>
import Multiselect from 'vue-multiselect';

export default {
    name: "multi_select",
    props: {
        value: {
            default: 'value'
        },
        options: {
            type: [Array, Object],
            default: () => ({})
        },
        openDirection: {
            type: String,
            default: 'bottom'
        },
        multiple: {
            type: Boolean,
            default: false
        },
        searchable: {
            type: Boolean,
            default: null
        },
        placeholder: {
            type: String,
            default: 'Select'
        },
        label: {
            type: String,
            default: 'label'
        },
        trackBy: {
            type: String,
            default: 'id'
        },
        disabled: {
            type: Boolean,
            default: false
        },
        allowAll: {
            type: Boolean,
            default: false
        },
        allowAllText: {
            type: String,
            required: false,
            default: 'All'
        },
        showLoadingCaption: {
            type: Boolean,
            default: false
        },
        className: {
            type: Object,
            default: () => ({})
        },
        castTo: {
            type: String,
            default: null
        },

        width: {
            type: String,
            default: '100%'
        }
    },
    data() {
        return {
            curatedValue: null,
        }
    },

    watch: {
        valueAsObject(value) {
            if (!this.multiple) {
                if (!value) {
                    return
                }
                this.curatedValue = value[this.trackBy]
            }
        }
    },

    computed: {
        hasAllText() {
            return this.allowAllText !== 'All';
        },

        optionsAsArray() {
            let vm = this,
                options = [];

            if (this.allowAll || this.hasAllText) {
                options.push({
                    [this.trackBy]: -1,
                    [this.label]: this.allowAllText
                });
            }

            if (Array.isArray(this.options) &&
                this.options.length > 0 &&
                Object.keys(this.options[0]).includes(this.trackBy) &&
                Object.keys(this.options[0]).includes(this.label)) {
                return options.concat(this.options)
            }

            Object.keys(this.options).forEach(function (key) {
                options.push({
                    [vm.trackBy]: key,
                    [vm.label]: vm.options[key]
                });
            });

            return options;
        },

        valueAsObject() {
            if (this.multiple) {
                return this.curatedValue
            }

            if ((this.allowAll || this.hasAllText) && this.value === null) {
                return {
                    [this.trackBy]: -1,
                    [this.label]: this.allowAllText
                }
            }

            let item = _.find(this.optionsAsArray, {[this.trackBy]: Number(this.value)});
            if (typeof item === 'undefined') {
                item = _.find(this.optionsAsArray, {[this.trackBy]: String(this.value)});
            }

            return typeof item !== 'undefined' ? item : null;
        },

        localDisabled() {
            return this.optionsAsArray.length === 0 || this.disabled;
        },

        localPlaceholder() {
            if (this.optionsAsArray.length === 0 && this.showLoadingCaption) {
                return 'Loading...';
            }

            if (this.optionsAsArray.length === 0) {
                return this.placeholder;
            }

            return this.placeholder;
        },

        isSearchable() {
            return this.searchable !== null
                ? this.searchable
                : this.optionsAsArray.length > 1
        }
    },

    methods: {
        inputValue(value) {
            if ((this.allowAll || this.hasAllText) && (value[this.trackBy] === '-1' || value[this.trackBy] === -1)) {
                this.$emit('select', null);
                return this.$emit('input', null);
            }

            if (value !== null) {
                if (this.multiple) {
                    this.curatedValue = value
                } else {
                    this.curatedValue = value[this.trackBy]
                    if (this.castTo === 'number') {
                        this.curatedValue = Number(this.curatedValue)
                    }
                }

                this.$emit('select', value);
                this.$emit('input', this.curatedValue);
            }
        },

        onSearchValue(value) {
            return this.$emit('search-change', value)
        },

        onSelectClose() {
            return this.$emit('close')
        },
    },

    components: {
        Multiselect
    }
}
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>

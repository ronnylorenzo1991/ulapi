<template>
    <div class="card shadow">
        <loader :show="isLoading" style="z-index: 10 !important;"/>
        <div class="card-header border-0" v-if="hasHeader">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="mb-0">{{ title }}</h3>
                </div>
                <div class="col text-right" v-if="hasSettings">
                    <a @click="$emit('onSettingsClick')" class="btn btn-sm btn-primary">{{ settingText }}</a>
                </div>
                <div class="col text-right" slot v-else>
                    <slot name="custom-settings"></slot>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <vue-table :ref="reference"
                       :api-url="apiUrl"
                       :fields="fields"
                       data-path="data"
                       pagination-path=""
                       :per-page="perPage"
                       @toggleSwitchActivate="toggleSwitchActivate"
                       @vuetable:pagination-data="onPaginationData"
                       :css="css.table"
                       detail-row-transition="fade"
                       @vuetable:loading="onLoading"
                       @vuetable:loaded="onLoaded">
                <template slot="actions-slot" slot-scope="props">
                    <div class="custom-actions" v-if="hasCustomActions">
                        <slot name="custom-actions" :props="props"></slot>
                    </div>
                    <div class="custom-actions" v-else>
                        <button type="button" class="btn btn-secondary btn-icon-only rounded-circle"
                                @click="onAction('show', props.rowData, props.rowIndex)">
                            <span class="btn-inner--icon"><i class="fa fa-eye"></i></span>
                        </button>
                        <button type="button" class="btn btn-primary btn-icon-only rounded-circle"
                                @click="onAction('delete', props.rowData, props.rowIndex)">
                            <span class="btn-inner--icon"><i class="fa fa-trash"></i></span>
                        </button>
                    </div>
                </template>
            </vue-table>
        </div>
        <div class="card-footer py-4">
            <nav aria-label="...">
                <div class="row" :style="'font-size:' + paginationFontSize">
                    <div class="col-sm-8">
                        <vuetable-pagination-info ref="paginationInfo" v-show="hasPaginationInfo">
                        </vuetable-pagination-info>
                    </div>
                    <div class="col-sm-4" >
                        <vuetable-pagination ref="pagination" v-show="hasPagination"
                                             @vuetable-pagination:change-page="onChangePage"
                                             :css="css.pagination"></vuetable-pagination>
                    </div>
                </div>
            </nav>
        </div>
    </div>
</template>

<script>
import VueTable from 'vuetable-2'
import VuetablePagination from "vuetable-2/src/components/VuetablePagination";
import VuetablePaginationInfo from 'vuetable-2/src/components/VuetablePaginationInfo';

export default {
    name: "simpleTable",
    components: {
        VueTable,
        VuetablePagination,
        VuetablePaginationInfo
    },
    props: {
        hasPaginationInfo: {
            type: Boolean,
            default: true
        },
        hasPagination: {
            type: Boolean,
            default: true
        },
        paginationFontSize: {
            type: String,
            default: 'large'
        },
        hasHeader: {
            type: Boolean,
            default: true
        },
        title: {
            type: String,
            default: ''
        },
        hasSettings: {
            type: Boolean,
            default: true
        },
        fields: {
            required: true,
            type: Array,
            default: []
        },
        settingText: {
            type: String,
            default: 'Settings'
        },
        perPage: {
            default: 5
        },
        apiUrl: {
            required: true,
        },
        reference: {
            required: true,
        },
        css: {
            type: Object,
            default: () => {
                return {
                    table: {
                        tableClass: 'table table-hover',
                        ascendingIcon: 'fa fa-sort-up',
                        descendingIcon: 'fa fa-sort-down',
                        sortableIcon: 'fa fa-sort'
                    },
                    pagination: {
                        wrapperClass: 'pagination justify-content-end mb-0',
                        activeClass: 'active',
                        disabledClass: 'is_disabled',
                        pageClass: 'page-link',
                        linkClass: 'page-link',
                        paginationClass: 'pagination',
                        paginationInfoClass: 'float-left',
                        dropdownClass: 'form-control',
                        icons: {
                            first: 'fa fa-chevron-left',
                            prev: 'fa fa-angle-left',
                            next: 'fa fa-angle-right',
                            last: 'fa fa-chevron-right',
                        }
                    }
                }
            }
        },
        hasCustomActions: {
            type: Boolean,
            default: false
        },
    },
    data() {
        return {
            isLoading: false
        }
    },
    computed: {},
    watch: {},
    methods: {
        onLoading() {
            this.isLoading = true
        },

        onLoaded() {
            this.isLoading = false
        },

        toggleSwitchActivate(data) {
            this.$emit('toggleField', data)
        },

        onPaginationData(paginationData) {
            this.$refs.pagination.setPaginationData(paginationData);
            this.$refs.paginationInfo.setPaginationData(paginationData);
        },

        onChangePage(page) {
            this.$refs[this.reference].changePage(page);
        },
        onAction(event, data, index) {
            this.$emit(event, data, index)
        },
    },
}
</script>

<style>
.is_disabled {
    pointer-events: none !important;
    cursor: default !important;
    opacity: 0.3 !important;
}

.active {
    background-color: #5e72e4 !important;
    color: white !important;
}

.pagination {
    margin-top: 1rem;
}
</style>

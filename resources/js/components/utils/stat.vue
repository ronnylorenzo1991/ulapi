<template>
    <div class="card card-stats mb-4 mb-xl-0">
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <h5 class="card-title text-uppercase text-muted mb-0">{{ source }}</h5>
                    <span class="h2 font-weight-bold mb-0">{{ total }}</span>
                </div>
                <div class="col-auto">
                    <div class="text-white rounded-circle shadow" :class="'icon icon-shape bg-' + color">
                        <i :class="icon"></i>
                    </div>
                </div>
            </div>
            <p class="mt-3 mb-0 text-muted text-sm" v-if="compare">
                <span class="text-success mr-2"><i class="fa fa-arrow-up"></i> 3.48%</span>
                <span class="text-nowrap">Since last month</span>
            </p>
        </div>
    </div>
</template>

<script>
export default {
    name: "stat",
    props: {
        source: {
            type: String,
            required: true
        },
        color: {
            type: String,
            default: 'red'
        },
        icon: {
            type: String,
            default: 'fas fa-chart-bar'
        },
        compare: {
            type: Boolean,
            default: false
        },
        apiUrl: {
            required: true
        }
    },
    data() {
        return {
            total: 0,
        }
    },
    watch: {
    },
    methods: {
        getTotalData() {
            axios.get(this.apiUrl + this.source)
            .then(response => {
                if (response.status === 200) {
                    this.total = response.data.total
                }
            })
        },
    },
    created() {
        this.getTotalData()
    }
}
</script>

<style scoped>

</style>

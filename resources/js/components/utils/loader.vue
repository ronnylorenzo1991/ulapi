<template>
    <vue-element-loading :active="show" spinner="spinner" color="#FF6700"/>
</template>

<script>
import VueElementLoading from 'vue-element-loading'

export default {
    components: {
        VueElementLoading
    },
    name: 'loader',
    props: {
        show: {
            type: Boolean,
            default: false
        }
    }
};
</script>

<template>
    <div>
        <transition enter-active-class="animated fadeIn zoomIn"
                    leave-active-class="animated fadeOut zoomOut">
            <div class="img-viewer" v-if="show">
                        <button type="button" class="close" aria-label="Close" @click="close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                <button type="button" class="btn btn-sm btn-primary"
                      :style="{top: coordinates[1] - 30 + 'px', left: coordinates[0] + 10 + 'px', 'z-index': 'auto', 'position': 'absolute'}">
                    {{ classes }}
                </button>
                <box-drawer :b-left="coordinates[0]"
                            :b-top="coordinates[1]"
                            :b-width="coordinates[2]"
                            :b-height="coordinates[3]" style="z-index: auto"/>
                <img width="100%" height="100%" :src="imageSrc" alt="">
            </div>
        </transition>

        <div class="mask" v-if="show">
        </div>
    </div>

</template>

<script>
import boxDrawer from "./boxDrawer";

export default {
    name: "imageViewer",

    components: {
        boxDrawer
    },
    props: {
        show: {
            type: Boolean,
            default: false,
        },
        imageSrc: {
            required: true,
        },
        alt: {
            required: true,
        },
        coordinates: {
            required: true,
        },
        classes: {
            required: true,
        }
    },
    methods:{
        close() {
            this.$emit('close')
        }
    }
}
</script>

<style scoped>

.img-viewer {
    position: fixed;
    max-width: 80%;
    top: 30%;
    left: 36%;
    border-radius: 3px;
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.9);
    z-index: 10000;
}

@media screen and (orientation: portrait) {
    .img-viewer img, .img-viewer .box-wrapper {
        max-width: 90%;
    }
}

@media screen and (orientation: landscape) {
    .img-viewer img, .img-viewer .box-wrapper {
        max-height: 90%;
    }
}

.mask {
    background-color: rgb(0, 0, 0);
    background-color: rgba(0, 0, 0, 0.4);
    /* now center the mask*/
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 100%;
    height: 110%;
    padding: 20px;
    text-align: center;
    z-index: 9999;
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.close > span {
    font-size: 1.25rem;
    line-height: 17px;
    display: block;
    width: 1.25rem;
    height: 1.25rem;
    transition: all .15s ease;
    color: rgba(0, 0, 0, .6);
    border-radius: 50%;
    background-color: #f9f9f9;
    padding-top: 2px;
}

.close {
    position:absolute;
    left:96%;
    top:2%;
}
</style>

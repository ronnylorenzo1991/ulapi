<template>
    <div>
    <transition name="fade">
        <div v-show="show" :id="modalId" class="modal fade show" role="dialog" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" :class="modalClass" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalBodyLabel"> {{ title }} </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="cancel">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <slot></slot>
                    </div>
                    <div :class="footerClassAlignment" v-if="!hiddenFooter">
                        <slot name="footer">
                            <slot name="first-buttons"></slot>

                            <button :id="cancelId" v-if="!hiddenCancelButton" type="button" :class="cancelButtonClass"
                                    @click="cancel" rel="prev" :data-dismiss="modalId"> {{ cancelText }}
                            </button>

                            <slot name="middle-buttons"></slot>

                            <button :id="acceptId" v-if="!hiddenAcceptButton" type="button" :class="acceptButtonClass"
                                    @click="accept" rel="next" :disabled="acceptButtonDisabled"> {{ acceptText }}
                            </button>

                        </slot>
                    </div>
                </div>
            </div>
        </div>
    </transition>
    <div class="modal-backdrop show" v-if="show"></div>
    </div>
</template>

<script>
export default {
    name: "modal",

    props: {
        size: {
            type: [String, Boolean],
            required: false,
            default: false
        },
        transition: {
            type: String,
            default: 'modal'
        },
        modalId: {
            type: String,
            default: 'modal'
        },
        show: {
            type: Boolean,
            default: false
        },
        title: {
            type: String,
            default: 'New Form'
        },
        acceptId: {
            type: String,
            default: 'acceptButton'
        },
        cancelId: {
            type: String,
            default: 'cancelButton'
        },
        hiddenFooter: {
            type: Boolean,
            default: false
        },
        hiddenAcceptButton: {
            type: Boolean,
            default: false
        },
        hiddenCancelButton: {
            type: Boolean,
            default: false
        },
        acceptButtonDisabled: {
            type: Boolean,
            default: false
        },
        acceptText: {
            type: String,
            default: 'aceptar'
        },
        cancelText: {
            type: String,
            default: 'cancelar'
        },
        cancelButtonClass: {
            type: String,
            default: 'btn --blue-empty --uppercase'
        },
        acceptButtonClass: {
            type: String,
            default: 'btn btn-primary --uppercase'
        },
    },

    computed: {
        footerClassAlignment() {
            return this.alignFooterContentToEnd ? 'modal-footer' : 'self-modal-footer';
        },
        modalClass() {
            const sizes = {
                small: 'modal-sm',
                'small-medium': 'modal-sm-md',
                medium: 'modal-md',
                large: 'modal-lg',
                extraLarge: 'modal-xl',
                full: 'modal-full'
            };

            if (this.size in sizes) {
                return sizes[this.size]
            }

            return {
                'modal-lg': this.large,
                'modal-sm': this.small,
                'modal-md': this.medium,
                'modal-full': this.full,
                'modal-xl': this.extraLarge
            };
        }
    },

    watch: {
        show(value) {
            if (value) {
                document.body.className += ' modal-open';
            }
            else {
                window.setTimeout(() => {
                    document.body.className = document.body.className.replace(/\s?modal-open/, '');
                }, this.duration || 0);
            }
        }
    },
    methods: {
        cancel() {
            this.$emit('cancel');
        },
        accept() {
            this.$emit('accept');
        },
    },
    created() {
        if (this.show) {
            document.body.className += ' modal-open';
        }
    },
    beforeDestroy() {
        document.body.className = document.body.className.replace(/\s?modal-open/, '');
    },
}
</script>

<style scoped>
.modal {
    display: block;
}

.modal-transition {
    transition: all .6s ease;
}

.modal-leave {
    border-radius: 1px !important;
}

.modal-transition .modal-dialog, .modal-transition .modal-backdrop {
    transition: all .5s ease;
}

.modal-enter .modal-dialog, .modal-leave .modal-dialog {
    opacity: 0;
    transform: translateY(-30%);
}

.modal-enter .modal-backdrop, .modal-leave .modal-backdrop {
    opacity: 0;
}

.modal-title {
    color: #2b343f;
    margin: 0;
    word-wrap: break-word;
    width: 100%;
    text-align: left;
}

.modal-body {
    padding: 20px;
}

.modal-footer button i {
    padding-right: 5px;
}

.modal-md {
    max-width: 750px;
}

.modal-footer button {
    min-width: 80px;
}

.self-modal-footer {
    align-items: center;
    padding: 1rem;
    border-top: 1px solid #e9ecef;
    border-bottom-right-radius: .3rem;
    border-bottom-left-radius: .3rem;
}

.align-end {
    justify-content: flex-end;
    display: flex;
}

.modal-sm-md {
    max-width: 670px;
}

.fade-enter-active, .fade-leave-active {
    transition: opacity .5s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
    opacity: 0;
}
</style>

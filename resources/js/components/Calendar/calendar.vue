<template>
    <div class="container">
        <div class='smart-calendar-main'>
            <div>
                <div class="calendar-header --no-select" v-if="showHeaderMenu">
                    <div class="left">
                    </div>
                    <div class="center">
                        <button class="btn btn-circle fc-prev-button" id="next" @click="">
                            <i class="fa fa-chevron-left pt-1"></i>
                        </button>
                        <div id="title" v-html=""></div>
                        <button class="btn btn-circle fc-next-button" id="prev" @click="">
                            <i class="fa fa-chevron-right pt-1"></i>
                        </button>
                    </div>
                    <div class="right">

                    </div>
                </div>
                <FullCalendar
                    class='smart-calendar'
                    :options='calendarOptions'
                    ref="fullCalendar">
                </FullCalendar>
            </div>
        </div>
    </div>
</template>

<script>
import FullCalendar from '@fullcalendar/vue'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import listPlugin from '@fullcalendar/list'
import interactionPlugin from '@fullcalendar/interaction'
import momentPlugin from '@fullcalendar/moment'
import format from 'date-fns/format';

export default {
    name: "dashboard",

    components: {
        FullCalendar,
    },

    props: {
        showHeaderMenu: {
            type: Boolean,
            default: true
        },
        urlEvents: {
            required: true,
        }
    },

    data: function () {
        return {
            canDrag: false,
            calendarOptions: {
                contentHeight: 1000,
                eventRender: this.eventRender,
                headerToolbar: false,
                customButtons: {

                },
                events: this.urlEvents,
                plugins: [
                    dayGridPlugin,
                    timeGridPlugin,
                    interactionPlugin,
                    listPlugin,
                    momentPlugin,
                ],
                firstDay: 1,
                eventDisplay: 'auto',
                buttonText: {
                    listMonth: 'month',
                    listYear: 'year',
                    listWeek: 'week',
                    listDay: 'day'
                },
                initialView: 'dayGridMonth',
                eventOrder: 'eventTypeSort',
                editable: true,
                selectable: true,
                selectMirror: true,
                dayMaxEvents: true,
                weekends: true,
                weekNumbers: false,
            },
            currentDate: null,
            currentViewType: 'dayGridMonth',
        }
    },

    methods: {
        updateCurrentViewType() {
            this.currentViewType = this.calendarApi.view.type
        },

        initialView() {
            return 'dayGridMonth'
        },
    },

    created() {
        this.calendarOptions.initialView = this.initialView()
        this.currentDate = `<b>${format(new Date(), 'MMMM')}</b> ${format(new Date(), 'yyyy')}`
    },

    mounted() {
        this.calendarApi = this.$refs.fullCalendar.getApi()
        this.updateCurrentViewType()
    }
}
</script>

<style scoped>

</style>

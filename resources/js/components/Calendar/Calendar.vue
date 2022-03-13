<template>
    <div class="container smart-calendar">
        <div class='smart-calendar-main'>
            <div>
                <div class="calendar-header --no-select" v-if="showHeaderMenu">
                    <div class="left">
                    </div>
                    <div class="center-title">
                        <button class="btn btn-circle fc-prev-button" id="next" @click="calendarPrev">
                            <i class="fa fa-chevron-left pt-1"></i>
                        </button>
                        <div id="title" v-html="currentDate"></div>
                        <button class="btn btn-circle fc-next-button" id="prev" @click="calendarNext">
                            <i class="fa fa-chevron-right pt-1"></i>
                        </button>
                    </div>
                    <div class="right">
                        <multi_select name="selectView" :searchable="false" track-by="value" label='name' style="font-size: 15px !important;"
                                      v-model="currentViewType" :options="getSelectViewOptions"/>
                    </div>
                </div>
                <FullCalendar
                    class='smart-calendar'
                    :options='calendarOptions'
                    ref="fullCalendar">
                </FullCalendar>
            </div>
        </div>
        <ContextMenu ref="menu" :quick-event-focused="isQuickEventFocused" :key="contextMenuKey">
            <template>
                <div v-if="isHandleDate">
                     <span class="dropdown-item" data-cy="btn_goto_day" @click="addEvent">
                        <i class="fa fa-calendar-plus"></i> Agregar Turno
                    </span>
                    <span class="dropdown-item" data-cy="btn_goto_day" @click="goToPointerDay('listDay')">
                        <i class="fa fa-calendar"></i> Ir al Dia
                    </span>
                    <span class="dropdown-item" data-cy="btn_goto_week" @click="goToPointerDay('listWeek')">
                        <i class="fa fa-calendar-minus"></i> Ir a la Semana
                    </span>
                </div>
                <div v-if="isHandleEvent">
                    <span class="dropdown-item" data-cy="btn_add_payment_event" v-if="pointerEvent.extendedProps.status_id >= 2"
                          @click="addPayment">
                        <i class="fa fa-money-bill" aria-hidden="true"></i> Cotizar Turno
                    </span>
                    <span class="dropdown-item" data-cy="btn_confirm_event" v-if="pointerEvent.extendedProps.status_id === 1"
                          @click="confirmEvent">
                        <i class="fa fa-calendar-check" aria-hidden="true"></i> Confirmar Turno
                    </span>
                    <span class="dropdown-item" data-cy="btn_pending_event" v-else
                          @click="pendingEvent">
                        <i class="fa fa-calendar-check" aria-hidden="true"></i> Pasar a pendiente
                    </span>
                    <span class="dropdown-item" data-cy="btn_edit_event"
                          @click="editEvent">
                        <i class="fa fa-edit" aria-hidden="true"></i> Editar Turno
                    </span>
                    <span class="dropdown-item" data-cy="btn_remove_event"
                          @click="removeEvent">
                        <i class="fa fa-trash" aria-hidden="true"></i> Eliminar Turno
                    </span>
                </div>
            </template>
        </ContextMenu>
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
import ContextMenu from "./ContextMenu";

export default {
    name: "Calendar",

    components: {
        FullCalendar,
        ContextMenu,
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
            isQuickEventFocused: false,
            contextMenuKey: 0,
            canDrag: false,
            calendarOptions: {
                contentHeight: 1000,
                eventRender: this.eventRender,
                headerToolbar: false,
                customButtons: {},
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
                eventAllow: this.canDrag,
                eventDrop: this.eventDrop,
                eventClick: this.eventClick,
                dateClick: this.handleDateSelect,
                eventContent: this.eventContent
            },
            currentDate: null,
            currentViewType: 'dayGridMonth',
            maxMobileWidth: 574,
            isHandleEvent: false,
            isHandleDate: false,
            calendarApi: null,
            pointerDate: null,
            pointerEvent: null,
        }
    },

    watch: {
        currentViewType(newVal) {
            if (newVal === 'today') {
                this.calendarApi.today()
                this.calendarUpdateDateTitle()
                return
            }

            this.calendarApi.changeView(newVal)
            this.calendarUpdateDateTitle()
        },
    },

    computed: {
        isMobileScreen() {
            return window.innerWidth <= this.maxMobileWidth
        },

        getSelectViewOptions() {
            return [
                {
                    'name': 'Mes',
                    'value': this.isMobileScreen ? 'listMonth' : 'dayGridMonth',
                },
                {
                    'name': 'Semana',
                    'value': 'listWeek',
                },
                {
                    'name': 'Día',
                    'value': 'listDay',
                },
                {
                    'name': 'Hoy',
                    'value': 'today',
                },
            ]
        },
    },

    methods: {
        updateCurrentViewType() {
            this.currentViewType = this.calendarApi.view.type
        },

        initialView() {
            return 'dayGridMonth'
        },

        calendarPrev() {
            this.calendarApi.prev();
            this.calendarUpdateDateTitle()
        },

        calendarNext() {
            this.calendarApi.next();
            this.calendarUpdateDateTitle()
        },

        calendarUpdateDateTitle() {
            let date
            switch (this.currentViewType) {
                case 'listDay':
                    date = `${format(this.calendarApi.getDate(), 'dd MMMM yyyy')}`
                    break
                case 'today':
                    date = `${format(this.calendarApi.getDate(), 'dd MMMM yyyy')}`
                    break
                case 'listWeek':
                    date = `${format(this.calendarApi.view.activeStart, 'MMMM dd')} - ${format(this.calendarApi.view.activeEnd, 'MMMM dd yyyy')}`
                    break
                default:
                    date = `<b>${format(this.calendarApi.getDate(), 'MMMM')}</b> ${format(this.calendarApi.getDate(), 'yyyy')}`
                    break
            }
            this.currentDate = date
        },

        eventDrop(event) {
            this.$emit('eventDrop', event.event)
        },

        eventClick(event) {
            if (event.event.extendedProps.clickable === true) {
                this.isHandleDate = false
                this.isHandleEvent = true
                this.pointerEvent = event.event
                this.$refs.menu.open(event.jsEvent)
            }
        },

        keepContextMenu() {
            this.isQuickEventFocused = true
        },

        unkeepContextMenu() {
            this.isQuickEventFocused = false
        },

        handleDateSelect(selectInfo) {
            this.isHandleDate = true
            this.isHandleEvent = false
            this.$refs.menu.open(selectInfo.jsEvent)
            this.pointerDate = selectInfo.dateStr
            selectInfo.jsEvent.preventDefault();
        },

        goToDay(selectInfo) {
            this.calendarApi.changeView('listDay')
            this.calendarApi.gotoDate(selectInfo.dateStr)
            this.closeContextMenu()
            this.updateCurrentViewType()
        },

        goToWeek(selectInfo) {
            this.calendarApi.changeView('listDay')
            this.calendarApi.gotoDate(selectInfo.dateStr)
            this.forceCloseContextMenu()
            this.updateCurrentViewType()
        },

        goToPointerDay(view) {
            this.calendarApi.changeView(view)
            this.calendarApi.gotoDate(this.pointerDate)
            this.forceCloseContextMenu()
            this.updateCurrentViewType()
        },

        addEvent() {
            this.$emit('addEvent', this.pointerDate)
            this.forceCloseContextMenu()
        },

        closeContextMenu() {
            this.unkeepContextMenu()
            this.$refs.menu.close()
        },

        forceCloseContextMenu() {
            this.contextMenuKey++
        },

        confirmEvent() {
            this.$emit('confirmEvent', this.pointerEvent)
            this.forceCloseContextMenu()
        },

        addPayment() {
            this.$emit('addPayment', this.pointerEvent)
            this.forceCloseContextMenu()
        },

        pendingEvent() {
            this.$emit('pendingEvent', this.pointerEvent)
            this.forceCloseContextMenu()
        },

        removeEvent() {
            this.$emit('removeEvent', this.pointerEvent)
            this.forceCloseContextMenu()
        },

        editEvent() {
            this.$emit('editEvent', this.pointerEvent)
            this.closeContextMenu()
        },

        refreshEvents() {
            this.calendarApi.refetchEvents()
        }
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
.smart-calendar {
    min-height: 100%;
    font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
    font-size: 14px;
}

.smart-calendar .fc-button {
    height: 33px;
}

.smart-calendar .fc-button .fc-icon {
    vertical-align: top !important;
}

.smart-calendar .fc .fc-button-primary {
    background-color: #12518F;
    border-color: #12518F;
}

.smart-calendar .fc .fc-button-primary:not(:disabled):active, .fc .fc-button-primary:not(:disabled).fc-button-active {
    background-color: #122F4D;
    border-color: #122F4D;
}

.smart-calendar .fc, .smart-calendar .calendar-header { /* the calendar root */
    max-width: 1300px;
    margin: 10px auto;
}

.smart-calendar .fc .fc-daygrid-event {
    margin-top: 2px;
}

.smart-calendar .fc-event,
.smart-calendar .fc-agenda .fc-event-time,
.smart-calendar .fc-event a {
    padding: 0;
    background-color: #ffffff;
    border-color: #ffffff;
}

.smart-calendar-main {
    flex-grow: 1;
    padding: 0 1em 1em 1em;
}

.smart-calendar .nav-title {
    margin-left: auto;
    margin-right: auto;
}

.smart-calendar .legend {
    margin-left: auto;
    margin-right: auto;
    max-width: 1100px;
    list-style: none;
    padding: 40px
}

.smart-calendar .legend li {
    float: left;
    margin-right: 10px;
}

.smart-calendar .fc-event-time {
    display: none;
}

.smart-calendar .fc-daygrid-dot-event .fc-event-title {
    font-weight: normal;
}

/* Small devices (desktops, max 423px)*/
@media (max-width: 423px) {
    .smart-calendar-main {
        flex-grow: 1;
        padding: 1em;
    }
}

/* Medium devices (desktops, max 940px)*/
@media (max-width: 1190px) {
    .smart-calendar {
        font-size: 10px;
    }

    .smart-calendar .legend {
        list-style: none;
        padding: 10px;
        font-size: 10px
    }
}

/* Large devices (desktops, max 1030px)*/
@media (max-width: 1030px) {
    .smart-calendar .nav-title h2 {
        font-size: 15px;
        padding-right: 20px;
    }
}

/* Small devices (desktops, max 500px)*/
@media (max-width: 500px) {
    .smart-calendar .fc-header-toolbar {
        font-size: 8px;
    }

    .smart-calendar {
        font-size: 10px;
    }

    .smart-calendar .fc-button {
        height: 23px;
    }

    .smart-calendar .legend {
        list-style: none;
        padding: 10px;
        font-size: 8px
    }

    .smart-calendar #title {
        font-size: 18px !important;
        margin: 0 10px !important;
        padding-top: 4px;
    }
}

/* Small devices (desktops, max 501px and up)*/
@media (min-width: 501px) {
    .smart-calendar .fc-header-toolbar {
        font-size: 10px;
    }
}

/* Medium devices (desktops, max 941px and up)*/
@media (min-width: 1191px) {
    .smart-calendar .fc-header-toolbar {
        font-size: 14px;
    }

    .smart-calendar .legend {
        list-style: none;
        padding: 10px;
        font-size: 10px
    }
}

.small-font-size {
    font-size: 10px;
}

.calendar-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 50px;
    position: relative;
    z-index: 10;
}

.calendar-header .center-title {
    display: flex;
}

.calendar-header button#next, .calendar-header button#prev  {
    color: #0a5e9d;
}

.fc-col-header {
    width: 100% !important;
}

.fc-col-header-cell {
    background-color: #e1e7ed;
}

th.fc-col-header-cell {
    padding: 5px;
    font-weight: normal;
}

.fc-day-today .fc-daygrid-day-number {
    background-color: #0f2943;
    color: white !important;
    border-radius: 15px;
    margin: 1px 3px;
    width: 30px;
    padding: 5px 7px;
    display: block;
    text-align: center;
}

.smart-calendar #title {
    color: #2d2d2d;
    margin: 0 27px;
    font-size: 22px;
    padding-top: 4px;
}

a.fc-daygrid-event {
    border-radius: 3px;
    margin: 3px 10px !important;
}

.fc-event-main .fc-event-title-text {
    font-size: 0.5rem;
    color: #847a7a;
    white-space: break-spaces;
    padding: 5px 8px 0px;
}

.fc-event-main .fc-event-title-description {
    color: #3a3737;
    padding: 3px 8px 3px;
    font-size: 0.7rem;
    white-space: break-spaces !important;
}

.fc .fc-popover-body {
    overflow-y: scroll;
    height: 20em;
}

.fc-theme-standard td, .fc-theme-standard th, .fc-theme-standard .fc-scrollgrid {
    border: 1px solid #e1e7ed;
}
</style>

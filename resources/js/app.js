import Alpine from 'alpinejs';

import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';

window.Alpine = Alpine;
window.FullCalendar = Calendar;

Alpine.start();

const updateLiveNotifications = async () => {
    const itemsElement = document.getElementById('notification-items');
    const countElement = document.getElementById('notification-count');
    const pendingElement = document.getElementById('notification-pending');

    if (!itemsElement || !countElement || !pendingElement) {
        return;
    }

    try {
        const response = await fetch('/notifications/live', {
            headers: { Accept: 'application/json' },
            credentials: 'same-origin',
        });

        if (!response.ok) {
            return;
        }

        const data = await response.json();
        countElement.textContent = '';
        countElement.classList.toggle('hidden', data.count === 0);
        pendingElement.textContent = `${data.count} pendientes`;
        itemsElement.replaceChildren();

        if (data.items.length === 0) {
            const empty = document.createElement('p');
            empty.className = 'px-4 py-6 text-center text-sm text-muted';
            empty.textContent = 'No hay notificaciones pendientes.';
            itemsElement.appendChild(empty);
            return;
        }

        data.items.forEach((notification) => {
            const link = document.createElement('a');
            link.href = notification.url;
            link.className = 'block border-b border-hairline px-4 py-3 hover:bg-base focus:outline-none focus:ring-2 focus:ring-inset focus:ring-teal';

            const title = document.createElement('span');
            title.className = `block text-sm font-semibold ${notification.priority === 'high' ? 'text-alert' : 'text-ink'}`;
            title.textContent = notification.title;

            const message = document.createElement('span');
            message.className = 'mt-1 block text-xs leading-5 text-muted';
            message.textContent = notification.message;

            link.append(title, message);
            itemsElement.appendChild(link);
        });
    } catch (error) {
        // La interfaz conserva los últimos datos visibles si el servidor no responde.
    }
};

const updateLiveDashboard = async () => {
    const dashboard = document.querySelector('[data-live-dashboard]');

    if (!dashboard) {
        return;
    }

    try {
        const response = await fetch(dashboard.dataset.liveUrl, {
            headers: { Accept: 'application/json' },
            credentials: 'same-origin',
        });

        if (!response.ok) {
            return;
        }

        const data = await response.json();
        const documentParser = new DOMParser();
        const parsedDocument = documentParser.parseFromString(data.html, 'text/html');
        const updatedDashboard = parsedDocument.querySelector('[data-live-dashboard]');

        if (updatedDashboard) {
            dashboard.replaceWith(updatedDashboard);
        }
    } catch (error) {
        // La interfaz conserva los últimos datos visibles si el servidor no responde.
    }
};

document.addEventListener('DOMContentLoaded', () => {
    const refreshLiveData = () => {
        if (document.visibilityState === 'visible') {
            updateLiveNotifications();
            updateLiveDashboard();
        }
    };

    refreshLiveData();
    window.setInterval(refreshLiveData, 60000);
    document.addEventListener('visibilitychange', refreshLiveData);
});

document.addEventListener('submit', (event) => {
    const form = event.target.closest('form[data-confirm-message]');

    if (!form) {
        return;
    }

    if (form.dataset.confirmed === 'true') {
        delete form.dataset.confirmed;
        return;
    }

    event.preventDefault();
    window.dispatchEvent(new CustomEvent('confirm-action', {
        detail: {
            form,
            title: form.dataset.confirmTitle || 'Confirmar acción',
            message: form.dataset.confirmMessage,
            label: form.dataset.confirmLabel || 'Confirmar',
        },
    }));
});

document.addEventListener('DOMContentLoaded', () => {
    const calendarElement = document.getElementById('surgery-calendar');

    if (calendarElement) {
        const calendar = new Calendar(calendarElement, {
        plugins: [
            dayGridPlugin,
            timeGridPlugin,
            interactionPlugin,
        ],

        initialView: 'dayGridMonth',

        locale: 'es',

        firstDay: 1,

        height: 'auto',

        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay',
        },

        buttonText: {
            today: 'Hoy',
            month: 'Mes',
            week: 'Semana',
            day: 'Día',
        },

        events: calendarElement.dataset.eventsUrl,

        eventClick(info) {
            if (info.event.url) {
                window.location.href = info.event.url;
            }
        },

        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false,
        },
        });

        calendar.render();
    }

    const schedulerElement = document.getElementById('surgery-scheduler');

    if (!schedulerElement) {
        return;
    }

    const form = document.getElementById(schedulerElement.dataset.formId);
    const dateInput = form?.querySelector('[name="scheduled_date"]');
    const startInput = form?.querySelector('[name="start_time"]');
    const endInput = form?.querySelector('[name="end_time"]');
    const typeInput = form?.querySelector('[name="surgery_type_id"]');
    const veterinarianInput = form?.querySelector('[name="veterinarian_id"]');
    const roomInput = form?.querySelector('[name="operating_room_id"]');
    const messageElement = document.getElementById('surgery-scheduler-message');
    const durations = JSON.parse(schedulerElement.dataset.durations || '{}');
    const currentSurgeryId = schedulerElement.dataset.currentSurgeryId;
    let allEvents = [];
    let draftSelection = null;

    const isBlockingEvent = (event) => {
        if (currentSurgeryId && String(event.id) === String(currentSurgeryId)) {
            return false;
        }

        return !['cancelled', 'no_show'].includes(event.extendedProps?.state);
    };

    const selectedResourceEvents = () => allEvents.filter((event) => {
        if (!isBlockingEvent(event)) {
            return false;
        }

        const veterinarianMatches = veterinarianInput?.value
            && String(event.extendedProps?.veterinarian_id) === String(veterinarianInput.value);
        const roomMatches = roomInput?.value
            && String(event.extendedProps?.operating_room_id) === String(roomInput.value);

        return veterinarianMatches || roomMatches;
    });

    const overlaps = (start, end, event) => {
        const eventStart = new Date(event.start);
        const eventEnd = new Date(event.end);

        return start < eventEnd && end > eventStart;
    };

    const updateMessage = (text, isError = false) => {
        if (!messageElement) {
            return;
        }

        messageElement.textContent = text;
        messageElement.classList.toggle('text-alert', isError);
        messageElement.classList.toggle('text-muted', !isError);
    };

    const updateEvents = () => {
        scheduler.removeAllEvents();
        scheduler.addEventSource(selectedResourceEvents());

        if (draftSelection) {
            scheduler.addEvent({
                id: 'draft-selection',
                title: 'Horario seleccionado',
                start: draftSelection.start,
                end: draftSelection.end,
                backgroundColor: '#dbeafe',
                borderColor: '#2563eb',
                textColor: '#1e3a8a',
                editable: false,
            });
        }

        if (!veterinarianInput?.value || !roomInput?.value) {
            updateMessage('Selecciona un veterinario y un quirófano para validar el horario.');
            return;
        }

        updateMessage('Los bloques visibles representan cirugías que ocupan al veterinario o el quirófano seleccionados.');
    };

    const durationForCurrentType = () => {
        const duration = Number(durations[typeInput?.value] || 0);
        return duration > 0 ? duration : 60;
    };

    const formatTime = (date) => date.toTimeString().slice(0, 5);

    const formatDate = (date) => {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');

        return `${year}-${month}-${day}`;
    };

    const setScheduleFromRange = (start, end) => {
        draftSelection = { start, end };

        if (dateInput) {
            dateInput.value = formatDate(start);
        }

        if (startInput) {
            startInput.value = formatTime(start);
        }

        if (endInput) {
            endInput.value = formatTime(end);
        }

        updateMessage(`Horario seleccionado: ${formatTime(start)} - ${formatTime(end)}.`);

        scheduler.unselect();
        updateEvents();
    };

    const clearDraftSelection = () => {
        draftSelection = null;
        scheduler.getEventById('draft-selection')?.remove();
    };

    const scheduler = new Calendar(schedulerElement, {
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
        initialView: 'timeGridWeek',
        locale: 'es',
        firstDay: 1,
        height: 'auto',
        allDaySlot: false,
        selectable: true,
        selectMirror: true,
        nowIndicator: true,
        slotMinTime: '07:00:00',
        slotMaxTime: '19:00:00',
        slotDuration: '00:15:00',
        snapDuration: '00:15:00',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay',
        },
        buttonText: {
            today: 'Hoy',
            month: 'Mes',
            week: 'Semana',
            day: 'Día',
        },
        events: [],
        selectAllow(selection) {
            const start = selection.start;
            const end = selection.end;
            const conflicts = selectedResourceEvents().some((event) => overlaps(start, end, event));

            return Boolean(veterinarianInput?.value && roomInput?.value) && !conflicts;
        },
        select(selection) {
            setScheduleFromRange(selection.start, selection.end);
            scheduler.unselect();
        },
        dateClick(info) {
            if (info.view.type === 'dayGridMonth') {
                scheduler.changeView('timeGridDay', info.date);
                updateMessage('Selecciona una hora disponible para esta fecha.');
                return;
            }

            if (!veterinarianInput?.value || !roomInput?.value) {
                updateMessage('Selecciona primero un veterinario y un quirófano.', true);
                return;
            }

            const start = info.date;
            const end = new Date(start.getTime() + durationForCurrentType() * 60000);
            const conflicts = selectedResourceEvents().some((event) => overlaps(start, end, event));

            if (conflicts) {
                updateMessage('Ese horario se cruza con una cirugía existente.', true);
                return;
            }

            setScheduleFromRange(start, end);
        },
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false,
        },
    });

    fetch(schedulerElement.dataset.eventsUrl)
        .then((response) => response.json())
        .then((events) => {
            allEvents = events;
            scheduler.addEventSource(selectedResourceEvents());
            updateEvents();
        })
        .catch(() => {
            updateMessage('No se pudo cargar la agenda. Puedes continuar, pero la disponibilidad se verificará al guardar.', true);
        });

    [dateInput, typeInput, veterinarianInput, roomInput].forEach((input) => {
        input?.addEventListener('change', () => {
            clearDraftSelection();

            if (input === dateInput && dateInput.value) {
                scheduler.gotoDate(dateInput.value);
            }

            updateEvents();
        });
    });

    if (dateInput?.value) {
        scheduler.gotoDate(dateInput.value);
    }

    scheduler.render();
});

var calendar;
var Calendar = FullCalendar.Calendar;
var events = [];

$(function() {
    if (!!scheds) {
        Object.keys(scheds).map(k => {
            var row = scheds[k]
            events.push({ id: row.id, title: row.title, start: row.start_datetime, end: row.end_datetime });
        })
    }

    var date = new Date()
    var d = date.getDate(),
        m = date.getMonth(),
        y = date.getFullYear()

    calendar = new Calendar(document.getElementById('calendar'), {
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,dayGridWeek,list',
        },
        customButtons: {
            prev: {
                text: 'Custom Prev',
                click: function () {
                    calendar.prev();
                },
                backgroundColor: '#808080', // Set your secondary color
            },
            next: {
                text: 'Custom Next',
                click: function () {
                    calendar.next();
                },
                backgroundColor: '#808080', // Set your secondary color
            },
            month: {
                text: 'Custom Month',
                click: function () {
                    calendar.changeView('dayGridMonth');
                },
                backgroundColor: '#808080', // Set your secondary color
            },
            week: {
                text: 'Custom Week',
                click: function () {
                    calendar.changeView('dayGridWeek');
                },
                backgroundColor: '#808080', // Set your secondary color
            },
            list: {
                text: 'Custom List',
                click: function () {
                    calendar.changeView('list');
                },
                backgroundColor: '#808080', // Set your secondary color
            },
        },
        selectable: true,
        themeSystem: 'bootstrap',
        events: events,
        eventClick: function(info) {
            var _details = $('#event-details-modal')
            var id = info.event.id
            if (!!scheds[id]) {
                _details.find('#title').text(scheds[id].title)
                _details.find('#description').text(scheds[id].description)
                _details.find('#start').text(scheds[id].sdate)
                _details.find('#end').text(scheds[id].edate)
                _details.find('#edit,#delete').attr('data-id', id)
                _details.modal('show')
            } else {
                alert("Event is undefined");
            }
        },
        eventDidMount: function(info) {
            // Do Something after events mounted
        },
        editable: true
    });

    calendar.render();

    // Form reset listener
    $('#schedule-form').on('reset', function() {
        $(this).find('input:hidden').val('')
        $(this).find('input:visible').first().focus()
    })

    // Edit Button
    $('#edit').click(function() {
        var id = $(this).attr('data-id')
        if (!!scheds[id]) {
            var _form = $('#schedule-form')
            console.log(String(scheds[id].start_datetime), String(scheds[id].start_datetime).replace(" ", "\\t"))
            _form.find('[name="id"]').val(id)
            _form.find('[name="title"]').val(scheds[id].title)
            _form.find('[name="description"]').val(scheds[id].description)
            _form.find('[name="start_datetime"]').val(String(scheds[id].start_datetime).replace(" ", "T"))
            _form.find('[name="end_datetime"]').val(String(scheds[id].end_datetime).replace(" ", "T"))
            $('#event-details-modal').modal('hide')
            _form.find('[name="title"]').focus()
        } else {
            alert("Event is undefined");
        }
    })

    // Delete Button / Deleting an Event
    $('#delete').click(function() {
        var id = $(this).attr('data-id')
        if (!!scheds[id]) {
            var _conf = confirm("Are you sure to delete this scheduled event?");
            if (_conf === true) {
                location.href = "./delete_schedule.php?id=" + id;
            }
        } else {
            alert("Event is undefined");
        }
    })
});

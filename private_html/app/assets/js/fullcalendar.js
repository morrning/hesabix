$(function () {

  // sample calendar events data

  var Draggable = FullCalendar.Draggable;
  var calendarEl = document.getElementById('fullcalendar');
  var containerEl = document.getElementById('external-events');

  var curYear = moment().format('YYYY');
  var curMonth = moment().format('MM');

  // Calendar Event Source
  var calendarEvents = {
    id: 1,
    backgroundColor: 'rgba(1,104,250, .15)',
    borderColor: '#0168fa',
    events: [{
      id: '1',
      start: curYear + '-' + curMonth + '-08T08:30:00',
      end: curYear + '-' + curMonth + '-08T13:00:00',
      title: 'ملاقات با توسعه دهندگان گوگل',
      description: 'لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم لورم ایپسوم ...'
    }, {
      id: '2',
      start: curYear + '-' + curMonth + '-10T09:00:00',
      end: curYear + '-' + curMonth + '-10T17:00:00',
      title: 'نوشتن/ بازنویسی کد ها',
      description: 'لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم لورم ایپسوم متن ساختگی ...'
    }, {
      id: '3',
      start: curYear + '-' + curMonth + '-13T12:00:00',
      end: curYear + '-' + curMonth + '-13T18:00:00',
      title: 'کنفرانس تلفنی',
      description: 'لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم لورم ایپسوم متن ساختگی ...'
    }, {
      id: '4',
      start: curYear + '-' + curMonth + '-15T07:30:00',
      end: curYear + '-' + curMonth + '-15T15:30:00',
      title: 'سفر هفتگی با تیم',
      description: 'لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم لورم ایپسوم ...'
    }, {
      id: '5',
      start: curYear + '-' + curMonth + '-17T10:00:00',
      end: curYear + '-' + curMonth + '-19T15:00:00',
      title: 'فستیوال طراحی وب',
      description: 'لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم لورم ایپسوم ...'
    }, {
      id: '6',
      start: curYear + '-' + curMonth + '-08T13:00:00',
      end: curYear + '-' + curMonth + '-08T18:30:00',
      title: 'مراسم ازدواج رضا افشار',
      description: 'لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم لورم ایپسوم ...'
    }]
  };

  // Birthday Events Source
  var birthdayEvents = {
    id: 2,
    backgroundColor: 'rgba(16,183,89, .25)',
    borderColor: '#10b759',
    events: [{
        id: '7',
        start: curYear + '-' + curMonth + '-01T18:00:00',
        end: curYear + '-' + curMonth + '-01T23:30:00',
        title: 'مهمانی پدرام',
        description: 'لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم لورم ایپسوم ...'
      },
      {
        id: '8',
        start: curYear + '-' + curMonth + '-21T15:00:00',
        end: curYear + '-' + curMonth + '-21T21:00:00',
        title: 'جشن تولد مریم',
        description: 'لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم لورم ایپسوم ...'
      },
      {
        id: '9',
        start: curYear + '-' + curMonth + '-23T15:00:00',
        end: curYear + '-' + curMonth + '-23T21:00:00',
        title: 'جشن تولد ساناز',
        description: 'لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم لورم ایپسوم ...'
      }
    ]
  };


  var holidayEvents = {
    id: 3,
    backgroundColor: 'rgba(241,0,117,.25)',
    borderColor: '#f10075',
    events: [{
        id: '10',
        start: curYear + '-' + curMonth + '-04',
        end: curYear + '-' + curMonth + '-06',
        title: 'روز جشن'
      },
      {
        id: '11',
        start: curYear + '-' + curMonth + '-26',
        end: curYear + '-' + curMonth + '-27',
        title: 'روز یادبود'
      },
      {
        id: '12',
        start: curYear + '-' + curMonth + '-28',
        end: curYear + '-' + curMonth + '-29',
        title: 'روز ولنتاین'
      }
    ]
  };

  var discoveredEvents = {
    id: 4,
    backgroundColor: 'rgba(0,204,204,.25)',
    borderColor: '#00cccc',
    events: [{
      id: '13',
      start: curYear + '-' + curMonth + '-17T08:00:00',
      end: curYear + '-' + curMonth + '-18T11:00:00',
      title: 'سمینار ورکشاپ طراحی وب'
    }]
  };

  var meetupEvents = {
    id: 5,
    backgroundColor: 'rgba(91,71,251,.2)',
    borderColor: '#5b47fb',
    events: [{
        id: '14',
        start: curYear + '-' + curMonth + '-03',
        end: curYear + '-' + curMonth + '-05',
        title: 'کنفراس ویدیویی آموزش UI/UX'
      },
      {
        id: '15',
        start: curYear + '-' + curMonth + '-18',
        end: curYear + '-' + curMonth + '-20',
        title: 'کنفرانس آموزش انگولار'
      }
    ]
  };


  var otherEvents = {
    id: 6,
    backgroundColor: 'rgba(253,126,20,.25)',
    borderColor: '#fd7e14',
    events: [{
        id: '16',
        start: curYear + '-' + curMonth + '-06',
        end: curYear + '-' + curMonth + '-08',
        title: 'روز استراحت'
      },
      {
        id: '17',
        start: curYear + '-' + curMonth + '-29',
        end: curYear + '-' + curMonth + '-31',
        title: 'روز استراحت'
      }
    ]
  };

  new Draggable(containerEl, {
    itemSelector: '.fc-event',
    eventData: function (eventEl) {
      return {
        title: eventEl.innerText
      };
    }
  });


  // initialize the calendar
  var calendar = new FullCalendar.Calendar(calendarEl, {
    headerToolbar: {
      left: "prev,today,next",
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
    },
    editable: true,
    droppable: true, // this allows things to be dropped onto the calendar
    fixedWeekCount: true,
    // height: 300,
    initialView: 'dayGridMonth',
    timeZone: 'UTC',
    hiddenDays: [],
    navLinks: 'true',
    // weekNumbers: true,
    // weekNumberFormat: {
    //   week:'numeric',
    // },
    dayMaxEvents: 2,
    events: [],
    eventSources: [calendarEvents, birthdayEvents, holidayEvents, discoveredEvents, meetupEvents, otherEvents],
    drop: function (info) {
      // remove the element from the "Draggable Events" list
      // info.draggedEl.parentNode.removeChild(info.draggedEl);
    },
    eventClick: function (info) {
      var eventObj = info.event;
      console.log(info);
      $('#modalTitle1').html(eventObj.title);
      $('#modalBody1').html(eventObj._def.extendedProps.description);
      $('#eventUrl').attr('href', eventObj.url);
      $('#fullCalModal').modal("show");
    },
    dateClick: function (info) {
      $("#createEventModal").modal("show");
      console.log(info);
    },
  });

  calendar.render();


});
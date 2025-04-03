@extends('layouts.master')
@section('title')
    NSE Trading Holidays
@endsection
@section('css')
    <link href="{{ URL::asset('build/libs/fullcalendar/main.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Market
        @endslot
        @slot('title')
            NSE Trading Holidays
        @endslot
    @endcomponent
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-xl-3">
                    <div class="card card-h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Market Segments</h5>
                        </div>
                        <div class="card-body">
                            <div id="market-segments">
                                <p class="text-muted">Filter holidays by market segment</p>
                                <div class="form-check mb-2">
                                    <input class="form-check-input segment-filter" type="checkbox" value="all"
                                        id="segment-all" checked>
                                    <label class="form-check-label" for="segment-all">
                                        All Segments
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input segment-filter" type="checkbox" value="CM"
                                        id="segment-cm" checked>
                                    <label class="form-check-label" for="segment-cm">
                                        <span class="badge bg-primary-subtle text-primary">CM</span> Capital Market
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input segment-filter" type="checkbox" value="FO"
                                        id="segment-fo" checked>
                                    <label class="form-check-label" for="segment-fo">
                                        <span class="badge bg-success-subtle text-success">FO</span> Futures & Options
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input segment-filter" type="checkbox" value="CD"
                                        id="segment-cd" checked>
                                    <label class="form-check-label" for="segment-cd">
                                        <span class="badge bg-info-subtle text-info">CD</span> Currency Derivatives
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h5 class="mb-1">Upcoming Holidays</h5>
                        <p class="text-muted">Next trading holidays</p>
                        <div class="pe-2 me-n1 mb-3" data-simplebar style="height: 400px">
                            <div id="upcoming-holidays-list"></div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body bg-info-subtle">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i data-feather="calendar" class="text-info icon-dual-info"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fs-15">NSE Trading Holidays</h6>
                                    <p class="text-muted mb-0">Trading holidays for different market segments of the
                                        National Stock Exchange of India (NSE).</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- end col-->

                <div class="col-xl-9">
                    <div class="card card-h-100">
                        <div class="card-body">
                            <div id="calendar"></div>
                        </div>
                    </div>
                </div><!-- end col -->
            </div>
            <!--end row-->

            <div style='clear:both'></div>

            <!-- Holiday Details MODAL -->
            <div class="modal fade" id="holiday-modal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0">
                        <div class="modal-header p-3 bg-soft-info">
                            <h5 class="modal-title" id="modal-title">Holiday Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="holiday-details">
                                <div class="d-flex mb-3">
                                    <div class="flex-shrink-0 me-3">
                                        <i class="ri-calendar-event-line text-muted fs-16"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="d-block fw-semibold mb-0" id="holiday-date"></h6>
                                    </div>
                                </div>
                                <div class="d-flex mb-3">
                                    <div class="flex-shrink-0 me-3">
                                        <i class="ri-information-line text-muted fs-16"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="d-block fw-semibold mb-0" id="holiday-description"></h6>
                                    </div>
                                </div>
                                <div class="d-flex mb-3">
                                    <div class="flex-shrink-0 me-3">
                                        <i class="ri-building-line text-muted fs-16"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div id="holiday-segments"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> <!-- end modal-content-->
                </div> <!-- end modal dialog-->
            </div> <!-- end modal-->
        </div>
    </div> <!-- end row-->
@endsection
@push('js')
    <script src="{{ URL::asset('build/libs/fullcalendar/index.global.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize variables
            let calendarEl = document.getElementById('calendar');
            let upcomingHolidaysList = document.getElementById('upcoming-holidays-list');
            let allHolidays = [];
            let filteredHolidays = [];
            let calendar;

            // Fetch holidays data
            fetchHolidays();

            // Initialize segment filters
            initializeFilters();

            // Initialize sync button
            document.getElementById('btn-sync-holidays').addEventListener('click', syncHolidays);

            // Function to fetch holidays
            function fetchHolidays() {
                fetch('{{ route('holidays.data') }}')
                    .then(response => response.json())
                    .then(data => {
                        allHolidays = data;
                        applyFilters();
                        initializeCalendar();
                        updateUpcomingHolidays();
                    })
                    .catch(error => {
                        console.error('Error fetching holidays:', error);
                    });
            }

            // Function to initialize filters
            function initializeFilters() {
                // Handle segment filters
                document.querySelectorAll('.segment-filter').forEach(filter => {
                    filter.addEventListener('change', function() {
                        if (this.value === 'all') {
                            // If "All Segments" is checked/unchecked, update all other checkboxes
                            let isChecked = this.checked;
                            document.querySelectorAll('.segment-filter:not([value="all"])').forEach(
                                cb => {
                                    cb.checked = isChecked;
                                });
                        } else {
                            // If any individual segment is unchecked, uncheck "All Segments"
                            if (!this.checked) {
                                document.getElementById('segment-all').checked = false;
                            }

                            // If all individual segments are checked, check "All Segments"
                            let allIndividualChecked = Array.from(
                                document.querySelectorAll('.segment-filter:not([value="all"])')
                            ).every(cb => cb.checked);

                            if (allIndividualChecked) {
                                document.getElementById('segment-all').checked = true;
                            }
                        }

                        applyFilters();
                    });
                });
            }

            // Function to apply filters
            function applyFilters() {
                // Get selected segments
                let selectedSegments = Array.from(
                    document.querySelectorAll('.segment-filter:not([value="all"]):checked')
                ).map(cb => cb.value);

                // Filter holidays based on selected segments
                filteredHolidays = allHolidays.filter(holiday =>
                    selectedSegments.includes(holiday.market_segment)
                );

                // Update calendar events
                if (calendar) {
                    calendar.removeAllEvents();
                    addEventsToCalendar();
                }

                // Update upcoming holidays
                updateUpcomingHolidays();
            }

            // Function to initialize calendar
            function initializeCalendar() {
                calendar = new FullCalendar.Calendar(calendarEl, {
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
                    },
                    initialView: 'dayGridMonth',
                    themeSystem: 'bootstrap5',
                    events: [],
                    editable: false,
                    droppable: false,
                    selectable: true,
                    dateClick: function(info) {
                        // Check if there are holidays on this date
                        const clickedDate = info.dateStr;
                        const holidaysOnDate = filteredHolidays.filter(h =>
                            h.trading_date.split('T')[0] === clickedDate
                        );

                        if (holidaysOnDate.length > 0) {
                            showHolidayDetails(holidaysOnDate);
                        }
                    },
                    eventClick: function(info) {
                        // Show holiday details when an event is clicked
                        const holidayId = info.event.id;
                        const holiday = filteredHolidays.find(h => h.id.toString() === holidayId);

                        if (holiday) {
                            showHolidayDetails([holiday]);
                        }
                    }
                });

                addEventsToCalendar();
                calendar.render();
            }

            // Function to add events to calendar
            function addEventsToCalendar() {
                filteredHolidays.forEach(holiday => {
                    let eventColor;
                    switch (holiday.market_segment) {
                        case 'CM':
                            eventColor = '#405189'; // Primary color
                            break;
                        case 'FO':
                            eventColor = '#0ab39c'; // Success color
                            break;
                        case 'CD':
                            eventColor = '#299cdb'; // Info color
                            break;
                        default:
                            eventColor = '#f7b84b'; // Warning color
                    }

                    calendar.addEvent({
                        id: holiday.id,
                        title: holiday.description,
                        start: holiday.trading_date,
                        allDay: true,
                        className: `bg-${holiday.market_segment.toLowerCase()}-holiday`,
                        backgroundColor: eventColor,
                        borderColor: eventColor
                    });
                });
            }

            // Function to update upcoming holidays list
            function updateUpcomingHolidays() {
                if (!upcomingHolidaysList) return;

                // Clear existing list
                upcomingHolidaysList.innerHTML = '';

                // Get today's date
                const today = new Date();
                today.setHours(0, 0, 0, 0);

                // Filter upcoming holidays
                const upcoming = filteredHolidays
                    .filter(holiday => new Date(holiday.trading_date) >= today)
                    .sort((a, b) => new Date(a.trading_date) - new Date(b.trading_date))
                    .slice(0, 5); // Show only next 5 holidays

                if (upcoming.length === 0) {
                    upcomingHolidaysList.innerHTML =
                        '<div class="text-center p-3"><p class="text-muted">No upcoming holidays found</p></div>';
                    return;
                }

                // Create list items
                upcoming.forEach(holiday => {
                    const holidayDate = new Date(holiday.trading_date);
                    const formattedDate = holidayDate.toLocaleDateString('en-US', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });

                    let badgeClass;
                    switch (holiday.market_segment) {
                        case 'CM':
                            badgeClass = 'bg-primary-subtle text-primary';
                            break;
                        case 'FO':
                            badgeClass = 'bg-success-subtle text-success';
                            break;
                        case 'CD':
                            badgeClass = 'bg-info-subtle text-info';
                            break;
                        default:
                            badgeClass = 'bg-warning-subtle text-warning';
                    }

                    const listItem = document.createElement('div');
                    listItem.className = 'card mb-3';
                    listItem.innerHTML = `
                        <div class="card-body p-3">
                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm">
                                        <span class="avatar-title rounded-circle bg-light text-primary fs-5">
                                            ${holidayDate.getDate()}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fs-14 mb-1">${holiday.description}</h6>
                                    <p class="text-muted mb-0">${formattedDate}</p>
                                </div>
                            </div>
                            <div>
                                <span class="badge ${badgeClass}">${holiday.market_segment}</span>
                            </div>
                        </div>
                    `;

                    upcomingHolidaysList.appendChild(listItem);
                });
            }

            // Function to show holiday details
            function showHolidayDetails(holidays) {
                if (holidays.length === 0) return;

                // Get the first holiday for date and description
                const firstHoliday = holidays[0];
                const holidayDate = new Date(firstHoliday.trading_date);
                const formattedDate = holidayDate.toLocaleDateString('en-US', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });

                // Set modal content
                document.getElementById('holiday-date').textContent = formattedDate;
                document.getElementById('holiday-description').textContent = firstHoliday.description;

                // Set segments
                const segmentsContainer = document.getElementById('holiday-segments');
                segmentsContainer.innerHTML = '';

                // Group holidays by segment
                const segments = holidays.map(h => h.market_segment);

                // Add segment badges
                segments.forEach(segment => {
                    let badgeClass;
                    switch (segment) {
                        case 'CM':
                            badgeClass = 'bg-primary-subtle text-primary';
                            break;
                        case 'FO':
                            badgeClass = 'bg-success-subtle text-success';
                            break;
                        case 'CD':
                            badgeClass = 'bg-info-subtle text-info';
                            break;
                        default:
                            badgeClass = 'bg-warning-subtle text-warning';
                    }

                    const badge = document.createElement('span');
                    badge.className = `badge ${badgeClass} me-2`;

                    switch (segment) {
                        case 'CM':
                            badge.textContent = 'Capital Market';
                            break;
                        case 'FO':
                            badge.textContent = 'Futures & Options';
                            break;
                        case 'CD':
                            badge.textContent = 'Currency Derivatives';
                            break;
                        default:
                            badge.textContent = segment;
                    }

                    segmentsContainer.appendChild(badge);
                });

                // Show modal
                const holidayModal = new bootstrap.Modal(document.getElementById('holiday-modal'));
                holidayModal.show();
            }

            // Function to sync holidays
            function syncHolidays() {
                const button = document.getElementById('btn-sync-holidays');
                button.disabled = true;
                button.innerHTML = '<i class="ri-loader-4-line align-middle me-1 spin"></i> Syncing...';

                fetch('{{ route('holidays.sync') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Show success message
                            Swal.fire({
                                title: 'Success!',
                                text: data.message || 'Holidays synchronized successfully',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500
                            });

                            // Refresh data
                            setTimeout(() => {
                                fetchHolidays();
                            }, 1500);
                        } else {
                            // Show error message
                            Swal.fire({
                                title: 'Error!',
                                text: data.message || 'Failed to synchronize holidays',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error!',
                            text: 'An unexpected error occurred',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    })
                    .finally(() => {
                        // Re-enable button
                        setTimeout(() => {
                            button.disabled = false;
                            button.innerHTML =
                                '<i class="ri-refresh-line align-middle me-1"></i> Sync Holidays';
                        }, 1000);
                    });
            }
        });
    </script>

    <style>
        .spin {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        /* Dark mode text color fix */
        [data-layout-mode="dark"] .text-dark {
            color: #ffffff !important;
        }

        [data-layout-mode="dark"] .badge.bg-primary-subtle {
            background-color: rgba(64, 81, 137, 0.2) !important;
        }

        [data-layout-mode="dark"] .badge.bg-success-subtle {
            background-color: rgba(10, 179, 156, 0.2) !important;
        }

        [data-layout-mode="dark"] .badge.bg-info-subtle {
            background-color: rgba(41, 156, 219, 0.2) !important;
        }
    </style>
@endpush

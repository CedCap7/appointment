<?php include('includes/header.php'); ?>

<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }

    #calendar-container {
        width: 100%;
        max-width: 100%;
        margin: 30px auto;
        background: white;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    /* FullCalendar Button adjustments */
    .fc-toolbar {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
    }

    .fc-toolbar button {
        font-size: 1rem !important; /* Adjust the font size of buttons */
        padding: 10px 20px !important; /* Adjust button padding */
        border-radius: 5px !important;
    }

    .fc-toolbar-title {
        font-size: 1.25rem;
        margin-bottom: 10px;
    }

    /* Header logo adjustments */
    .header-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 20px;
    }

    .header-container img {
        height: 100px;
        margin: 0 10px;
    }

    .header-container div {
        text-align: center;
        flex-grow: 1;
    }

    .header-container h2 {
        font-size: 1.5rem;
    }

    .header-container h4 {
        font-size: 1.1rem;
    }

    /* Mobile optimization */
    @media (max-width: 768px) {
        #calendar-container {
            padding: 10px;
        }
        h2 {
            font-size: 20px;
        }
        .fc-toolbar-title {
            font-size: 16px;
        }

        .fc-toolbar button {
            font-size: 0.9rem !important; /* Slightly smaller buttons for smaller screens */
            padding: 8px 16px !important;
        }

        /* Stack logos vertically on mobile */
        .header-container {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .header-container img {
            height: 80px; /* Resize the logos for mobile */
            margin-bottom: 10px;
        }

        .header-container h2 {
            font-size: 1.2rem;
        }

        .header-container h4 {
            font-size: 1rem;
        }
    }

    @media (max-width: 480px) {
        h2 {
            font-size: 18px;
        }
        .fc-toolbar-title {
            font-size: 14px;
        }

        .fc-toolbar button {
            font-size: 0.8rem !important;
            padding: 6px 12px !important;
        }

        /* Further reduce logo size on very small screens */
        .header-container img {
            height: 60px; /* Smaller logo size */
        }
    }
</style>

</head>

<div id="calendar-container">
    <div id="calendar"></div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>

<!-- SweetAlert2 for better alerts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let calendarEl = document.getElementById('calendar');
    let submissionId = new URLSearchParams(window.location.search).get("submission_id");
    let labId = null;
    let requestType = null;
    let allowedDays = [];
    let maxSlots;

    if (!submissionId) {
        Swal.fire({
            icon: 'error',
            title: 'Submission ID Missing!',
            text: 'No submission ID found. Please try again.',
            confirmButtonColor: '#d33'
        }).then(() => {
            window.history.back();
        });
        return;
    }

    // Fetch lab_id & request_type from the database
    $.ajax({
        url: 'fetch_lab_id.php',
        type: 'GET',
        data: { submission_id: submissionId },
        dataType: 'json',
        success: function(response) {
            if (response.lab_id) {
                labId = response.lab_id;
                requestType = response.request_type;
                category = response.category; // Fetch category

                console.log("Lab ID:", labId);
                console.log("Request Type:", requestType);
                console.log("Category:", category);

                // Define Allowed Days & Slots Based on Lab & Request Type
                if (labId == 1) { 
                    if (requestType.trim() === "In-House") {
                        allowedDays = [6]; // Saturdays only
                        maxSlots = (category == "1") ? 5 : (category == "2") ? 3 : 8; 
                    } else if (requestType.trim() === "On-Site") {
                        allowedDays = [4, 5]; // Thursdays & Fridays
                        maxSlots = 10;
                    }
                } else if (labId == 2) {
                    allowedDays = [1, 2, 3]; // Mon-Wed
                    maxSlots = 10;
                } else if (labId == 3) {
                    allowedDays = [1, 2]; // Mon-Tue
                    maxSlots = 10;
                } else if (labId == 4) {
                    allowedDays = [1, 2, 3, 4, 5]; // Mon-Fri
                    maxSlots = 9999; // No limit
                }

                console.log("Allowed Days:", allowedDays);
                console.log("Max Slots:", maxSlots);

                // Initialize Calendar
                initCalendar();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Submission ID!',
                    text: 'No matching lab found for this submission.',
                    confirmButtonColor: '#d33'
                }).then(() => {
                    window.history.back();
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Server Error!',
                text: 'Failed to fetch lab details. Please try again later.',
                confirmButtonColor: '#d33'
            });
        }
    });

    function initCalendar() {
        let calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            selectable: true,
            height: 'auto',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: function (fetchInfo, successCallback, failureCallback) {
                $.ajax({
                    url: 'fetch_booked_dates.php',
                    type: 'GET',
                    data: { submission_id: submissionId },
                    success: function (response) {
                        successCallback(response);
                    },
                    error: function () {
                        failureCallback();
                    }
                });
            },
            dateClick: function (info) {
                let selectedDate = new Date(info.dateStr);
                let today = new Date();
                today.setHours(0, 0, 0, 0);
                let minBookableDate = new Date();
                minBookableDate.setDate(today.getDate() + 3);

                let dayOfWeek = selectedDate.getDay();
                let formattedDate = selectedDate.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });

                // Prevent past date selection
                if (selectedDate < minBookableDate) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Booking Not Allowed!',
                        text: 'You can only book dates that are at least 3 days ahead.',
                        confirmButtonColor: '#d33',
                    });
                    return;
                }

                // Prevent selection if the day is not allowed
                if (!allowedDays.includes(dayOfWeek)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Date!',
                        text: `Bookings for this laboratory are only allowed on specific days.`,
                        confirmButtonColor: '#d33',
                    });
                    return;
                }

                // Prevent booking if the quantity exceeds available slots
                $.ajax({
                    url: 'check_availability.php',
                    type: 'GET',
                    data: { submission_id: submissionId, selected_date: info.dateStr },
                    dataType: 'json',
                    success: function (availability) {
                        if (!availability.bookable) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Not Enough Slots!',
                                text: 'Your quantity exceeds the remaining available slots for this date.',
                                confirmButtonColor: '#d33',
                            });
                            return;
                        }

                        // Confirm Booking
                        Swal.fire({
                            title: 'Confirm Your Appointment',
                            text: `Do you want to book an appointment on ${formattedDate}?`,
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, book it!',
                            showLoaderOnConfirm: true,
                            preConfirm: () => {
                                return new Promise((resolve) => {
                                    Swal.showLoading();

                                    $.ajax({
                                        url: 'save_appointment.php',
                                        type: 'POST',
                                        data: { submission_id: submissionId, selected_date: info.dateStr },
                                        dataType: 'json',
                                        success: function (response) {
                                            resolve(response);
                                        },
                                        error: function () {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Server Error!',
                                                text: 'There was an issue processing your request. Please try again later.',
                                                confirmButtonColor: '#d33'
                                            });
                                        }
                                    });
                                });
                            }
                        }).then((result) => {
                            if (result.value) {
                                Swal.fire({
                                    icon: result.value.status === 'success' ? 'success' : 'error',
                                    title: result.value.status === 'success' ? 'Confirmed!' : 'Error!',
                                    text: result.value.message,
                                    confirmButtonColor: '#3085d6'
                                }).then(() => {
                                    window.location.href = result.value.redirect_url;
                                });
                            }
                        });
                    }
                });
            }
        });

        calendar.render();
    }
});
</script>

</body>
</html>

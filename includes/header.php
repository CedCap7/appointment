<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scheduling Purpose</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">

    <!-- SweetAlert2 CSS & JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Bootstrap CSS (latest version) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS for Mobile Optimization -->
    <style>
        button {
            font-size: 1.1rem !important; /* Increase text size */
            padding: 15px 30px !important; /* Adjust button size */
            border-radius: 8px !important; /* Optional: rounded corners */
        }

        #description-text {
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }

        #description-text.show {
            opacity: 1;
        }

        /* Container and text styling */
        body {
            background-color: rgb(101, 204, 243);
        }

        .header-container {
            text-align: center;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .header-container img {
            height: 80px; /* Logo height for responsiveness */
            margin-bottom: 10px; /* Space between logos */
        }

        .header-container h2 {
            font-size: 2rem; /* Larger heading for desktop */
        }

        .header-container h4 {
            font-size: 1.2rem; /* Adjusted font size */
        }

        /* Responsive Design for Mobile */
        @media (max-width: 768px) {
            .header-container img {
                height: 60px; /* Smaller logo size on mobile */
            }

            .header-container h2 {
                font-size: 1.5rem; /* Smaller heading on mobile */
            }

            .header-container h4 {
                font-size: 1rem;
            }
        }

        @media (max-width: 480px) {
            .header-container img {
                height: 50px; /* Further adjust logo size for very small screens */
            }

            .header-container h2 {
                font-size: 1.2rem; /* Further reduce heading size for very small screens */
            }

            .header-container h4 {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<meta charset="UTF-8">
<meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
<title>RSTL &mdash; Calendar Scheduling Management System</title>

<!-- General CSS Files -->
<link rel="stylesheet" href="assets/modules/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="assets/modules/fontawesome/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<!-- CSS Libraries -->
<link rel="stylesheet" href="assets/css/style.min.css">
<link rel="stylesheet" href="assets/css/components.min.css">
<body style="background-color:rgb(101, 204, 243)">

<!-- Container for logos and heading -->
<div class="container text-center mt-4">
    <div class="header-container">
        <!-- Logos -->
        <img src="assets/img/dost.png" alt="DOST Logo">
        <img src="assets/img/bagongpilipinas.png" alt="Bagong Pilipinas Logo">
        <!-- Heading Below Logos -->
        <h2>Regional Standards And Testing Laboratories</h2>
        <h4>Department of Science and Technology (DOST) - Region 10</h4>
    </div>

<!-- Purpose: Home page for the website. It provides two options to the user: check reservation status and make a reservation. -->
<!-- The user can click on the buttons to navigate to the respective pages. -->
<?php include('includes/header.php'); ?>
<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-12 col-md-10 col-lg-8">
            <div class="card card-info" style="border-radius: 10px;">
                <div class="card-header text-center">
                    <!-- Mobile-friendly heading with scaling for different screen sizes -->
                    <h1 class="w-100">WHAT DO YOU WANT TO DO TODAY?</h1>
                </div>
                <div class="card-body text-center">
                    <!-- Buttons with responsive design -->
                    <button class="btn btn-info m-2 btn-lg w-100 w-sm-auto" onclick="location.href='reservation_input.php'">
                        Check Reservation Status
                    </button>
                    <button class="btn btn-info m-2 btn-lg w-100 w-sm-auto" onclick="location.href='purpose.php'">
                        Make a Reservation
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('includes/footer.php'); ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
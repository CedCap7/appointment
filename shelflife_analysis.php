<?php include('includes/header.php'); ?>
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-12 col-md-6 col-lg-4">
                <div style="border-radius: 10px;" class="card card-info">
                    <div class="card-header text-center">
                        <h1>TYPE OF ANALYSIS?</h1>
                    </div>
                    <div class="card-body text-center">
                        <button class="btn btn-info m-1" onclick="location.href='shelflife_evaluation.php'">Shelf Life Evaluation</button>
                        <button class="btn btn-info m-1" onclick="location.href='shelflife_sensory_confirmation.php'">Sensory Evaluation</button>
                        <button class="btn btn-info m-1" onclick="location.href='noreserv.php'">Others</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php include('includes/footer.php'); ?>
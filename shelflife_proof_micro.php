<?php include('includes/header.php'); ?>

<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-12 col-md-10 col-lg-8">
            <div style="border-radius: 10px;" class="card card-info">
                <div class="card-header text-center">
                    <h1>Upload Proof of Microbiological Analysis</h1>
                </div>
                <div class="card-body text-center">
                    <form action="process_upload.php" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="full_name">Full Name</label>
                            <input type="text" name="full_name" id="full_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="contact">Contact Number</label>
                            <input type="text" name="contact" id="contact" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="proofFile">Upload Proof (PDF only)</label>
                            <input type="file" name="proofFile" id="proofFile" class="form-control" accept=".pdf" required>
                        </div>
                        <button type="submit" class="btn btn-success m-1">Upload & Continue</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>

<?php include('includes/header.php'); ?>

<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-12 col-md-10 col-lg-8">
            <div style="border-radius: 10px;" class="card card-info">
                <div class="card-header text-center">
                    <h1>Shelf Life Evaluation Form</h1>
                </div>
                <div class="card-body text-center">
                    <p>📄 Download the form, fill it out, and upload it when completed.</p>

                    <!-- 📥 Download Link -->
                    <a href="assets/files/shelflife_questionnaire.pdf" class="btn btn-primary m-1" download>Download Form</a>

                    <hr>

                    <!-- 📤 File Upload & Email Form -->
                    <form id="uploadForm" action="send_shelflife_form.php" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="formFile">Upload Completed Form (PDF only)</label>
                            <input type="file" name="formFile" id="formFile" class="form-control" accept=".pdf" required onchange="previewPDF(event)">
                        </div>

                        <!-- 🖼 PDF Preview -->
                        <div id="pdfPreviewContainer" style="display: none; margin-top: 10px;">
                            <embed id="pdfPreview" src="" type="application/pdf" width="100%" height="400px">
                        </div>

                        <hr>

                        <!-- 📧 Client Information -->
                        <div class="form-group">
                            <label for="full_name">Enter Your Full Name:</label>
                            <input type="text" name="full_name" id="full_name" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Enter Your Email Address:</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="contact">Enter Your Contact Number:</label>
                            <input type="text" name="contact" id="contact" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-success m-1">Send File</button>
                    </form>

                    <hr>
                    <button class="btn btn-secondary m-1" onclick="location.href='index.php'">Go Back</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewPDF(event) {
    var file = event.target.files[0];
    if (file && file.type === "application/pdf") {
        var fileURL = URL.createObjectURL(file);
        document.getElementById('pdfPreview').src = fileURL;
        document.getElementById('pdfPreviewContainer').style.display = "block";
    } else {
        alert("Invalid file type! Please upload a PDF file.");
        event.target.value = ""; // Clear the file input
        document.getElementById('pdfPreviewContainer').style.display = "none";
    }
}
</script>

<?php include('includes/footer.php'); ?>

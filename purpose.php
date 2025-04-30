<?php include('includes/header.php'); ?>

<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-12 col-md-10 col-lg-8">
            <div style="border-radius: 10px;" class="card card-info text-center">
                <div class="card-header text-center">
                    <h1 class="w-100">WHAT IS YOUR PURPOSE?</h1>
                </div>
                
                <!-- Desktop Layout (Buttons) -->
                <div class="card-body text-center d-none d-md-block">
                    <button class="btn btn-info m-1" 
                        onmouseover="updateDescription('Select this lab for services like tank-truck calibration, onsite calibration, and calibration of metrological devices.')"
                        onmouseout="clearDescription()"
                        onclick="location.href='metro_inhouse.php'">
                        Metrology Laboratory
                    </button>
                    <button class="btn btn-info m-1" 
                        onmouseover="updateDescription('Choose this lab for testing and analysis of food, feed products, raw materials, water, wastewater, fertilizers, plant tissues, and related materials.')"
                        onmouseout="clearDescription()"
                        onclick="location.href='physico_form.php'">
                        Chemical Testing Laboratory
                    </button>
                    <button class="btn btn-info m-1" 
                        onmouseover="updateDescription('Opt for this lab to test for microbial presence in food, feed, raw materials, and water samples.')"
                        onmouseout="clearDescription()"
                        onclick="location.href='micro_form.php'">
                        Microbiological Testing Laboratory
                    </button>
                    <button class="btn btn-info m-1" 
                        onmouseover="updateDescription('Use this lab to analyze the nutrient content and determine the shelf life of your food, feed products, and raw materials.')"
                        onmouseout="clearDescription()"
                        onclick="location.href='shelflife_sample_type.php'">
                        Shelf Life Laboratory
                    </button>
                </div>

                <!-- Mobile Layout (Buttons) -->
                <div class="card-body text-center d-md-none">
                    <button class="btn btn-info m-1 w-100" onclick="location.href='metro_inhouse.php'">
                        Metrology Laboratory
                    </button>
                    <button class="btn btn-info m-1 w-100" onclick="location.href='physico_form.php'">
                        Chemical Testing Laboratory
                    </button>
                    <button class="btn btn-info m-1 w-100" onclick="location.href='micro_form.php'">
                        Microbiological Testing Laboratory
                    </button>
                    <button class="btn btn-info m-1 w-100" onclick="location.href='shelflife_sample_type.php'">
                        Shelf Life Laboratory
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Description Table (Mobile View Only) -->
        <div class="d-md-none">
            <table class="table table-bordered mt-4">
                <thead>
                    <tr>
                        <th>Laboratory</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Metrology Laboratory</td>
                        <td>Services like tank-truck calibration, onsite calibration, and calibration of metrological devices.</td>
                    </tr>
                    <tr>
                        <td>Chemical Testing Laboratory</td>
                        <td>Testing and analysis of food, feed products, raw materials, water, wastewater, fertilizers, plant tissues, and related materials.</td>
                    </tr>
                    <tr>
                        <td>Microbiological Testing Laboratory</td>
                        <td>Testing for microbial presence in food, feed, raw materials, and water samples.</td>
                    </tr>
                    <tr>
                        <td>Shelf Life Laboratory</td>
                        <td>Analyzing nutrient content and determining the shelf life of food, feed products, and raw materials.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Description Box -->
    <h6 style="background-color: #ADDFFF; color: black; padding: 10px; border-radius: 7px; box-shadow: 0px 6px 8px rgba(0, 0, 0, 0.1); position: relative; z-index: 10;" id="description-text" class="m-3 font-weight-bold text-center"></h6>
</div>

<?php include('includes/footer.php'); ?>

<script>
// Update description text based on button hover for desktop
function updateDescription(text) {
    let desc = document.getElementById("description-text");
    desc.innerText = text;
    desc.classList.add("show");
}

// Clear description text
function clearDescription() {
    let desc = document.getElementById("description-text");
    desc.classList.remove("show");
}
</script>

<style>
/* Fix overlapping text on buttons */
button {
    white-space: normal; /* Allow text to wrap inside buttons */
    word-wrap: break-word;
    font-size: 1rem !important; /* Adjust font size */
    padding: 12px 24px !important; /* Add padding to make the button more spacious */
    line-height: 1.3; /* Adjust line height for readability */
}

/* Mobile Buttons Fix - Make text fit in buttons */
.d-md-none button {
    font-size: 1.1rem; /* Adjust font size for mobile */
    padding: 15px 25px !important;
    white-space: normal; /* Text wrapping */
    word-wrap: break-word;
}
</style>

<?php include('includes/header.php'); ?>

<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-12 col-md-9 col-lg-7">
            <div style="border-radius: 10px;" class="card card-info">
                <div class="card-header text-center">
                    <h1>CHECK RESERVATION STATUS</h1>
                </div>
                <div class="card-body text-center">
                    <form action="reservation_details.php" method="GET">
                        <input type="text" name="unique_id" placeholder="Please enter your transaction number" class="form-control m-1-2" required style="background-color: transparent; font-weight: bold;" onfocus="this.style.borderColor='black'; this.style.backgroundColor='transparent';" oninput="this.style.fontWeight='bold';" onblur="if(this.value==''){this.style.fontWeight='normal';}">
                        <style>
                            ::placeholder {
                                font-weight: normal;
                            }
                        </style>
                        <input type="submit" class="btn btn-info mt-3" value="SUBMIT">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>

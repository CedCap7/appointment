"use strict";

$(".swal-button").click(function () {
  const buttonId = $(this).data("id");

  swal({
    title: "Transaction Details",
    content: {
      element: "div",
      attributes: {
        innerHTML: `
          <table style="width: 100%; border-collapse: collapse;">
            <tr style="border-bottom: 1px solid #ddd;">
              <td style="padding: 8px; font-weight: bold;">Sample Name</td>
              <td style="padding: 8px;">Palapa Cubes</td>
            </tr>
            <tr style="border-bottom: 1px solid #ddd;">
              <td style="padding: 8px; font-weight: bold;">Number of Sample</td>
              <td style="padding: 8px;">3</td>
            </tr>
            <tr style="border-bottom: 1px solid #ddd;">
              <td style="padding: 8px; font-weight: bold;">Testing/Calibration Service <br>to avail</td>
              <td style="padding: 8px;">Proximate Analysis</td>
            </tr>
            <tr>
              <td style="padding: 8px; font-weight: bold;">Phone Number</td>
              <td style="padding: 8px;">09171234567</td>
            </tr>
          </table>
        `,
      },
    },
    buttons: {
      cancel: "Cancel",
      confirm: {
        text: "Confirm",
        value: true,
        visible: true,
        className: "",
        closeModal: true,
      },
    },
    dangerMode: true,
  }).then((willDelete) => {
    if (willDelete.value) {
      const sampleName = document.getElementById("sampleName").value;
      const numberOfSample = document.getElementById("numberOfSample").value;
      const service = document.getElementById("service").value;
      const phoneNumber = document.getElementById("phoneNumber").value;
      swal(
        `Poof! Your imaginary file with details: ${sampleName}, ${numberOfSample}, ${service}, ${phoneNumber} has been deleted!`,
        {
          icon: "success",
        }
      );
    } else {
      swal(
        "Reservation Confirmed! A confirmation email has been sent to the customer"
      );
    }
  });
});

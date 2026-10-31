/* *** *** *** *** *** *** *** *** *** *** *** *** *** *** *** ***
/////////////////   Down Load Button Function   /////////////////
*** *** *** *** *** *** *** *** *** *** *** *** *** *** *** *** */

// (function ($) {
//   'use strict';

//   $('#tm_download_btn').on('click', function () {
//     var downloadSection = $('#tm_download_section');
//     var cWidth = downloadSection.width();
//     var cHeight = downloadSection.height();
//     var topLeftMargin = 0;
//     var pdfWidth = cWidth + topLeftMargin * 2;
//     var pdfHeight = pdfWidth * 1.5 + topLeftMargin * 2;
//     var canvasImageWidth = cWidth;
//     var canvasImageHeight = cHeight;
//     var totalPDFPages = Math.ceil(cHeight / pdfHeight) - 1;

//     html2canvas(downloadSection[0], { allowTaint: true }).then(function (
//       canvas
//     ) {
//       canvas.getContext('2d');
//       var imgData = canvas.toDataURL('image/png', 1.0);
//       var pdf = new jsPDF('p', 'pt', [pdfWidth, pdfHeight]);
//       pdf.addImage(
//         imgData,
//         'PNG',
//         topLeftMargin,
//         topLeftMargin,
//         canvasImageWidth,
//         canvasImageHeight
//       );
//       for (var i = 1; i <= totalPDFPages; i++) {
//         pdf.addPage(pdfWidth, pdfHeight);
//         pdf.addImage(
//           imgData,
//           'PNG',
//           topLeftMargin,
//           -(pdfHeight * i) + topLeftMargin * 0,
//           canvasImageWidth,
//           canvasImageHeight
//         );
//       }
//       pdf.save('download.pdf');
//     });
//   });

// })(jQuery);



// $('#tm_download_btn').on('click', function () {
//   var orderId = '12345'; // Replace this with a dynamic value later

//   var downloadSection = $('#tm_download_section');
//   var cWidth = downloadSection.width();
//   var cHeight = downloadSection.height();
//   var topLeftMargin = 0;
//   var pdfWidth = cWidth + topLeftMargin * 2;
//   var pdfHeight = pdfWidth * 1.5 + topLeftMargin * 2;
//   var canvasImageWidth = cWidth;
//   var canvasImageHeight = cHeight;
//   var totalPDFPages = Math.ceil(cHeight / pdfHeight) - 1;

//   html2canvas(downloadSection[0], { allowTaint: true }).then(function (
//     canvas
//   ) {
//     canvas.getContext('2d');
//     var imgData = canvas.toDataURL('image/png', 1.0);
//     var pdf = new jsPDF('p', 'pt', [pdfWidth, pdfHeight]);
//     pdf.addImage(
//       imgData,
//       'PNG',
//       topLeftMargin,
//       topLeftMargin,
//       canvasImageWidth,
//       canvasImageHeight
//     );
//     for (var i = 1; i <= totalPDFPages; i++) {
//       pdf.addPage(pdfWidth, pdfHeight);
//       pdf.addImage(
//         imgData,
//         'PNG',
//         topLeftMargin,
//         -(pdfHeight * i) + topLeftMargin * 0,
//         canvasImageWidth,
//         canvasImageHeight
//       );
//     }
//     pdf.save('order_' + orderId + '.pdf');
//   });
// });

(function ($) {
  'use strict';

  $('#tm_download_btn').on('click', function () {
    // Get the order ID from the data attribute
    var orderId = $(this).data('order-id');

    var downloadSection = $('#tm_download_section');
    var cWidth = downloadSection.width();
    var cHeight = downloadSection.height();
    var topLeftMargin = 0;
    var pdfWidth = cWidth + topLeftMargin * 2;
    var pdfHeight = pdfWidth * 1.5 + topLeftMargin * 2;
    var canvasImageWidth = cWidth;
    var canvasImageHeight = cHeight;
    var totalPDFPages = Math.ceil(cHeight / pdfHeight) - 1;

    html2canvas(downloadSection[0], { scale: 1.8,allowTaint: true }).then(function (
      canvas
    ) {
      canvas.getContext('2d');
      var imgData = canvas.toDataURL('image/png', 1.0);
      
      var pdf = new jsPDF('p', 'pt', [pdfWidth, pdfHeight]);
      pdf.addImage(
        imgData,
        'PNG',
        topLeftMargin,
        topLeftMargin,
        canvasImageWidth,
        canvasImageHeight
      );
      for (var i = 1; i <= totalPDFPages; i++) {
        pdf.addPage(pdfWidth, pdfHeight);
        pdf.addImage(
          imgData,
          'PNG',
          topLeftMargin,
          -(pdfHeight * i) + topLeftMargin * 0,
          canvasImageWidth,
          canvasImageHeight
        );
      }
      // Save the PDF with the order ID as the filename
      pdf.save(orderId + '.pdf');
    });
  });
  
  $('#tm_download_btn_img').on('click', function () {
    // Get the order ID from the data attribute
    var orderId = $(this).data('order-id');

    var downloadSection = $('#tm_download_section');
    var cWidth = downloadSection.width();
    var cHeight = downloadSection.height();
    var topLeftMargin = 0;
    var pdfWidth = cWidth + topLeftMargin * 2;
    var pdfHeight = pdfWidth * 1.5 + topLeftMargin * 2;
    var canvasImageWidth = cWidth;
    var canvasImageHeight = cHeight;
    var totalPDFPages = Math.ceil(cHeight / pdfHeight) - 1;

    html2canvas(downloadSection[0], { scale: 1.8,allowTaint: true }).then(function (
      canvas
    ) {
      canvas.getContext('2d');
      var imgData = canvas.toDataURL('image/png', 1.0);
      
      // Create a temporary link to trigger the image download
      var link = document.createElement('a');
      link.href = imgData;
      link.download = orderId + '.png'; // Set the file name

      // Trigger the download
      link.click();
      
      // Save the PDF with the order ID as the filename
      //pdf.save(orderId + '.pdf');
    });
  });

})(jQuery);






$(document).ready(function () {
  // Load Lottie animation
  lottie.loadAnimation({
    container: document.getElementById('lottie-player'),
    renderer: 'svg',
    loop: true,
    autoplay: true,
    path: 'customer.json' // Make sure this file is in the same directory
  });


  $('.main-btn').click(function () {
    const btnText = $(this).text().toLowerCase();

    if (btnText.includes("business")) {
      window.location.href = "./businesses";
    } else if (btnText.includes("customer")) {
      window.location.href = "./customer";
    }
  });

});

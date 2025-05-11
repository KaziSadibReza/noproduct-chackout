jQuery(document).ready(function ($) {
  $("#custom_amount").on("input", function (e) {
    e.preventDefault();

    const amount = $(this).val();
    if (amount === "" || isNaN(amount) || parseFloat(amount) < 0) return;

    $.ajax({
      type: "POST",
      url: cartUpdateParams.ajax_url,
      data: {
        action: "update_cart_amount",
        custom_amount: amount,
        nonce: cartUpdateParams.nonce,
      },
      success: function (response) {
        if (response.success) {
          // Update the checkout total for faster update
          $('[data-title="Total"] .woocommerce-Price-amount').html(
            response.data.total
          );
          // Trigger update for getting the new total
          $(document.body).trigger("update_checkout");
        }
      },
    });
  });
});

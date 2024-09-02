jQuery(document).ready(function ($) {
  const logo_field = $("#pips_invoice_logo");
  logo_field.on("click", () => {
    var image_frame;
    if (image_frame) {
      image_frame.open();
    }
    // Define image_frame as wp.media object
    image_frame = wp.media({
      title: "Select Logo",
      multiple: false,
      library: {
        type: "image",
      },
    });

    image_frame.on("close", function () {
      // On close, get selections and save to the hidden input
      // plus other AJAX stuff to refresh the image preview
      var selection = image_frame.state().get("selection");
      var url = null;
      selection.each(function (attachment) {
        url = attachment.attributes.url;
      });
      if (!url) return true;
      $("input#pips_invoice_logo").val(url);
      $(".woocommerce-save-button").prop("disabled", false);
    });

    image_frame.on("open", function () {
      // On open, get the id from the hidden input
      // and select the appropiate images in the media manager
      var selection = image_frame.state().get("selection");
      var id = $("input#pips_invoice_logo").val();
      var attachment = wp.media.attachment(id);
      attachment.fetch();
      selection.add(attachment ? [attachment] : []);
    });

    image_frame.open();
  });
});

(function($) {
  
$('#jopfre-change-pass-form').ajaxForm({
  url:  jopfreChangePass.ajaxUrl,
  data: {
    action: 'jopfre-change-pass',
    nonce: jopfreChangePass.nonce
  },
  dataType: 'json',
  beforeSubmit: function(formData, jqForm, options) {
    $('.status').html('');
  },
  success: function(res, statusText, xhr, $form) {
    console.log(res);
    console.log(statusText);
    $('.'+res.statusField).addClass(res.status).html(res.message);
    if(res.status === "success") {
      setTimeout(function(){ 
        location.reload();
      }, 1000);
    }
  },
  error: function(xhr, status, error) {
    console.log(xhr);
    console.log(status);
    console.log(error);
  }
});

  
})( jQuery );
(function ($) {
  $(document).ready(function() {    
    var hasError = false;
    $('#phone-number-field').on('blur', function validatePhoneNumber() {
      var phoneNum = $(this).val().replace(/\D/g, '');
      if (phoneNum.length != 10) {
        $('.phoneerr').html('Please enter a valid 10-digit phone number.');
        hasError = true;
      } else {
        $('.phoneerr').html('');
        hasError = false;
        $('#phone-number-field').off('blur', validatePhoneNumber);
      }
    });

    Drupal.behaviors.myModuleBehavior = {
      attach: function (context, settings) {
        $('#edit-email-error').once('myModuleBehavior').each(function() {
          alert('Invalid email!');
        });
      }
    };
  });
  })(jQuery);

  
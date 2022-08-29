$(function () {
  'use strict';

  $.validator.setDefaults({
    submitHandler: function () {
      alert("ارسال شد!");
    }
  });
  $(function () {
    // validate signup form on keyup and submit
    $("#signupForm").validate({
      rules: {
        name: {
          required: true,
          minlength: 3
        },
        password: {
          required: true,
          minlength: 5
        },
        confirm_password: {
          required: true,
          minlength: 5,
          equalTo: "#password"
        },
        email: {
          required: true,
          email: true
        },
        topic: {
          required: "#newsletter:checked",
          minlength: 2
        },
        agree: "required"
      },
      messages: {
        name: {
          required: "لطفا نام خود را وارد کنید",
          minlength: "نام نباید کمتر از 3 کاراکتر باشد"
        },
        password: {
          required: "لطفا رمز عبور خود را وارد کنید",
          minlength: "طول رمز عبور نباید کمتر از 5 کاراکتر باشد"
        },
        confirm_password: {
          required: "لطفا رمز عبور خود را وارد کنید",
          minlength: "طول رمز عبور نباید کمتر از 5 کاراکتر باشد",
          equalTo: "لطفا تکرار رمز عبور را صحیح وارد کنید"
        },
        email: "لطفا یک آدرس ایمیل معتبر وارد کنید",
      },
      errorPlacement: function (label, element) {
        label.addClass('mt-1 tx-13 text-danger');
        label.insertAfter(element);
      },
      highlight: function (element, errorClass) {
        $(element).parent().addClass('validation-error')
        $(element).addClass('border-danger')
      }
    });
  });
});
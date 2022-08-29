$(function () {

  showSwal = function (type) {
    'use strict';
    if (type === 'basic') {
      swal.fire({
        text: 'لورم ایپسوم متن ساختگی با تولید سادگی',
        confirmButtonText: 'بستن',
        confirmButtonClass: 'btn btn-danger',
      })
    } else if (type === 'title-and-text') {
      Swal.fire(
        'لورم ایپسوم؟',
        'لورم ایپسوم متن ساختگی با تولید سادگی',
        'question'
      )
    } else if (type === 'title-icon-text-footer') {
      Swal.fire({
        type: 'error',
        title: 'اوپس...',
        text: 'خطایی رخ داده است!',
        footer: '<a href>آیا سوالی دارید؟</a>'
      })
    } else if (type === 'custom-html') {
      Swal.fire({
        title: '<strong><u>مثال</u> HTML</strong>',
        icon: 'info',
        html: 'شما می توانید از <b>متن تو پُر</b> ، ' +
          '<a href="//google.com">لینک</a> ' +
          'و هر تگ HTML دیگری استفاده کنید',
        showCloseButton: true,
        showCancelButton: true,
        focusConfirm: false,
        confirmButtonText: '<i class="fa fa-thumbs-up"></i> عالی!',
        confirmButtonAriaLabel: 'Thumbs up, great!',
        cancelButtonText: '<i data-feather="thumbs-up"></i>',
        cancelButtonAriaLabel: 'Thumbs down',
      });
      feather.replace();
    } else if (type === 'custom-position') {
      Swal.fire({
        position: 'top-end',
        icon: 'success',
        title: 'اطلاعات شما با موفقیت ذخیره شد',
        showConfirmButton: false,
        timer: 1500
      })
    } else if (type === 'passing-parameter-execute-cancel') {
      const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
          confirmButton: 'btn btn-success',
          cancelButton: 'btn btn-danger me-2'
        },
        buttonsStyling: false,
      })

      swalWithBootstrapButtons.fire({
        title: 'آیا مطمئن هستید؟',
        text: "بعد از حذف شما قادر به بازگردانی اطلاعات نخواهید بود",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonClass: 'me-2',
        confirmButtonText: 'بله، حذف شود!',
        cancelButtonText: 'نه، لغو شود',
        reverseButtons: true
      }).then((result) => {
        if (result.value) {
          swalWithBootstrapButtons.fire(
            'حذف شد!',
            'فایل مورد نظر شما حذف شد!',
            'success'
          )
        } else if (
          // Read more about handling dismissals
          result.dismiss === Swal.DismissReason.cancel
        ) {
          swalWithBootstrapButtons.fire(
            'لغو شد',
            'شما حذف فایل را لغو کردید :)',
            'error'
          )
        }
      })
    } else if (type === 'message-with-auto-close') {
      let timerInterval
      Swal.fire({
        title: 'هشدار با قابلیت بسته شدن خودکار!',
        html: 'این هشدار بعد از <b></b> میلی ثانیه بسته می شود.',
        timer: 2000,
        timerProgressBar: true,
        didOpen: () => {
          Swal.showLoading()
          timerInterval = setInterval(() => {
            const content = Swal.getHtmlContainer()
            if (content) {
              const b = content.querySelector('b')
              if (b) {
                b.textContent = Swal.getTimerLeft()
              }
            }
          }, 100)
        },
        willClose: () => {
          clearInterval(timerInterval)
        }
      }).then((result) => {
        /* Read more about handling dismissals below */
        if (result.dismiss === Swal.DismissReason.timer) {
          console.log('I was closed by the timer')
        }
      })
    } else if (type === 'message-with-custom-image') {
      Swal.fire({
        title: 'هشدار شیرین!',
        text: 'مدال به همراه یک عکس سفارشی',
        // imageUrl: '../../../../../../https@unsplash.it/400/200',
        imageUrl: '../../../assets/images/photos/img3.jpg',
        imageWidth: 400,
        imageHeight: 200,
        imageAlt: 'تصویر سفارشی',
      })
    } else if (type === 'mixin') {
      const Toast = Swal.mixin({
        toast: true,
        position: 'top-start',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
      });

      Toast.fire({
        icon: 'success',
        title: 'ثبت نام شما با موفقیت انجام شد'
      })
    }
  }

});
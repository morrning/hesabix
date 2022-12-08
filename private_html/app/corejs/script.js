$(document).on('keydown', 'input[type=number]', function(e) {
    var key = e.charCode || e.keyCode || 0;
    // allow backspace, tab, delete, enter, arrows, numbers and keypad numbers ONLY
    // home, end, period, and numpad decimal
    return (
        key == 8 ||
        key == 9 ||
        key == 13 ||
        key == 46 ||
        key == 110 ||
        key == 190 ||
        (key >= 35 && key <= 40) ||
        (key >= 48 && key <= 57) ||
        (key >= 96 && key <= 105));
});

var modalLoaded = null;
var lastPage = ['/app/dashboard'];
function coreLoading(state = true){
    if(state){
        $('.c-loading').removeClass('d-none')
    }
    else{
        $('.c-loading').addClass('d-none');
    }
}
 function backBtn(){
    if(lastPage.length <= 1){
        loadComponent('/app/dashboard');
    }
    else{
        lastPage.pop();
        loadComponent(lastPage.pop());
    }

 }
$(document).on('submit', 'form', function(e){
    if(!$('form').hasClass('ajax-off')){
        e.preventDefault(); // avoid to execute the actual submit of the form.
        $('.c-loading').removeClass('d-none')
        var form = $(this);
        var actionUrl = form.attr('action');
        $.ajax({
            type: "POST",
            url: actionUrl,
            data: form.serialize(), // serializes the form's elements.
            success: function(data)
            {
                $('.c-loading').addClass('d-none');
                if(data.hasOwnProperty('swal')){
                    if(data.swal.hasOwnProperty('reload')){
                        swal.fire(data.swal).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        })
                    }
                    else{
                        swal.fire(data.swal)
                    }
                }
                if(data.hasOwnProperty('component')){
                    loadComponent(data.component)
                }
                if(data.hasOwnProperty('clear')){
                    if(data.clear){
                        $("form").trigger('reset');
                    }
                }
                if(data.hasOwnProperty('modal-stay')){}
                else{
                    modalLoaded.hide();
                    $('#CoreModal').remove();
                }

            }
        });
    }

});

$(document).ajaxError(
    function (event, jqXHR, ajaxSettings, thrownError) {
        if(thrownError == "Not Found" || jqXHR.status == 404){
            window.location = Routing.generate('home');
        }
        else{
            swal.fire('خطایی به وجود آمده. نوع خطا : ' + thrownError);
        }

    });
function loadComponent(component,modalShow=false){
    $('.c-loading').removeClass('d-none');
    $.ajax({
        url: component,
    })
        .done(function( data ) {
            $('.c-loading').addClass('d-none');
            if(! modalShow){
                lastPage.push(component);
            }
            if(data.hasOwnProperty('swal')){
                if(data.swal.hasOwnProperty('reload')){
                    swal.fire(data.swal).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    })
                }
                else{
                    swal.fire(data.swal)
                }
            }
            if(data.hasOwnProperty('view')){
                if(!modalShow){
                    $('.app-body').html(data.view.content);
                    document.title = data.title;
                    $('.app-title').html(data.title);
                    if(data.hasOwnProperty('topView')){
                        $('.app-top').html(data.topView.content);
                    }
                    else{
                        $('.app-top').html('');
                    }
                }
                else{
                    let modal = '<div class="modal fade" id="CoreModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="CoreModalLabel" aria-hidden="true">\n' +
                        '  <div class="modal-dialog">\n' +
                        '    <div class="modal-content">\n' +
                        '      <div class="modal-header bg-light">\n' +
                        '        <h5 class="modal-title" id="CoreModalLabel"><i class="bi bi-list me-2"></i> ' + data.title + '</h5>\n' +
                        '        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>\n' +
                        '      </div>\n' +
                        '      <div class="modal-body m-md-2">\n' +
                        '        ' + data.view.content + '\n' +
                        '      </div>\n' +
                        '    </div>\n' +
                        '  </div>\n' +
                        '</div>\n';
                    $('body').append($(modal));
                    modalLoaded = new bootstrap.Modal('#CoreModal');
                    const modalevent = document.getElementById('CoreModal')
                    modalevent.addEventListener('hidden.bs.modal', event => {
                        $('#CoreModal').remove();
                    })
                    modalLoaded.show();
                }
                reloadJs();
            }

        });
}

function reloadJs(){
    $(function () {
        'use strict';
        if( $('table.dtds').length )
        {
            var table = new Tabulator("table.dtds", {
            });
        }

        $(function () {
            if ( $.fn.dataTable.isDataTable( '.dtd' ) ) {
                table = $('.dtd').DataTable();
            }
            else {
                $('.dtd').DataTable({
                    "aLengthMenu": [
                        [10, 30, 50, -1],
                        [10, 30, 50, "همه"]
                    ],
                    "iDisplayLength": 10,
                    "language": {
                        url: '/app/assets/dt/fa.json'
                    }
                });
            }
        });
    });
}
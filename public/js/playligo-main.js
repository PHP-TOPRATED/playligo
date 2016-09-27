$(document).ready(function () {

    // Clear filter form
    $('html').on('click', '.btn-clear', function (event) {
        event.preventDefault();
        $.ajax({
            url: $(this).closest('form').attr('action'),
            type: 'POST',
            dataType: 'json',
            data: {reset_form: 1, _token: $("input[name='_token']").val()},
            success: function (data) {
                setTimeout(function () {
                    location.reload();
                }, 1000);
            }
        });
    });

    $('html').on('click', '.cancel-button', function (event) {
        event.preventDefault();
        var goto = $(this).attr('goto');
        window.location = goto;
    });

    // $('html').on('click', '.btn-modal-rad', function (event) {
    //     event.preventDefault();
    //     var target = $(this).attr("href");
    //
    //     // load the url and show modal on success
    //     $("#basicModal .modal-content").load(target, function() {
    //       $('#basicModal').modal('show');
    //     });
    // });

    $('html').on('click', '.btn-modal', function (event) {
        event.preventDefault();
        var target = $(this).attr("href");

        $('#basicModal').find('.modal-content').html('');
        $('#basicModal').modal('show');
        $('#basicModal').find('.modal-content').load($(this).attr('href'));
    });

    // $("#basicModal").on('hidden.bs.modal', function () {
    $('body').on('hidden.bs.modal', '#basicModal', function (e) {
        // $(this).data('bs.modal', null);
        // $(".modal-body").html("");
        //  $(this).removeData('bs.modal');
        $("#previewVideo").attr("src", "");
    });

    $('html').on('submit', '.submit-ajax', function (event) {
        event.preventDefault();
        darkLoading(true);
        var formData = new FormData();
        var data = $(this).serializeArray();
        $.each($(':file'), function () {
            var fileinput = $(this);
            $.each(fileinput[0].files, function (key, file) {
                formData.append(fileinput[0].name, file);
            });
        });
        $.each(data, function (key, input) {
            formData.append(input.name, input.value)
        });
        $.ajax({
            url: $(this).closest('form').attr('action'),
            type: 'POST',
            dataType: 'json',
            processData: false,
            contentType: false,
            data: formData,
            success: function (data) {
                darkLoading(false);
                sweetAlert("Yay!", data.message, "success");
                setTimeout(function () {
                    if (data.redirect) {
                        window.location = data.redirect;
                    } else {
                        location.reload();
                    }
                }, 2000);
            },
            error: function (xhr, status, error) {
                darkLoading(false);
                var err = jQuery.parseJSON(xhr.responseText);
                var errStr = '';
                $.each(err, function (key, value) {

                    if (key != 'redirect') {
                        errStr = errStr + value + "\n";
                    }
                });
                sweetAlert("Oops...", errStr, "error");
                if (err.redirect) {
                    setTimeout(function () {
                        window.location = err.redirect;
                    }, 2000);
                }
            }
        });
    });

    $('html').on('submit', '.submit-ajax-get', function (event) {
        event.preventDefault();
        darkLoading(true);
        $.ajax({
            url: $(this).closest('form').attr('action'),
            type: 'GET',
            dataType: 'json',
            data: $(this).serialize(),
            success: function (data) {
                if (data.message) {
                    darkLoading(false);
                    swal({
                        title: "Yay!",
                        text: data.message,
                        type: "success",
                        showConfirmButton: false
                    });
                }
                setTimeout(function () {
                    if (data.redirect) {
                        window.location = data.redirect;
                    } else {
                        location.reload();
                    }
                }, 2000);
            },
            error: function (xhr, status, error) {
                darkLoading(false);
                var err = jQuery.parseJSON(xhr.responseText);
                var errStr = '';
                $.each(err, function (key, value) {
                    errStr = errStr + value + "\n";
                });
                sweetAlert("Oops...", errStr, "error");
            }
        });
    });

});

function darkLoading(state) {
    if (state) {
        $('.loading-overlay').addClass('active');
    } else {
        $('.loading-overlay').removeClass('active');
    }
}

// Loadning animation
function loader(container, state) {
    var loading_html = '<div class="loader">' +
        '<img src="/img/gears.svg" alt="Loading..."/>' +
        '</div>';
    if (state) {
        container.append(loading_html);
    } else {
        container.children('.loader').remove();
    }
};

$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});

$('body').on('click', '.btn-publish', function (event) {
    event.preventDefault();
    var pl_id = $(this).data('pl_id');
    var url = $(this).data('url');
    var title_url = $(this).data('title_url');
    swal.showInputError("You need to write playlist title!");
    if (!$(this).data('title')) {
        swal({
            title: "Add an Inspirational Title",
            text: '<fieldset>' +
            '<input id="swal-title-input" type="text" tabindex="3" placeholder="">' +
            '<span class="help-block">(e.g. 10 tours and activities you didn\'t expect to find in Bali)</span>' +
            '<div class="sa-input-error"></div>' +
            '</fieldset>',
            html: true,
            showCancelButton: true,
            confirmButtonText: 'Submit',
            closeOnConfirm: false
        }, function (inputValue) {
            var titleValue = $('#swal-title-input').val();
            if (titleValue === false) return false;
            if (titleValue === "") {
                swal.showInputError("You need to write playlist title!");
                return false;
            } else {
                $.ajax({
                    url: title_url,
                    type: 'POST',
                    dataType: 'json',
                    data: {pl_title: titleValue, pl_id: pl_id},
                    success: function (data) {
                        publishPlaylist(pl_id, url);
                    },
                    error: function (xhr, status, error) {
                        console.log(xhr.responseText);
                        var err = jQuery.parseJSON(xhr.responseText);
                        var errStr = '';
                        $.each(err, function (key, value) {
                            errStr = errStr + value + "\n";
                        });
                        sweetAlert("Oops...", errStr, "error");
                    }
                });
            }
        });
    } else {
        publishPlaylist(pl_id, url);
    }
});

function publishPlaylist(pl_id, url) {
    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'json',
        data: {
            id: pl_id
        },
        success: function (data) {
            darkLoading(false);
            sweetAlert("Yay!", data.message, "success");
            setTimeout(function () {
                if (data.redirect) {
                    window.location = data.redirect;
                } else {
                    location.reload();
                }
            }, 2000);
        },
        error: function (xhr, status, error) {
            darkLoading(false);
            var err = jQuery.parseJSON(xhr.responseText);
            var errStr = '';
            $.each(err, function (key, value) {
                errStr = errStr + value + "\n";
            });
            sweetAlert("Oops...", errStr, "error");
        }
    });
}
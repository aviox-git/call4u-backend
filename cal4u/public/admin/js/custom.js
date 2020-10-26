$(document).ready(function () {
    setTimeout(function () {
        //$(".error-success-msg-wrapper").animate({ height: 0, opacity: 0 }, 'slow').remove();
    }, 3000);

    $('.status-active-deactive').bootstrapToggle({
        on: 'Enabled',
        off: 'Disabled',
        size: 'small'
    });

    function bootstrapNotifyAlert(type, message)
    {
        $.notify({
            // options
            icon: 'glyphicon glyphicon-warning-sign',
            title: '',
            message: message,
            url: 'https://github.com/mouse0270/bootstrap-notify',
            target: '_blank'
        },{
            // settings
            element: 'body',
            position: null,
            type: type,
            allow_dismiss: true,
            newest_on_top: false,
            showProgressbar: false,
            placement: {
                from: "top",
                align: "right"
            },
            offset: 20,
            spacing: 10,
            z_index: 1031,
            delay: 5000,
            timer: 1000,
            url_target: '_blank',
            mouse_over: null,
            animate: {
                enter: 'animated fadeInDown',
                exit: 'animated fadeOutUp'
            },
            onShow: null,
            onShown: null,
            onClose: null,
            onClosed: null,
            icon_type: 'class',
            template: '<div data-notify="container" class="col-xs-11 col-sm-3 alert alert-{0} bootstrap-notify-wrapper" role="alert" style="z-index: 9999">' +
                '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                '<span data-notify="icon"></span> ' +
                '<span data-notify="title">{1}</span> ' +
                '<span data-notify="message">{2}</span>' +
                '<div class="progress" data-notify="progressbar">' +
                '<div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
                '</div>' +
                '<a href="{3}" target="{4}" data-notify="url"></a>' +
                '</div>'
        });

        $(".bootstrap-notify-wrapper").css("z-index", "9999");
    }

    $(".status-active-deactive").on('change', function () {
        let status = $(this).is(":checked") === true ? '1' : '0';
        let edit_id = $(this).attr("data-edit-id");
        let url = $(this).attr("data-url");

        $.ajax({
            url: url,
            type: "POST",
            dataType: "JSON",
            data: {
                edit_id: edit_id,
                status: status,
                _token: CSRF_TOKEN_AJAX
            },
            success: function (response) {
                bootstrapNotifyAlert('success', response.message);
            },
            error: function (error) {

            }
        });
    });

    $('.delete-item-confirmation').bootstrap_confirm_delete({
        debug: false,
        heading: 'Delete',
        message: 'Are you sure you want to delete this item?',
        btn_ok_label: 'Yes',
        btn_cancel_label: 'Cancel',
        data_type: 'post',
        callback: (event) => {
            // grab original clicked delete button
            var button = event.data.originalObject;
            // execute delete operation
            button.closest('tr').remove();
        },
        delete_callback: () => {
            $("#delete-resource").submit();
        },
        cancel_callback: () => {
            console.log('cancel button clicked');
        },
    });
});

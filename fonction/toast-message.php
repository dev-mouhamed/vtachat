<?php if(!empty($_SESSION['notification'])): ?>
    <script>
        $(function () {
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": true,
                "progressBar": true,
                "positionClass": "toast-top-center", // position bien prise
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };

            toastr["<?php echo $_SESSION['notification']['type']; ?>"](
                "<?php echo addslashes($_SESSION['notification']['message']); ?>"
            );
        });
    </script>
<?php endif; unset($_SESSION['notification']); ?>

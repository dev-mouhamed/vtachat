<?php if(!empty($_SESSION['alert'])):?>
        <script>
            $.notify({
                icon: 'icon-bell',
                title: 'Infos',
                message: '<?php echo $_SESSION['alert']['message'];?>',
            },{
                type: '<?php echo $_SESSION['alert']['type'];?>',
                placement: {
                    from: "bottom",
                    align: "right"
                },
                time: 1000,
            });
        </script>
	
<?php endif; unset($_SESSION['alert']);?>
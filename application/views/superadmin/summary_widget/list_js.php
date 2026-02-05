<script>
$(document).ready(function() {
    $('.delete-widget').on('click', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        var title = $(this).data('title');
        
        Swal.fire({
            title: "Are you sure?",
            text: "You are about to delete widget: " + title,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    });
});
</script>

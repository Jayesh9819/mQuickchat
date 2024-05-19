<?php
if (isset($_SESSION['role'])) {


    $role = $_SESSION['role'];
    if ($role != 'User') {
        echo '<script src="../Public/Chats/globalNotifications.js" > </script>';
    } else {
        echo '<script src="../Public/Chats/usernot.js" > </script>';
    }
}
include './Public/Popup/popup.php'
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>
    $(document).ready(function() {


        let lastSeenUpdate = function() {
            $.get('../Public/Pages/Chat/app/ajax/update_last_seen.php')
                .done(function(data) {
                    console.log('Success:', data); // Successful response handling
                })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    console.error('AJAX Error:', textStatus); // Error handling
                });
        };

        lastSeenUpdate(); // Initial call
        setInterval(lastSeenUpdate, 10000); // Set to run every 10 seconds
    });
    $(document).ready(function() {


        let lastSeenUpdat = function() {
            $.get('../api/notification.php')
                .done(function(data) {
                    console.log('Success:', data); // Successful response handling
                })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    console.error('AJAX Error:', textStatus); // Error handling
                });
        };

        lastSeenUpdat(); // Initial call
        setInterval(lastSeenUpdat, 700); // Set to run every 10 seconds
    });
</script>
<!-- jQuery Library - Load this first to ensure it's available for all jQuery-dependent scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>

<!-- Toastr Notification Library - Depends on jQuery, so it comes after jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<!-- Bootstrap Bundle JS - Includes Popper; necessary for Bootstrap components; comes after jQuery -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<script src="../assets/js/jquery.min.js"></script>
<script src="../assets/js/popper.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
<script src="../assets/js/waypoints.min.js"></script>
<!-- <script src="../assets/js/jquery.easing.min.js"></script> -->
<script src="../assets/js/owl.carousel.min.js"></script>
<script src="../assets/js/jquery.animatedheadline.min.js"></script>
<script src="../assets/js/jquery.counterup.min.js"></script>
<script src="../assets/js/wow.min.js"></script>
<script src="../assets/js/date-clock.js"></script>
<script src="../assets/js/dark-mode-switch.js"></script>
<script src="../assets/js/active.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</div> <!-- End of content-wrapper -->
    </div> <!-- End of app-container -->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Toggle mobile menu
    $(document).ready(function() {
        $('#mobile-menu-button').click(function() {
            $('.sidebar').toggleClass('active');
        });
        
        // Close mobile menu when clicking outside
        $(document).click(function(event) {
            if (!$(event.target).closest('.sidebar').length && 
                !$(event.target).closest('#mobile-menu-button').length && 
                $('.sidebar').hasClass('active')) {
                $('.sidebar').removeClass('active');
            }
        });
    });
</script>
</body>
</html>
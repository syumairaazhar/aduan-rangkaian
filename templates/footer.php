            </div> <!-- Close content-body -->
            <footer class="app-footer">
                &copy; <?php echo date("Y"); ?> PKNS Network Careline. All rights reserved.
            </footer>
        </div> <!-- Close main-content -->
    </div> <!-- Close app-layout -->
    
    <script>
        // Simple Sidebar Toggle for Mobile views
        $(document).ready(function() {
            $('#mobileNavToggle').on('click', function() {
                $('.sidebar').toggleClass('active');
            });
            
            // Close sidebar when clicking outside of it on mobile
            $(document).on('click', function(event) {
                if (!$(event.target).closest('.sidebar, #mobileNavToggle').length) {
                    $('.sidebar').removeClass('active');
                }
            });
        });
    </script>
</body>
</html>

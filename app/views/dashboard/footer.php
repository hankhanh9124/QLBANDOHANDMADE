<!-- app/views/dashboard/footer.php -->
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Real-time Return Request Polling
        (function() {
            let lastCount = -1;
            const checkInterval = 30000; // Check every 30 seconds
            const apiUrl = '<?php echo BASE_URL; ?>index.php?url=Dashboard/checkNewReturns';
            const toast = document.getElementById('return-notification-toast');
            const countSpan = document.getElementById('new-return-count');

            function poll() {
                $.ajax({
                    url: apiUrl,
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        if (data && typeof data.count !== 'undefined') {
                            const currentCount = parseInt(data.count);
                            
                            // Initialize lastCount on first run
                            if (lastCount === -1) {
                                lastCount = currentCount;
                                return;
                            }

                            // If count increased, show notification
                            if (currentCount > lastCount) {
                                countSpan.textContent = currentCount;
                                toast.style.display = 'block';
                                
                                // Play a subtle notification sound (optional)
                                try {
                                    const audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3');
                                    audio.play();
                                } catch(e) {}
                                
                                // Auto hide after 10 seconds
                                setTimeout(() => {
                                    toast.style.display = 'none';
                                }, 10000);
                            }
                            
                            lastCount = currentCount;
                        }
                    },
                    error: function() {
                        console.log('Error checking for new returns');
                    }
                });
            }

            // Start polling
            if (apiUrl.indexOf('Dashboard') !== -1) {
                // Initial check after 5 seconds to not interfere with page load
                setTimeout(poll, 5000);
                // Regular interval
                setInterval(poll, checkInterval);
            }
        })();
    </script>
    <?php include_once 'app/views/shares/chat_widget.php'; ?>
</body>
</html>

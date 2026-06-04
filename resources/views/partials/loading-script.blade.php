<script>
    /**
     * Brilliant WO Loading Overlay Handler
     * Handles loading indicators across all pages
     */
    
    document.addEventListener('DOMContentLoaded', function() {
        // Add loading on all navigation links with loading classes
        const navigationLinks = document.querySelectorAll('a[data-loading="true"], a.module-link, aside a');
        
        navigationLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                // Skip if it's a hash link, anchor, or has data-no-loading
                if (this.getAttribute('data-no-loading') === 'true' || 
                    this.href.includes('#') || 
                    this.href.includes('javascript:') ||
                    this.href.startsWith('http')) {
                    return;
                }
                
                // Show loading overlay
                if (window.loadingOverlay) {
                    // Add small delay to prevent too quick show
                    setTimeout(() => window.loadingOverlay.show(), 100);
                }
            });
        });

        // Add loading on form submissions
        const forms = document.querySelectorAll('form[data-loading="true"], #form-booking, form[method="POST"]');
        
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                // Show loading overlay when form is submitted
                if (window.loadingOverlay && this.getAttribute('data-no-loading') !== 'true') {
                    window.loadingOverlay.show();
                }
            });
        });

        // Hide loading after page load completes
        window.addEventListener('load', function() {
            // Don't auto-hide, let the server response handle it
            // setTimeout(() => window.loadingOverlay?.hide(), 500);
        });

        // Allow hiding loading manually
        window.hideLoading = function() {
            window.loadingOverlay?.hide();
        };
    });

    // Auto-hide loading on successful redirects
    // This will be triggered when the page changes
    window.addEventListener('beforeunload', function() {
        // Optionally hide when navigating away
    });
</script>

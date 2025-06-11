/**
 * Global signature validation override
 * This script disables signature validation throughout the application
 */
(function() {
    console.log('Signature validation override loaded');
    
    // Function to override signature validation
    function overrideSignatureValidation() {
        // Override common validation functions
        window.validateSignature = function() { return true; };
        window.isInvoiceSigned = function() { return true; };
        window.requiresSignature = function() { return false; };
        window.checkSignature = function() { return true; };
        
        // Override any modal or alert about signatures
        if (window.alert) {
            const originalAlert = window.alert;
            window.alert = function(message) {
                if (typeof message === 'string' && (
                    message.toLowerCase().includes('sign') || 
                    message.toLowerCase().includes('signature') || 
                    message.toLowerCase().includes('not signed')
                )) {
                    console.log('Suppressing signature alert:', message);
                    return;
                }
                return originalAlert.apply(this, arguments);
            };
        }
        
        // Override jQuery modal if it exists
        if (window.jQuery && jQuery.fn.modal) {
            const originalModal = jQuery.fn.modal;
            jQuery.fn.modal = function(options) {
                // Check if this is a signature-related modal
                const modalContent = this.find('.modal-body').text();
                if (modalContent && (
                    modalContent.toLowerCase().includes('sign') || 
                    modalContent.toLowerCase().includes('signature') || 
                    modalContent.toLowerCase().includes('not signed')
                )) {
                    console.log('Suppressing signature modal');
                    return this;
                }
                return originalModal.apply(this, arguments);
            };
        }
        
        // Patch any print buttons to bypass signature checks
        setTimeout(function() {
            const printButtons = document.querySelectorAll('button[data-action="print"], .print-invoice, .print-receipt, [onclick*="print"]');
            printButtons.forEach(function(button) {
                const originalClick = button.onclick;
                button.onclick = function(e) {
                    console.log('Print button clicked, bypassing signature check');
                    
                    // Try to get the invoice ID
                    let invoiceId = button.getAttribute('data-id') || 
                                   button.closest('[data-id]')?.getAttribute('data-id') ||
                                   new URLSearchParams(window.location.search).get('id');
                    
                    if (invoiceId) {
                        // Open the print URL directly
                        window.open('/admin/pos-cash-sales/invoice/print/' + invoiceId, '_blank');
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    }
                    
                    // If we couldn't find an ID, try the original handler
                    if (originalClick) {
                        try {
                            return originalClick.call(this, e);
                        } catch(err) {
                            console.error('Error in original click handler:', err);
                        }
                    }
                };
            });
        }, 1000);
    }
    
    // Run immediately
    overrideSignatureValidation();
    
    // Also run when the DOM is loaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', overrideSignatureValidation);
    }
    
    // And run again after the page is fully loaded
    window.addEventListener('load', overrideSignatureValidation);
    
    // Also run periodically to catch dynamically added elements
    setInterval(overrideSignatureValidation, 2000);
})();

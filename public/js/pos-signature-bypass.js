/**
 * POS Cash Sales Signature Bypass
 * This script specifically targets the POS cash sales signature validation
 */
(function() {
    console.log('POS signature bypass loaded');
    
    // Function to run when DOM is loaded
    function initSignatureBypass() {
        // Override any print button click handlers
        const printButtons = document.querySelectorAll('.print-receipt, .print-invoice, [data-action="print"], button:contains("Print")');
        
        if (printButtons.length > 0) {
            console.log('Found print buttons:', printButtons.length);
            
            printButtons.forEach(function(button) {
                // Save original click handler
                const originalClick = button.onclick;
                
                // Replace with our handler
                button.onclick = function(e) {
                    console.log('Print button clicked, bypassing signature check');
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Try to get the invoice ID
                    let invoiceId = this.getAttribute('data-id') || 
                                   this.closest('[data-id]')?.getAttribute('data-id') ||
                                   new URLSearchParams(window.location.search).get('id');
                    
                    if (!invoiceId) {
                        // Try to find it in the URL
                        const urlMatch = window.location.href.match(/\/(\d+)$/);
                        if (urlMatch) {
                            invoiceId = urlMatch[1];
                        }
                    }
                    
                    if (invoiceId) {
                        // Convert to base64 if needed
                        if (!invoiceId.includes('=')) {
                            invoiceId = btoa(invoiceId);
                        }
                        
                        // Open the print URL directly
                        window.open('/admin/pos-cash-sales/invoice/print/' + invoiceId, '_blank');
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
        }
        
        // Close any signature validation modals
        const closeModalButtons = document.querySelectorAll('.modal .close, .modal .btn-close, .modal button:contains("OK")');
        closeModalButtons.forEach(function(button) {
            const modal = button.closest('.modal');
            const modalContent = modal?.querySelector('.modal-body')?.textContent || '';
            
            if (modalContent.toLowerCase().includes('not signed') || 
                modalContent.toLowerCase().includes('signature')) {
                button.click();
            }
        });
    }
    
    // Override common signature validation functions
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
    
    // Run when DOM is loaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSignatureBypass);
    } else {
        initSignatureBypass();
    }
    
    // Also run after a short delay to catch any dynamically added elements
    setTimeout(initSignatureBypass, 1000);
    
    // And periodically check for new elements
    setInterval(initSignatureBypass, 3000);
})();

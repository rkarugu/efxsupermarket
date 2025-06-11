/**
 * Disable signature validation for invoices
 * This script overrides any signature validation functions
 */
(function() {
    // Override any signature validation functions
    if (typeof window.validateSignature !== 'undefined') {
        window.validateSignature = function() { return true; };
    }
    
    // Override any invoice signing check functions
    if (typeof window.isInvoiceSigned !== 'undefined') {
        window.isInvoiceSigned = function() { return true; };
    }
    
    // Override any signature required checks
    if (typeof window.requiresSignature !== 'undefined') {
        window.requiresSignature = function() { return false; };
    }
    
    // Add a global event listener to intercept any signature validation errors
    window.addEventListener('DOMContentLoaded', function() {
        // Find and remove any signature validation UI elements
        const signatureElements = document.querySelectorAll('.signature-area, .e-sign-required, .e-sign-container');
        signatureElements.forEach(function(element) {
            element.style.display = 'none';
        });
        
        // Override any click handlers on print buttons to bypass signature checks
        const printButtons = document.querySelectorAll('[data-action="print"], .print-invoice, .print-receipt');
        printButtons.forEach(function(button) {
            const originalClick = button.onclick;
            button.onclick = function(e) {
                // If there's an original click handler, try to execute it
                if (originalClick) {
                    try {
                        return originalClick.call(this, e);
                    } catch(err) {
                        console.log('Bypassing signature validation...');
                    }
                }
                
                // Get the invoice ID from the button or its parent
                const invoiceId = this.dataset.id || this.closest('[data-id]')?.dataset.id;
                if (invoiceId) {
                    // Open the print URL directly
                    window.open('/admin/pos-cash-sales/invoice/print/' + invoiceId, '_blank');
                    return false;
                }
            };
        });
    });
    
    // Intercept any modal dialogs about signature validation
    const originalAlert = window.alert;
    window.alert = function(message) {
        if (message && (
            message.includes('sign') || 
            message.includes('signature') || 
            message.toLowerCase().includes('not signed')
        )) {
            console.log('Suppressing signature alert:', message);
            return;
        }
        return originalAlert.apply(this, arguments);
    };
    
    // Also override any custom modal implementations
    if (typeof window.showModal !== 'undefined') {
        const originalShowModal = window.showModal;
        window.showModal = function(title, message) {
            if (message && (
                message.includes('sign') || 
                message.includes('signature') || 
                message.toLowerCase().includes('not signed')
            )) {
                console.log('Suppressing signature modal:', message);
                return;
            }
            return originalShowModal.apply(this, arguments);
        };
    }
    
    console.log('Signature validation disabled');
})();

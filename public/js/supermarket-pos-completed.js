/**
 * Completed Sales Management
 * Handles viewing, reprinting, and returns for completed sales
 */

// Show New Sale Tab
function showNewSale() {
    $('#products-section').show();
    $('#completed-section').hide();
    $('#btn-new-sale').removeClass('btn-modern-secondary').addClass('btn-modern-primary');
    $('#btn-completed').removeClass('btn-modern-primary').addClass('btn-modern-secondary');
}

// Show Completed Sales Tab
function showCompletedSales() {
    $('#products-section').hide();
    $('#completed-section').show();
    $('#btn-new-sale').removeClass('btn-modern-primary').addClass('btn-modern-secondary');
    $('#btn-completed').removeClass('btn-modern-secondary').addClass('btn-modern-primary');
    
    // Set default dates (today)
    const today = new Date().toISOString().split('T')[0];
    $('#completed-date-from').val(today);
    $('#completed-date-to').val(today);
    
    // Load completed sales
    loadCompletedSales();
}

// Load Completed Sales
function loadCompletedSales() {
    console.log('Loading completed sales...');
    
    const dateFrom = $('#completed-date-from').val();
    const dateTo = $('#completed-date-to').val();
    
    $.ajax({
        url: '/admin/pos-cash-sales/supermarket/completed',
        method: 'GET',
        data: {
            date_from: dateFrom,
            date_to: dateTo
        },
        success: function(response) {
            if (response.success) {
                console.log('✅ Loaded ' + response.sales.length + ' completed sales');
                renderCompletedSales(response.sales);
            } else {
                console.error('Error loading completed sales:', response.message);
                showErrorMessage('Error loading completed sales: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Error loading completed sales:', error);
            console.error('Response:', xhr.responseText);
            showErrorMessage('Error loading completed sales. Please try again.');
        }
    });
}

// Render Completed Sales
function renderCompletedSales(sales) {
    const container = $('#completed-sales-list');
    container.empty();
    
    if (sales.length === 0) {
        container.html(`
            <div style="text-align: center; padding: 60px 20px; color: #999;">
                <i class="fa fa-inbox" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
                <p style="font-size: 16px;">No completed sales found</p>
                <p style="font-size: 14px;">Try selecting a different date range</p>
            </div>
        `);
        return;
    }
    
    sales.forEach(sale => {
        const paymentMethodsText = sale.payment_methods.map(p => 
            `${p.method}: KES ${p.amount.toFixed(2)}`
        ).join(', ');
        
        const returnButton = sale.can_return ? `
            <button class="btn-action btn-action-return" onclick="initiateSaleReturn(${sale.id})">
                <i class="fa fa-undo"></i> Return
            </button>
        ` : '';
        
        const card = `
            <div class="completed-sale-card">
                <div class="completed-sale-header">
                    <div class="completed-sale-info">
                        <div class="completed-sale-number">#${sale.sales_no}</div>
                        <div class="completed-sale-meta">
                            <span><i class="fa fa-calendar"></i> ${sale.date} at ${sale.time}</span>
                            <span><i class="fa fa-user"></i> ${sale.customer_name}</span>
                            ${sale.customer_phone ? `<span><i class="fa fa-phone"></i> ${sale.customer_phone}</span>` : ''}
                            <span><i class="fa fa-user-circle"></i> Cashier: ${sale.cashier}</span>
                        </div>
                        <div class="completed-sale-items">
                            <i class="fa fa-shopping-bag"></i> ${sale.items_count} item(s) | Payment: ${paymentMethodsText}
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <div class="completed-sale-total">KES ${sale.total_amount.toFixed(2)}</div>
                        <div class="completed-sale-actions" style="margin-top: 10px;">
                            <button class="btn-action btn-action-print" onclick="reprintReceipt(${sale.id})">
                                <i class="fa fa-print"></i> Reprint
                            </button>
                            ${returnButton}
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        container.append(card);
    });
}

// Reprint Receipt
function reprintReceipt(saleId) {
    console.log('Reprinting receipt for sale:', saleId);
    
    // Open receipt in new window
    const receiptUrl = `/admin/pos-cash-sales/supermarket/receipt/${saleId}`;
    window.open(receiptUrl, '_blank', 'width=800,height=600');
}

// Initiate Sale Return
function initiateSaleReturn(saleId) {
    console.log('Initiating return for sale:', saleId);
    
    // TODO: Implement return functionality
    showInfoMessage('Return functionality coming soon! Sale ID: ' + saleId);
    
    // This will be implemented in the next phase:
    // 1. Load sale details
    // 2. Show return modal with items
    // 3. Allow selecting items to return
    // 4. Process the return
    // 5. Update inventory
    // 6. Generate credit note
}

// Show error message without using alert
function showErrorMessage(message) {
    const errorHtml = `
        <div style="position: fixed; top: 20px; right: 20px; z-index: 99999; max-width: 400px;">
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" onclick="this.parentElement.parentElement.remove()">&times;</button>
                <h4><i class="fa fa-exclamation-circle"></i> Error</h4>
                <p>${message}</p>
            </div>
        </div>
    `;
    $('body').append(errorHtml);
    
    // Auto-remove after 5 seconds
    setTimeout(function() {
        $('.alert-danger').fadeOut(function() { $(this).parent().remove(); });
    }, 5000);
}

// Show info message
function showInfoMessage(message) {
    const infoHtml = `
        <div style="position: fixed; top: 20px; right: 20px; z-index: 99999; max-width: 400px;">
            <div class="alert alert-info alert-dismissible">
                <button type="button" class="close" onclick="this.parentElement.parentElement.remove()">&times;</button>
                <h4><i class="fa fa-info-circle"></i> Information</h4>
                <p>${message}</p>
            </div>
        </div>
    `;
    $('body').append(infoHtml);
    
    // Auto-remove after 3 seconds
    setTimeout(function() {
        $('.alert-info').fadeOut(function() { $(this).parent().remove(); });
    }, 3000);
}


jQuery(document).ready(function($) {
    
    // Auto-refresh odds every 5 minutes
    let refreshInterval;
    
    function startAutoRefresh() {
        refreshInterval = setInterval(function() {
            refreshOdds();
        }, 5 * 60 * 1000); // 5 minutes
    }
    
    function stopAutoRefresh() {
        if (refreshInterval) {
            clearInterval(refreshInterval);
        }
    }
    
    // Refresh odds function
    function refreshOdds() {
        const container = $('.sports-odds-container');
        const sport = container.data('sport') || 'soccer_epl';
        const regions = container.data('regions') || 'us,uk,eu';
        const markets = container.data('markets') || 'h2h';
        
        // Show loading indicator
        showLoadingIndicator();
        
        $.ajax({
            url: ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'refresh_odds',
                sport: sport,
                regions: regions,
                markets: markets,
                nonce: ajax_object.nonce
            },
            success: function(response) {
                if (response.success) {
                    updateOddsDisplay(response.data);
                    showSuccessMessage('Odds updated successfully');
                } else {
                    showErrorMessage('Failed to update odds');
                }
            },
            error: function() {
                showErrorMessage('Network error occurred');
            },
            complete: function() {
                hideLoadingIndicator();
            }
        });
    }
    
    // Show loading indicator
    function showLoadingIndicator() {
        if ($('.odds-loading-overlay').length === 0) {
            $('.sports-odds-container').append(
                '<div class="odds-loading-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.8); display: flex; align-items: center; justify-content: center; z-index: 999;">' +
                '<div class="odds-loading"></div>' +
                '<span style="margin-left: 10px;">Updating odds...</span>' +
                '</div>'
            );
        }
    }
    
    // Hide loading indicator
    function hideLoadingIndicator() {
        $('.odds-loading-overlay').remove();
    }
    
    // Update odds display
    function updateOddsDisplay(oddsData) {
        // This would update the display with new data
        // Implementation depends on the specific structure
        console.log('Updated odds data:', oddsData);
        
        // Add live indicator
        if ($('.live-indicator').length === 0) {
            $('.odds-header h3').prepend('<span class="live-indicator"></span>');
        }
    }
    
    // Show success message
    function showSuccessMessage(message) {
        showNotification(message, 'success');
    }
    
    // Show error message
    function showErrorMessage(message) {
        showNotification(message, 'error');
    }
    
    // Show notification
    function showNotification(message, type) {
        const notification = $('<div class="odds-notification odds-notification-' + type + '">' + message + '</div>');
        
        $('body').append(notification);
        
        // Style the notification
        notification.css({
            position: 'fixed',
            top: '20px',
            right: '20px',
            padding: '15px 20px',
            borderRadius: '8px',
            color: 'white',
            fontWeight: '600',
            zIndex: '9999',
            opacity: '0',
            transform: 'translateX(100%)',
            transition: 'all 0.3s ease'
        });
        
        if (type === 'success') {
            notification.css('background', '#28a745');
        } else {
            notification.css('background', '#dc3545');
        }
        
        // Animate in
        setTimeout(function() {
            notification.css({
                opacity: '1',
                transform: 'translateX(0)'
            });
        }, 100);
        
        // Remove after 3 seconds
        setTimeout(function() {
            notification.css({
                opacity: '0',
                transform: 'translateX(100%)'
            });
            setTimeout(function() {
                notification.remove();
            }, 300);
        }, 3000);
    }
    
    // Handle match tab clicks
    $(document).on('click', '.match-tab', function() {
        $('.match-tab').removeClass('active');
        $(this).addClass('active');
        
        // Here you could filter matches or load different data
        const matchNumber = $(this).text();
        console.log('Selected match:', matchNumber);
    });
    
    // Handle odd item clicks for betting
    $(document).on('click', '.odd-item', function() {
        const team = $(this).find('.outcome-name').text();
        const odds = $(this).find('.odd-value').text();
        
        // Add selection effect
        $(this).addClass('selected');
        setTimeout(function() {
            $('.odd-item').removeClass('selected');
        }, 2000);
        
        console.log('Selected bet:', team, 'with odds:', odds);
        
        // You could open a betting slip or modal here
        showBettingInfo(team, odds);
    });
    
    // Show betting information
    function showBettingInfo(team, odds) {
        const message = 'You selected: ' + team + ' with odds ' + odds;
        showNotification(message, 'success');
    }
    
    // Add CSS for selected odd items
    $('<style>')
        .prop('type', 'text/css')
        .html(`
            .odd-item.selected {
                background-color: #28a745 !important;
                color: white !important;
                border-color: #28a745 !important;
                transform: scale(1.05) !important;
            }
            .odd-item.selected .outcome-name,
            .odd-item.selected .odd-value {
                color: white !important;
            }
        `)
        .appendTo('head');
    
    // Initialize auto-refresh
    if ($('.sports-odds-container').length > 0) {
        startAutoRefresh();
    }
    
    // Manual refresh button
    if ($('.refresh-odds-btn').length === 0) {
        $('.odds-header').append(
            '<button class="refresh-odds-btn" style="margin-left: 20px; padding: 8px 16px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">Refresh</button>'
        );
    }
    
    $(document).on('click', '.refresh-odds-btn', function() {
        refreshOdds();
    });
    
    // Pause auto-refresh when page is not visible
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            stopAutoRefresh();
        } else {
            startAutoRefresh();
        }
    });
    
    // Clean up on page unload
    $(window).on('beforeunload', function() {
        stopAutoRefresh();
    });
    
    // Add keyboard shortcuts
    $(document).keydown(function(e) {
        // Press 'R' to refresh
        if (e.key === 'r' || e.key === 'R') {
            if (!$(e.target).is('input, textarea')) {
                e.preventDefault();
                refreshOdds();
            }
        }
    });
    
    // Touch/swipe support for mobile
    let touchStartX = 0;
    let touchEndX = 0;
    
    $('.sports-odds-container').on('touchstart', function(e) {
        touchStartX = e.changedTouches[0].screenX;
    });
    
    $('.sports-odds-container').on('touchend', function(e) {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    });
    
    function handleSwipe() {
        const swipeThreshold = 50;
        const diff = touchStartX - touchEndX;
        
        if (Math.abs(diff) > swipeThreshold) {
            if (diff > 0) {
                // Swiped left - next match
                const currentTab = $('.match-tab.active');
                const nextTab = currentTab.next('.match-tab');
                if (nextTab.length) {
                    nextTab.click();
                }
            } else {
                // Swiped right - previous match
                const currentTab = $('.match-tab.active');
                const prevTab = currentTab.prev('.match-tab');
                if (prevTab.length) {
                    prevTab.click();
                }
            }
        }
    }
    
    // Add tooltips for better UX
    $(document).on('mouseenter', '.odd-item', function() {
        const team = $(this).find('.outcome-name').text();
        const odds = $(this).find('.odd-value').text();
        const tooltip = '<div class="odds-tooltip">Click to select ' + team + ' (odds: ' + odds + ')</div>';
        
        $('body').append(tooltip);
        
        const $tooltip = $('.odds-tooltip');
        $tooltip.css({
            position: 'absolute',
            background: '#333',
            color: 'white',
            padding: '8px 12px',
            borderRadius: '4px',
            fontSize: '12px',
            zIndex: '9999',
            whiteSpace: 'nowrap',
            opacity: '0',
            transition: 'opacity 0.3s ease'
        });
        
        // Position tooltip
        const offset = $(this).offset();
        $tooltip.css({
            top: offset.top - 40,
            left: offset.left + ($(this).width() / 2) - ($tooltip.width() / 2)
        });
        
        setTimeout(() => $tooltip.css('opacity', '1'), 100);
    });
    
    $(document).on('mouseleave', '.odd-item', function() {
        $('.odds-tooltip').remove();
    });
});

// Utility functions
function formatCurrency(amount, currency = 'USD') {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: currency
    }).format(amount);
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function calculatePayout(stake, odds) {
    return stake * odds;
}

function calculateProbability(odds) {
    return (1 / odds * 100).toFixed(1) + '%';
}
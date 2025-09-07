@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
<div class="row">
    <!-- Header Section -->
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-2">
                            <i class="fas fa-language mr-2"></i> Smart Auto Translations Pro
                        </h4>
                        <p class="card-text text-muted">
                            Advanced translation dashboard with real-time progress tracking
                        </p>
                    </div>
                    <div class="text-right">
                        <div class="badge badge-info badge-lg">
                            {{ $providerInfo['icon'] }} {{ $providerInfo['name'] }}
                        </div>
                        <br>
                        <small class="text-muted">{{ $providerInfo['description'] }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Quick Access Actions -->
    <div class="col-lg-8">
        <!-- Quick Access Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-external-link-alt"></i> Translation Tools</h5>
                <p class="mb-0 text-muted">Use the dedicated translation pages for full functionality</p>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <i class="fas fa-paint-brush fa-3x text-primary mb-3"></i>
                                <h5>Theme Translations</h5>
                                <p class="text-muted">Translate theme files (JSON format)</p>
                                <a href="{{ route('vig-auto-translations.theme') }}" class="btn btn-primary">
                                    <i class="fas fa-arrow-right"></i> Go to Theme Translations
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <i class="fas fa-cogs fa-3x text-success mb-3"></i>
                                <h5>Plugin Translations</h5>
                                <p class="text-muted">Translate plugin and core files (PHP arrays)</p>
                                <a href="{{ route('vig-auto-translations.plugin') }}" class="btn btn-success">
                                    <i class="fas fa-arrow-right"></i> Go to Plugin Translations
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Provider Testing Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-vial"></i> Provider Testing</h5>
                <p class="mb-0 text-muted">Test your translation providers and verify API connectivity</p>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <button class="btn btn-outline-primary btn-block" onclick="testSpecificProvider('google')">
                            <i class="fas fa-globe"></i> Test Google
                        </button>
                    </div>
                    <div class="col-md-4 mb-2">
                        <button class="btn btn-outline-warning btn-block" onclick="testSpecificProvider('aws')">
                            <i class="fas fa-cloud"></i> Test AWS
                        </button>
                    </div>
                    <div class="col-md-4 mb-2">
                        <button class="btn btn-outline-info btn-block" onclick="testSpecificProvider('chatgpt')">
                            <i class="fas fa-robot"></i> Test ChatGPT
                        </button>
                    </div>
                </div>
                <hr>
                <button class="btn btn-secondary btn-block" onclick="showTestResults()">
                    <i class="fas fa-clipboard-list"></i> Test All Providers
                </button>
            </div>
        </div>
    </div>
    
    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Statistics Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-chart-bar"></i> Statistics</h5>
                <button class="btn btn-sm btn-outline-secondary float-right" onclick="refreshStats()">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
            <div class="card-body" id="stats-container">
                <div class="text-center">
                    <div class="spinner-border text-info" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Loading statistics...</p>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-bolt"></i> Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-danger btn-sm mb-2" onclick="clearAllCache()">
                        <i class="fas fa-trash-alt"></i> Clear All Cache
                    </button>
                    <button class="btn btn-outline-info btn-sm mb-2" onclick="refreshStats()">
                        <i class="fas fa-chart-line"></i> Refresh Statistics
                    </button>
                    <a href="{{ route('vig-auto-translations.settings') }}" class="btn btn-outline-secondary btn-sm mb-2">
                        <i class="fas fa-cog"></i> Provider Settings
                    </a>
                    <button class="btn btn-outline-warning btn-sm" onclick="showTestResults()">
                        <i class="fas fa-clipboard-list"></i> Test All Providers
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Recent Activity -->
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-history"></i> Recent Activity</h5>
            </div>
            <div class="card-body" id="activity-log">
                <p class="text-muted text-center">No recent activity</p>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container for Notifications -->
<div class="position-fixed bottom-0 right-0 p-3" style="z-index: 5; right: 0; bottom: 0;">
    <div id="toast-container"></div>
</div>

<!-- Success Results Modal -->
<div class="modal fade" id="resultsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Translation Results</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="results-content">
                <!-- Results content will be populated by JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="downloadResults()">
                    <i class="fas fa-download"></i> Download Report
                </button>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
$(document).ready(function() {
    // Initialize page
    initializePage();
    
    // Load initial statistics
    refreshStats();
});

function initializePage() {
    console.log('Smart Auto Translations Pro Dashboard loaded');
    addToActivityLog('Dashboard initialized', 'info');
}

// Removed unused form handlers since we use legacy pages for translations

// Removed unused translation functions since we use legacy pages

// Removed unused progress monitoring functions

// Removed unused progress update functions

// Removed unused form enable functions

// Removed unused completion results functions

// Removed unused group loading functions

function refreshStats() {
    $('#stats-container').html(`
        <div class="text-center">
            <div class="spinner-border text-info" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Loading statistics...</p>
        </div>
    `);
    
    $.ajax({
        url: `{{ route('vig-auto-translations.dashboard.stats') }}`,
        method: 'GET',
        success: function(response) {
            if (response.success) {
                displayStats(response.stats);
            } else {
                $('#stats-container').html('<p class="text-danger">Failed to load statistics</p>');
            }
        },
        error: function() {
            $('#stats-container').html('<p class="text-danger">Error loading statistics</p>');
        }
    });
}

function displayStats(stats) {
    let content = `
        <div class="row text-center">
            <div class="col-6">
                <h4 class="text-primary">${stats.cache_entries || 0}</h4>
                <small class="text-muted">Cache Entries</small>
            </div>
            <div class="col-6">
                <h4 class="text-success">${stats.available_locales?.length || 0}</h4>
                <small class="text-muted">Languages</small>
            </div>
        </div>
        <hr>
        <div class="mb-3">
            <h6>Current Provider:</h6>
            <div class="badge badge-info">
                ${stats.provider_info?.icon} ${stats.provider_info?.name}
            </div>
        </div>
    `;
    
    if (stats.available_locales && stats.available_locales.length > 0) {
        content += `
            <div class="mb-3">
                <h6>Available Languages:</h6>
                <div class="d-flex flex-wrap">
        `;
        stats.available_locales.forEach(locale => {
            content += `<span class="badge badge-light mr-1 mb-1">${locale}</span>`;
        });
        content += `</div></div>`;
    }
    
    if (stats.last_translation) {
        content += `
            <div class="mb-3">
                <h6>Last Translation:</h6>
                <small class="text-muted">${stats.last_translation}</small>
            </div>
        `;
    }
    
    $('#stats-container').html(content);
}

function clearAllCache() {
    if (confirm('Are you sure you want to clear all translation cache?')) {
        $.ajax({
            url: `{{ route('vig-auto-translations.dashboard.clear-cache') }}`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showToast(response.message, 'success');
                    refreshStats();
                    addToActivityLog('Cleared all translation cache', 'success');
                } else {
                    showToast(response.message, 'error');
                }
            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON?.message || 'Failed to clear cache';
                showToast(errorMsg, 'error');
            }
        });
    }
}

function testSpecificProvider(provider) {
    showToast(`Testing ${provider.toUpperCase()} provider...`, 'info');
    
    $.ajax({
        url: `{{ route('vig-auto-translations.dashboard.test-provider') }}`,
        method: 'POST',
        data: { driver: provider },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                showToast(response.message, 'success');
                
                if (response.details) {
                    let content = `
                        <div class="alert alert-success">
                            <h5>Provider Test Results</h5>
                            <p><strong>Provider:</strong> ${response.details.provider.name}</p>
                            <p><strong>Test Text:</strong> "${response.details.test_text}"</p>
                            <p><strong>Translation:</strong> "${response.details.translated}"</p>
                            <p><strong>Response Time:</strong> ${response.details.duration}</p>
                        </div>
                    `;
                    
                    $('#results-content').html(content);
                    $('#resultsModal').modal('show');
                }
                
                addToActivityLog(`Provider test successful: ${provider}`, 'success');
            } else {
                showToast(response.message, 'error');
                addToActivityLog(`Provider test failed: ${provider}`, 'error');
            }
        },
        error: function(xhr) {
            const errorMsg = xhr.responseJSON?.message || 'Provider test failed';
            showToast(errorMsg, 'error');
            addToActivityLog(`Provider test failed: ${errorMsg}`, 'error');
        }
    });
}


function showTestResults() {
    // Test all providers
    const providers = ['google', 'aws', 'chatgpt'];
    let results = [];
    let completed = 0;
    
    showToast('Testing all providers...', 'info');
    
    providers.forEach(provider => {
        $.ajax({
            url: `{{ route('vig-auto-translations.dashboard.test-provider') }}`,
            method: 'POST',
            data: { driver: provider },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                results.push({
                    provider: provider,
                    success: response.success,
                    data: response
                });
            },
            error: function(xhr) {
                results.push({
                    provider: provider,
                    success: false,
                    error: xhr.responseJSON?.message || 'Test failed'
                });
            },
            complete: function() {
                completed++;
                if (completed === providers.length) {
                    displayAllTestResults(results);
                }
            }
        });
    });
}

function displayAllTestResults(results) {
    let content = '<div class="alert alert-info"><h5>Provider Test Results</h5></div>';
    
    results.forEach(result => {
        const statusClass = result.success ? 'success' : 'danger';
        const statusIcon = result.success ? '✅' : '❌';
        
        content += `
            <div class="card mb-3">
                <div class="card-header bg-${statusClass} text-white">
                    ${statusIcon} ${result.provider.toUpperCase()}
                </div>
                <div class="card-body">
        `;
        
        if (result.success && result.data.details) {
            content += `
                <p><strong>Status:</strong> ${result.data.message}</p>
                <p><strong>Translation:</strong> "${result.data.details.translated}"</p>
                <p><strong>Response Time:</strong> ${result.data.details.duration}</p>
            `;
        } else {
            content += `<p><strong>Error:</strong> ${result.error || result.data.message}</p>`;
        }
        
        content += '</div></div>';
    });
    
    $('#results-content').html(content);
    $('#resultsModal').modal('show');
}

function showToast(message, type = 'info') {
    const icons = {
        success: 'fas fa-check-circle',
        error: 'fas fa-exclamation-circle',
        warning: 'fas fa-exclamation-triangle',
        info: 'fas fa-info-circle'
    };
    
    const colors = {
        success: 'bg-success',
        error: 'bg-danger',
        warning: 'bg-warning',
        info: 'bg-info'
    };
    
    const toastId = 'toast-' + Date.now();
    const toast = `
        <div class="toast ${colors[type]} text-white" id="${toastId}" role="alert" data-delay="5000">
            <div class="toast-body">
                <i class="${icons[type]} mr-2"></i> ${message}
                <button type="button" class="ml-2 mb-1 close text-white" data-dismiss="toast">
                    <span>&times;</span>
                </button>
            </div>
        </div>
    `;
    
    $('#toast-container').prepend(toast);
    $(`#${toastId}`).toast('show');
    
    // Auto remove after hiding
    $(`#${toastId}`).on('hidden.bs.toast', function() {
        $(this).remove();
    });
}

function addToActivityLog(message, type = 'info') {
    const icons = {
        success: 'fas fa-check text-success',
        error: 'fas fa-times text-danger',
        warning: 'fas fa-exclamation-triangle text-warning',
        info: 'fas fa-info-circle text-info'
    };
    
    const timestamp = new Date().toLocaleTimeString();
    const logEntry = `
        <div class="d-flex align-items-center mb-2">
            <i class="${icons[type]} mr-2"></i>
            <div class="flex-grow-1">
                <small class="d-block">${message}</small>
                <small class="text-muted">${timestamp}</small>
            </div>
        </div>
    `;
    
    const activityLog = $('#activity-log');
    
    // Remove "No recent activity" message
    if (activityLog.find('p.text-center').length) {
        activityLog.empty();
    }
    
    activityLog.prepend(logEntry);
    
    // Keep only last 5 entries
    const entries = activityLog.children();
    if (entries.length > 5) {
        entries.slice(5).remove();
    }
}

function downloadResults() {
    // This would generate and download a results report
    showToast('Results download feature coming soon!', 'info');
}

// Dashboard is now focused on monitoring only
</script>
@endpush

@stop

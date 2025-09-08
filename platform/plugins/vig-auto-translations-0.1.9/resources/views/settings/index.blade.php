@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="row">
        <div class="col-md-9">
            {{-- Main Settings Form --}}
            <div class="widget meta-boxes">
                <div class="widget-title">
                    <h4>{{ trans('plugins/vig-auto-translations::vig-auto-translations.title') }}</h4>
                </div>
                <div class="widget-body">
                    {!! $form->renderForm() !!}
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            {{-- Cache Management Panel --}}
            <div class="widget meta-boxes">
                <div class="widget-title">
                    <h4><i class="fas fa-broom"></i> Cache Management</h4>
                </div>
                <div class="widget-body">
                    <div class="alert alert-info">
                        <small>
                            <i class="fas fa-info-circle"></i>
                            Clear caches when translations aren't updating or after changing settings.
                        </small>
                    </div>
                    
                    <div class="mb-3">
                        <button type="button" class="btn btn-warning btn-sm w-100" id="clear-translation-cache-btn">
                            <i class="fas fa-language"></i>
                            Clear Translation Cache
                        </button>
                        <small class="text-muted d-block mt-1">
                            Clears cached translations only
                        </small>
                    </div>
                    
                    <div class="mb-3">
                        <button type="button" class="btn btn-danger btn-sm w-100" id="clear-all-caches-btn">
                            <i class="fas fa-trash-alt"></i>
                            Clear All Caches
                        </button>
                        <small class="text-muted d-block mt-1">
                            Clears all Laravel caches (config, routes, views, etc.)
                        </small>
                    </div>
                    
                    <hr>
                    
                    <div class="text-center">
                        <small class="text-muted">
                            <i class="fas fa-clock"></i>
                            Last cache clear: <span id="last-cache-clear">Never</span>
                        </small>
                    </div>
                </div>
            </div>
            
            {{-- Quick Actions Panel --}}
            <div class="widget meta-boxes">
                <div class="widget-title">
                    <h4><i class="fas fa-bolt"></i> Quick Actions</h4>
                </div>
                <div class="widget-body">
                    <div class="mb-2">
                        <a href="{{ route('vig-auto-translations.plugin') }}" class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-globe"></i>
                            Plugin Translations
                        </a>
                    </div>
                    
                    <div class="mb-2">
                        <a href="{{ route('vig-auto-translations.theme') }}" class="btn btn-info btn-sm w-100">
                            <i class="fas fa-paint-brush"></i>
                            Theme Translations
                        </a>
                    </div>
                    
                    <hr>
                    
                    <div class="text-center">
                        <small class="text-muted">
                            Current Provider: 
                            <strong>
                                @php
                                    $currentDriver = setting('vig_translate_driver', 'google');
                                    $providerNames = [
                                        'google' => 'Google Translate',
                                        'aws' => 'Amazon Translate',
                                        'chatgpt' => 'ChatGPT/OpenAI'
                                    ];
                                    echo $providerNames[$currentDriver] ?? 'Google Translate';
                                @endphp
                            </strong>
                        </small>
                    </div>
                </div>
            </div>
            
            {{-- System Info Panel --}}
            <div class="widget meta-boxes">
                <div class="widget-title">
                    <h4><i class="fas fa-info-circle"></i> System Info</h4>
                </div>
                <div class="widget-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <tr>
                                <td><small>Plugin Version:</small></td>
                                <td><small><strong>v1.0.0</strong></small></td>
                            </tr>
                            <tr>
                                <td><small>Cache System:</small></td>
                                <td><small>{{ config('cache.default') }}</small></td>
                            </tr>
                            <tr>
                                <td><small>Queue System:</small></td>
                                <td><small>{{ config('queue.default') }}</small></td>
                            </tr>
                            @php
                                $stats = app(\VigStudio\VigAutoTranslations\EnhancedAutoTranslateManager::class)->getTranslationStats();
                            @endphp
                            <tr>
                                <td><small>Available Locales:</small></td>
                                <td><small>{{ count($stats['supported_locales']) }}</small></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('footer')
<script>
    $(document).ready(function() {
        // Clear Translation Cache
        $('#clear-translation-cache-btn').on('click', function() {
            const button = $(this);
            const originalHtml = button.html();
            
            if (!confirm('Clear translation cache? This will remove all cached translations.')) {
                return;
            }
            
            button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Clearing...');
            
            $.ajax({
                url: '{{ route('vig-auto-translations.settings.clear-translation-cache') }}',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.error) {
                        showError('Failed to clear translation cache: ' + response.message);
                    } else {
                        showSuccess(response.message || 'Translation cache cleared successfully!');
                        updateLastCacheTime();
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Failed to clear translation cache';
                    showError(message);
                },
                complete: function() {
                    button.prop('disabled', false).html(originalHtml);
                }
            });
        });
        
        // Clear All Caches
        $('#clear-all-caches-btn').on('click', function() {
            const button = $(this);
            const originalHtml = button.html();
            
            if (!confirm('Clear ALL caches? This includes config, routes, views, and translations.\\n\\nWarning: This may temporarily slow down your site.')) {
                return;
            }
            
            button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Clearing All...');
            
            $.ajax({
                url: '{{ route('vig-auto-translations.settings.clear-all-caches') }}',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.error) {
                        showError('Failed to clear caches: ' + response.message);
                    } else {
                        showSuccess(response.message || 'All caches cleared successfully!');
                        updateLastCacheTime();
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Failed to clear all caches';
                    showError(message);
                },
                complete: function() {
                    button.prop('disabled', false).html(originalHtml);
                }
            });
        });
        
        function updateLastCacheTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString();
            $('#last-cache-clear').text(timeString);
        }
        
        function showSuccess(message) {
            if (typeof Botble !== 'undefined' && Botble.showSuccess) {
                Botble.showSuccess(message);
            } else {
                alert('Success: ' + message);
            }
        }
        
        function showError(message) {
            if (typeof Botble !== 'undefined' && Botble.showError) {
                Botble.showError(message);
            } else {
                alert('Error: ' + message);
            }
        }
    });
</script>
@endpush

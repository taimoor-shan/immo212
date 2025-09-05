{{-- <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Character counter for system message
        const systemMessageField = document.getElementById('vig_translate_chatgpt_system_message');
        const charCounter = document.getElementById('char-counter');
        
        if (systemMessageField && charCounter) {
            // Update character count on input
            function updateCharCount() {
                const currentLength = systemMessageField.value.length;
                charCounter.textContent = currentLength;
                
                // Color coding based on length
                charCounter.parentElement.className = 'text-end mt-1';
                if (currentLength > 1800) {
                    charCounter.parentElement.classList.add('text-danger');
                } else if (currentLength > 1500) {
                    charCounter.parentElement.classList.add('text-warning');
                } else {
                    charCounter.parentElement.classList.add('text-muted');
                }
            }
            
            // Initial count
            updateCharCount();
            
            // Update on input
            systemMessageField.addEventListener('input', updateCharCount);
            systemMessageField.addEventListener('paste', function() {
                setTimeout(updateCharCount, 10); // Small delay for paste to complete
            });
        }
        
        // Show/hide provider-specific settings
        const driverRadios = document.querySelectorAll('input[name="vig_translate_driver"]');
        const settingWrappers = document.querySelectorAll('.setting-wrapper');
        
        function toggleProviderSettings() {
            const selectedDriver = document.querySelector('input[name="vig_translate_driver"]:checked')?.value;
            
            settingWrappers.forEach(wrapper => {
                const wrapperType = wrapper.getAttribute('data-type');
                if (wrapperType === selectedDriver) {
                    wrapper.classList.remove('hidden');
                } else {
                    wrapper.classList.add('hidden');
                }
            });
        }
        
        // Initialize provider settings visibility
        toggleProviderSettings();
        
        // Add event listeners to radio buttons
        driverRadios.forEach(radio => {
            radio.addEventListener('change', toggleProviderSettings);
        });
    });
</script>
<div class="flexbox-annotated-section">
    <div class="flexbox-annotated-section-annotation">
        <div class="annotated-section-title pd-all-20">
            <h2>{{ trans('plugins/vig-auto-translations::vig-auto-translations.title') }}</h2>
        </div>
        <div class="annotated-section-description pd-all-20 p-none-t">
            <p class="color-note">{{ trans('plugins/vig-auto-translations::vig-auto-translations.description') }}</p>
        </div>
    </div>

    <div class="flexbox-annotated-section-content">
        <div class="wrapper-content pd-all-20">
            <form action="{{ route('vig-auto-translations.settings.update') }}" method="POST" class="main-setting-form">
                @csrf
                @method('PUT')
                
            <div class="form-group mb-3">
                <label class="text-title-field" for="vig_translate_driver">{{ trans('plugins/vig-auto-translations::vig-auto-translations.setting_driver') }}</label>

                <label class="me-2">
                    <input type="radio"
                           name="vig_translate_driver"
                           value="google"
                           @if (setting('vig_translate_driver') == 'google' || empty(setting('vig_translate_driver'))) checked @endif
                           class="setting-select-options">{{ trans('plugins/vig-auto-translations::vig-auto-translations.google') }}
                </label>
                <label class="me-2">
                    <input type="radio"
                           name="vig_translate_driver"
                           value="aws"
                           @if (setting('vig_translate_driver') == 'aws') checked @endif
                           class="setting-select-options" data-target="#vig-translate-aws-settings">{{ trans('plugins/vig-auto-translations::vig-auto-translations.aws') }}
                </label>
                <label class="me-2">
                    <input type="radio"
                           name="vig_translate_driver"
                           value="chatgpt"
                           @if (setting('vig_translate_driver') == 'chatgpt') checked @endif
                           class="setting-select-options" data-target="#vig-translate-chatgpt-settings">{{ trans('plugins/vig-auto-translations::vig-auto-translations.chatgpt') }}
                </label>
            </div>

            <div id="vig-translate-aws-settings" data-type="aws" @class([
                'mb-4 border rounded-top rounded-bottom p-3 bg-light setting-wrapper',
                'hidden' => setting('vig_translate_driver') != 'aws',
            ])>

                <div class="form-group mb-3">
                    <label class="text-title-field" for="vig_translate_aws_key">{{ trans('plugins/vig-auto-translations::vig-auto-translations.aws_key') }}</label>
                    <input data-counter="120"
                           type="text"
                           class="next-input"
                           name="vig_translate_aws_key"
                           id="vig_translate_aws_key"
                           value="{{ setting('vig_translate_aws_key', config('plugins.vig-auto-translations.general.aws_key')) }}" placeholder="{{ trans('plugins/vig-auto-translations::vig-auto-translations.aws_key') }}">
                </div>

                <div class="form-group mb-3">
                    <label class="text-title-field" for="vig_translate_aws_secret">{{ trans('plugins/vig-auto-translations::vig-auto-translations.aws_secret') }}</label>
                    <input data-counter="120"
                           type="text"
                           class="next-input"
                           name="vig_translate_aws_secret"
                           id="vig_translate_aws_secret"
                           value="{{ setting('vig_translate_aws_secret', config('plugins.vig-auto-translations.general.aws_secret')) }}" placeholder="{{ trans('plugins/vig-auto-translations::vig-auto-translations.aws_secret') }}">
                </div>

                <div class="form-group mb-3">
                    <label class="text-title-field" for="vig_translate_aws_region">{{ trans('plugins/vig-auto-translations::vig-auto-translations.aws_region') }}</label>
                    <input data-counter="120"
                           type="text"
                           class="next-input"
                           name="vig_translate_aws_region"
                           id="vig_translate_aws_region"
                           value="{{ setting('vig_translate_aws_region', config('plugins.vig-auto-translations.general.aws_region')) }}" placeholder="{{ trans('plugins/vig-auto-translations::vig-auto-translations.aws_region') }}">
                </div>
            </div>

            <div id="vig-translate-chatgpt-settings" data-type="chatgpt" @class([
                'mb-4 border rounded-top rounded-bottom p-3 bg-light setting-wrapper',
                'hidden' => setting('vig_translate_driver') != 'chatgpt',
            ])>

                <div class="form-group mb-3">
                    <label class="text-title-field" for="vig_translate_chatgpt_key">{{ trans('plugins/vig-auto-translations::vig-auto-translations.chatgpt_key') }}</label>
                    <input data-counter="120"
                           type="text"
                           class="next-input"
                           name="vig_translate_chatgpt_key"
                           id="vig_translate_chatgpt_key"
                           value="{{ setting('vig_translate_chatgpt_key', config('plugins.vig-auto-translations.general.chatgpt_key')) }}" placeholder="{{ trans('plugins/vig-auto-translations::vig-auto-translations.chatgpt_key') }}">
                </div>

                <div class="form-group mb-3">
                    <label class="text-title-field" for="vig_translate_chatgpt_model">{{ trans('plugins/vig-auto-translations::vig-auto-translations.chatgpt_model') }}</label>
                    <select class="next-input form-select" name="vig_translate_chatgpt_model" id="vig_translate_chatgpt_model">
                        @foreach($modelOptions ?? [] as $value => $label)
                            <option value="{{ $value }}" @if(setting('vig_translate_chatgpt_model', 'gpt-4.1') == $value) selected @endif>
                                {{ $label }}
                            </option>
                        @endforeach
                        @if(empty($modelOptions))
                            <option value="gpt-4.1" selected>GPT-4.1 (Latest Flagship) - Superior coding, 1M token context</option>
                            <option value="gpt-4.1-mini">GPT-4.1 Mini - Balanced speed/cost, 128K tokens</option>
                            <option value="gpt-4.1-nano">GPT-4.1 Nano - Ultra-low latency, 32K tokens</option>
                        @endif
                    </select>
                    <div class="form-text text-muted">
                        {{ trans('plugins/vig-auto-translations::vig-auto-translations.chatgpt_model_help') }}
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label class="text-title-field" for="vig_translate_chatgpt_system_message">{{ trans('plugins/vig-auto-translations::vig-auto-translations.chatgpt_system_message') }}</label>
                    <textarea class="next-input form-control" 
                              name="vig_translate_chatgpt_system_message" 
                              id="vig_translate_chatgpt_system_message" 
                              rows="6" 
                              maxlength="2000"
                              placeholder="You are an expert professional translator with specialized expertise in {source_language} to {target_language} translations. Your task is to provide accurate, contextually appropriate translations that maintain the exact intent and nuance of the original text...">{{ setting('vig_translate_chatgpt_system_message', config('plugins.vig-auto-translations.general.chatgpt_system_message')) }}</textarea>
                    <div class="form-text text-muted">
                        {{ trans('plugins/vig-auto-translations::vig-auto-translations.chatgpt_system_message_help') }}
                    </div>
                    <div class="text-end mt-1">
                        <small class="text-muted">Characters: <span id="char-counter">0</span>/2000</small>
                    </div>
                </div>
                
                <div class="form-group mb-3 text-end">
                    <button type="submit" class="btn btn-info">
                        <i class="fa fa-save"></i> {{ trans('core/base::forms.save') }}
                    </button>
                    <a href="{{ route('vig-auto-translations.theme') }}" class="btn btn-secondary ms-2">
                        <i class="fa fa-arrow-left"></i> {{ trans('core/base::forms.cancel') }}
                    </a>
                </div>
                
            </form>

            </div>
        </div>
    </div>
</div> --}}

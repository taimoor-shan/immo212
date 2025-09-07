@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
    <div class="widget meta-boxes">
        <div class="widget-title">
            <h4>&nbsp; {{ trans('plugins/translation::translation.translations') }}</h4>
        </div>
        <div class="widget-body box-translation">
            {!! Form::open(['role' => 'form']) !!}
            <div class="row">
                <div class="form-group col-md-6">
                    <label class="fw-bold mb-2">{{ trans('plugins/vig-auto-translations::vig-auto-translations.group') }}</label>
                    <select name="group" id="group" data-value="group" class="form-control ui-select group-select select-search-full">
                        <option value="">{{ isset($isBulkMode) && $isBulkMode ? 'All Groups (Bulk Mode)' : '----' }}</option>
                        @if(isset($allGroups))
                            @foreach ($allGroups as $groupKey)
                                <option value="{{ $groupKey }}"{{ $groupKey == $group ? ' selected' : '' }}>{{ $groupKey }}</option>
                            @endforeach
                        @elseif(isset($translations))
                            @foreach ($translations as $key => $value)
                                <option value="{{ $key }}"{{ $key == $group ? ' selected' : '' }}>{{ $key }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="form-group col-md-6">
                    <label class="fw-bold mb-2">{{ trans('plugins/vig-auto-translations::vig-auto-translations.lang') }}</label>
                    <select name="ref_lang" id="ref_lang" data-value="ref_lang" class="form-control ui-select group-select select-search-full">
                        <option value="">----</option>
                        @foreach ($locales as $key => $locale)
                            <option value="{{ $key }}"{{ $key == $ref_lang ? ' selected' : '' }}>{{ $locale['name'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <br>
            {!! Form::close() !!}

            @if (isset($isBulkMode) && $isBulkMode)
                {{-- BULK MODE: Translate ALL groups at once --}}
                <div class="alert alert-info" role="alert">
                    <h5 class="alert-heading">
                        <i class="fas fa-globe"></i> Bulk Translation Mode (All Groups)
                    </h5>
                    <p>This mode allows you to translate ALL {{ count($allGroups) }} available groups at once, equivalent to:</p>
                    <ul>
                        <li><strong>CLI:</strong> <code>php artisan vig:translate:core {{ $ref_lang }}</code></li>
                        <li><strong>Botble Default:</strong> <code>php artisan cms:translation:auto-translate-core {{ $ref_lang }}</code></li>
                    </ul>
                </div>

                {{-- Current Provider Info --}}
                <div class="alert alert-success" role="alert">
                    <strong><i class="fas fa-cog"></i> Current provider:</strong>
                    <span class="badge badge-info">{{ $providerName }}</span>
                    @if($currentDriver === 'chatgpt')
                        <span class="badge badge-secondary">{{ setting('vig_translate_chatgpt_model', 'gpt-4.1') }}</span>
                    @endif
                    | <a href="{{ route('vig-auto-translations.settings') }}" class="alert-link">Change Settings</a>
                </div>

                {{-- Bulk Translation Form --}}
                <form method="POST" action="{{ route('vig-auto-translations.plugin.bulk-translate-all') }}" class="mb-4">
                    @csrf
                    <input type="hidden" name="locale" value="{{ $ref_lang }}">
                    
                    <div class="row">
                        <div class="col-md-8">
                            <h5>🌍 Translate All {{ count($allGroups) }} Groups to {{ $locales[$ref_lang]['name'] ?? $ref_lang }}</h5>
                            <p class="text-muted">
                                This will process all core, plugin, and package translation groups using {{ $providerName }}.
                                <strong>Warning:</strong> This operation may take several minutes.
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <button type="submit" class="btn btn-success btn-lg" id="bulk-translate-btn">
                                <i class="fas fa-globe"></i>
                                Translate All Groups
                            </button>
                        </div>
                    </div>
                </form>

                {{-- Available Groups Display --}}
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-list"></i> Available Groups ({{ count($allGroups) }})</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach ($allGroups as $groupItem)
                                @php
                                    $groupDisplay = $groupItem;
                                    $badgeClass = 'secondary';
                                    
                                    if (str_starts_with($groupItem, 'core/')) {
                                        $badgeClass = 'primary';
                                        $name = \Illuminate\Support\Str::headline(\Illuminate\Support\Str::slug(\Illuminate\Support\Str::afterLast($groupItem, '/')));
                                        $groupDisplay = $name . ' (core)';
                                    } elseif (str_starts_with($groupItem, 'plugins/')) {
                                        $badgeClass = 'success';
                                        $plugin = \Illuminate\Support\Str::beforeLast(\Illuminate\Support\Str::after($groupItem, 'plugins/'), '/');
                                        $name = \Illuminate\Support\Str::afterLast($groupItem, '/');
                                        
                                        if ($plugin !== $name) {
                                            $name = \Illuminate\Support\Str::headline(\Illuminate\Support\Str::slug($name));
                                            $groupDisplay = $name . ' (' . $plugin . ')';
                                        } else {
                                            $groupDisplay = \Illuminate\Support\Str::headline(\Illuminate\Support\Str::slug($name));
                                        }
                                    } elseif (str_starts_with($groupItem, 'packages/')) {
                                        $badgeClass = 'warning';
                                        $name = \Illuminate\Support\Str::headline(\Illuminate\Support\Str::slug(\Illuminate\Support\Str::afterLast($groupItem, '/')));
                                        $groupDisplay = $name . ' (package)';
                                    }
                                @endphp
                                <div class="col-md-4 col-lg-3 mb-2">
                                    <span class="badge badge-{{ $badgeClass }} w-100" title="{{ $groupItem }}">
                                        {{ $groupDisplay }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i>
                                Select a specific group from the dropdown above to switch to single-group translation mode.
                            </small>
                        </div>
                    </div>
                </div>

            @elseif (!empty($group))
                @if (!empty($ref_lang))
                    {{-- Current Provider Info --}}
                    <div class="alert alert-success" role="alert">
                        <strong><i class="fas fa-cog"></i> Current provider:</strong> 
                        @php
                            $currentDriver = setting('vig_translate_driver', 'google');
                            $providerNames = [
                                'google' => 'Google Translate',
                                'aws' => 'Amazon Translate',
                                'chatgpt' => 'ChatGPT/OpenAI'
                            ];
                            $providerName = $providerNames[$currentDriver] ?? 'Google Translate';
                        @endphp
                        <span class="badge badge-info">{{ $providerName }}</span>
                        | <a href="{{ route('vig-auto-translations.settings') }}" class="alert-link">Change Settings</a>
                    </div>

                    {{-- Action Buttons - Proper Order --}}
                    <div class="mb-3">
                        <button class="btn btn-warning btn-sm btn-translate-all mr-2">
                            <i class="fa-sharp fa-solid fa-language"></i> {{ trans('plugins/vig-auto-translations::vig-auto-translations.translate_all', ['language' => $locales[$ref_lang]['name']]) }}
                        </button>
                        
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#publishModal">
                            <i class="fas fa-upload"></i> {{ trans('plugins/translation::translation.publish_translations') }}
                        </button>
                    </div>

                    {{-- Export Warning --}}
                    <div class="alert alert-info" role="alert">
                        <i class="fas fa-info-circle"></i> {{ trans('plugins/translation::translation.export_warning', ['lang_path' => lang_path()]) }}
                    </div>

                    {{-- Publish Modal --}}
                    <div class="modal fade" id="publishModal" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">{{ trans('plugins/translation::translation.publish_translations') }}</h5>
                                    <button type="button" class="close" data-dismiss="modal">
                                        <span>&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p><i class="fas fa-exclamation-triangle text-warning"></i> This will publish all translations for the <strong>{{ $group }}</strong> group.</p>
                                    <p>Are you sure you want to continue?</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <form method="POST" action="{{ route('translations.group.publish') }}" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="group" value="{{ $group }}">
                                        <button type="submit" class="btn btn-primary button-publish-groups">
                                            <i class="fas fa-upload"></i> Publish Now
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <hr>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ trans('plugins/vig-auto-translations::vig-auto-translations.key') }}</th>
                                <th>{{ trans('plugins/vig-auto-translations::vig-auto-translations.value') }}</th>
                                @if (!empty($ref_lang))
                                    <th>{{ $locales[$ref_lang]['name'] }}</th>
                                    <th></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($translations[$group] as $key => $value)
                                @php $item = $translationData[$key] ?? null @endphp
                                <tr id="{{ $key }}">
                                    <td>{{ $key }}</td>
                                    <td>{{ $value }}</td>
                                    @if (!empty($ref_lang))
                                        <td class="text-start">
                                            <a href="#edit" class="editable status-{{ $item ? $item->status : 0 }} locale-{{ $ref_lang }}"
                                               data-locale="{{ $ref_lang }}" data-name="{{ $ref_lang . '|' . $key }}"
                                               data-type="textarea" data-pk="{{ $item ? $item->id : 0 }}" data-url="{{ $editUrl }}"
                                               data-title="{{ trans('plugins/translation::translation.edit_title') }}">{!! $item ? htmlentities($item->value, ENT_QUOTES, 'UTF-8', false) : '' !!}</a>
                                        </td>
                                        <td>
                                            <button class="btn btn-primary btn-xs btn-begin-translate-auto"
                                                    data-name="{{ $ref_lang . '|' . $key }}"
                                                    data-value="{{ $value }}"
                                                    data-reset="0"
                                                    type="button"
                                                    title="{{ trans('plugins/vig-auto-translations::vig-auto-translations.translate') }}">
                                                <i class="fa-sharp fa-solid fa-language"></i> {{ trans('plugins/vig-auto-translations::vig-auto-translations.translate') }}
                                            </button>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-info">{{ trans('plugins/translation::translation.choose_group_msg') }}</p>
            @endif
        </div>
        <div class="clearfix"></div>
    </div>
@stop


@push('footer')
    <script>
        $('.editable').editable({
            mode: 'inline'
        }).on('hidden', (e, reason) => {
            let locale = $(event.currentTarget).data('locale');
            if (reason === 'save') {
                $(event.currentTarget).removeClass('status-0').addClass('status-1');
            }
            if (reason === 'save' || reason === 'nochange') {
                let $next = $(event.currentTarget).closest('tr').next().find('.editable.locale-' + locale);
                setTimeout(() => {
                    $next.editable('show');
                }, 300);
            }
        });

        $(document).on('click', '.btn-begin-translate-auto', function(event) {
            event.preventDefault();
            event.stopPropagation();
            $(this).prop('disabled', true).addClass('button-loading');
            var name = $(this).data('name');
            var value = $(this).data('value');
            var reset = $(this).data('reset');

            $.ajax({
                type: 'POST',
                url: "{{ route('vig-auto-translations.plugin') }}",
                data: {
                    '_token': "{{ csrf_token() }}",
                    'group': "{{ $group }}",
                    'auto': !reset,
                    'value': value,
                    'name': name
                },
                success: res => {
                    if (!res.error) {
                        Botble.showSuccess(res.message);
                    } else {
                        Botble.showError(res.message);
                    }
                    location.reload();
                    $(this).prop('disabled', false).removeClass('button-loading');
                },
                error: res => {
                    $(this).prop('disabled', false).removeClass('button-loading');
                    Botble.handleError(res);
                }
            });
        });

        $(document).on('click', '.btn-translate-all', function(event) {
            event.preventDefault();
            event.stopPropagation();
            $(this).prop('disabled', true).addClass('button-loading');

            $.ajax({
                type: 'POST',
                url: "{{ route('vig-auto-translations.plugin.all') }}",
                data: {
                    '_token': "{{ csrf_token() }}",
                    'group': "{{ $group }}",
                    'ref_lang': "{{ request('ref_lang') }}",
                },
                success: res => {
                    if (!res.error) {
                        Botble.showSuccess(res.message);
                    } else {
                        Botble.showError(res.message);
                    }
                    location.reload();
                    $(this).prop('disabled', false).removeClass('button-loading');
                },
                error: res => {
                    $(this).prop('disabled', false).removeClass('button-loading');
                    Botble.handleError(res);
                }
            });
        });

        $('.group-select').on('change', () => {
            let group = $('#group').val();
            let ref_lang = $('#ref_lang').val();
            window.location.href = getRouteGetData(group, ref_lang);
        });

        function getRouteGetData(group, ref_lang) {
            return route('vig-auto-translations.plugin', {
                group: group,
                ref_lang: ref_lang
            })
        }
        
        // Bulk translation form handler
        $(document).on('submit', 'form[action*="bulk-translate-all"]', function(e) {
            const submitBtn = $(this).find('#bulk-translate-btn');
            
            // Show loading state
            submitBtn.prop('disabled', true)
                     .html('<i class="fas fa-spinner fa-spin"></i> Translating All Groups...');
            
            // Show progress notification
            Botble.showNotice('Bulk translation started! This may take several minutes. Please wait...', 'info');
        });
        
        // Publish translations form handler
        $(document).on('submit', 'form[action*="translations.group.publish"]', function(e) {
            const submitBtn = $(this).find('.button-publish-groups');
            
            // Show loading state
            submitBtn.prop('disabled', true)
                     .html('<i class="fas fa-spinner fa-spin"></i> Publishing...');
            
            // Show progress notification
            Botble.showNotice('Publishing translations to files...', 'info');
        });
        
        // Handle publish modal show event to reset button state if needed
        $('#publishModal').on('show.bs.modal', function () {
            const submitBtn = $(this).find('.button-publish-groups');
            submitBtn.prop('disabled', false)
                     .html('<i class="fas fa-upload"></i> Publish Now');
        });
    </script>
@endpush

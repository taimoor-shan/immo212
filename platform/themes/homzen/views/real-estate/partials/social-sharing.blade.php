@php
    $socialSharing = \Botble\Theme\Supports\ThemeSupport::getSocialSharingButtons($model->url, $model->name);
@endphp
@if($socialSharing)
    <div class="property-share-dropdown" style="position: relative; display: inline-block;">
        <button type="button" class="roundBtn" id="shareDropdownBtn-{{ $model->id ?? 'default' }}">
           <i class="fa-solid fa-share-nodes"></i>

        </button>
        <div class="share-dropdown-menu" id="shareDropdownMenu-{{ $model->id ?? 'default' }}" style="display: none; position: absolute; top: 100%; right: 0; background: #ffffff; border: 1px solid #a8beea; border-radius: 6px; padding: 12px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); z-index: 1000; min-width: 200px; margin-top: 5px;">
            <ul style="list-style: none; margin: 0; padding: 0;">
                @foreach($socialSharing as $index => $social)
                    <li style="{{ $loop->last ? '' : 'margin-bottom: 8px;' }}">
                        <a href="{{ $social['url'] }}"
                           target="_blank"
                           title="{{ $social['name'] }}"
                           style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; text-decoration: none; color: #082479; border-radius: 4px; transition: all 0.3s ease;"
                           onmouseover="this.style.backgroundColor='#e4eaf5'"
                           onmouseout="this.style.backgroundColor='transparent'">
                            <span style="width: 16px; height: 16px; display: inline-flex; align-items: center; justify-content: center;">
                                {!! $social['icon'] !!}
                            </span>
                            <span>{{ $social['name'] }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const shareBtn = document.getElementById('shareDropdownBtn-{{ $model->id ?? 'default' }}');
        const shareMenu = document.getElementById('shareDropdownMenu-{{ $model->id ?? 'default' }}');

        if (shareBtn && shareMenu) {
            // Toggle dropdown on button click
            shareBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                if (shareMenu.style.display === 'none' || shareMenu.style.display === '') {
                    shareMenu.style.display = 'block';
                } else {
                    shareMenu.style.display = 'none';
                }
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!shareBtn.contains(e.target) && !shareMenu.contains(e.target)) {
                    shareMenu.style.display = 'none';
                }
            });

            // Prevent dropdown from closing when clicking inside menu
            shareMenu.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }
    });
    </script>
@endif

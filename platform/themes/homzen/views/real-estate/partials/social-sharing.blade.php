@php
    $socialSharing = \Botble\Theme\Supports\ThemeSupport::getSocialSharingButtons($model->url, $model->name);
@endphp
@if($socialSharing)
    <div class="property-share-dropdown" style="position: relative; display: inline-block;">
        <button type="button" class="roundBtn" id="shareDropdownBtn-{{ $model->id ?? 'default' }}">
           <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M16.61 21q-.994 0-1.687-.695q-.692-.696-.692-1.69q0-.15.132-.757l-7.197-4.273q-.324.374-.793.587t-1.007.213q-.986 0-1.676-.702T3 12t.69-1.683t1.676-.702q.537 0 1.007.213t.793.588l7.198-4.255q-.07-.194-.101-.385q-.032-.192-.032-.392q0-.993.697-1.689Q15.625 3 16.62 3t1.688.697T19 5.389t-.695 1.688t-1.69.692q-.542 0-1-.222t-.78-.597l-7.199 4.273q.07.194.101.386q.032.191.032.391t-.032.391t-.1.386l7.198 4.273q.323-.375.78-.597q.458-.222 1-.222q.994 0 1.69.696q.695.698.695 1.693t-.697 1.688t-1.692.692"/></svg>

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

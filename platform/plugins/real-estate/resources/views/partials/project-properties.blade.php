@php
    // Determine if we're in admin or user context by checking the current route pattern
    $isUserContext = request()->is('account/*') || !is_in_admin();
    $createPropertyRoute = $isUserContext 
        ? route('public.account.properties.create', ['project_id' => $project->id, 'from_project' => 1])
        : route('property.create', ['project_id' => $project->id, 'from_project' => 1]);
@endphp
<div class="project-properties-management">
    @if($project && $project->exists)
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="text-muted">{{ $project->properties()->count() }} properties in this project</span>
            <a href="{{ $createPropertyRoute }}"
               class="btn btn-primary btn-sm">
                <i class="fa fa-plus"></i> Add Property
            </a>
        </div>
        @if($project->properties()->count() > 0)
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Floor</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Beds</th>
                            <th>Size</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($project->properties()->with('categories')->latest()->take(10)->get() as $property)
                            <tr>
                                <td>
                                    <strong>{{ Str::limit($property->name, 25) }}</strong>
                                    @if($property->unique_id)
                                        <br><small class="text-muted">ID: {{ $property->unique_id }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($property->category)
                                        <span class="badge badge-info">{{ $property->category->name }}</span>
                                    @else
                                        <span class="text-muted">--</span>
                                    @endif
                                </td>
                                <td>
                                    @if($property->floor_name)
                                        {{ $property->floor_name }}
                                    @elseif($property->number_floor)
                                        Floor {{ $property->number_floor }}
                                    @else
                                        <span class="text-muted">--</span>
                                    @endif
                                </td>
                                <td>{{ format_price($property->price, $property->currency) }}</td>
                                <td>{!! $property->status->toHtml() !!}</td>
                                <td>{{ $property->number_bedroom ?: '--' }}</td>
                                <td>{{ $property->square_text ?: '--' }}</td>
                                @php
                                    $editPropertyRoute = $isUserContext 
                                        ? route('public.account.properties.edit', $property->id)
                                        : route('property.edit', $property->id);
                                    $deleteUrl = $isUserContext
                                        ? route('public.account.properties.destroy', $property->id)
                                        : route('property.destroy', $property->id);
                                @endphp
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ $editPropertyRoute }}" 
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <button class="btn btn-outline-danger btn-sm delete-property"
                                                data-id="{{ $property->id }}"
                                                data-name="{{ $property->name }}"
                                                data-url="{{ $deleteUrl }}">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($project->properties()->count() > 10)
                @php
                    $allPropertiesRoute = $isUserContext 
                        ? route('public.account.properties.index', ['project_id' => $project->id])
                        : route('property.index', ['project_id' => $project->id]);
                @endphp
                <div class="text-center mt-3">
                    <a href="{{ $allPropertiesRoute }}" 
                       class="btn btn-outline-secondary btn-sm">View All Properties ({{ $project->properties()->count() }})</a>
                </div>
            @endif
        @else
            <div class="text-center py-4">
                <div class="mb-3">
                    <i class="fa fa-home fa-3x text-muted"></i>
                </div>
                <p class="text-muted">No properties added to this project yet.</p>
                <a href="{{ $createPropertyRoute }}"
                   class="btn btn-primary">Add First Property</a>
            </div>
        @endif
    @else
        <div class="alert alert-info">
            <i class="fa fa-info-circle"></i>
            Save the project first to manage properties.
        </div>
    @endif
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle delete property buttons
    document.querySelectorAll('.delete-property').forEach(button => {
        button.addEventListener('click', function() {
            const propertyId = this.getAttribute('data-id');
            const propertyName = this.getAttribute('data-name');
            const deleteUrl = this.getAttribute('data-url');
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            if (confirm('Are you sure you want to delete "' + propertyName + '"?')) {
                fetch(deleteUrl, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    // Check if response is ok before parsing JSON
                    if (!response.ok) {
                        // Show descriptive alert with status and text
                        alert(`Error deleting property: ${response.status} ${response.statusText}`);
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    
                    // Return the JSON promise, handle parsing errors in the catch block
                    return response.json().catch(jsonError => {
                        alert('Error: Server returned invalid response. Please try again.');
                        throw new Error('Invalid JSON response from server');
                    });
                })
                .then(data => {
                    // On success, check if error is false
                    if (data.error === false) {
                        // Remove the corresponding row from the table
                        const row = this.closest('tr');
                        if (row) {
                            row.remove();
                            // Update property count if available
                            const countElement = document.querySelector('.text-muted');
                            if (countElement && countElement.textContent.includes('properties in this project')) {
                                const currentCount = parseInt(countElement.textContent.match(/\d+/)[0]);
                                countElement.textContent = `${currentCount - 1} properties in this project`;
                            }
                        } else {
                            // Fallback to page reload if row removal fails
                            location.reload();
                        }
                    } else {
                        alert('Error deleting property: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Delete property error:', error);
                    // Only show alert if it wasn't already shown above
                    if (!error.message.includes('HTTP')) {
                        alert('Network error while deleting property. Please check your connection and try again.');
                    }
                });
            }
        });
    });
});
</script>

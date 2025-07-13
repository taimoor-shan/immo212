/**
 * Vacation Rental Admin JavaScript
 */

(function($) {
    'use strict';

    class VacationRentalAdmin {
        constructor() {
            this.init();
        }

        init() {
            this.initializeDataTables();
            this.initializeCalendar();
            this.initializeDatePickers();
            this.initializeFormValidation();
            this.initializeStatusUpdates();
            this.bindEvents();
        }

        initializeDataTables() {
            // Initialize vacation rental property table
            if ($('#vacation-rental-properties-table').length) {
                $('#vacation-rental-properties-table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    order: [[0, 'desc']],
                    columnDefs: [
                        { orderable: false, targets: [1, 6] }, // Image and operations columns
                        { searchable: false, targets: [1, 6] }
                    ]
                });
            }

            // Initialize vacation rental booking table
            if ($('#vacation-rental-bookings-table').length) {
                $('#vacation-rental-bookings-table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    order: [[0, 'desc']],
                    columnDefs: [
                        { orderable: false, targets: [-1] }, // Actions column
                        { searchable: false, targets: [-1] }
                    ]
                });
            }
        }

        initializeCalendar() {
            // Initialize availability calendar if present
            if ($('#availability-calendar').length) {
                this.loadAvailabilityCalendar();
            }
        }

        loadAvailabilityCalendar() {
            const $calendar = $('#availability-calendar');
            const propertyId = $calendar.data('property-id');
            
            if (!propertyId) return;

            // Load calendar data via AJAX
            $.ajax({
                url: '/admin/real-estate/vacation-rentals/availability-data',
                method: 'GET',
                data: {
                    property_id: propertyId,
                    start_date: moment().startOf('month').format('YYYY-MM-DD'),
                    end_date: moment().endOf('month').format('YYYY-MM-DD')
                },
                success: (data) => {
                    this.renderCalendar($calendar, data);
                },
                error: (xhr) => {
                    console.error('Failed to load calendar data:', xhr);
                    this.showError('Failed to load calendar data');
                }
            });
        }

        renderCalendar($container, data) {
            // Simple calendar rendering - can be enhanced with a proper calendar library
            let html = '<div class="availability-calendar">';
            html += '<div class="calendar-header">';
            html += '<h5>Availability Calendar</h5>';
            html += '</div>';
            html += '<div class="calendar-body">';
            
            // Render calendar days based on data
            if (data && data.length > 0) {
                data.forEach(day => {
                    const statusClass = `calendar-day ${day.status}`;
                    html += `<div class="${statusClass}" data-date="${day.date}">`;
                    html += `<span class="day-number">${moment(day.date).format('D')}</span>`;
                    html += `<span class="day-status">${day.status}</span>`;
                    html += '</div>';
                });
            } else {
                html += '<div class="text-center p-4">No availability data found</div>';
            }
            
            html += '</div>';
            html += '</div>';
            
            $container.html(html);
        }

        initializeDatePickers() {
            // Initialize date pickers for booking forms
            if ($('.date-picker').length) {
                $('.date-picker').each(function() {
                    $(this).datepicker({
                        format: 'yyyy-mm-dd',
                        autoclose: true,
                        todayHighlight: true,
                        startDate: new Date()
                    });
                });
            }
        }

        initializeFormValidation() {
            // Booking form validation
            $('#booking-form').on('submit', (e) => {
                if (!this.validateBookingForm()) {
                    e.preventDefault();
                    return false;
                }
            });
        }

        validateBookingForm() {
            let isValid = true;
            const $form = $('#booking-form');

            // Clear previous errors
            $form.find('.is-invalid').removeClass('is-invalid');
            $form.find('.invalid-feedback').remove();

            // Validate required fields
            $form.find('[required]').each(function() {
                const $field = $(this);
                if (!$field.val().trim()) {
                    $field.addClass('is-invalid');
                    $field.after('<div class="invalid-feedback">This field is required</div>');
                    isValid = false;
                }
            });

            // Validate email format
            const $email = $form.find('input[type="email"]');
            if ($email.length && $email.val()) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test($email.val())) {
                    $email.addClass('is-invalid');
                    $email.after('<div class="invalid-feedback">Please enter a valid email address</div>');
                    isValid = false;
                }
            }

            return isValid;
        }

        initializeStatusUpdates() {
            // Handle booking status updates
            $('.booking-status-select').on('change', (e) => {
                const $select = $(e.target);
                const bookingId = $select.data('booking-id');
                const newStatus = $select.val();
                
                this.updateBookingStatus(bookingId, newStatus);
            });
        }

        updateBookingStatus(bookingId, status) {
            $.ajax({
                url: `/admin/real-estate/vacation-rentals/bookings/${bookingId}`,
                method: 'PUT',
                data: {
                    status: status,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: (response) => {
                    this.showSuccess('Booking status updated successfully');
                    // Refresh the table if it exists
                    if ($.fn.DataTable.isDataTable('#vacation-rental-bookings-table')) {
                        $('#vacation-rental-bookings-table').DataTable().ajax.reload();
                    }
                },
                error: (xhr) => {
                    console.error('Failed to update booking status:', xhr);
                    this.showError('Failed to update booking status');
                }
            });
        }

        bindEvents() {
            // Refresh button
            $('.btn-refresh').on('click', (e) => {
                e.preventDefault();
                this.refreshData();
            });

            // Delete confirmation
            $('.btn-delete').on('click', (e) => {
                if (!confirm('Are you sure you want to delete this item?')) {
                    e.preventDefault();
                    return false;
                }
            });

            // Property selection for availability
            $('#property-select').on('change', (e) => {
                const propertyId = $(e.target).val();
                if (propertyId) {
                    window.location.href = `${window.location.pathname}?property_id=${propertyId}`;
                }
            });
        }

        refreshData() {
            // Refresh DataTables
            if ($.fn.DataTable.isDataTable('#vacation-rental-properties-table')) {
                $('#vacation-rental-properties-table').DataTable().ajax.reload();
            }
            
            if ($.fn.DataTable.isDataTable('#vacation-rental-bookings-table')) {
                $('#vacation-rental-bookings-table').DataTable().ajax.reload();
            }

            // Refresh calendar
            if ($('#availability-calendar').length) {
                this.loadAvailabilityCalendar();
            }

            this.showSuccess('Data refreshed successfully');
        }

        showSuccess(message) {
            this.showNotification(message, 'success');
        }

        showError(message) {
            this.showNotification(message, 'error');
        }

        showNotification(message, type = 'info') {
            // Simple notification system - can be enhanced with a proper notification library
            const alertClass = type === 'success' ? 'alert-success' : 
                              type === 'error' ? 'alert-danger' : 'alert-info';
            
            const $alert = $(`
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `);

            // Add to page
            if ($('.main-content').length) {
                $('.main-content').prepend($alert);
            } else {
                $('body').prepend($alert);
            }

            // Auto-hide after 5 seconds
            setTimeout(() => {
                $alert.fadeOut(() => $alert.remove());
            }, 5000);
        }
    }

    // Initialize when document is ready
    $(document).ready(() => {
        new VacationRentalAdmin();
    });

})(jQuery);

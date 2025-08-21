# Property Consult Form Customization Guide

## Overview

This guide explains how to customize the property consult form layout and fields in the Homzen theme. The consult form is used when potential buyers want to inquire about a property.

## Current Structure

The consult form is implemented using:
- `ConsultForm` class: `platform/plugins/real-estate/src/Forms/Fronts/ConsultForm.php`
- Contact template: `platform/themes/homzen/views/real-estate/single-layouts/partials/contact.blade.php`
- JavaScript handling: `platform/themes/homzen/assets/js/script.js`

## Available Customization Methods

### Method 1: Admin Panel Custom Fields

**Steps:**
1. Go to Admin Panel → Real Estate → Consult Custom Fields
2. Create new custom fields with options:
   - Text fields
   - Number fields
   - Dropdowns
   - Checkboxes
   - Radio buttons
   - Date/DateTime/Time fields
3. Set field order, labels, and requirements

**Pros:**
- No coding required
- User-friendly interface
- Easy to manage

**Cons:**
- Limited customization options
- Basic styling

### Method 2: Template Override (Recommended)

**Steps:**
1. Replace the current contact template with the enhanced version:
   ```bash
   cp platform/themes/homzen/views/real-estate/single-layouts/partials/contact-enhanced.blade.php \
      platform/themes/homzen/views/real-estate/single-layouts/partials/contact.blade.php
   ```

2. Clear cache:
   ```bash
   php artisan cache:clear
   ```

**Features Added:**
- Property name as readonly field
- Preferred contact method dropdown
- Budget range selector
- Viewing timeframe options
- Financing status dropdown
- Additional questions textarea
- Enhanced styling with better UX
- Property summary card
- Mobile-optimized design

### Method 3: Custom Form Class

**Steps:**
1. Create custom form class extending `ConsultForm`
2. Register the custom form in your theme's service provider
3. Update templates to use the custom form

**Example Service Provider Registration:**
```php
// In platform/themes/homzen/src/ThemeServiceProvider.php
use Botble\Theme\Homzen\Forms\CustomConsultForm;

public function boot()
{
    // Override the default consult form
    $this->app->bind(
        \Botble\RealEstate\Forms\Fronts\ConsultForm::class,
        CustomConsultForm::class
    );
}
```

## Form Field Types Available

### Standard Fields
- `text` - Text input
- `email` - Email input
- `tel` - Phone number input
- `textarea` - Multi-line text area
- `select` - Dropdown selection
- `radio` - Radio buttons
- `checkbox` - Checkboxes
- `date` - Date picker
- `datetime-local` - Date and time picker
- `time` - Time picker
- `number` - Number input
- `hidden` - Hidden field

### Field Configuration Options
```php
->add('field_name', 'field_type', [
    'label' => __('Field Label'),
    'required' => true|false,
    'attr' => [
        'class' => 'form-control',
        'placeholder' => __('Placeholder text'),
        'id' => 'field-id'
    ],
    'choices' => [ // For select/radio fields
        'value1' => 'Label 1',
        'value2' => 'Label 2'
    ]
])
```

## Form Styling Customization

### CSS Classes Structure
```css
.enhanced-consult-form          // Main form container
.enhanced-consult-form .form-group    // Field wrapper
.enhanced-consult-form label           // Field labels  
.enhanced-consult-form .form-control   // Input fields
.enhanced-consult-form .tf-btn         // Submit button
.property-summary-card                 // Property info card
```

### Responsive Design
The enhanced form includes:
- Mobile-first responsive design
- Touch-friendly form controls
- Optimized modal sizing
- Accessible form labels and inputs

## JavaScript Enhancements

### Form Validation
```javascript
// Enhanced form validation with loading states
const form = document.querySelector('.enhanced-consult-form');
if (form) {
    form.addEventListener('submit', function(e) {
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.classList.add('btn-loading');
            submitBtn.disabled = true;
        }
    });
}
```

### Additional Features
- Loading spinner on submit
- Form validation feedback
- Auto-focus on form open
- Phone number reveal functionality

## Form Data Handling

### Backend Processing
The form data is processed by:
- `ConsultController@store`
- Validation via `SendConsultRequest`
- Email notifications sent to property owner/agent

### Custom Field Data Access
Custom fields are stored in the `consults` table under the `custom_fields` JSON column:
```php
$consult = Consult::find($id);
$budgetRange = $consult->custom_fields['budget_range'] ?? null;
$viewingTime = $consult->custom_fields['viewing_timeframe'] ?? null;
```

## Vacation Rental Integration

The wishlist functionality already works for vacation rentals as shown in:
- JavaScript: `data-type="vacation_rental"` (line 2322-2324 in script.js)
- Cookie handling: `vacation_rental_wishlist` cookie
- Template: Vacation rental header already includes wishlist button

To apply similar form enhancements to vacation rentals, modify:
`platform/themes/homzen/views/real-estate/single-layouts/partials/vacation-rental-contact.blade.php`

## Testing Checklist

- [ ] Form displays correctly in modal
- [ ] All fields validate properly
- [ ] Required fields show errors
- [ ] Form submits successfully
- [ ] Email notifications work
- [ ] Mobile responsive design
- [ ] Accessibility compliance
- [ ] Multi-language support

## Troubleshooting

### Common Issues

1. **Form not displaying:** Check if `RealEstateHelper::isEnabledConsultForm()` returns true
2. **Missing fields:** Verify field names don't conflict with existing ones
3. **Styling issues:** Ensure CSS is properly loaded and not cached
4. **Validation errors:** Check `SendConsultRequest` validation rules
5. **Email not sending:** Verify mail configuration and templates

### Debug Mode
Enable debug mode to see detailed error messages:
```php
// In .env file
APP_DEBUG=true
```

## Performance Considerations

- Use form field caching for dropdowns with many options
- Optimize modal loading for better UX
- Minimize CSS/JS payload
- Use lazy loading for non-critical form enhancements

## Security Best Practices

- Always validate form data on the server side
- Sanitize user input before storage
- Use CSRF protection (already included)
- Implement rate limiting for form submissions
- Validate file uploads if added

## Further Customization

For more advanced customizations:
1. Create custom field types
2. Add AJAX form submission
3. Implement multi-step forms
4. Add file upload capabilities
5. Integrate with CRM systems
6. Add form analytics tracking

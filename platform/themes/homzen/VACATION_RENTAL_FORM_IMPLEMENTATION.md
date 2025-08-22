# Vacation Rental Contact Form Implementation

## What Was Implemented

I've created a complete vacation rental contact form that follows Botble framework conventions and uses Bootstrap row/column structure as requested.

## File Modified

**`platform/themes/homzen/views/real-estate/single-layouts/partials/vacation-rental-contact.blade.php`**

## Key Features Implemented

### ✅ **Author Information Handling**
- Proper author variable assignment: `$author = $vacationRental->author ?? null;`
- Safe null checking to prevent errors
- Display of author avatar, name, phone, and email
- Integration with existing theme settings for hiding phone/email

### ✅ **Bootstrap Row/Column Structure**
The form uses proper Bootstrap structure with each input wrapped in row/column layout:
```html
<div class="row">
    <div class="col-md-6 ip-group">
        <label>Name</label>
        <input type="text" class="form-control" name="name">
    </div>
    <div class="col-md-6 ip-group">
        <label>Email</label>
        <input type="email" class="form-control" name="email">
    </div>
</div>
```

### ✅ **Botble Framework Conventions**
- Uses standard `contact-form` class for existing JavaScript handling
- Uses `ip-group` class following theme patterns
- Leverages existing `route('public.send.consult')` endpoint
- Uses proper `consult_custom_fields[]` naming for custom fields
- CSRF protection included
- Form validation handled by existing framework logic

### ✅ **Vacation Rental Specific Fields**
- **Check-in/Check-out dates** (date inputs)
- **Number of guests** (dynamically generated based on property max guests)
- **Preferred contact method** (Email, Phone, WhatsApp, Both)
- **Purpose of stay** (Vacation, Business, Family Visit, Special Event, etc.)
- **Group type** (Family, Couple, Friends, Business, Solo)
- **Special requirements** (Pet policies, accessibility, parking, etc.)

### ✅ **Property Information Display**
- Read-only vacation rental name field
- Property summary card showing:
  - Price per night
  - Property type/category
  - Minimum stay, maximum guests, number of bedrooms (as badges)

### ✅ **Existing Framework Integration**
- Uses existing form submission handling (no custom JavaScript needed)
- Integrates with existing consult form processing
- Uses existing phone link functionality pattern
- Leverages existing modal Bootstrap components
- Uses existing theme styling with minimal overrides

## Form Structure

### Row 1: Property Information (Read-only)
- Full-width vacation rental name

### Row 2: Personal Information
- Col 6: Name (required)
- Col 6: Email (required)

### Row 3: Contact Details
- Col 6: Phone number
- Col 6: Preferred contact method

### Row 4: Stay Details
- Col 4: Check-in date
- Col 4: Check-out date
- Col 4: Number of guests

### Row 5: Stay Information
- Col 6: Purpose of stay
- Col 6: Group type

### Row 6: Message (Full-width)
- Required message textarea

### Row 7: Special Requirements (Full-width)
- Optional requirements textarea

### Row 8: Submit Button (Full-width)
- Send Message button using theme styling

## Data Submission

Form submits to the existing `public.send.consult` route with:
- `type`: "vacation_rental"
- `data_id`: vacation rental ID
- Standard fields: name, email, phone, content
- Custom fields in `consult_custom_fields[]` array:
  - `preferred_contact_method`
  - `checkin_date`
  - `checkout_date`
  - `number_of_guests`
  - `stay_purpose`
  - `group_type`
  - `special_requirements`

## Styling Approach

- **Minimal CSS overrides** to maintain theme consistency
- **Uses existing theme classes** (`contact-form`, `ip-group`, `tf-btn`, etc.)
- **Bootstrap-compatible** responsive design
- **Mobile-optimized** with proper responsive breakpoints

## JavaScript Usage

- **Minimal JavaScript** - only the existing phone link functionality
- **No custom form validation** - relies on framework handling
- **No AJAX submission** - uses standard form post
- **Framework compatibility** - doesn't interfere with existing theme JS

## Integration Points

The form integrates with existing Botble systems:
- ✅ Consult form processing
- ✅ Email notifications
- ✅ Admin panel consult management
- ✅ Custom fields storage
- ✅ Multi-language support
- ✅ Theme settings (hide phone/email)

## Testing Checklist

- [ ] Form displays properly in vacation rental single view
- [ ] Author information shows correctly
- [ ] All fields render with proper Bootstrap structure
- [ ] Form submits successfully
- [ ] Email notifications are sent
- [ ] Custom fields data is stored properly
- [ ] Mobile responsive design works
- [ ] Phone link functionality works
- [ ] Modal opens and closes properly

## Benefits of This Implementation

1. **Framework Compliance** - Uses Botble conventions
2. **Bootstrap Structure** - Full control over row/column layout
3. **No Breaking Changes** - Doesn't interfere with existing functionality
4. **Maintainable** - Minimal custom code, follows patterns
5. **Extensible** - Easy to add more fields using same structure
6. **Responsive** - Mobile-friendly design
7. **Vacation Rental Specific** - Fields relevant to booking inquiries

This implementation gives you complete control over the form HTML structure while maintaining compatibility with the existing Botble framework and theme patterns.

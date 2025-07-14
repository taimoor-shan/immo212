# Debug Vacation Rental Calendar Issue

## Current Problem
The form is submitting successfully but WITHOUT availability_data fields. The JavaScript is not creating the hidden inputs.

## Debug Steps

### 1. Check if Calendar is Being Initialized

Open browser console and run:
```javascript
// Check current state
debugVacationRentalCalendar()

// Check if elements exist
console.log('Type select:', document.getElementById('type'))
console.log('Vacation rental metabox:', document.querySelector('[data-type="vacation_rental"]'))
console.log('Calendar container:', document.getElementById('property-availability-calendar'))
console.log('Calendar section:', document.getElementById('calendar-section'))

// Check property type
const typeSelect = document.getElementById('type')
console.log('Current property type:', typeSelect ? typeSelect.value : 'not found')
```

### 2. Manually Initialize Calendar

If calendar is not initialized, try:
```javascript
// Force initialization
debugInitializeCalendar()

// Check if it worked
console.log('Calendar instance:', window.propertyAvailabilityCalendar)
```

### 3. Test Calendar Actions

If calendar is initialized, test the actions:
```javascript
// Check if calendar has pending changes
if (window.propertyAvailabilityCalendar) {
    console.log('Pending changes:', window.propertyAvailabilityCalendar.pendingChanges)
    
    // Try to manually update form inputs
    window.propertyAvailabilityCalendar.updateFormInputs()
    
    // Check if hidden inputs were created
    console.log('Hidden inputs:', {
        blocked: document.querySelector('input[name="availability_data[blocked_dates]"]'),
        maintenance: document.querySelector('input[name="availability_data[maintenance_dates]"]'),
        unblocked: document.querySelector('input[name="availability_data[unblocked_dates]"]')
    })
}
```

### 4. Check Form Submission

Before submitting the form:
```javascript
// Check form data
const form = document.querySelector('form.js-base-form')
if (form) {
    const formData = new FormData(form)
    const availabilityData = {}
    for (let [key, value] of formData.entries()) {
        if (key.includes('availability_data')) {
            availabilityData[key] = value
        }
    }
    console.log('Availability data in form:', availabilityData)
}
```

## Expected Issues

### Issue 1: Calendar Not Initialized
**Symptoms**: `window.propertyAvailabilityCalendar` is undefined
**Cause**: Property type not set to vacation_rental or metabox not visible
**Fix**: Set property type to vacation_rental and check if metabox appears

### Issue 2: Calendar Initialized But No Actions
**Symptoms**: Calendar exists but `pendingChanges` is empty
**Cause**: User hasn't selected dates and applied actions
**Fix**: Select dates in calendar and click "Block Selected Dates"

### Issue 3: Actions Applied But No Hidden Inputs
**Symptoms**: `pendingChanges` has data but no hidden inputs in form
**Cause**: `updateFormInputs()` not working or wrong form selector
**Fix**: Check `findPropertyForm()` method and form selector

### Issue 4: Hidden Inputs Created But Not Submitted
**Symptoms**: Hidden inputs exist but not in form submission
**Cause**: Form submission interceptor not working
**Fix**: Check form submission listener and timing

## Quick Fix Test

Try this in browser console to manually create the inputs:
```javascript
// Manually create hidden inputs for testing
const form = document.querySelector('form.js-base-form')
if (form) {
    // Remove existing inputs
    form.querySelectorAll('input[name*="availability_data"]').forEach(input => input.remove())
    
    // Create test inputs
    const testData = {
        'availability_data[blocked_dates]': JSON.stringify(['2025-07-15', '2025-07-16']),
        'availability_data[maintenance_dates]': JSON.stringify(['2025-07-17']),
        'availability_data[unblocked_dates]': JSON.stringify([])
    }
    
    Object.entries(testData).forEach(([name, value]) => {
        const input = document.createElement('input')
        input.type = 'hidden'
        input.name = name
        input.value = value
        form.appendChild(input)
        console.log('Created input:', name, '=', value)
    })
    
    console.log('Test inputs created. Now submit the form.')
}
```

## Next Steps

1. Run the debug commands above
2. Identify which issue is occurring
3. Apply the appropriate fix
4. Test form submission with manual inputs if needed

$(() => {
    $(document).on('change', '#type', (event) => {
        const selectedType = $(event.currentTarget).val()

        // Handle period field visibility (only for rent)
        if (selectedType === 'rent') {
            $('#period').closest('.period-form-group').removeClass('hidden').fadeIn()
        } else {
            $('#period').closest('.period-form-group').addClass('hidden').fadeOut()
        }

        // Handle vacation rental fields visibility
        if (selectedType === 'vacation_rental') {
            $('.vacation-rental-fields').addClass('fade-in').fadeIn(400)
        } else {
            $('.vacation-rental-fields').removeClass('fade-in').fadeOut(300)
        }
    })

    $(document).on('change', '#never_expired', (event) => {
        if ($(event.currentTarget).is(':checked') === true) {
            $('#auto_renew').closest('.auto-renew-form-group').addClass('hidden').fadeOut()
        } else {
            $('#auto_renew').closest('.auto-renew-form-group').removeClass('hidden').fadeIn()
        }
    })
})

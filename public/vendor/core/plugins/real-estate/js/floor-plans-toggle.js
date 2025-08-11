$(document).ready(function() {
    // Toggle floor plans interface based on number_floor value
    function toggleFloorPlansInterface() {
        const numberFloor = parseInt($('#number_floor').val()) || 1;
        
        if (numberFloor === 1) {
            // Show single floor plan fields
            $('.single-floor-field').show();
            $('.multi-floor-field').hide();
            $('#single-floor-plan').show();
            $('#multi-floor-plans').hide();
        } else {
            // Show multi floor plans repeater
            $('.single-floor-field').hide();
            $('.multi-floor-field').show();
            $('#single-floor-plan').hide();
            $('#multi-floor-plans').show();
        }
    }
    
    // Initial toggle on page load
    toggleFloorPlansInterface();
    
    // Toggle when number_floor changes
    $('#number_floor').on('change input', function() {
        toggleFloorPlansInterface();
    });
    
    // Auto-generate floor plans when number_floor increases
    $('#number_floor').on('change', function() {
        const numberFloor = parseInt($(this).val()) || 1;
        
        if (numberFloor > 1) {
            // Check if we need to auto-generate floor plan entries
            const currentFloorPlans = $('.repeater-item').length;
            
            if (currentFloorPlans === 0) {
                // Auto-generate floor plan entries
                for (let i = 1; i <= numberFloor; i++) {
                    const floorName = i === 1 ? 'Ground Floor' : 
                                     i === 2 ? '1st Floor' :
                                     i === 3 ? '2nd Floor' :
                                     i === 4 ? '3rd Floor' :
                                     `${i-1}th Floor`;
                    
                    // Trigger add repeater item and set the name
                    // This would need to integrate with the existing repeater JS
                    console.log(`Would create floor plan for: ${floorName}`);
                }
            }
        }
    });
});

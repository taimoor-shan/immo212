$(document).ready(function() {
    'use strict';
    
    /**
     * Conditional Floor Plans Handler
     * Toggles between single floor plan fields and multiple floor plans repeater
     * based on the number_floor field value
     */
    class ConditionalFloorPlans {
        constructor() {
            this.numberFloorField = $('#number_floor');
            this.singleFloorSection = $('#single-floor-plan');
            this.multiFloorSection = $('#multi-floor-plans');
            this.singleFloorFields = $('.single-floor-field');
            this.multiFloorFields = $('.multi-floor-field');
            
            this.init();
        }
        
        init() {
            // Initial toggle on page load
            this.toggleFloorPlansInterface();
            
            // Toggle when number_floor changes
            this.numberFloorField.on('change input', () => {
                this.toggleFloorPlansInterface();
            });
            
            // Auto-generate floor plans when number increases
            this.numberFloorField.on('change', () => {
                this.handleFloorCountChange();
            });
        }
        
        /**
         * Toggle floor plans interface based on number_floor value
         */
        toggleFloorPlansInterface() {
            const numberFloor = parseInt(this.numberFloorField.val()) || 1;
            
            if (numberFloor === 1) {
                this.showSingleFloorInterface();
            } else {
                this.showMultiFloorInterface();
            }
        }
        
        /**
         * Show single floor plan fields
         */
        showSingleFloorInterface() {
            this.singleFloorSection.show();
            this.multiFloorSection.hide();
            this.singleFloorFields.show();
            this.multiFloorFields.hide();
            
            // Clear multi-floor data when switching to single
            this.clearMultiFloorData();
        }
        
        /**
         * Show multiple floor plans repeater
         */
        showMultiFloorInterface() {
            this.singleFloorSection.hide();
            this.multiFloorSection.show();
            this.singleFloorFields.hide();
            this.multiFloorFields.show();
            
            // Clear single floor data when switching to multi
            this.clearSingleFloorData();
        }
        
        /**
         * Handle floor count changes for auto-generation
         */
        handleFloorCountChange() {
            const numberFloor = parseInt(this.numberFloorField.val()) || 1;
            
            if (numberFloor > 1) {
                this.autoGenerateFloorPlans(numberFloor);
            }
        }
        
        /**
         * Auto-generate floor plan entries based on floor count
         */
        autoGenerateFloorPlans(floorCount) {
            const repeaterContainer = $('.repeater-container');
            const currentItems = repeaterContainer.find('.repeater-item').length;
            
            // Only auto-generate if no items exist
            if (currentItems === 0) {
                for (let i = 1; i <= floorCount; i++) {
                    const floorName = this.generateFloorName(i);
                    this.addFloorPlanItem(floorName, i);
                }
            }
        }
        
        /**
         * Generate appropriate floor name based on floor number
         */
        generateFloorName(floorNumber) {
            if (floorNumber === 1) {
                return 'Ground Floor';
            } else if (floorNumber === 2) {
                return '1st Floor';
            } else if (floorNumber === 3) {
                return '2nd Floor';
            } else if (floorNumber === 4) {
                return '3rd Floor';
            } else {
                return `${floorNumber - 1}th Floor`;
            }
        }
        
        /**
         * Add a new floor plan item to the repeater
         */
        addFloorPlanItem(floorName, floorNumber) {
            // Find the repeater add button using the correct selector
            const addButton = $('[data-target="repeater-add"]');
            if (addButton.length) {
                addButton.trigger('click');

                // Set the floor name in the newly added item
                setTimeout(() => {
                    const repeaterGroup = $('.repeater-group');
                    const lastFieldset = repeaterGroup.find('fieldset').last();
                    const nameField = lastFieldset.find('input[name*="[name]"]');
                    if (nameField.length) {
                        nameField.val(floorName);
                    }
                }, 200);
            }
        }
        
        /**
         * Clear single floor data when switching to multi-floor
         */
        clearSingleFloorData() {
            // Only clear if user confirms and there's data to clear
            const floorName = $('#floor_name').val();
            const floorImage = $('#floor_plan_image').val();
            const floorDocument = $('#floor_plan_document').val();

            if (floorName || floorImage || floorDocument) {
                const confirmClear = confirm('Switching to multiple floors will clear single floor data. Continue?');
                if (confirmClear) {
                    $('#floor_name').val('');
                    // Clear media fields - these are handled by the media field components
                    $('#floor_plan_image').val('');
                    $('#floor_plan_document').val('');

                    // Trigger change events to update UI
                    $('#floor_plan_image, #floor_plan_document').trigger('change');
                }
            }
        }

        /**
         * Clear multi-floor data when switching to single
         */
        clearMultiFloorData() {
            // Only clear if user confirms and there are items to clear
            const repeaterFieldsets = $('.repeater-group fieldset');
            if (repeaterFieldsets.length > 0) {
                const confirmClear = confirm('Switching to single floor will clear multiple floor plans data. Continue?');
                if (confirmClear) {
                    repeaterFieldsets.each(function() {
                        const removeButton = $(this).find('[data-target="repeater-remove"]');
                        if (removeButton.length) {
                            removeButton.trigger('click');
                        }
                    });
                }
            }
        }
        
        /**
         * Migrate single floor data to multi-floor format
         */
        migrateSingleToMulti() {
            const floorName = $('#floor_name').val();
            const floorImage = $('#floor_plan_image').val();
            const floorDocument = $('#floor_plan_document').val();

            if (floorName || floorImage || floorDocument) {
                // Add the single floor data as the first item in multi-floor
                this.addFloorPlanItem(floorName || 'Ground Floor', 1);

                // Set the image and document if available
                setTimeout(() => {
                    const repeaterGroup = $('.repeater-group');
                    const firstFieldset = repeaterGroup.find('fieldset').first();

                    if (floorImage) {
                        const imageField = firstFieldset.find('input[name*="[image]"]');
                        if (imageField.length) {
                            imageField.val(floorImage);
                        }
                    }
                }, 300);
            }
        }

        /**
         * Migrate multi-floor data to single floor format
         */
        migrateMultiToSingle() {
            const repeaterGroup = $('.repeater-group');
            const firstFieldset = repeaterGroup.find('fieldset').first();

            if (firstFieldset.length) {
                const firstName = firstFieldset.find('input[name*="[name]"]').val();
                const firstImage = firstFieldset.find('input[name*="[image]"]').val();

                if (firstName) {
                    $('#floor_name').val(firstName);
                }
                if (firstImage) {
                    $('#floor_plan_image').val(firstImage);
                    $('#floor_plan_image').trigger('change');
                }
            }
        }
    }
    
    // Initialize the conditional floor plans handler
    new ConditionalFloorPlans();
    
    // Additional helper functions for form validation
    window.validateFloorPlans = function() {
        const numberFloor = parseInt($('#number_floor').val()) || 1;
        
        if (numberFloor === 1) {
            // Validate single floor plan
            const floorName = $('#floor_name').val();
            const floorImage = $('#floor_plan_image').val();
            
            if (!floorName && !floorImage) {
                alert('Please provide at least a floor name or floor plan image.');
                return false;
            }
        } else {
            // Validate multi-floor plans
            const repeaterItems = $('.repeater-item');
            if (repeaterItems.length === 0) {
                alert('Please add floor plans for your multi-story property.');
                return false;
            }
            
            // Check each floor plan has at least a name
            let hasError = false;
            repeaterItems.each(function() {
                const name = $(this).find('input[name*="[name]"]').val();
                if (!name) {
                    hasError = true;
                    return false;
                }
            });
            
            if (hasError) {
                alert('Please provide a name for each floor plan.');
                return false;
            }
        }
        
        return true;
    };
});

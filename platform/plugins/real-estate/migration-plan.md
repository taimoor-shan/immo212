
Plan

# Vacation Rental Refactoring Plan

## Current Issues Analysis
- Vacation rental functionality is tightly coupled with Property model
- Mixed concerns in Property model (sale/rent vs vacation rental)
- Difficult to isolate vacation rental features
- Property model is becoming too complex

## Recommended Solution: Separate Models in Same Plugin

### 1. Create New Models Structure
```
RealEstate Plugin:
├── Property (for sale/rent properties)
├── Project (existing)
└── VacationRental (new main model)
    ├── VacationRentalBooking (existing)
    ├── VacationRentalAvailability (rename/refactor from PropertyAvailability)
    └── VacationRentalCalendarEvent (rename/refactor)
```

### 2. Migration Strategy

#### Phase 1: Create VacationRental Model
- Create new `re_vacation_rentals` table
- Migrate vacation rental properties to new table
- Create proper relationships

#### Phase 2: Refactor Dependencies
- Update controllers to use new models
- Refactor services (AvailabilityService, etc.)
- Update forms and tables

#### Phase 3: Clean Up
- Remove vacation rental fields from Property model
- Update frontend templates
- Remove unused code

### 3. Benefits of This Approach
- ✅ Clean separation of concerns
- ✅ Easier to maintain vacation rental features
- ✅ Keeps shared infrastructure (locations, accounts, etc.)
- ✅ No plugin dependency complexity
- ✅ Maintains existing functionality
- ✅ Better scalability for vacation rental features

### 4. Database Changes Required

#### New Tables:
```sql
-- Main vacation rental table
CREATE TABLE `re_vacation_rentals` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `description` text,
    `content` longtext,
    `location` varchar(255),
    `images` json,
    `price` decimal(15,2), -- per night
    `currency_id` bigint unsigned,
    `city_id` bigint unsigned,
    `state_id` bigint unsigned,
    `country_id` bigint unsigned,
    `latitude` decimal(10,8),
    `longitude` decimal(11,8),
    
    -- Vacation rental specific fields
    `check_in_time` time,
    `check_out_time` time,
    `minimum_stay` int,
    `maximum_stay` int,
    `maximum_guests` int,
    `cleaning_fee` decimal(15,2),
    `security_deposit` decimal(15,2),
    `house_rules` text,
    `cancellation_policy` text,
    
    -- Common fields
    `status` varchar(60),
    `is_featured` tinyint(1),
    `author_id` bigint unsigned,
    `author_type` varchar(255),
    `created_at` timestamp,
    `updated_at` timestamp,
    PRIMARY KEY (`id`)
);

-- Rename existing tables
RENAME TABLE `re_property_availability` TO `re_vacation_rental_availability`;
RENAME TABLE `re_property_calendar_events` TO `re_vacation_rental_calendar_events`;
```

### 5. Code Structure Changes

#### Models:
```php
// New VacationRental model
class VacationRental extends BaseModel
{
    // Vacation rental specific logic
    public function bookings()
    {
        return $this->hasMany(VacationRentalBooking::class);
    }
    
    public function availability()
    {
        return $this->hasMany(VacationRentalAvailability::class);
    }
}

// Clean Property model
class Property extends BaseModel
{
    // Remove vacation rental fields and relationships
    // Keep only sale/rent property logic
}
```

#### Controllers:
- Keep existing vacation rental controllers
- Update to use VacationRental model instead of Property
- Maintain same routes and functionality

### 6. Migration Steps

#### Step 1: Create VacationRental Model and Migration
1. Generate VacationRental model
2. Create migration for re_vacation_rentals table
3. Create data migration to copy vacation rental properties

#### Step 2: Update Relationships
1. Update VacationRentalBooking to use vacation_rental_id
2. Update availability tables to use vacation_rental_id
3. Update calendar events

#### Step 3: Refactor Controllers and Services
1. Update all vacation rental controllers
2. Update AvailabilityService
3. Update pricing services

#### Step 4: Update Frontend
1. Update forms to work with VacationRental
2. Update admin tables and views
3. Update public booking interfaces

#### Step 5: Clean Up
1. Remove vacation rental fields from Property
2. Update Property forms and validation
3. Remove unused relationships

### 7. Minimal Breaking Changes
- Existing vacation rental URLs and routes remain same
- Existing bookings and data preserved
- API endpoints maintain compatibility
- Admin interface looks the same to users

### 8. Implementation Timeline
- **Week 1**: Create models and migrations
- **Week 2**: Update controllers and services  
- **Week 3**: Update frontend and forms
- **Week 4**: Testing and cleanup
- **Week 5**: Data migration and deployment

## Conclusion
This approach gives you the best of both worlds:
- Clean separation of vacation rentals from properties
- Maintains all existing functionality
- Easier to develop vacation rental specific features
- No plugin dependency complexity
- Scalable for future enhancements

The vacation rental feature is substantial enough to warrant its own model structure while staying within the real estate plugin ecosystem.
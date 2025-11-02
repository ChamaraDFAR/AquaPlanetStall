-- Migration script to update existing booking categories based on total_price
-- This updates bookings that are likely U-section restaurant bookings

-- First, update the ENUM to include restaurant categories (if not already done)
ALTER TABLE bookings MODIFY COLUMN category ENUM('Ornamental','Other','General Restaurant','Special Restaurant') NOT NULL;

-- Update bookings with total_price = 200000 to 'General Restaurant'
-- (assuming these are single U-section General Restaurant bookings)
UPDATE bookings 
SET category = 'General Restaurant' 
WHERE category = 'Other' 
  AND total_price = 200000
  AND id IN (
    SELECT DISTINCT b.id 
    FROM bookings b
    INNER JOIN booking_items bi ON b.id = bi.booking_id
    INNER JOIN stalls s ON bi.stall_id = s.id
    WHERE s.id LIKE 'U%' 
      AND s.category_id = 1
      AND b.total_price = 200000
  );

-- Update bookings with total_price = 400000 to 'Special Restaurant'
-- (assuming these are single U-section Special Restaurant bookings)
UPDATE bookings 
SET category = 'Special Restaurant' 
WHERE category = 'Other' 
  AND total_price = 400000
  AND id IN (
    SELECT DISTINCT b.id 
    FROM bookings b
    INNER JOIN booking_items bi ON b.id = bi.booking_id
    INNER JOIN stalls s ON bi.stall_id = s.id
    WHERE s.id LIKE 'U%' 
      AND s.category_id = 2
      AND b.total_price = 400000
  );

-- Alternative: Update based on stall category_id if available
-- This is a more accurate approach
UPDATE bookings b
INNER JOIN booking_items bi ON b.id = bi.booking_id
INNER JOIN stalls s ON bi.stall_id = s.id
SET b.category = CASE 
    WHEN s.category_id = 1 THEN 'General Restaurant'
    WHEN s.category_id = 2 THEN 'Special Restaurant'
    ELSE b.category
END
WHERE s.id LIKE 'U%' 
  AND s.category_id IN (1, 2)
  AND b.category IN ('Other', 'Ornamental');


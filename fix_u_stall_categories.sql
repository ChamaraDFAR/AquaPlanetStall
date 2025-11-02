-- Script to fix U-section stall category assignments
-- U1-U15 should be Special Restaurant (category_id = 2)
-- U16-U20 should be General Restaurant (category_id = 1)

-- Update U1-U15 to Special Restaurant (category_id = 2)
UPDATE stalls 
SET category_id = 2, 
    price = 400000
WHERE id LIKE 'U%' 
  AND CAST(SUBSTRING(id, 2) AS UNSIGNED) BETWEEN 1 AND 15;

-- Update U16-U20 to General Restaurant (category_id = 1)
UPDATE stalls 
SET category_id = 1, 
    price = 200000
WHERE id LIKE 'U%' 
  AND CAST(SUBSTRING(id, 2) AS UNSIGNED) BETWEEN 16 AND 20;


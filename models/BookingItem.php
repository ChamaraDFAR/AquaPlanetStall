<?php
// BookingItem model for booking item data and validation
class BookingItem
{
    public $stall_id;
    public $organization;
    public $category_id;
    public $price;

    public function __construct($stall_id, $organization, $category_id = null, $price = 0)
    {
        $this->stall_id = $stall_id;
        $this->organization = $organization;
        $this->category_id = $category_id;
        $this->price = $price;
    }
}

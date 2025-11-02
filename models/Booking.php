<?php
// Booking model (for future use, e.g. ORM or data transfer)

require_once __DIR__ . '/BookingItem.php';

class Booking
{
    public $id;
    public $reference;
    public $category;
    public $total_price;
    /** @var BookingItem[] */
    public $items = [];

    public function __construct($reference, $category, $total_price)
    {
        $this->reference = $reference;
        $this->category = $category;
        $this->total_price = $total_price;
    }

    public function addItem(BookingItem $item)
    {
        $this->items[] = $item;
    }

    public function validate()
    {
        if (empty($this->reference) || empty($this->category) || $this->total_price < 0) {
            return false;
        }
        foreach ($this->items as $item) {
            if (empty($item->stall_id) || empty($item->organization)) {
                return false;
            }
        }
        return true;
    }
}

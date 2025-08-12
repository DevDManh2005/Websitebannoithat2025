<?php

namespace App\View\Components;

use Illuminate\View\Component;

class WishlistIcon extends Component
{
    public $productId;
    public $isWishlisted;

    /**
     * Create a new component instance.
     */
    public function __construct($productId, $isWishlisted = false)
    {
        $this->productId = $productId;
        $this->isWishlisted = $isWishlisted;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('frontend.components.wishlist-icon');
    }
}

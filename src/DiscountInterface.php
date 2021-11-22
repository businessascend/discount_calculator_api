<?php

declare(strict_types=1);

namespace app\src;

interface DiscountInterface
{
    public function calculateDiscount(): void;

    public function getDiscountInfo() : array;
}
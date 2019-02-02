<?php

function format_cost(float $cost): string {
    $cost = ceil($cost);
    if ($cost >= 1000) {
        $cost = number_format($cost, 0, ',', ' ');
    }
    return $cost . ' ₽';
};

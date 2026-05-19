<?php

declare(strict_types=1);

namespace Stancer\Card;

enum PreferredNetwork: string
{
    case National = 'national';
    case Visa = 'visa';
    case MasterCard = 'mastercard';
}

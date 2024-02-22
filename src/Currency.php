<?php
declare(strict_types=1);

namespace Stancer;

/**
 * Currency list.
 */
enum Currency: string
{
    case AUD = 'aud';
    case CAD = 'cad';
    case CHF = 'chf';
    case DKK = 'dkk';
    case EUR = 'eur';
    case GBP = 'gbp';
    case JPY = 'jpy';
    case NOK = 'nok';
    case PLN = 'pln';
    case SEK = 'sek';
    case USD = 'usd';
}

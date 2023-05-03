<?php declare(strict_types=1);

namespace App\Enum;
enum StatusEnum : string {
    case PENDING = 'PENDING';
    case PAID = 'PAID';
    case APPROVED = 'APPROVED';
}

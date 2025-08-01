<?php

namespace Invoice\Customer\Application\Data;

use Invoice\Base\DataReadonly;
use Invoice\Customer\Domain\Primitives\CustomerId;
use Invoice\Customer\Domain\Primitives\CustomerName;
use Invoice\Customer\Domain\Primitives\Email;
use Invoice\Invoice\Domain\Primitives\DateTime;

final readonly class CustomerData extends DataReadonly
{
    public function __construct(
        public CustomerId $id,
        public CustomerName $name,
        public Email $email,
        public ?string $phone,
        public ?string $address,
        public ?string $city,
        public ?string $state,
        public ?string $zipCode,
        public ?string $country,
        public DateTime $createdAt,
        public DateTime $updatedAt,
    ) {}
}

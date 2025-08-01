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
        public ?string $phone = null,
        public ?string $address = null,
        public ?string $city = null,
        public ?string $state = null,
        public ?string $zipCode = null,
        public ?string $country = null,
        public DateTime $createdAt,
        public DateTime $updatedAt,
    ) {}
}
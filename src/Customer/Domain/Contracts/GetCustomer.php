<?php

namespace Invoice\Customer\Domain\Contracts;

use Invoice\Customer\Application\Data\CustomerData;
use Invoice\Customer\Domain\Primitives\CustomerId;

interface GetCustomer
{
    public function findById(CustomerId $id): ?CustomerData;

    /** @return \Invoice\Customer\Application\Data\CustomerData[] */
    public function getAll(): array;
}

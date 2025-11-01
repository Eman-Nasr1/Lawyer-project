<?php

namespace App\Modules\Addresses\Repositories;

use App\Models\Address;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AddressRepositoryInterface
{
    public function paginateForLawyer(int $lawyerId, int $perPage = 15): LengthAwarePaginator;
    public function paginateForCompany(int $companyId, int $perPage = 15): LengthAwarePaginator;
    public function findOrFail(int $id): Address;
    public function create(array $data): Address;
    public function update(int $id, array $data): Address;
    public function delete(int $id): void;
    public function setAsPrimary(int $id): void;
    public function unsetOtherPrimaries(string $addressableType, int $addressableId, ?int $exceptId = null): void;
}


<?php

namespace App\Modules\Addresses\Repositories\Eloquent;

use App\Models\Address;
use App\Modules\Addresses\Repositories\AddressRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AddressRepository implements AddressRepositoryInterface
{
    public function paginateForLawyer(int $lawyerId, int $perPage = 15): LengthAwarePaginator
    {
        return Address::where('addressable_type', 'lawyer')
            ->where('addressable_id', $lawyerId)
            ->latest()
            ->paginate($perPage);
    }

    public function paginateForCompany(int $companyId, int $perPage = 15): LengthAwarePaginator
    {
        return Address::where('addressable_type', 'company')
            ->where('addressable_id', $companyId)
            ->latest()
            ->paginate($perPage);
    }

    public function findOrFail(int $id): Address
    {
        return Address::findOrFail($id);
    }

    public function create(array $data): Address
    {
        return Address::create($data);
    }

    public function update(int $id, array $data): Address
    {
        $address = $this->findOrFail($id);
        $address->update($data);
        return $address->fresh();
    }

    public function delete(int $id): void
    {
        $this->findOrFail($id)->delete();
    }

    public function setAsPrimary(int $id): void
    {
        $address = $this->findOrFail($id);
        $this->unsetOtherPrimaries($address->addressable_type, $address->addressable_id, $id);
        $address->update(['is_primary' => true]);
    }

    public function unsetOtherPrimaries(string $addressableType, int $addressableId, ?int $exceptId = null): void
    {
        $query = Address::where('addressable_type', $addressableType)
            ->where('addressable_id', $addressableId)
            ->where('is_primary', true);
        
        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }
        
        $query->update(['is_primary' => false]);
    }
}


<?php

namespace App\Modules\Addresses\Services;

use App\Models\Address;
use App\Modules\Addresses\Repositories\AddressRepositoryInterface as Repo;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AddressService
{
    public function __construct(private Repo $repo) {}

    public function listForLawyer(int $lawyerId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repo->paginateForLawyer($lawyerId, $perPage);
    }

    public function listForCompany(int $companyId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repo->paginateForCompany($companyId, $perPage);
    }

    public function get(int $id): Address
    {
        return $this->repo->findOrFail($id);
    }

    public function createForLawyer(int $lawyerId, array $data): Address
    {
        $data['addressable_type'] = 'lawyer';
        $data['addressable_id'] = $lawyerId;
        
        // If this is set as primary, unset others
        if (!empty($data['is_primary'])) {
            $this->repo->unsetOtherPrimaries('lawyer', $lawyerId);
        }
        
        return $this->repo->create($data);
    }

    public function createForCompany(int $companyId, array $data): Address
    {
        $data['addressable_type'] = 'company';
        $data['addressable_id'] = $companyId;
        
        // If this is set as primary, unset others
        if (!empty($data['is_primary'])) {
            $this->repo->unsetOtherPrimaries('company', $companyId);
        }
        
        return $this->repo->create($data);
    }

    public function update(int $id, array $data): Address
    {
        $address = $this->repo->findOrFail($id);
        
        // If setting as primary, unset others
        if (isset($data['is_primary']) && $data['is_primary']) {
            $this->repo->unsetOtherPrimaries($address->addressable_type, $address->addressable_id, $id);
        }
        
        return $this->repo->update($id, $data);
    }

    public function delete(int $id): void
    {
        $this->repo->delete($id);
    }

    public function setAsPrimary(int $id): Address
    {
        $this->repo->setAsPrimary($id);
        return $this->repo->findOrFail($id);
    }
}


<?php

namespace App\Repositories\Eloquents;

use App\Models\Customer;
use App\Repositories\Interfaces\BaseRepository;
use App\Repositories\Interfaces\ICustomerRepository;

class CustomerRepository extends BaseRepository implements ICustomerRepository
{
    public function __construct()
    {
        $this->model = new Customer();
    }

    public function getAll($perPage) {
        return $this->model->where('is_active', 1)->paginate($perPage);
    }

    public function getCustomers($perPage, $searchActive, $searchName, $searchEmail, $searchAddress)
    {
        $customers = $this->model->where(function ($query) use ($searchActive) {
            if (isset($searchActive)) {
                $query->where('is_active', $searchActive);
            }
        })->where(function ($query) use ($searchName, $searchEmail, $searchAddress) {
            if ($searchName) {
                $query->where('customer_name', 'like', '%'.$searchName.'%');
            }
            if ($searchEmail) {
                $query->orWhere('email', 'like', '%'.$searchEmail.'%');
            }
            if ($searchAddress) {
                $query->orWhere('address', 'like', '%'.$searchAddress.'%');
            }
        })->paginate($perPage);
        return $customers;
    }

    public function addCustomer($customer) {
        return $this->model->create($customer);
    }

    public function updateCustomer($customer_id , $data) {
        return $this->model->where("customer_id", $customer_id)->update($data);
    }

}

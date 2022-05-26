<?php

namespace App\Repositories\Interfaces;

interface  ICustomerRepository extends IBaseRepository
{

    public function getCustomers($perPage, $searchActive, $searchName, $searchEmail, $searchAddress);

    public function getAll($perPage);

    public function addCustomer($customer);

    public function updateCustomer($customer_id , $data);
}

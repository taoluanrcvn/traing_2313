<?php

namespace App\Repositories\Interfaces;

interface  ICustomerRepository extends IBaseRepository
{
    /**
     * Get all
     * @return mixed
     */
    public function getCustomers($perPage, $searchActive, $searchName, $searchEmail, $searchAddress);

    /**
     * Get all
     * @return mixed
     */
    public function getAll($perPage);

    public function existEmailCustomer($email);

    public function addCustomer($customer);
}

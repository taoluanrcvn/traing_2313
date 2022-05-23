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

}

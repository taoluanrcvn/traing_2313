<?php

namespace App\Repositories\Interfaces;

interface IProductRepository extends IBaseRepository
{
    public function getAll($perPage);

    public function getProducts($perPage, $searchSales, $searchName, $searchPriceFrom, $searchPriceTo);

    public function deleteProduct($product_id);

    public function addProduct($product);

    public function getProductIdLast();
}

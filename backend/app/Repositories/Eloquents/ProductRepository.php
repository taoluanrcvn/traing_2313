<?php

namespace App\Repositories\Eloquents;

use App\Models\Product;
use App\Repositories\Interfaces\BaseRepository;
use App\Repositories\Interfaces\IProductRepository;

class ProductRepository extends BaseRepository implements IProductRepository
{
    public function __construct()
    {
        $this->model = new Product();
    }

    public function getAll($perPage) {
        return $this->model->orderBy('created_at', 'DESC')->paginate($perPage);
    }

    public function getProducts($perPage, $searchSales, $searchName, $searchPriceFrom, $searchPriceTo)
    {
        $products = $this->model->where(function ($query) use ($searchSales) {
            if (isset($searchSales) && (int) $searchSales !== 2) {
                $query->where('is_sales', $searchSales)->where('inventory','>' , 0);
            } else if (isset($searchSales) && (int) $searchSales === 2) {
                $query->where('inventory', 0);
            }
        })->where(function ($query) use ($searchName, $searchPriceFrom, $searchPriceTo) {
            if ($searchName) {
                $query->where('product_name', 'like', '%'.$searchName.'%');
            }
            if ($searchPriceFrom && $searchPriceTo) {
                $query->whereBetween('product_price', [$searchPriceFrom, $searchPriceTo]);
            }
            if ($searchPriceFrom && !$searchPriceTo) {
                $query->where('product_price', '>=', $searchPriceFrom);
            }
            if (!$searchPriceFrom && $searchPriceTo) {
                $query->where('product_price', '<=', $searchPriceTo);
            }
        })->paginate($perPage);
        return $products;
    }

    public function deleteProduct($product_id) {
        return $this->model->where('product_id', $product_id)->delete();
    }

    public function addProduct($product) {
        return $this->model->create($product);
    }

    public function getProductIdLast() {
        return $this->model->orderBy('created_at', 'DESC')->first();
    }

    public function updateProduct($product_id, $data) {
        return $this->model->where("product_id", $product_id)->update($data);
    }
}

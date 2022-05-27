<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Response\ResponseJson;
use App\Models\Product;
use App\Repositories\Interfaces\IProductRepository;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use mysql_xdevapi\Exception;
use Illuminate\Support\Facades\Auth;
class ProductController extends Controller
{
    protected $productRepository;

    public function __construct(IProductRepository $productRepository)
    {
        $this->middleware('jwt.verify');
        $this->productRepository = $productRepository;
    }
    /**
     * Lấy danh sách khách hàng (tìm kiếm, phần trang)
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->perPage;
        $isSearch = $request->isSearch;
        if (!$isSearch) {
            $products = $this->productRepository->getAll($perPage);
        } else {
            $products = $this->productRepository->getProducts($perPage, $request->isSale, $request->name, $request->minPrice, $request->maxPrice);
        }
        return ResponseJson::success($products);
    }

    /**
     * Thêm sản phẩm mới
     *
     * @param  \Illuminate\Http\ProductRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        $validated = $request->validated();

        $image_64 = $validated["product_image"];
        $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];   // .jpg .png .pdf

        $type_support = ['jpeg', 'png', 'jpg'];
        if (!in_array($extension, $type_support)) {
            return ResponseJson::error(["detail" => trans('messages.validate.image')]);
        }

        $imageName = $this->uploadImage($image_64, $extension);

        $validated["product_image"] = $imageName;

        $productLast = $this->productRepository->getProductIdLast();
        $productIdNext = $this->createProductId($productLast->product_id);
        $validated["product_id"] = $productIdNext;

        $addProduct = $this->productRepository->addProduct($validated);
        if ($addProduct) {
            return ResponseJson::success($addProduct);
        }
        return ResponseJson::error(['detail' => trans('messages.status_query.add_fail')]);
    }

    protected function createProductId($product_id_last) {
        $firstCharacter = $product_id_last[0];
        $numberLast = substr($product_id_last, 1);
        $nextNumber = $numberLast + 1;
        $MAX_LENGTH_NUMBER = 9;
        $zero_time = $MAX_LENGTH_NUMBER - strlen($nextNumber);
        $zero_string = '';
        for ($i = 0 ; $i < $zero_time ; $i++) {
            $zero_string .= '0';
        }
        return $firstCharacter . $zero_string . $nextNumber;
    }

    protected function uploadImage($image_64, $extension) {
        $replace = substr($image_64, 0, strpos($image_64, ',')+1);

        $image = str_replace($replace, '', $image_64);

        $image = str_replace(' ', '+', $image);

        $imageName = 'images/' .uniqid().'.'.$extension;

        $isUpLoad = Storage::disk('public')->put($imageName, base64_decode($image));
        if (!$isUpLoad) {
            return ResponseJson::error(["detail" => trans('messages.upload_error')]);
        }
        return $imageName;
    }
    /**
     * Cập nhật product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, Product $product)
    {
        $validated = $request->validated();
        $isUpload = $validated["is_upload"];
        if ($isUpload) {
            $image_64 = $validated["product_image"];
            $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];   // .jpg .png .pdf

            $type_support = ['jpeg', 'png', 'jpg'];
            if (!in_array($extension, $type_support)) {
                return ResponseJson::error(["detail" => trans('messages.validate.image')]);
            }

            $imageName = $this->uploadImage($image_64, $extension);
            $validated["product_image"] = $imageName;
        }

        if ($validated["inventory"] == 0) {
            $validated["is_sales"] = 0;
        }
        unset($validated["is_upload"]);
        $isUpdate = $this->productRepository->updateProduct($product->product_id, $validated);
        if ($isUpdate) {
            if ($isUpload) {
                unlink(storage_path('app/public/'. $product->product_image));
            }
            return ResponseJson::success();
        }
        return ResponseJson::error(['detail' => trans('messages.status_query.add_fail')]);
    }

    /**
     * Xóa sản phẩm
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        try {
            $userCurrent = Auth::user();
            if (!$userCurrent->is_admin) {
                return ResponseJson::error(["detail" => trans('messages.role.not_permission')]);
            }
            $this->productRepository->deleteProduct($product->product_id);
            return ResponseJson::success();
        } catch (Exception $e) {
            return ResponseJson::error($e);
        }
    }
}

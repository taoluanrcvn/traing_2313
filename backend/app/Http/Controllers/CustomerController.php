<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerAddRequest;
use App\Http\Requests\CustomerPostRequest;
use App\Http\Response\ResponseJson;
use App\Models\Customer;
use App\Repositories\Interfaces\ICustomerRepository;
use http\Env\Response;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    protected $customerRepository;

    public function __construct(ICustomerRepository $customerRepository)
    {
        $this->middleware('jwt.verify');
        $this->customerRepository = $customerRepository;
    }

    /**
     * Lấy danh sách khách hàng (tìm kiếm, phần trang)..
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->perPage;
        $isSearch = $request->isSearch;
        if (!$isSearch) {
            $customer = $this->customerRepository->getAll($perPage);
        } else {
            $customer = $this->customerRepository->getCustomers($perPage, $request->isActive, $request->customer_name, $request->email, $request->address);
        }
        return response()->json([
            "statusCode" => true,
            "data" => $customer,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Thêm khách hàng mới
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomerAddRequest $request)
    {
        $validated = $request->validated();

        $existEmail = $this->customerRepository->existEmailCustomer($validated['email']);
        if ($existEmail) {
            return ResponseJson::error(["email" => trans('messages.email.exist')]);
        }

        $addCustomer = $this->customerRepository->addCustomer($validated);
        return ResponseJson::success($addCustomer);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        //
    }

    /**
     * Cập nhật thông tin khách hàng
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Customer $customer)
    {
        //
    }

    /**
     * Xóa khách hàng
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        //
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Http\Response\ResponseJson;
use App\Models\Customer;
use App\Repositories\Interfaces\ICustomerRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        return ResponseJson::success($customer);
    }

    /**
     * Thêm khách hàng mới
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomerRequest $request)
    {
        $validated = $request->validated();
        $userCurrent = Auth::user();
        if ($userCurrent->group_role === 'Reviewer' || $userCurrent->group_role === 'Editor') {
            return ResponseJson::error(["permission" => trans('messages.role.not_permission')]);
        }
        $addCustomer = $this->customerRepository->addCustomer($validated);

        return ResponseJson::success($addCustomer);
    }

    /**
     * Cập nhật thông tin khách hàng
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(CustomerRequest $request, Customer $customer)
    {
        $validated = $request->validated();
        $userCurrent = Auth::user();
        if ($userCurrent->group_role === 'Reviewer') {
            return ResponseJson::error(["detail" => trans('messages.role.not_permission')]);
        }
        $updateCustomer = $this->customerRepository->updateCustomer($customer->customer_id ,$validated);
        if ($updateCustomer) {
            return ResponseJson::success();
        }
        return ResponseJson::error(["detail" => trans('messages.status_query.update_fail')]);
    }

}

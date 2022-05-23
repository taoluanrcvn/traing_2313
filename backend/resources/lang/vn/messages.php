<?php
return [
    'user' => [
        'locked' => 'Tài khoản của bạn đã bị khóa',
        'deleted' => 'Tài khoản của đã bị xóa',
        'required' => 'Chưa nhập tên',
        'min_length_name' => 'Tên phải dài hơn 5 ký tự',
        'not_exist' => 'Người dùng không tồn tại!',
        'not_permission_lock' => 'Người dùng này không được khóa',
        'not_permission_unlock' => 'Người dùng này không được mở khóa',
        'not_permission' => 'Bạn không có quyên để thực hiện tính năng này!',
        'not_permission_delete' => 'Người dùng này không được xóa!',
    ],
    'email' => [
        'required' => 'E-mail không được để trống!',
        'not_exist' => 'E-mail không tồn tại!',
        'malformed' => 'E-mail không đúng định dạng',
        'exist' =>  'E-mail đã tồn tại!'
    ],
    'password' => [
        'fail' => 'Mật khẩu không đúng!',
        'min' => 'Mật khẩu phải hơn 5 ký tự',
        'required' => 'Mật khẩu không được để trống'
    ],
    'password_confirm' => [
        'required' => 'Chưa nhập mật khẩu xác nhận',
        'mismatched' => 'Mật khẩu không khớp'
    ],
    'auth' => [
        'unauthorized' => 'Không được phép tạo token'
    ],
    'role' => [
        'not_permission' => 'Bạn không có quyền thực hiện tính năng này'
    ],
    'is_active' => [
        'required' => 'Chưa chọn trạng thái'
    ],
    'group_role' => [
        'required' => 'Chưa chọn nhóm',
        'not_permission' => 'Bạn không được thêm người dùng ở nhóm này'
    ]
];

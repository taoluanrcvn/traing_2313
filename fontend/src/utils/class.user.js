export class User {
    id = null;
    name = null;
    email = null;
    verify_email = null;
    is_active = 0 | 1;
    is_delete = 0 | 1;
    group_role = 'Admin' | 'Reviewer' | 'Editor';
    last_login_at = null;
    last_login_ip = null;
    is_admin = false;
}

export class Role {
    admin = 'Admin';
    editor = 'Editor';
    reviewer = 'Reviewer';
}
export class Customer {
    customer_id = null;
    customer_name = null;
    email = null;
    tel_num = null;
    is_active = 0 | 1;
    address = null;
}
// export default {user , Role};

export class User {
    id = null;
    name = null;
    email = null;
    verify_email = null;
    is_active = null;
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

export class Product {
    product_id = null;
    product_name = null;
    product_image = null;
    product_price = null;
    is_sales = null;
    inventory = null;
    description ='';
}
// export default {user , Role};

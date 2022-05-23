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
// export default {user , Role};

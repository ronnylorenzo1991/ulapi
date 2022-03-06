import Dashboard from '../../js/pages/dashboard/index';
import Users from '../../js/pages/users/index';
import Roles from '../../js/pages/roles/index';

export default [
    {
        path: '/',
        component: Dashboard
    },
    {
        path: '/dashboard',
        component: Dashboard
    },
    {
        path: '/users',
        component: Users
    },
    {
        path: '/roles',
        component: Roles
    },
];

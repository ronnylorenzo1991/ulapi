import Dashboard from '../../js/pages/dashboard/index';
import Users from '../../js/pages/users/index';
import Roles from '../../js/pages/roles/index';
import Turns from '../../js/pages/turns/index';

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
        path: '/turns',
        component: Turns
    },{
        path: '/users',
        component: Users
    },
    {
        path: '/roles',
        component: Roles
    },
];

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


var CodeliApp = angular.module('CodeliApp', ["ngRoute"]);


/**
 * Lets do some routing
 */
var ANGULAR_TEMPLATES_URL = '/codeli/frontend/templates/';

CodeliApp.config(['$routeProvider',
    function ($routeProvider)
    {
        $routeProvider.when('/dashboard', {
            templateUrl: ANGULAR_TEMPLATES_URL + 'dashboard.html',
            controller: 'DashboardController'
        });

        /**
         * User Management
         */
        $routeProvider.when('/user', {
            templateUrl: ANGULAR_TEMPLATES_URL + 'user-management.html',
            controller: 'UserController'
        }).when('/user/add', {
            templateUrl: ANGULAR_TEMPLATES_URL + 'forms/user-add.html',
            controller: 'UserAddController'
        }).when('/user/:uid/edit', {
            templateUrl: ANGULAR_TEMPLATES_URL + 'forms/user-edit.html',
            controller: 'UserEditController'
        });

        /**
         * RBAC Management
         * - Roles Management
         * - Role Permissions Management
         */
        $routeProvider.when('/rbac/role/add', {
            templateUrl: ANGULAR_TEMPLATES_URL + 'forms/role-add.html',
            controller: 'RoleAddController'
        }).when('/rbac/role/:rid/edit', {
            templateUrl: ANGULAR_TEMPLATES_URL + 'forms/role-add.html',
            controller: 'RoleEditController'
        }).when('/rbac/role', {
            templateUrl: ANGULAR_TEMPLATES_URL + 'pages/roles.html',
            controller: 'RolesViewController'
        });

        $routeProvider.when('/user/role/permission', {
            templateUrl: ANGULAR_TEMPLATES_URL + 'forms/role-permissions.html',
            controller: 'RolePermissionsController'
        });

        $routeProvider.otherwise({
            redirectTo: 'dashboard'
        });
    }
]);

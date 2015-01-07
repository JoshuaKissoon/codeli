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
        }).otherwise({
            redirectTo: '/dashboard'
        });
    }
]);

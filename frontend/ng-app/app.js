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

CodeliApp.config(['$routeProvider', function ($routeProvider)
    {
        $routeProvider.when('/dashboard', {
            title: 'Dashboard',
            templateUrl: ANGULAR_TEMPLATES_URL + 'ng-views/dashboard.html',
            controller: 'DashboardController'
        });

        $routeProvider.otherwise({
            redirectTo: '/dashboard'
        });
    }
]);

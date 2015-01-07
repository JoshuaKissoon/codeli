/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


CodeliApp.controller("MainController", ['$scope', '$http',
    function ($scope, $http)
    {
        $http.get("?urlq=admin/user").success(function (data)
        {
            /* Set all loaded tradebots as de-activated */
            
        });
    }
]);
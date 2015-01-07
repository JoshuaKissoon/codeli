/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


CodeliApp.controller("MainController", ['$scope', '$http', 'Data',
    function ($scope, $http, Data)
    {
        for (var i = 0; i < 10; i++)
        {
            Data.get("admin/user").then(function (result)
            {
                /* Set all loaded tradebots as de-activated */

            });
        }
    }
]);
/**
 * Javascript file with ng-app code for Land Ownership Types
 */

/**
 * Controller to handle the addition of a land ownership type
 * 
 * @param $scope
 * @param Data
 */
CodeliApp.controller("RoleAddController", ['$scope', 'Data',
    function ($scope, Data)
    {
        $scope.Form = {};
        var form = $scope.Form;

        form.Errors = [];

        form.object = {};
        form.object.title = "";
        form.object.description = "";

        form.submitHandler = function ()
        {
            form.Errors = [];
            if ("" === form.object.title)
            {
                form.Errors.push("Invalid title.");
            }

            /* Everything's good */
            Data.put("rbac/role", form.object).then(function (result)
            {
                if (true === result.success)
                {
                    window.location = "#rbac/role";
                }
                else
                {
                    form.Errors.push(result.message);
                }
            });

        };

    }
]);

/**
 * Controller to handle the editing of a connection type
 * 
 * @param $scope
 * @param Data
 */
CodeliApp.controller("RoleEditController", ['$scope', 'Data', '$routeParams',
    function ($scope, Data, $routeParams)
    {
        $scope.Form = {};
        var form = $scope.Form;

        form.Errors = [];
        form.rid = $routeParams.rid;

        form.object = {};
        form.object.title = "";
        form.object.description = "";

        /* Lets load the connection type */
        Data.get("rbac/role/" + form.rid).then(function (result)
        {
            if (true === result.success)
            {
                form.object = result.data;
            }
        });


        form.submitHandler = function ()
        {
            form.Errors = [];
            if ("" === form.object.title)
            {
                form.Errors.push("Invalid title.");
            }

            /* Everything's good */
            Data.post("rbac/role", form.object).then(function (result)
            {
                if (true === result.success)
                {
                    window.location = "#rbac/role";
                }
                else
                {
                    form.Errors.push(result.message);
                }
            });

        };

    }
]);

/**
 * Controller to handle the displaying of all land ownership types
 * 
 * @param $scope
 * @param Data
 */
CodeliApp.controller("RolesViewController", ['$scope', 'Data',
    function ($scope, Data)
    {
        $scope.Display = {};
        var display = $scope.Display;

        /**
         * Lets load the set of Connection Types
         */
        Data.post("rbac/role/all", {}).then(function (result)
        {
            if (true === result.success)
            {
                display.roles = result.data;
            }
        });

    }
]);
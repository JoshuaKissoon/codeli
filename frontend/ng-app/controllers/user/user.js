/**
 * File with different controllers for a user
 * 
 * @author Joshua Kissoon
 * @since 20150108
 */


/**
 * Controller to handle user login
 */
CodeliApp.controller("LoginController", ['$scope', 'Data',
    function ($scope, Data)
    {

        $scope.LoginForm = {};
        $scope.LoginForm.Errors = [];

        $scope.LoginForm.userId = "";
        $scope.LoginForm.password = "";

        /**
         * Function that handles the submission of the login form
         */
        $scope.LoginForm.SubmitHandler = function ()
        {
            $scope.LoginForm.Errors = [];

            if ($scope.LoginForm.userId === "")
            {
                $scope.LoginForm.Errors.push("Please enter a valid user id");
            }

            if ($scope.LoginForm.password === "")
            {
                $scope.LoginForm.Errors.push("Please enter a valid password.");
            }

            if ($scope.LoginForm.Errors.length < 1)
            {
                Data.post("admin/user/login", $scope.LoginForm).then(function (result)
                {
                    /* User logged in successfully, redirect to the dashboard */
                    if (result.success === true)
                    {
                        window.location = "#dashboard";
                    }
                    else
                    {
                        $scope.LoginForm.Errors.push(result.message);
                    }
                });
            }
        };
    }
]);

/**
 * Controller to handle user login
 */
CodeliApp.controller("UserController", ['$scope', 'Data', 'Settings',
    function ($scope, Data, Settings)
    {
        $scope.UserMgmt = {};
        $scope.UserMgmt.sidebarTemplate = Settings.getTemplate("components/user-mgmt-sidebar.html");

        /**
         * Lets load all user's data
         */
        Data.post("admin/user/all").then(function (result)
        {
            $scope.UserMgmt.users = result.data;
        });
    }
]);

/**
 * Controller to handle user addition
 */
CodeliApp.controller("UserAddController", ['$scope', 'Data', 'Settings',
    function ($scope, Data, Settings)
    {
        $scope.Form = {};
        var form = $scope.Form;

        $scope.sidebarTemplate = Settings.getTemplate("components/user-mgmt-sidebar.html");
        form.Errors = [];

        form.object = {};
        form.object.userId = "";
        form.object.password = "";
        form.object.email = "";
        form.object.firstName = "";
        form.object.lastName = "";
        form.object.otherName = "";
        form.object.roles = [];

        /* Load the set of possible roles */
        Data.post("rbac/role/all", {}).then(function (result)
        {
            form.roles = result.data;
        });

        form.SubmitHandler = function ()
        {
            form.Errors = [];
            if ("" === form.userId)
            {
                form.Errors.push("Please enter a valid user id.");
            }
            if ("" === form.userId)
            {
                form.Errors.push("Please enter a valid password.");
            }

            if (form.Errors.length > 0)
            {
                return;
            }

            /* Everything good, lets add the user now */
            Data.put("admin/user", form.object).then(function (result)
            {
                /* User logged in successfully, redirect to the dashboard */
                if (result.success === true)
                {
                    window.location = "#user";
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
 * Controller to handle user editing
 */
CodeliApp.controller("UserEditController", ['$scope', 'Data', 'Settings', '$routeParams',
    function ($scope, Data, Settings, $routeParams)
    {
        $scope.Form = {};
        var form = $scope.Form;

        $scope.sidebarTemplate = Settings.getTemplate("components/user-mgmt-sidebar.html");
        form.Errors = [];

        form.uid = $routeParams.uid;

        form.object = {};
        form.object.userId = "";
        form.object.password = "";
        form.object.email = "";
        form.object.firstName = "";
        form.object.lastName = "";
        form.object.otherName = "";
        form.object.roles = [];

        /* Load the set of possible roles */
        Data.post("rbac/role/all", {}).then(function (result)
        {
            form.roles = result.data;
        });

        /* Load the user */
        Data.get("admin/user/" + form.uid).then(function (result)
        {

            /* Lets set the roles to true */
            for (var rid in result.data.roles)
            {
                result.data.roles[rid] = true;
            }

            form.object = result.data;
        });


        form.SubmitHandler = function ()
        {
            form.Errors = [];
            if ("" === form.userId)
            {
                form.Errors.push("Please enter a valid user id.");
            }
            if ("" === form.userId)
            {
                form.Errors.push("Please enter a valid password.");
            }

            if (form.Errors.length > 0)
            {
                return;
            }

            /* Everything good, lets add the user now */
            Data.post("admin/user", form.object).then(function (result)
            {
                /* User logged in successfully, redirect to the dashboard */
                if (result.success === true)
                {
                    window.location = "#user";
                }
                else
                {
                    form.Errors.push(result.message);
                }
            });

        };
    }
]);
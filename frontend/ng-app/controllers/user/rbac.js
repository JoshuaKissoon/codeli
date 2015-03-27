/**
 * File that handles all Role Based Access Control Operations
 * 
 * @author Joshua Kissoon
 * @since 20150113
 */

/**
 * Controller to handle displaying editing of the different role permissions
 * 
 * @author Joshua Kissoon
 * @since 20150113
 */
CodeliApp.controller("RolePermissionsController", ['$scope', 'Data',
    function ($scope, Data)
    {
        /**
         * Lets load roles
         */
        $scope.RBAC = {};
        var rbac = $scope.RBAC;

        $scope.md = true;

        /**
         * Load all roles in the system
         */
        Data.post("rbac/role/all", {}).then(function (result)
        {
            rbac.roles = result.data;

            for (var rid in rbac.roles)
            {
                var role = rbac.roles[rid];
                role.formattedPermissions = [];
                for (var id in role.permissions)
                {
                    var perm = role.permissions[id];
                    role.formattedPermissions[perm.permission] = true;
                }
            }
        });

        /**
         * Load all permissions in the system
         */
        Data.get("rbac/permission/all").then(function (result)
        {
            rbac.permissions = result.data;
        });

        /**
         * Method to update a role permission
         * 
         * @param {Integer} rid The Role Id
         * @param {String} permission The Permission Id
         * @param {Boolean} checked Whether the checkbox is checked or not
         */
        rbac.UpdateRolePermission = function (rid, permission, checked)
        {
            var data = {
                "rid": rid,
                "permission": permission
            };
            /**
             * Add the new permission to this role
             */
            if (true === checked)
            {
                Data.put("rbac/role/permission", data).then(function (result)
                {
                    /* @todo Show some notification or something */
                });
            }
            /**
             * Remove the permission from this role
             */
            else
            {
                Data.post("rbac/role/permission", data).then(function (result)
                {
                    /* @todo Show some notification or something */
                });
            }
        };
    }
]);



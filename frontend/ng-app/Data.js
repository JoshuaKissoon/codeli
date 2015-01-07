/*
 * Data Service for MisApp
 * 
 * This is a REST based service client for this app which calls the specific API url for getting or posting data
 * 
 * @author Indrajeet Nagda
 * @since 20141204 IST
 */
app.factory("Data", ['$http',
    function ($http) {

        var serviceBase = '/codeli/?urlq=/';

        var obj = {};

        /**
         * Get method
         * 
         * @param q The API sub-url
         */
        obj.get = function (q) {
            return $http.get(serviceBase + q).then(function (results)
            {
                return results.data;
            });
        };

        /**
         * Implementation of post method
         * 
         * @param q The API sub-url
         * @param object The data to post to the server
         */
        obj.post = function (q, object) {

            var http = $http({
                headers: {'Content-Type': 'application/json'},
                method: "post",
                url: serviceBase + q,
                data: object
            });
            return http.then(function (results)
            {
                return results.data;
            });
        };

        /**
         * Put method implementation
         * 
         * @param q The API sub-url
         * @param object The data to post to the server
         */
        obj.put = function (q, object) {
            var http = $http({
                headers: {'Content-Type': 'application/json'},
                method: "put",
                url: serviceBase + q,
                data: object
            });
            return http.then(function (results)
            {
                return results.data;
            });
        };

        /**
         * Delete method implementation
         * 
         * @param q The API sub-url
         * @param object The data to post to the server
         */
        obj.delete = function (q)
        {
            return $http.delete(serviceBase + q).then(function (results)
            {
                return results.data;
            });
        };

        /**
         * Now we return the object with all the methods
         */
        return obj;
    }]);

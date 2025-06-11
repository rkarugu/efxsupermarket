(function () {
    'use strict';
    angular
        .module('app')
        .factory('AuthenticationService', AuthenticationService);

        //var api_base_url = 'http://demo2server.in/sites/laravelapp/restaurants/api/';

       // / var api_base_url = 'http://localhost/restaurants/api/';

        var api_base_url = 'http://45.33.125.246/api/';

    AuthenticationService.$inject = ['$http', '$cookies', '$rootScope', '$timeout', 'UserService','$templateCache'];
    function AuthenticationService($http, $cookies, $rootScope, $timeout, UserService,$templateCache) {
        var service = {};
        service.Login = Login;
        service.ClearCredentials = ClearCredentials;
        service.getNewResquestFroDj = getNewResquestFroDj;
        service.updateDjRequestStatus = updateDjRequestStatus;
        return service;

        function Login(username, password, callback) {
            $http.post(api_base_url+'loginDjUser', { username: username, password: password })
                .then(function (response) {
                    callback(response);
                });
        }

        function getNewResquestFroDj(restaurant_id, user_id,returnresponse) {
            $http.post(api_base_url+'getNewResquestFroDj', { restaurant_id: restaurant_id, user_id: user_id})
                .then(function (response) {
                    returnresponse(response);
                });
        }

        function updateDjRequestStatus(request_id, current_status, returnresponse) 
        {
            $http.post(api_base_url+'updateDjRequestStatus', { request_id: request_id, current_status: current_status })
                .then(function (response) {
                    returnresponse(response);
                });
        } 

        function ClearCredentials() {
             $templateCache.removeAll();
            $rootScope.globals = {};
            $cookies.remove('globals');
            $http.defaults.headers.common.Authorization = 'Basic';
        }
    }



})();
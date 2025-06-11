(function () {
    'use strict';
    angular
        .module('app')
        .factory('AuthenticationService', AuthenticationService);

        //var api_base_url = 'http://demo2server.in/sites/laravelapp/restaurants/api/';

        //var api_base_url = 'http://localhost/restaurants/api/';

        var api_base_url = 'http://hotelwizbayleys.com/api/';

    AuthenticationService.$inject = ['$http', '$cookies', '$rootScope', '$timeout', 'UserService','$templateCache'];
    function AuthenticationService($http, $cookies, $rootScope, $timeout, UserService,$templateCache) {
        var service = {};
        service.Login = Login;
        service.ClearCredentials = ClearCredentials;
        service.getOrderedItemForPrintClassUsers = getOrderedItemForPrintClassUsers;
        service.updateOrderedItemStatus = updateOrderedItemStatus;
        service.cancleItemsFromOrder = cancleItemsFromOrder;
        service.printbillForOrder = printbillForOrder;
        service.managedocketstatus = managedocketstatus;

        

        return service;

        function Login(username, password, callback) {
            $http.post(api_base_url+'loginPrintClassUser', { username: username, password: password, is_balley_request: 1 })
                .then(function (response) {
                    callback(response);
                });
        }

        function getOrderedItemForPrintClassUsers(restaurant_id, print_class_id,print_class_user_id, returnresponse) {
            $http.post(api_base_url+'getOrderedItemForPrintClassUsers', { restaurant_id: restaurant_id, print_class_id: print_class_id,print_class_user_id:print_class_user_id, is_balley_request: 1 })
                .then(function (response) {
                    returnresponse(response);
                });
        }

        function updateOrderedItemStatus(ordered_id, current_status,print_class_id, returnresponse) 
        {
            $http.post(api_base_url+'updateOrderedItemStatus', { ordered_id: ordered_id, current_status: current_status,print_class_id:print_class_id, is_balley_request: 1 })
                .then(function (response) {
                    returnresponse(response);
                });
        } 

        function cancleItemsFromOrder(ordered_id, print_class_id, returnresponse) 
        {
            $http.post(api_base_url+'cancleItemsFromOrder', { ordered_id: ordered_id, print_class_id:print_class_id, is_balley_request: 1 })
                .then(function (response) {
                    returnresponse(response);
                });
        } 

        function printbillForOrder(ordered_id, print_type,print_class_user_id,returnresponse) 
        {
            $http.post(api_base_url+'printbillForOrder', { ordered_id: ordered_id,print_type:print_type, print_class_user_id:print_class_user_id, is_balley_request: 1 })
                .then(function (response) {
                    returnresponse(response);
                });
        } 


        function managedocketstatus(ordered_id, print_class_user_id,returnresponse) 
        {
            $http.post(api_base_url+'managedocketstatus', { ordered_id: ordered_id, print_class_user_id:print_class_user_id, is_balley_request: 1 })
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
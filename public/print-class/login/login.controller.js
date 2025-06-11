(function () {
    'use strict';

    angular
        .module('app')
        .controller('LoginController', LoginController);

    LoginController.$inject = ['$location', 'AuthenticationService', 'FlashService','$rootScope','$localStorage'];
    function LoginController($location, AuthenticationService, FlashService,$rootScope,$localStorage) {
        var vm = this;

        vm.login = login;

        (function initController() {
            // reset login status
            AuthenticationService.ClearCredentials();
        })();

        function login() {
            vm.dataLoading = true;
            AuthenticationService.Login(vm.username, vm.password, function (response) {
                if (response.data.status == true) 
                {
                   // AuthenticationService.SetCredentials(vm.username, vm.password);



                    //var authdata = Base64.encode(username + ':' + password);

                    $rootScope.globals = {
                        currentUser: {
                            username: vm.username,
                           // authdata: authdata,
                            userdetails:response.data.userdetails
                        }
                    };

                    $localStorage.userdetails= response.data.userdetails;



                    $location.path('/');
                } 
                else 
                {
                    FlashService.Error(response.data.message);
                    vm.dataLoading = false;
                }
            });
        };
    }

})();

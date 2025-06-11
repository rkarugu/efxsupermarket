(function () {
    'use strict';

    angular
        .module('app')
        .controller('PageController', PageController);

    PageController.$inject = ['UserService', '$rootScope','$localStorage','$location','$scope','$timeout','AuthenticationService','$sce'];
    function PageController(UserService, $rootScope,$localStorage,$location,$scope,$timeout,AuthenticationService,$sce) {
        var vm = this;
        vm.user = $localStorage.userdetails;
        vm.name = $localStorage.userdetails.name;
        vm.user_id = $localStorage.userdetails.user_id;
        vm.restaurant_id = $localStorage.userdetails.restaurant_id;
        vm.logout = logout;
        vm.can_call_ajax = true;
        vm.UpdateStatus = UpdateStatus;
        $scope.countDown_tick =  [];
        $scope.alreadyprinted=[];
        $scope.beepsound=false;

        var totalcountDown = [];
        $scope.countDowWatch = function () {
            if($localStorage.userdetails)
            {
                if(vm.can_call_ajax == true)
                {
                     AuthenticationService.getNewResquestFroDj(vm.restaurant_id, vm.user_id, function (response) {
                        if (response.data.status == true) 
                        {
                            $scope.countDown_tick =  response.data.data;
                            angular.forEach( $scope.countDown_tick, function(value, key) 
                            {
                                var alreadyrendered = getCookie('oldrenderedrequestsdata');
                                if( alreadyrendered.indexOf(value.request_id) =='-1' && $scope.beepsound== false )
                                {
                                    $scope.beepsound=true;
                                    var audio = new Audio('to-the-point.ogg'); 
                                    audio.play();  
                                }
                                addIntoCookie('oldrenderedrequestsdata',value.request_id);
                            });
                            $scope.beepsound=false;
                        } 
                    });
                }
            $timeout(function(){
                $scope.countDowWatch();
            },5000)
            }
  
        };
        $scope.countDowWatch();

        function logout()
        {
          $localStorage.$reset();
          $location.path('/login');
        }

        function addIntoCookie(cookie_name,value)
        {
            var is_cookie_exist = getCookie(cookie_name);
            if (is_cookie_exist != "") 
            {
                var arr = JSON.parse(is_cookie_exist);
                arr.push(value); 
            }
            else
            {
                var arr = [value];
            }
            var uniqueNames = [];
            $.each(arr, function(i, el){
                if($.inArray(el, uniqueNames) === -1) uniqueNames.push(el);
            });
            var json_str = JSON.stringify(uniqueNames); 
            setCookie(cookie_name,json_str,1);
        }


        function getCookie(cname) 
        {
            var name = cname + "=";
            var decodedCookie = decodeURIComponent(document.cookie);
            var ca = decodedCookie.split(';');
            for(var i = 0; i <ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') {
                    c = c.substring(1);
                }
                if (c.indexOf(name) == 0) {
                    return c.substring(name.length, c.length);
                }
            }
            return "";
        }

        function setCookie(cname, cvalue, exdays) 
        {
            var d = new Date();
            d.setTime(d.getTime() + (exdays*24*60*60*1000));
            var expires = "expires="+ d.toUTCString();
            document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
        }

        function UpdateStatus(item)
        {
            vm.can_call_ajax = false;
            vm.status = item.status;
            vm.status_color = item.status_color;
            item.status = 'Updating...';

            
            AuthenticationService.updateDjRequestStatus(item.request_id,vm.status, function (response) {
                if (response.data.status == true) 
                {
                    item.status =  response.data.data.status;
                    item.status_color = response.data.data.status_color;
                } 
                else
                {
                    item.status =  vm.status;
                    item.status_color = vm.status_color;
                }
                vm.can_call_ajax = true;
            });
        }

        $scope.htmlAdText = function(text){
        return $sce.trustAsHtml(text);
        }



        
    }

})();
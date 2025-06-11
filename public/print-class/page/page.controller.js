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
        vm.print_class_id = $localStorage.userdetails.print_class_id;
        vm.restaurant_id = $localStorage.userdetails.restaurant_id;
        vm.print_class_name = $localStorage.userdetails.print_class_name;

        vm.print_class_user_id = $localStorage.userdetails.print_class_user_id;
        vm.logout = logout;
        vm.can_call_ajax = true;
        vm.UpdateStatus = UpdateStatus;
        vm.cancleItems = cancleItems;
        vm.printingbill = printingbill;
        vm.managedocketstatus = managedocketstatus;




     


        
        //$scope.countDown_tick =  $localStorage.userdetails.orderdetail;
         $scope.countDown_tick =  [];
         $scope.alreadyprinted=[];
         $scope.beepsound=false;


        var totalcountDown = [];
        $scope.countDowWatch = function () {
            if($localStorage.userdetails)
            {
                if(vm.can_call_ajax == true)
                {
                     AuthenticationService.getOrderedItemForPrintClassUsers(vm.restaurant_id, vm.print_class_id,vm.print_class_user_id, function (response) {
                        if (response.data.status == true) 
                        {
                             var is_cookie_exist = getCookie('printedDocketWithCookie_'+vm.print_class_user_id);
                            if (is_cookie_exist != "") 
                            {
                                $scope.alreadyprinted= JSON.parse(is_cookie_exist);
                                
                            }

                            $scope.countDown_tick =  response.data.orderdetail;


                            angular.forEach( $scope.countDown_tick, function(value, key) {


                                 if(value.status == 'NEW')
                                {
                                    //alert();
                                    //alert(value.status);
                                  printingbill(value,'D');
                                }

                                    

                                     var alreadyrendered = getCookie('oldrenderedordersdata_'+vm.print_class_user_id);

                                     
                                    

                                   // alert(alreadyrendered.indexOf(25));
                                    if( alreadyrendered.indexOf(value.order_number) =='-1' && $scope.beepsound== false )
                                    {

                                      $scope.beepsound=true;

                                        var audio = new Audio('to-the-point.ogg'); 
                                      
                                        audio.play();  
                                    }

                                    addIntoCookie('oldrenderedordersdata_'+vm.print_class_user_id,value.order_number);
                                   
                                    

                            });
                            $scope.beepsound=false;
                        } 
                    });
                }
               

            $timeout(function(){
                $scope.countDowWatch();
            },10000)
            }
  
        };
        $scope.countDowWatch();

       

        function logout()
        {
          $localStorage.$reset();
          $location.path('/login');
        }


        function managedocketstatus(item)
        {
            AuthenticationService.managedocketstatus(item.order_number,vm.print_class_user_id, function (response) {

                var return_data =  response.data;
                if(return_data.status_changed == true)
                {
                    item.status =  return_data.changed_status;
                    item.status_color = return_data.status_color;
                    item.can_print_docket = return_data.can_print; 
                }

            });
        }
        function printingbill(item,print_type)
        {
            AuthenticationService.printbillForOrder(item.order_number,print_type,vm.print_class_user_id, function (response) {
                managedocketstatus(item);
                var receipt_data = response.data;
                var divContents = receipt_data;

               // item.canpritdocketsnow='no';
               // addIntoCookie('printedDocketWithCookie_'+vm.print_class_user_id, item.order_number);


            

          /*  
            var printWindow = window.open('http://demo2server.in/', 'popup', 'width=400');
            printWindow.document.write('<html><head><title>Bill</title>');
            printWindow.document.write('</head><body >');
            printWindow.document.write(divContents);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.print(); */


            var frame1 = document.createElement('iframe');
            frame1.name = "frame"+item.order_number;
            frame1.style.position = "absolute";
            frame1.style.top = "-1000000px";
            document.body.appendChild(frame1);
            var frameDoc = frame1.contentWindow ? frame1.contentWindow : frame1.contentDocument.document ? frame1.contentDocument.document : frame1.contentDocument;
            frameDoc.document.open();
            frameDoc.document.write('<html><head><title>Docket</title>');
            frameDoc.document.write('</head><body>');
            frameDoc.document.write(divContents);
            frameDoc.document.write('</body></html>');
            frameDoc.document.close();
            setTimeout(function () {

                window.frames["frame"+item.order_number].focus();
                window.frames["frame"+item.order_number].print();
                document.body.removeChild(frame1);
                
            }, 1000);

            
            return false;




           

           





            });
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
            AuthenticationService.updateOrderedItemStatus(item.order_number,vm.status,vm.print_class_id, function (response) {
                if (response.data.status == true) 
                {
                    item.status =  response.data.new_status.status;
                    item.status_color = response.data.new_status.status_color;
                } 
                else
                {
                    item.status =  vm.status;
                    item.status_color = vm.status_color;
                }
                vm.can_call_ajax = true;
            });
        }


        function cancleItems(item)
        {
           item.can_cancle_item = 'cancled'; 
            vm.can_call_ajax = false;

            AuthenticationService.cancleItemsFromOrder(item.order_number,vm.print_class_id, function (response) {
                if (response.data.status == true) 
                {
                    
                } 
               
                vm.can_call_ajax = true;
            });

        }

        $scope.htmlAdText = function(text){
        return $sce.trustAsHtml(text);
        }



        
    }

})();
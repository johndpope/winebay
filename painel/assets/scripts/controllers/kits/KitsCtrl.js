mainProject.controller('KitsCtrl', function ($scope, $http, $state, User) {

  $scope.SavePackage = function () {

      var data = {
          "param1": "value1",
          "param2": "value2",
          "param3": "value3"
      };


      var config = {
        headers: {
            'Access-Control-Allow-Origin': 'https://app-wineblue.herokuapp.com',
            'Access-Control-Allow-Headers': 'Origin, Content-Type, Accept',
            'Access-Control-Allow-Credentials': 'true',
            'Access-Control-Allow-Methods': 'GET, POST, PUT, DELETE, OPTIONS'
        }
      };

      $http.post("https://app-wineblue.herokuapp.com/api/package/edit", data, config, {
        
      }).then(
          function (res) {
              $scope.geralLoad = false;
          },
          function (res) {
              $scope.geralLoad = false;
              bootbox.alert("Não foi possível salvar os dados. Tente novamente.");
          }
      );
  };
});

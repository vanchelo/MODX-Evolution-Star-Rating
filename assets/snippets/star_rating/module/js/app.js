(function (angular) {
    var app = angular.module('Rating', []);

    app.controller('RatingController', function ($scope, $http) {
        var module_path = '/assets/snippets/star_rating/module/';
        $scope.connector = module_path + 'processors/connector.php';

        $scope.getData = function () {
            $http({
                url: $scope.connector,
                method: 'get',
                headers: {action: 'list'}
            }).success(function (data) {
                $scope.resources = data.data;
                $scope.total = data.total;
            });
        };

        $scope.getSortDir = function (column) {
            if ($scope.order == column) {
                return $scope.revers ? 'desc' : 'asc';
            }
        };

        $scope.order = 'id';
        $scope.orderDir = 'ASC';
        $scope.limit = 100;
        $scope.query = '';
        $scope.type = '';
        $scope.revers = false;

        $scope.change = function (order) {
            $scope.params = {
                limit: $scope.limit = parseInt($scope.limit, 10) || 10
            };

            if ($scope.order == order) $scope.revers = !$scope.revers;
            else $scope.revers = false;

            if ($scope.query !== '') $scope.params.query = $scope.query;

            if (order !== '' && typeof order !== 'undefined') {
                $scope.params.order = order;
                $scope.order = order;
            } else if ($scope.order !== '') {
                $scope.params.order = $scope.order
            }

            if (parseInt($scope.id, 10)) $scope.params.id = parseInt($scope.id, 10);

            if ($scope.type > 0) $scope.params.type = $scope.type;

            $scope.params.orderDir = $scope.revers ? 'DESC' : 'ASC';
            $http({
                url: $scope.connector,
                method: 'POST',
                headers: {action: 'list'},
                params: $scope.params
            }).
                success(function (data) {
                    $scope.resources = data.data;
                    $scope.total = data.total;
                });
        };

        $scope.get = function (resource) {
            $http({
                url: $scope.connector,
                method: 'POST',
                headers: {action: 'get'},
                params: {id: resource.id}
            }).
                success(function (data) {
                    if (data.success == true) {
                        $scope.resource = data.data;
                        var modal = $("#edit");
                        modal.modal('show');
                    }
                });
        };

        $scope.reset = function (resource) {
            if (!confirm('Обнулить рейтинг с голосами?')) {
                return false;
            }

            $http({
                url: $scope.connector,
                method: 'get',
                headers: {action: 'reset'},
                params: {id: resource.id}
            }).
                success(function (data) {
                    if (data.success == true) {
                        $scope.change();
                    }
                });
        };

        $scope.getData();
    })
})(angular);


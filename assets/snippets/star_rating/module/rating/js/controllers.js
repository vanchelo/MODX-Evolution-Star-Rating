function ResourcesList($scope, $http) {
    $scope.url = '/assets/modules/rating/connector.php';
    $scope.connector = '/assets/modules/rating/processors/connector.php';
    $http.get($scope.url).success(function(data) {
        $scope.resources = data.data;
        $scope.totalResources = data.total;
        $scope.totalFounded = data.total;
    });

    $scope.getSortDir = function(column) {
        if ($scope.order == column) {
            return $scope.revers ? 'desc' : 'asc';
        }
    };

    $scope.edit = function(resource) {
        $scope.longtitle = resource.longtitle;
        $("#edit").modal('show');
    };

    $scope.order = 'id';
    $scope.orderDir = 'ASC';
    $scope.limit = 100;
    $scope.query = '';
    $scope.type = '';
    $scope.revers = false;

    $scope.update = function(id) {
        console.log('ID: ', id);
    };

    $scope.change = function(order) {
        $scope.params = {
            limit: $scope.limit = parseInt($scope.limit, 10) || 10,
        };
        if ($scope.order == order) {
            $scope.revers = !$scope.revers;
        } else {
            $scope.revers = false;
        }
        if ($scope.query !== '') {
            $scope.params.query = $scope.query;
        }
        if (order !== '' && typeof order !== 'undefined') {
            $scope.params.order = order;
            $scope.order = order;
        } else if ($scope.order !== '') {
            $scope.params.order = $scope.order
        }

        if (parseInt($scope.id, 10)) {
            $scope.params.id = parseInt($scope.id, 10);
        }
        if ($scope.type > 0) {
            $scope.params.type = $scope.type;
        }

        $scope.params.orderDir = $scope.revers ? 'DESC' : 'ASC';
        $http({
            url   : $scope.url,
            method: 'POST',
            params: $scope.params
        }).
            success(function(data) {
                $scope.resources = data.data;
                $scope.totalFounded = data.total;
            });
    };

    $scope.get = function(id) {
        $http({
            url    : $scope.connector,
            method : 'POST',
            headers: {
                action: 'get'
            },
            params : {
                id: id
            }
        }).
            success(function(data) {
                if (data.success == true) {
                    $scope.resource = data.data;
                    var modal = $("#edit");
                    modal.modal('show');
                }
            });
    };
}

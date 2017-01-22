(function (angular) {
  'use strict';

  angular
    .module('StarRating', [])
    .config([
      '$compileProvider', function ($compileProvider) {
        $compileProvider.debugInfoEnabled(false);
      }
    ])
    .constant('RatingConfig', function () {
      return {
        connectorUrl: '/assets/snippets/star_rating/module/processors/connector.php',
        limit: 100
      }
    })
    .factory('Rating', Rating)
    .controller('RatingController', RatingController);

  RatingController.$inject = ['$scope', 'RatingConfig', 'Rating'];

  function RatingController($scope, RatingConfig, Rating) {
    $scope.order = 'id';
    $scope.orderDir = 'ASC';
    $scope.limit = RatingConfig.limit;
    $scope.query = '';
    $scope.type = '';
    $scope.revers = false;

    /////////

    init();

    /////////

    function init() {
      Rating.fetch().then(function (data) {
        $scope.resources = data.resources;
        $scope.total = data.total;
      });
    }

    $scope.getSortDir = function (column) {
      if ($scope.order == column) {
        return $scope.revers ? 'desc' : 'asc';
      }
    };

    $scope.change = function (order) {
      $scope.params = {
        limit: $scope.limit = parseInt($scope.limit, 10) || 10
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

      Rating.fetch($scope.params).then(function (data) {
        $scope.resources = data.resources;
        $scope.total = data.total;
      });
    };

    $scope.get = function (resource) {
      Rating.get(resource.id).then(function (data) {
        if (data.success == true) {
          $scope.resource = data.data;
          $('#edit').modal('show');
        }
      });
    };

    $scope.reset = function (resource) {
      if (!confirm('Обнулить рейтинг с голосами?')) {
        return false;
      }

      Rating.reset(resource.id).then(function (data) {
        if (data.success == true) {
          $scope.change();
        }
      });
    };

    $scope.fullReset = function () {
      if (!confirm('Обнулить все рейтинги?')) {
        return false;
      }

      Rating.resetAll().then(function (data) {
        if (data.success == true) {
          $scope.change();
        }
      });
    };
  }

  Rating.$inject = ['$http', 'RatingConfig'];

  function Rating($http, RatingConfig) {
    var url = RatingConfig.connectorUrl;

    // Exports
    return {
      fetch: fetch,
      get: get,
      reset: reset,
      resetAll: resetAll
    };

    function fetch(params) {
      return $http({
        url: url,
        method: 'get',
        headers: { action: 'list' },
        params: params || {}
      }).then(function (data) {
        return {
          resources: data.data,
          total: data.total
        }
      });
    }

    function reset(resourceId) {
      return $http({
        url: url,
        method: 'get',
        headers: { action: 'reset' },
        params: { id: resourceId }
      });
    }

    function resetAll() {
      return $http({
        url: url,
        method: 'get',
        headers: { action: 'full-reset' }
      });
    }

    function get(resourceId) {
      return $http({
        url: url,
        method: 'POST',
        headers: { action: 'get' },
        params: { id: resourceId }
      });
    }
  }

  angular.bootstrap(document.body, ['StarRating'], {
    strictDi: true
  });
})(window.angular);


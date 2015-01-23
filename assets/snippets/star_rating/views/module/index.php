<?php defined('MODX_BASE_PATH') or die('Error'); ?>
<?php /** @var StarRating $app */ ?>
<!doctype html>
<html lang="en" ng-app>

<head>
    <meta charset="utf-8">
    <title><?= $app->trans('star_rating') ?></title>
    <link rel="stylesheet" href="<?= $app->getConfig('moduleUrl') ?>css/app.css"/>
    <link rel="stylesheet" href="<?= $app->getConfig('moduleUrl') ?>css/bootstrap.min.css"/>
    <style>
        body { padding: 10px 0; }
        .table th { cursor: pointer; }
        .table td, .table th { vertical-align: middle; }
    </style>

    <!-- Scripts -->
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="<?= $app->getConfig('moduleUrl') ?>js/bootstrap.min.js"></script>
    <script src="<?= $app->getConfig('moduleUrl') ?>libs/angular/angular.js"></script>
    <script src="<?= $app->getConfig('moduleUrl') ?>js/controllers.js"></script>
    <!-- Scripts -->
</head>

<body ng-controller="ResourcesList" ng-cloak>

<div class="container">
    <div class="row">
        <div class="col-md-8">
            <h3 style="margin: 0"><?= $app->trans('star_rating') ?></h3>
        </div>
        <div class="col-md-4 text-right">
            <div class="information">
                <span><b>Всего</b> &middot; <span class="label label-success" ng-bind="total"></span></span>
                <span><b>Показано</b> &middot; <span class="label label-success" ng-bind="resources.length"></span></span>
            </div>
        </div>
    </div>

    <hr/>

    <form class="form-inline">
        <div class="form-group" style="margin-right: 10px">
            <input style="width: 300px;" type="text" class="form-control" ng-model="query" ng-change="change()" placeholder="Поиск по названию документа">
        </div>
        <div class="form-group" style="margin-right:10px">
            <label for="perPage">Кол-во</label>
            <input style="width:80px" type="text" class="form-control text-center" id="perPage" ng-model="limit" ng-change="change()">
        </div>
        <div class="form-group">
            <label for="resId">ID ресурса</label>
            <input style="width: 80px" type="text" class="form-control text-center" id="resId" ng-model="id" ng-change="change()">
        </div>
            <button class="btn btn-default pull-right" ng-click="change()">Обновить</button>
    </form>

    <hr/>

    <table class="table table-striped table-hover table-condensed">
        <thead>
        <tr>
            <th style="width:40px;text-align:center" ng-click="change('id')">ID<i ng-class="getSortDir('id')"></i></th>
            <th style="width:280px;text-align:center" ng-click="change('longtitle')">Название<i
                    ng-class="getSortDir('longtitle')"></i></th>
            <th style="width:40px;text-align:center" ng-click="change('rating')">Рейтинг<i ng-class="getSortDir('rating')"></i></th>
            <th style="width:40px;text-align:center" ng-click="change('total')">Сумма оценок<i ng-class="getSortDir('total')"></i></th>
            <th style="width:40px;text-align:center" ng-click="change('votes')">Голоса<i ng-class="getSortDir('votes')"></i></th>
            <th style="width:100px"></th>
        </tr>
        </thead>
        <tr ng-repeat="resource in resources" ng-dblclick="get(resource.id)">
            <td style="text-align:center" ng-bind="resource.id"></td>
            <td ng-bind="resource.pagetitle"></td>
            <td style="text-align:center" ng-bind="resource.rating || '-'"></td>
            <td style="text-align:center" ng-bind="resource.total || '-'"></td>
            <td style="text-align:center" ng-bind="resource.votes || '-'"></td>
            <td>
                <button class="btn btn-success btn-xs" ng-click="get(resource)">Инфо</button>
                <button class="btn btn-danger btn-xs" ng-click="reset(resource)">Сбросить</button>
            </td>
        </tr>
    </table>

    <hr/>

    <!-- Modal -->
    <div id="edit" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Закрыть"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" ng-bind="resource.pagetitle"></h4>
                </div>
                <div class="modal-body">
                    <table class="table table-striped table-hover table-condensed">
                        <thead>
                        <tr>
                            <th style="width:40px">ID</th>
                            <th style="width:40px">Оценка</th>
                            <th style="width:100px">Дата</th>
                            <th style="width:100px">IP</th>
                        </tr>
                        </thead>
                        <tr ng-repeat="vote in resource.votes">
                            <td ng-bind="vote.id"></td>
                            <td ng-bind="vote.vote"></td>
                            <td><span ng-bind="vote.date"></span> в <span ng-bind="vote.time"></span></td>
                            <td ng-bind="vote.ip"></td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</div>

</body>
</html>

<?php

if (!isset($modx) || !$modx instanceof DocumentParser) {
    die;
}

$path = MODX_BASE_URL.'assets/snippets/star_rating/module/';
?>
<!doctype html>
<html lang="en" ng-app ng-controller="ResourcesList">

<head>
    <meta charset="utf-8">
    <title>My HTML File</title>
    <link rel="stylesheet" href="<?php echo $path; ?>css/app.css"/>
    <link rel="stylesheet" href="<?php echo $path; ?>css/bootstrap.min.css"/>
    <link rel="stylesheet" href="<?php echo $path; ?>css/bootstrap-responsive.min.css"/>
    <style>
        body {
            padding-top: 20px;
        }

        .table th {
            cursor: pointer;
        }

        .table td, .table th {
            vertical-align: middle;
        }
    </style>
</head>

<body>

<div class="container">
    <form class="form-inline" method="GET" action="">
        <label>
            <input class="span5" type="text" ng-model="query" ng-change="change()" placeholder="Поиск по названию..."/>
        </label>
        <label>
            Кол-во
            <input class="span1" type="text" ng-model="limit" ng-change="change()"/>
        </label>
        <label>
            ID
            <input class="span1" type="text" ng-model="id" ng-change="change()"/>
        </label>
    </form>

    <div>Всего ресурсов: {{ totalResources }} | Найдено: {{ totalFounded }} | Показано: {{ resources.length }}</div>

    <table class="table table-striped table-hover table-condensed">
        <thead>
        <tr>
            <th style="width: 40px" ng-click="change('id')">ID<i ng-class="getSortDir('id')"></i></th>
            <th style="width: 330px" ng-click="change('longtitle')">Название<i
                    ng-class="getSortDir('longtitle')"></i></th>
            <th style="width:40px">Рейтинг</th>
            <th style="width: 40px" ng-click="change('total')">Сумма оценок<i ng-class="getSortDir('total')"></i></th>
            <th style="width: 40px" ng-click="change('votes')">Голоса<i ng-class="getSortDir('votes')"></i></th>
        </tr>
        </thead>
        <tr ng-repeat="resource in resources" ng-dblclick="get(resource.id)">
            <td>{{resource.id}}</td>
            <td>{{resource.pagetitle}}</td>
            <td>{{resource.total / resource.votes || '-'}}</td>
            <td>{{resource.total || '-'}}</td>
            <td>{{resource.votes || '-'}}</td>
        </tr>
    </table>

    <!-- Modal -->
    <div id="edit" class="modal hide edit-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel">{{resource.pagetitle}}</h3>
        </div>
        <div class="modal-body">
            <table class="table table-striped table-hover table-condensed">
                <thead>
                <tr>
                    <th style="width: 40px">ID</th>
                    <th style="width: 40px">Оценка</th>
                    <th style="width: 100px">Дата</th>
                    <th style="width: 100px">IP</th>
                </tr>
                </thead>
                <tr ng-repeat="vote in resource.votes" ng-dblclick="get(resource.id)">
                    <td>{{vote.id}}</td>
                    <td>{{vote.vote}}</td>
                    <td>{{vote.date}} в {{vote.time}}</td>
                    <td>{{vote.ip}}</td>
                </tr>
            </table>
        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true">Закрыть</button>
        </div>
    </div>

</div>

<!-- Scripts -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="<?php echo $path; ?>js/bootstrap.min.js"></script>
<script src="<?php echo $path; ?>libs/angular/angular.js"></script>
<script src="<?php echo $path; ?>js/controllers.js"></script>
<!-- Scripts -->

</body>
</html>

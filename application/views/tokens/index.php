<div class="row"><div class="col-md-12">
<?php
echo TbHtml::buttonGroup([
    [
        'icon' => 'plus',
        'url' => ['tokens/create', 'surveyId' => $this->survey->sid]
    ]
]);
$columns = isset($dataProvider->data[0]) ? $dataProvider->data[0]->attributeNames() : [];

$columns = [
    [
        'header' => gT("Actions"),
        'class' => TbButtonColumn::class,
        'deleteButtonUrl' => function(Token $model, $row) {
            return App()->createUrl('tokens/delete', ['id' => $model->primaryKey, 'surveyId' => $model->surveyId]);
        },
        'viewButtonUrl' => function(Token $model, $row) {
            return App()->createUrl('tokens/view', ['id' => $model->primaryKey, 'surveyId' => $model->surveyId]);
        },
        'updateButtonUrl' => function(Token $model, $row) {
            return App()->createUrl('tokens/update', ['id' => $model->primaryKey, 'surveyId' => $model->surveyId]);
        },
        'template' => '{createResponse}{view}{update}{delete}',
        'buttons' => [
            'createResponse' => [
                'icon' => TbHtml::ICON_CERTIFICATE,
                'title' => gT("Execute survey with this token."),
                'visible' => function($row, Token $model) {
                    return $model->usesleft > 0;
                },
                'url' => function(Token $model, $row) {
                    return App()->createUrl('surveys/start', ['token' => $model->token, 'id' => $model->surveyId]);
                }
            ]
        ]
    ], [
        'class' => WhRelationalColumn::class,
        'name' => 'Responses',
        'url' => App()->createUrl('tokens/responses', ['surveyId' => $this->survey->sid]),
        'value' => function(\Token $model) {
            /**
             * @todo Optimize this, this will fire a query for each token. Count (cstatrelation) does not work on custom keys.
             *
            */
            return count($model->responses);
        },
//        'afterAjaxUpdate' => 'js:function(tr,rowid,data){
//        bootbox.alert("I have afterAjax events too!<br/>This will only happen once for row with id: "+rowid);
//    }'
    ], [
        'header' => gT("Series"),
        'visible' => $this->survey->use_series,
        'class' => TbButtonColumn::class,
        'template' => "{appendNew}{appendCopy}",
        'buttons' => [
            'appendNew' => [
                'icon' => TbHtml::ICON_PLUS,
                'label' => gT("Add empty response to series"),
                'visible' => function($row, Token $model) {
                    return $model->completed != 'N';
                },
                'url' => function(Token $model, $row) {
                    return App()->createUrl('responses/append', ['id' => $model->primaryKey, 'surveyId' => $model->surveyId, 'copy' => false]);
                }
            ],
            'appendCopy' => [
                'icon' => TbHtml::ICON_PLUS_SIGN,
                'label' => gT("Add response to series, based on last response"),
                'visible' => function($row, Token $model) {
                    return $model->completed != 'N';
                },
                'url' => function(Token $model, $row) {
                    return App()->createUrl('responses/append', ['id' => $model->primaryKey, 'surveyId' => $model->surveyId, 'copy' => true]);
                }
            ]
        ]
    ]
] + $columns;
$this->widget(WhGridView::class, [
    'dataProvider' => $dataProvider,
    'columns' => $columns,
    'responsiveTable' => true,

]);
?></div></div>
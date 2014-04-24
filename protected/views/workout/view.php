<?php
/* @var $this WorkoutController */
/* @var $model Workout */
?>
<script>
    //not allow to write letters in the number textfield
    function validateKeys(ele, evt, ints) {

        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;
        return true;
    }
    $(function () {
        if (<?php if($model->id == ""){echo '0';}else{ echo $model->id;}  ?> !=
        0
        )
        {

            $('#dtpDate').prop('disabled', true);
            $('#txtName').prop('disabled', true);
            $('#dpWorkout').prop('disabled', true);
            $('#txtDescription').prop('disabled', true);

        }
        //validate if a exercise exist in a workout when dropdown change
        $('#dpExercise').change(function () {
            var url = window.location.pathname;
            var exercise = $('#dpExercise').val();

            $.ajax({
                type: "get",
                url: window.location.pathname,
                dataType: "json",
                data: "r=WorkoutDetail/noRepeatExercise&id=<?php echo $model->id; ?>&exercise=" + exercise,
                success: function (data) {
                    if (data.id != 0) {
                        alert("you need to choose another exercise, because the exercise selected is already in the workout");
                        $('#dpExercise').val("");


                    }

                }, error: function (request, status, error) {
                }

            });

        });

        $("#time").mask('00:00', {reverse: true});
    });

</script>

<?php

$this->menu = array(

    array(
        'label' => 'Create Workout',
        'url' => array(
            'create'
        )
    ),
    array(
        'label' => 'Update Workout',
        'url' => array(
            'update',
            'id' => $model->id
        )
    ),
    array(
        'label' => 'Delete Workout',
        'url' => '#',
        'linkOptions' => array(
            'submit' => array(
                'delete',
                'id' => $model->id
            ),
            'confirm' => 'Are you sure you want to delete this item?'
        )
    ),
    array(
        'label' => 'Manage Workout',
        'url' => array(
            'admin'
        )
    )
);

?>

<div class="row">
    <div class="col-mod-12">
        <?php
        $this->widget('bootstrap.widgets.BsBreadcrumb', array(
            'links' => array(
                'Workouts' => array(
                    'view',
                    'id' => 0
                ),
                $model->name
            )
        ));
        foreach (Yii::app()->user->getFlashes() as $key => $message) {
            echo '<div class="alert alert-danger alert-dismissable">' . $message . "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>x</button></div>\n";
        };
        ?>
    </div>
</div>

<h3 class="page-header">Manage Workouts</h3>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-archon">
            <div class="panel-heading">
                <h3 class="panel-title">Add Workout</h3>
            </div>
            <div class="panel-body">
                <?php
                $form = $this->beginWidget('bootstrap.widgets.BsActiveForm', array(
                    'id' => 'workout-form',
                    // Please note: When you enable ajax validation, make sure the corresponding
                    // controller action is handling ajax validation correctly.
                    // There is a call to performAjaxValidation() commented in generated controller code.
                    // See class documentation of CActiveForm for details on this.
                    'enableAjaxValidation' => false
                ));
                ?>

                <?php echo $form->errorSummary($model); ?>

                <?php echo $form->dateFieldControlGroup($model, 'date', array('span' => 1, 'id' => 'dtpDate', 'class' => 'input-150')); ?>

                <?php echo $form->textFieldControlGroup($model, 'name', array('span' => 1, 'maxlength' => 45, 'id' => 'txtName')); ?>
                <div class="form-group">
                    <?php echo $form->label($model, 'workout_typeid'); ?>
                    <?php echo $form->dropDownList($model, 'workout_typeid', CHtml::listData(WorkoutType::model()->findAll(), 'id', 'name'), array('id' => 'dpWorkout')); //echo $form->textFieldControlGroup($model,'workout_typeid',array('span'=>5)); ?>
                </div>

                <?php echo $form->textFieldControlGroup($model, 'description', array('span' => 1, 'maxlength' => 150, 'id' => 'txtDescription', 'class' => 'input-300')); ?>
                <div class="col-full form-actions input-button">
                    <?php
                    if ($model->id == "") {
                        echo BsHtml::submitButton('Add WOD', array(
                            'color' => BsHtml::BUTTON_COLOR_PRIMARY,
                            'size' => BsHtml::BUTTON_SIZE_SMALL,
                            'submit' => 'create'
                        ));
                    }
                    ?>
                </div>
                <?php $this->endWidget(); ?>
            </div>
        </div>
    </div>
</div>

<?php if ($model->id != "") { ?>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-archon">
                <div class="panel-heading">
                    <h3 class="panel-title">Add Exercise</h3>
                </div>
                <div class="panel-body">
                    <?php
                    $form = $this->beginWidget('bootstrap.widgets.BsActiveForm', array(
                        'id' => 'workout-detail-form',
                        // 'layout' => TbHtml::FORM_LAYOUT_HORIZONTAL,
                        // Please note: When you enable ajax validation, make sure the corresponding
                        // controller action is handling ajax validation correctly.
                        // There is a call to performAjaxValidation() commented in generated controller code.
                        // See class documentation of CActiveForm for details on this.
                        'enableAjaxValidation' => false
                    ));

                    echo $form->errorSummary($modelDetail);
                    ?>

                    <div style="display: none;">
                        <?php echo $form->dropDownList($modelDetail, 'workoutid', CHtml::listData(Workout::model()->findAll(), 'id', 'name')); ?>
                    </div>

                    <div class="column">
                        <?php
                        if ($model->workout_typeid == 2) {
                            if (!Workout::model()->hasSons($model->id)) {
                                echo $form->label($modelDetail, 'total_time');
                                echo $form->textField($modelDetail, 'total_time', array('id' => 'time', 'class' => 'form-control'));
// 			$this->widget ( 'CMaskedTextField', array (
// 					'model' => $modelDetail,
// 					'attribute' => 'total_time',
// 					'mask' => '99:99',
// 					'htmlOptions' => array (
// 							'class' => 'form-control' 
// 					) 
// 			) );
                            } elseif ($modelDetail->id != "") {
                                $modelDetail->total_time = WorkoutDetail::model()->sonTotalTime($model->id);
                                echo $form->label($modelDetail, 'total_time');
                                echo $form->textField($modelDetail, 'total_time', array('id' => 'time', 'class' => 'form-control'));
// 			$this->widget ( 'CMaskedTextField', array (
// 					'model' => $modelDetail,
// 					'attribute' => 'total_time',
// 					'mask' => '99:99',
// 					'htmlOptions' => array (
// 							'class' => 'form-control' 
// 					)

// 			) );
                            } else {
                                $modelDetail->total_time = WorkoutDetail::model()->sonTotalTime($model->id);
                                echo $form->label($modelDetail, 'total_time', array(
                                    'style' => 'display:none;'
                                ));
                                echo $form->textField($modelDetail, 'total_time', array('id' => 'time', 'class' => 'form-control', 'style' => 'display:none;'));
// 			$this->widget ( 'CMaskedTextField', array (
// 					'model' => $modelDetail,
// 					'attribute' => 'total_time',
// 					'mask' => '99:99',
// 					'htmlOptions' => array (
// 							'class' => 'form-control',
//                              'style'=> 'display:none;'
// 					)

// 			) );
                            }
                        }

                        ?>
                        <?php echo $form->label($modelDetail, 'exerciseid'); ?>
                        <?php echo $form->dropDownList($modelDetail, 'exerciseid', CHtml::listData(Exercise::model()->findAll(array('order' => 'name')), 'id', 'name'), array('id' => 'dpExercise', 'prompt' => 'Select a Exercise')); ?>
                        <?php

                        ?>

                    </div>
                    <div class="column">
                        <?php

                        if ($model->workout_typeid == 1 || $model->workout_typeid == 3) {
                            if (!Workout::model()->hasSons($model->id)) {

                                echo $form->label($modelDetail, 'total_reps');
                                echo $form->numberField($modelDetail, 'total_reps', array(
                                    'lenght' => 11,
                                    'min' => 0,
                                    'onKeyPress' => 'return validateKeys(this, event,3);'
                                ));
                            } else {
                                $modelDetail->total_reps = WorkoutDetail::model()->sonTotalReps($model->id);
                                echo $form->label($modelDetail, 'total_reps');
                                echo $form->numberField($modelDetail, 'total_reps', array(
                                    'lenght' => 11,
                                    'min' => 0,
                                    'onKeyPress' => 'return validateKeys(this, event,3);'
                                ));
                            }
                        }

                        ?>


                    </div>
                    <div class="column">
                        <div style="font-weight: bold; font-size: 0.9em; display: block;">Measure</div>
                        <div class="column">
                            <?php echo $form->checkBoxControlGroup($modelDetail, 'measure_weight'); ?>

                        </div>
                        <div class="column">
                            <?php echo $form->checkBoxControlGroup($modelDetail, 'measure_height'); ?>
                        </div>
                        <div class="column">

                            <?php

                            echo $form->checkBoxControlGroup($modelDetail, 'measure_calories');
                            echo $form->hiddenField($modelDetail, 'workoutid', array(
                                'value' => $model->id
                            ))?>
                        </div>

                        <div class="column">
                            <?php echo $form->checkBoxControlGroup($modelDetail, 'measure_assist'); ?>
                        </div>


                        <div class="column">
                            <?php
                            if ($modelDetail->id == "" && $model->id != "") {
                                echo BsHtml::submitButton('Add Another Exercise', array(
                                    'color' => BsHtml::BUTTON_COLOR_PRIMARY,
                                    'size' => BsHtml::BUTTON_SIZE_SMALL,
                                    'submit' => '../WorkoutDetail/create'
                                ));
                            } elseif ($modelDetail->id != "") {
                                echo BsHtml::submitButton('Update Exercise', array(
                                    'color' => BsHtml::BUTTON_COLOR_PRIMARY,
                                    'size' => BsHtml::BUTTON_SIZE_SMALL,
                                    'submit' => array(
                                        'WorkoutDetail/update',
                                        'id' => $modelDetail->id
                                    )
                                ));
                            }

                            ?>
                        </div>

                    </div>
                    <div class="form-actions input-button">
                        <?php

                        if ($model->id != "") {
                            echo BsHtml::linkButton('Done Adding exercises', array(
                                'color' => BsHtml::BUTTON_COLOR_PRIMARY,
                                'size' => BsHtml::BUTTON_SIZE_SMALL,
                                'url' => array(
                                    'view',
                                    'id' => 0
                                )
                            ));
                        }
                        ?>
                    </div>

                    <?php $this->endWidget(); ?>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<div class="panel panel-archon">
    <div class="panel-heading">
        <h3 class="panel-title">Workout Selected: <?php echo $model->name; ?></h3>
    </div>
    <div class="panel-body">
        <table class="table table-striped">
            <thead>
            <tr>
                <!--  <th><?php //echo CHtml::encode($model->getAttributeLabel('id')); ?></th> -->
                <th><?php echo CHtml::encode($model->getAttributeLabel('date')); ?></th>
                <th><?php echo CHtml::encode($model->getAttributeLabel('name')); ?></th>
                <th><?php echo CHtml::encode($model->getAttributeLabel('description')); ?></th>
                <th><?php echo CHtml::encode($model->getAttributeLabel('workout_typeid')); ?></th>
                <?php if ($model->workout_typeid == 2) { ?>
                    <th>Total Time</th>
                <?php } ?>
            </tr>
            </thead>
            <tbody>
            <tr>
                <!--  	<td><?php //echo CHtml::encode($model->id); ?></td> -->
                <td><?php echo CHtml::encode($model->date); ?></td>
                <td><?php echo CHtml::encode($model->name); ?></td>
                <td><?php echo CHtml::encode($model->description); ?></td>
                <td><?php

                    if (!isset ($model->workoutType->name)) {
                        echo "";
                    } else {
                        echo CHtml::encode($model->workoutType->name);
                    }
                    ?></td>
                <?php

                if ($model->workout_typeid == 2) {
                    if (Workout::model()->hasSons($model->id)) {
                        ?>
                        <td> <?php echo CHtml::encode(WorkoutDetail::model()->sonTotalTime($model->id)) ?></td>
                    <?php
                    } else {
                        ?>
                        <td>00:00</td>
                    <?php
                    }
                }
                ?>
                <!--  <td><?php // echo  CHtml::link('<i class="glyphicon glyphicon-edit"style="margin-left:-10px;"></i>',array('Workout/update','id'=>$model->id))//TbHtml::link('',array('icon' => TbHtml::ICON_EDIT,'url'=>array('Workout/update','id'=>$model->id)));//TbHtml::icon(TbHtml::ICON_EDIT) ?> </td>-->

            </tr>
            <tr>
                <!--  <td></td> -->
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <!--  <td></td>-->

            </tr>
            </tbody>

        </table>
        <?php

        $this->widget('bootstrap.widgets.BsGridView', array(
            'id' => 'releasenote-grid',
            'selectableRows' => 0,
            'dataProvider' => WorkoutDetail::model()->search2($model->id),
            // 'selectionChanged' => 'js:userClicks',
            'columns' => array(
                /*array(
                        'name' => 'id',
                        'header' => '#',
                ),*/
                array(
                    'name' => 'exerciseid',
                    'value' => '$data->exercise->name',

                    'header' => 'Exercise'
                ),
                array(
                    'class' => 'CCheckBoxColumn',
                    'name' => 'measure_weight',

                    'checked' => '$data->measure_weight == 1',
                    'header' => 'Weight',
                    'disabled' => 'true'
                ),
                array(
                    'class' => 'CCheckBoxColumn',
                    'checked' => '$data->measure_height == 1',

                    'name' => 'measure_height',
                    'header' => 'Height',
                    'disabled' => 'true'
                ),

                array(
                    'class' => 'CCheckBoxColumn',
                    'name' => 'measure_calories',

                    'checked' => '$data->measure_calories == 1',
                    'header' => 'Calories',
                    'disabled' => 'true'
                ),
                array(
                    'class' => 'CCheckBoxColumn',
                    'checked' => '$data->measure_assist == 1',
                    'name' => 'measure_assist',

                    'header' => 'Assist',
                    'disabled' => 'true'
                ),
                array(
                    'name' => 'total_reps',
                    'header' => 'Reps',
                    'visible' => $model->workout_typeid == 1 || $model->workout_typeid == 3
                ),
                // array (
                // 'name' => 'total_time',
                // 'header' => 'Time',
                // 'visible' => $model->workout_typeid == 2
                // )
                // ,

                /*
                 * array ( 'name' => 'workoutid', 'header'=> 'WorkOut', 'value'=>' $data->workout->name',
                 */

                /*
                 * array('name' => 'workoutid', 'header'=> 'WorkOut')
                 */

                array(
                    'class' => 'CButtonColumn',
                    'template' => '{update}{delete}',

                    'buttons' => array(

                        'update' => array(
                            'label' => '',
                            'imageUrl' => '',

                            'url' => "CHtml::normalizeUrl(array('/WorkoutDetail/update', 'id'=>\$data->id))",
                            'options' => array(

                                'class' => 'glyphicon glyphicon-edit',
                                'id' => "updateGrid"
                            )
                        ),
                        'delete' => array(
                            'label' => '',
                            'imageUrl' => '',
                            'url' => "CHtml::normalizeUrl(array('/WorkoutDetail/delete', 'id'=>\$data->id))",
                            'options' => array(
                                'class' => 'glyphicon glyphicon-remove'
                            )
                        )
                    )
                )
            )
        ));
        ?>
    </div>
</div>







<?php
// echo BsHtml::linkButton ( 'Add Exercise(s)', array (
// 'color' => BsHtml::BUTTON_COLOR_PRIMARY,
// 'size' => BsHtml::BUTTON_SIZE_SMALL,
// 'url' => array (
// 'WorkoutDetail/create',
// 'id' => $model->id
// )
// ) );
?>


<?php
$this->widget('bootstrap.widgets.BsListView', array(
    'dataProvider' => $dataProvider,
    'itemView' => 'father'
));
?>

<?php


namespace humhub\modules\tasks\widgets;


use humhub\components\Widget;
use humhub\modules\tasks\models\Task;
use humhub\widgets\Button;

class ChangeStatusButton extends Widget
{
    /**
     * @var Task
     */
    public $task;

    /**
     * @inheritdoc
     */
    public function run()
    {
        $state = $this->task->state;
        $proceedConfig = $state->getProceedConfig();
        $revertConfig = $state->getRevertConfig();

        if(empty($proceedConfig) && empty($revertConfig)) {
            return '';
        }

        if($state->canProceed($state->getDefaultProceedState())) {
            $primaryState = $state->getDefaultProceedState();
            $primaryUrl = $primaryState->getProceedUrl();
            $primaryStateConfig = $proceedConfig[$primaryState->getStatusId()];
            unset($proceedConfig[$primaryState->getStatusId()]);
        } else if($state->canRevert($state->getDefaultRevertState())) {
            $primaryState = $state->getDefaultRevertState();
            $primaryUrl = $primaryState->getRevertUrl();
            $primaryStateConfig = $revertConfig[$primaryState->getStatusId()];
            unset($revertConfig[$primaryState->getStatusId()]);
        } else {
            return '';
        }

        return $this->render('changeStatusButton', [
            'task' => $this->task,
            'proceedConfig' => $proceedConfig,
            'revertConfig' => $revertConfig,
            'primaryUrl' => $primaryUrl,
            'primaryStateConfig' => $primaryStateConfig]);
    }

}
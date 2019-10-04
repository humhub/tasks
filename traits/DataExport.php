<?php

namespace humhub\modules\tasks\traits;

use humhub\modules\comment\models\Comment;
use humhub\modules\tasks\models\Task;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use yii\helpers\ArrayHelper;

trait DataExport
{
    /**
     * Getting the parrent content container name related to task
     *
     * @return \Closure
     */
    private function getRelatedContainer()
    {
        return function ($model) {
            /* @var $model Task */
            return $model->content->container->getDisplayName();
        };
    }

    /**
     * Getting the checklist separated by ", " related to task
     *
     * @param array $relationsData
     * @return \Closure
     */
    private function getTaskItems($relationsData)
    {
        return function($model, $key) use ($relationsData) {
            $items = ArrayHelper::map($relationsData[$key]['items'], 'id', 'title');
            return implode(', ', $items);
        };
    }

    /**
     * Getting the list of assigned users separated by ", " related to task
     *
     * @param array $relationsData
     * @return \Closure
     */
    private function getAssignedUsers($relationsData)
    {
        return function($model, $key) use ($relationsData) {
            $usersInfo = $this->getUsersInfo($key, $relationsData, Task::USER_ASSIGNED);
            $users = ArrayHelper::map($usersInfo, 'id', 'fullname');
            return implode(', ', $users);
        };
    }

    /**
     * Getting the list of responsible users separated by ", " related to task
     *
     * @param array $relationsData
     * @return \Closure
     */
    private function getResponsibleUsers($relationsData)
    {
        return function($model, $key) use ($relationsData) {
            $usersInfo = $this->getUsersInfo($key, $relationsData, Task::USER_RESPONSIBLE);
            $users = ArrayHelper::map($usersInfo, 'id', 'fullname');
            return implode(', ', $users);
        };
    }

    /**
     * Getting the list of comments separated by " | " related to task
     *
     * @return \Closure
     */
    private function getComments()
    {
        return function ($model, $key) {
            $commentsCount = Comment::GetCommentCount(get_class($model), $key);
            $comments = Comment::GetCommentsLimited(get_class($model), $key, $commentsCount);

            $messages = array_map(function($comment) {
                /* @var $comment Comment */
                return $comment->createdBy ? $comment->createdBy->getDisplayName().': '.$comment->message : $comment->message;
            }, $comments);

            return implode(' | ', $messages);
        };
    }

    private function prepareData($key, $initialData, $subKey = null)
    {
        $data = [];
        array_map(function ($item) use (&$data, $key, $subKey) {
            $data[$item[$key]] = $subKey ? $item[$subKey] : $item;
        }, $initialData);
        return $data;
    }

    private function getUsersInfo($key, $data, $userType)
    {
        $profiles = $this->prepareData('id', $data[$key]['users'], 'profile');
        $taskUsers = [];
        array_map(function ($user) use (&$taskUsers) {
            $uniqueKey = "{$user['user_id']}_{$user['user_type']}";
            $taskUsers[$uniqueKey] = $user;
        }, $data[$key]['taskUsers']);

        return $this->prepareUsersInfo($profiles, $taskUsers, $userType);
    }

    private function prepareUsersInfo($profiles, $taskUsers, $userType)
    {
        $info = [];
        array_map(function ($profile) use (&$info, $taskUsers, $userType) {
            $uniqueKey = "{$profile['user_id']}_{$userType}";
            if (key_exists($uniqueKey, $taskUsers)) {
                $info[] = [
                    'id' => $profile['user_id'],
                    'fullname' => "{$profile['firstname']} {$profile['lastname']}"
                ];
            }
        }, $profiles);
        return $info;
    }
}
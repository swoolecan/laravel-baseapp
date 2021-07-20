<?php
declare(strict_types = 1);

namespace Framework\Baseapp\Observers;

use Framework\Baseapp\Models\Interfaces\BaseModelEventsInterface;

class AbstractObserver
{
    /**
     * 监听数据即将创建的事件。
     *
     * @param  BaseModelEventsInterface $model
     * @return void
     */
    public function creating(BaseModelEventsInterface $model)
    {
        $model->onCreating();
    }

    /**
     * 监听数据创建后的事件。
     *
     * @param  BaseModelEventsInterface $model
     * @return void
     */
    public function created(BaseModelEventsInterface $model)
    {
        $model->onCreated();
    }

    /**
     * 监听数据即将更新的事件。
     *
     * @param  BaseModelEventsInterface $model
     * @return void
     */
    public function updating(BaseModelEventsInterface $model)
    {
        $model->onUpdating();
    }

    /**
     * 监听数据更新后的事件。
     *
     * @param  BaseModelEventsInterface $model
     * @return void
     */
    public function updated(BaseModelEventsInterface $model)
    {
        $model->onUpdated();
    }

    /**
     * 监听数据即将保存的事件。
     *
     * @param  BaseModelEventsInterface $model
     * @return void
     */
    public function saving(BaseModelEventsInterface $model)
    {
        $model->onSaving();
    }

    /**
     * 监听数据保存后的事件。
     *
     * @param  BaseModelEventsInterface $model
     * @return void
     */
    public function saved(BaseModelEventsInterface $model)
    {
        $model->onSaved();
    }

    /**
     * 监听数据即将删除的事件。
     *
     * @param  BaseModelEventsInterface $model
     * @return void
     */
    public function deleting(BaseModelEventsInterface $model)
    {
        $model->onDeleting();
    }

    /**
     * 监听数据删除后的事件。
     *
     * @param  BaseModelEventsInterface $model
     * @return void
     */
    public function deleted(BaseModelEventsInterface $model)
    {
        $model->onDeleted();
    }

    /**
     * 监听数据即将从软删除状态恢复的事件。
     *
     * @param  BaseModelEventsInterface $model
     * @return void
     */
    public function restoring(BaseModelEventsInterface $model)
    {
        $model->onRestoring();
    }

    /**
     * 监听数据从软删除状态恢复后的事件。
     *
     * @param  BaseModelEventsInterface $model
     * @return void
     */
    public function restored(BaseModelEventsInterface $model)
    {
        $model->onRestored();
    }
}

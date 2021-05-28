<?php

namespace Framework\Baseapp\Models\Observers;

use Framework\Baseapp\Models\Interfaces\BaseModelEventsInterface;

class AbstractObserver
{
    public function creating(BaseModelEventsInterface $model)
    {
        $model->onCreating();
    }

    /**
     * Listen to the Comment creating event.
     */
    public function creating(Comment $comment): void
    {
        $comment->posted_at = now();
    }

    public function created(BaseModelEventsInterface $model)
    {
        $model->onCreated();
    }

    public function updating(BaseModelEventsInterface $model)
    {
        $model->onUpdating();
    }

    public function updated(BaseModelEventsInterface $model)
    {
        $model->onUpdated();
    }

    public function saving(BaseModelEventsInterface $model)
    {
        $model->onSaving();
    }
    /**
     * Listen to the Post saving event.
     */
    public function saving(Post $post): void
    {
        $post->slug = Str::slug($post->title, '-');
    }

    public function saved(BaseModelEventsInterface $model)
    {
        $model->onSaved();
    }

    public function deleting(BaseModelEventsInterface $model)
    {
        $model->onDeleting();
    }

    public function deleted(BaseModelEventsInterface $model)
    {
        $model->onDeleted();
    }

    public function restoring(BaseModelEventsInterface $model)
    {
        $model->onRestoring();
    }

    public function restored(BaseModelEventsInterface $model)
    {
        $model->onRestored();
    }
}
use App\Models\Token;
use App\Models\User;
use App\Repositories\Enums\CacheEnum;
use App\Repositories\Models\User;
use Illuminate\Support\Facades\Cache;

class UserObserver
{
    /**
     * Listen to the User creating event.
     */
    public function creating(User $user): void
    {
        $user->registered_at = now();
        $user->api_token = Token::generate();
    }
{
    public function updated(User $user)
    {
        Cache::forget(CacheEnum::getCacheKey(CacheEnum::AUTHORIZATION_USER));
    }

    public function deleted(User $user)
    {
        Cache::forget(CacheEnum::getCacheKey(CacheEnum::AUTHORIZATION_USER));
    }
}

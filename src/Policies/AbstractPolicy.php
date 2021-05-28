<?php

namespace Framework\Baseapp\Policies;

use Framework\Baseapp\Models\BaseModel;
use Framework\Baseapp\Models\Foundation\BaseUser;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class Policy
 *
 * @category Framework\Baseapp
 * @package Framework\Baseapp\Policies
 * @license https://opensource.org/licenses/MIT MIT
 */
abstract class AbstractPolicy
{
    use HandlesAuthorization;
    public function viewAny(User $user)
    {
        if ($user->can(PermissionEnum::ROUTE_POSTS_VIEW_ANY()->name)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the post.
     *
     * @param  User|null  $user
     * @param  Post  $post
     * @return mixed
     */
    public function view(?User $user, Post $post)
    {
        if ($post->published) {
            return true;
        }

        // visitors cannot view unpublished items
        if ($user === null) {
            return false;
        }

        // admin overrides published status
        if ($user->can(PermissionEnum::ROUTE_POSTS_VIEW()->name)) {
            return true;
        }

        // authors can view their own unpublished posts
        return $user->isOwnerOf($post);
    }

    /**
     * Determine whether the user can create posts.
     *
     * @param  User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        if ($user->can(PermissionEnum::ROUTE_POSTS_CREATE()->name)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the post.
     *
     * @param  User  $user
     * @param  Post  $post
     * @return mixed
     */
    public function delete(User $user, Post $post)
    {
        if ($user->isOwnerOf($post) || $user->can(PermissionEnum::ROUTE_POSTS_DELETE()->name)) {
            return true;
        }

        return false;
        return $user->isAdmin();
    }
    /**
     * Get The owner
     *
     * @param BaseUser $user user
     * @param BaseModel $model model
     *
     * @return bool
     */
    public function owner(BaseUser $user, BaseModel $model)
    {
        return $model->getUserId() == $user->getUserId();
    }

    public function before(User $user)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the user.
     */
    public function update(User $current_user, User $user): bool
    {
        return $current_user->id === $user->id;
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can generate a personnal access token.
     */
    public function api_token(User $current_user, User $user): bool
    {
        return $current_user->id === $user->id;
    }

    /**
     * Determine whether the user can store a medium.
     */
    public function store(User $user): bool
    {
        return $user->isAdmin();
    }
}

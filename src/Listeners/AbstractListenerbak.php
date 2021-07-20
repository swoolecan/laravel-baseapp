<?php

namespace Framework\Baseapp\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\ExampleEvent;

abstract class AbstractListenerbak
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param \App\Events\ExampleEvent $event
     */
    public function handle(ExampleEvent $event)
    {
    }
}
namespace Prettus\Repository\Listeners;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Contracts\RepositoryInterface;
use Prettus\Repository\Events\RepositoryEventBase;
use Prettus\Repository\Helpers\CacheKeys;

/**
 * Class CleanCacheRepository
 * @package Prettus\Repository\Listeners
 */
class CleanCacheRepositorybak
{

    /**
     * @var CacheRepository
     */
    protected $cache = null;

    /**
     * @var RepositoryInterface
     */
    protected $repository = null;

    /**
     * @var Model
     */
    protected $model = null;

    /**
     * @var string
     */
    protected $action = null;

    /**
     *
     */
    public function __construct()
    {
        $this->cache = app(config('repository.cache.repository', 'cache'));
    }

    /**
     * @param RepositoryEventBase $event
     */
    public function handle(RepositoryEventBase $event)
    {
        try {
            $cleanEnabled = config("repository.cache.clean.enabled", true);

            if ($cleanEnabled) {
                $this->repository = $event->getRepository();
                $this->model = $event->getModel();
                $this->action = $event->getAction();

                if (config("repository.cache.clean.on.{$this->action}", true)) {
                    $cacheKeys = CacheKeys::getKeys(get_class($this->repository));

                    if (is_array($cacheKeys)) {
                        foreach ($cacheKeys as $key) {
                            $this->cache->forget($key);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}

/**
 * Created by PhpStorm.
 * User: sheldon
 * Date: 18-5-3
 * Time: 下午6:18.
 */

namespace Yeelight\Listeners\Auth;

use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\Events\RefreshTokenCreated;

/**
 * Class PruneOldTokens
 *
 * @category Yeelight
 *
 * @package Yeelight\Listeners\Auth
 *
 * @author Sheldon Lee <xdlee110@gmail.com>
 *
 * @license https://opensource.org/licenses/MIT MIT
 *
 * @link https://www.yeelight.com
 */
class PruneOldTokensbak
{
    use SerializesModels;

    /**
     * Handle the event.
     *
     * @param RefreshTokenCreated $event RefreshTokenCreated
     *
     * @return void
     */
    public function handle(RefreshTokenCreated $event)
    {
        DB::table('oauth_refresh_tokens')
            ->where('access_token_id', '!=', $event->accessTokenId)
            ->where('revoked', true)->delete();
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}

/**
 * Created by PhpStorm.
 * User: sheldon
 * Date: 18-5-3
 * Time: 下午6:14.
 */

namespace Yeelight\Listeners\Auth;

use Carbon\Carbon;
use Illuminate\Queue\SerializesModels;
use Laravel\Passport\Events\AccessTokenCreated;
use Laravel\Passport\Token;

/**
 * Class RevokeOldTokens
 *
 * @category Yeelight
 *
 * @package Yeelight\Listeners\Auth
 *
 * @author Sheldon Lee <xdlee110@gmail.com>
 *
 * @license https://opensource.org/licenses/MIT MIT
 *
 * @link https://www.yeelight.com
 */
class RevokeOldTokensbak
{
    use SerializesModels;

    /**
     * Handle the event.
     *
     * @param AccessTokenCreated $event AccessTokenCreated
     *
     * @return void
     */
    public function handle(AccessTokenCreated $event)
    {
        Token::where('id', '!=', $event->tokenId)
            ->where('user_id', $event->userId)
            ->where('client_id', $event->clientId)
            ->where('expires_at', '<', Carbon::now())
            ->orWhere('revoked', true)
            ->delete();
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}

namespace Yeelight\Listeners\User;

use Yeelight\Events\User\UserLoggedInEvent;
use Yeelight\Events\User\UserRegisteredEvent;

/**
 * Class UserEventListener
 *
 * @category Yeelight
 *
 * @package Yeelight\Listeners\User
 *
 * @author Sheldon Lee <xdlee110@gmail.com>
 *
 * @license https://opensource.org/licenses/MIT MIT
 *
 * @link https://www.yeelight.com
 */
class UserEventListenerbak
{
    public function __construct()
    {
    }

    /**
     * Handle user login events.
     *
     * @param UserLoggedInEvent $event UserLoggedInEvent
     */
    public function onUserLogin(UserLoggedInEvent $event)
    {
    }

    /**
     * Handle user registered events.
     *
     * @param UserRegisteredEvent $event UserRegisteredEvent
     */
    public function onUserRegistered(UserRegisteredEvent $event)
    {
    }

    /**
     * Handle user logout events.
     */
    public function onUserLogout($event)
    {
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param \Illuminate\Events\Dispatcher $events Dispatcher
     */
    public function subscribe($events)
    {
        $events->listen(
            UserLoggedInEvent::class,
            'Yeelight\Listeners\User\UserEventListener@onUserLogin'
        );

        $events->listen(
            UserRegisteredEvent::class,
            'Yeelight\Listeners\User\UserEventListener@onUserRegistered'
        );

        /*$events->listen(
            'Yeelight\Events\UserLoggedOut',
            'Yeelight\Listeners\UserEventListener@onUserLogout'
        );*/
    }
}

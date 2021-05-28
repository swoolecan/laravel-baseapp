<?php

namespace Framework\Baseapp\Helpers;

/**
 * Class Controller
 *
 * @category Framework\Baseapp
 * @package Framework\Baseapp\Helpers
 * @license https://opensource.org/licenses/MIT MIT
 */
class Operation
{
    public function models()
    {
            'user' => $user,
            'posts_count' => $user->posts()->count(),
            'comments_count' => $user->comments()->count(),
            'likes_count' => $user->likes()->count(),
            'posts' => $user->posts()->withCount('likes', 'comments')->latest()->limit(5)->get(),
            'comments' => $user->comments()->with('post.author')->latest()->limit(5)->get()
            'comments' => Comment::with('post', 'author')->latest()->paginate(50)
            'users' => User::pluck('name', 'id')
            'media' => MediaLibrary::first()->media()->get()
            'posts' => Post::withCount('comments', 'likes')->with('author')->latest()->paginate(50)
            'users' => User::authors()->pluck('name', 'id'),
            'media' => MediaLibrary::first()->media()->get()->pluck('name', 'id')
            'users' => User::authors()->pluck('name', 'id'),
            'media' => MediaLibrary::first()->media()->get()->pluck('name', 'id')
            'comments' =>  Comment::lastWeek()->get(),
            'posts' => Post::lastWeek()->get(),
            'users' => User::lastWeek()->get(),
            'posts' => Post::search($request->input('q'))
                             ->with('author', 'likes')
                             ->withCount('comments', 'thumbnail', 'likes')
                             ->latest()
                             ->paginate(20)
        $post->comments_count = $post->comments()->count();
        $post->likes_count = $post->likes()->count();
    }
    public function unsubscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:newsletter_subscriptions,email'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $route = 'login';

            if (Auth::check()) {
                $route = 'home';
            }

            return redirect()->route($route)->withErrors($errors);
        }

        UnsubscribeEmailNewsletter::dispatch($request->input('email'));

        Session::flash('success', __('newsletter.unsubscribed'));

        return view('newsletters.unsubscribed');
    }
    public function authenticate(Request $request)
    {
        if (Auth::attempt(['email' => $request->input('email'), 'password' => $request->input('password')])) {
            $user = User::where('email', $request->input('email'))->first();

            return (new UserResource($user))
                    ->additional(['meta' => [
                        'access_token' => $user->api_token
                    ]]);
        }

        return response()->json(['message' => 'This action is unauthorized.'], 401);
    }
    public function index()
    {
//use Illuminate\Support\Facades\Http;
        $popularTv = Http::withToken(config('services.tmdb.token'))
            ->get('https://api.themoviedb.org/3/tv/popular')
            ->json()['results'];


        $viewModel = new TvViewModel(
            $popularTv,
            $topRatedTv,
            $genres,
        );

        return view('tv.index', $viewModel);
    }

    public function controllers()
    {
        $comment->update($request->validated());
        $comment->delete();
        return redirect()->route('admin.comments.edit', $comment)->withSuccess(__('comments.updated'));

        $image = $request->file('image');
        $name = $image->getClientOriginalName();

        MediaLibrary::first()
            ->addMedia($image)
            ->usingName($name)
            ->toMediaCollection();

        $post = Post::create($request->only(['title', 'content', 'posted_at', 'author_id', 'thumbnail_id']));

        return redirect()->route('admin.posts.edit', $post)->withSuccess(__('posts.created'));
        $post->update($request->only(['title', 'content', 'posted_at', 'author_id', 'thumbnail_id']));

        return redirect()->route('admin.posts.edit', $post)->withSuccess(__('posts.updated'));

        if ($request->filled('password')) {
            $request->merge([
            'users' => User::latest()->paginate(50)
            'roles' => Role::all()
//use Illuminate\Support\Facades\Hash;
                'password' => Hash::make($request->input('password'))
            ]);
        }

        $user->update(array_filter($request->only(['name', 'email', 'password'])));

        $role_ids = array_values($request->get('roles', []));
        $user->roles()->sync($role_ids);

        return redirect()->route('admin.users.edit', $user)->withSuccess(__('users.updated'));
//use Illuminate\Support\Facades\Cache;
        $posts = Cache::remember('feed-posts', now()->addHour(), fn () => Post::latest()->limit(20)->get());
    }
}

use App\Models\Token;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserTokenController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(): View
    {
        $user = auth()->user();

        $this->authorize('api_token', $user);

        return view('users.token', ['user' => $user]);
    }

    /**
     * Generate a personnal access token for the specified resource in storage.
     */
    public function update(): RedirectResponse
    {
        $user = auth()->user();

        $this->authorize('api_token', $user);

        $user->update([
            'api_token' => Token::generate()
        ]);

        return redirect()->route('users.token')->withSuccess(__('tokens.updated'));
    }

    public function index(Request $request, User $user): ResourceCollection
    {
        return PostResource::collection(
            $user->posts()->latest()->paginate($request->input('limit', 20))
        );
    }
    public function store(CommentsRequest $request, Post $post): CommentResource
    {
        $comment = new CommentResource(
            Auth::user()->comments()->create([
                'post_id' => $post->id,
                'content' => $request->input('content')
            ])
        );

        broadcast(new CommentPosted($comment, $post))->toOthers();

        return $comment;
    }
}


<?php

namespace Yeelight\Http\Controllers\Api\Controllers;

use Dingo\Api\Exception\DeleteResourceFailedException;
use Yeelight\Http\Requests\Api\ProductModelCreateRequest;
use Yeelight\Http\Requests\Api\ProductModelUpdateRequest;
use Yeelight\Repositories\Interfaces\ProductModelRepository;
use Yeelight\Validators\ProductModelValidator;

/**
 * Class ProductModelsController
 *
 * @category Yeelight
 *
 * @package Yeelight\Http\Controllers\Api\Controllers
 *
 * @author Sheldon Lee <xdlee110@gmail.com>
 *
 * @license https://opensource.org/licenses/MIT MIT
 *
 * @link https://www.yeelight.com
 */
class ProductModelsController extends BaseController
{
    /**
     * ProductModelRepository
     *
     * @var ProductModelRepository
     */
    protected $repository;

    /**
     * ProductModelValidator
     *
     * @var ProductModelValidator
     */
    protected $validator;

    /**
     * ProductModelsController constructor.
     *
     * @param ProductModelRepository $repository ProductModelRepository
     * @param ProductModelValidator $validator ProductModelValidator
     */
    public function __construct(
        ProductModelRepository $repository,
        ProductModelValidator $validator
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->repository->all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ProductModelCreateRequest $request ProductModelCreateRequest
     *
     * @return \Illuminate\Http\Response
     */
    public function store(ProductModelCreateRequest $request)
    {
        $data = $request->all();

        $productModel = $this->repository->create($data);

        // throw exception if store failed
        // throw new StoreResourceFailedException('Failed to store.');

        // A. return 201 created
        // return $this->response->created(null);

        // B. return data
        return $this->response->created();
    }

    /**
     * Display the specified resource.
     *
     * @param int $id id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ProductModelUpdateRequest $request ProductModelUpdateRequest
     * @param string $id id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(ProductModelUpdateRequest $request, $id)
    {
        $data = $request->all();

        $productModel = $this->repository->update($data, $id);

        // throw exception if update failed
        // throw new UpdateResourceFailedException('Failed to update.');

        // Updated, return 204 No Content
        return $this->response->noContent();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deleted = $this->repository->delete($id);

        if ($deleted) {
            // Deleted, return 204 No Content
            return $this->response->noContent();
        } else {
            // Failed, throw exception
            throw new DeleteResourceFailedException('Failed to delete.');
        }
    }
}


<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UsersRequest;
use App\Http\Resources\User as UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Return the users.
     */
    public function index(Request $request): ResourceCollection
    {
        return UserResource::collection(
            User::withCount(['comments', 'posts'])->with('roles')->latest()->paginate($request->input('limit', 20))
        );
    }

    /**
     * Return the specified resource.
     */
    public function show(User $user): UserResource
    {
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UsersRequest $request, User $user): UserResource
    {
        $this->authorize('update', $user);

        if ($request->filled('password')) {
            $request->merge([
                'password' => Hash::make($request->input('password'))
            ]);
        }

        $user->update(array_filter($request->only(['name', 'email', 'password'])));

        return new UserResource($user);
    }
}

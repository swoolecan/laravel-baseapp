<?php

namespace Framework\Baseapp\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

abstract class AbstractJob implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
    }

    public function handle()
    {
    }

    /*protected $email;

    public function __construct($email)
    {
        $this->email = $email;
    }

    public function handle(): void
    {
        $email = $this->email;

        $newsletterSubscription = NewsletterSubscription::where('email', $email)->first();
        if ($newsletterSubscription) {
            $newsletterSubscription->delete();
        }
    }
    public function handle(): void
    {
        $posts = Post::lastMonth()->get();
        $email = $this->email;

        Mail::to($this->email)->send(new Newsletter($posts, $email));
    }
    public function handle(): void
    {
        $newsletterSubscriptions = NewsletterSubscription::all();

        $newsletterSubscriptions->each(fn ($newsletterSubscription) => SendNewsletterSubscriptionEmail::dispatch($newsletterSubscription->email));

        PrepareNewsletterSubscriptionEmail::dispatch()->delay(carbon('next month'));
    }*/
}

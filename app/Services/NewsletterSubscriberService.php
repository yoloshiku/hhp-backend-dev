<?php

namespace App\Services;

use App\Models\NewsletterSubscriber;
use App\Repositories\Interfaces\NewsletterSubscriberRepositoryInterface;
use App\Services\Interfaces\NewsletterSubscriberServiceInterface;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminNotificationNewsletterSubscriberMail;
use App\Mail\NewsletterSubscriberMail;

/**
 * Class NewsletterSubscriberService
 *
 * Handles business logic related to newsletter_subscribers.
 */
class NewsletterSubscriberService implements NewsletterSubscriberServiceInterface
{
    /**
     * @var NewsletterSubscriberRepositoryInterface
     */    
    protected $newsletterSubscriberRepository;

    /**
     * NewsletterSubscriberService constructor.
     *
     * @param NewsletterSubscriberRepositoryInterface $newsletterSubscriberRepository
     */    
    public function __construct(NewsletterSubscriberRepositoryInterface $newsletterSubscriberRepository)
    {
        $this->newsletterSubscriberRepository = $newsletterSubscriberRepository;
    }
    
    /**
     * Store a new Newsletter Subscriber,
     * and send a message to the email
     * specified in .env . and the email
     * gotten from the subscriber's form
     *
     * @param array $data
     * @return NewsletterSubscriber
     */
    public function store(array $data): NewsletterSubscriber
    {
        $newsletterSubscriber = $this->newsletterSubscriberRepository->create($data);
        
        Mail::to(config('mail.admin_newsletter_subscriber_address')) // or admin email
            ->send(new AdminNotificationNewsletterSubscriberMail($data));
        
        Mail::to($data['email'])
            ->send(new NewsletterSubscriberMail($data));
        
        return $newsletterSubscriber;
    }
}
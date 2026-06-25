<?php

namespace App\Repositories;

use App\Models\NewsletterSubscriber;
use App\Repositories\Interfaces\NewsletterSubscriberRepositoryInterface;

/**
 * Class NewsletterSubscriberRepository
 *
 * Handles database operations related to the NewsletterSubscriber model.
 * This class implements the NewsletterSubscriberRepositoryInterface contract
 * and is responsible for persisting newsletter_subscribers submitted
 * through the application's newsletter subscriber form.
 */
class NewsletterSubscriberRepository implements NewsletterSubscriberRepositoryInterface
{
    /**
     * Create a new newsletter_subscribers record in the database.
     *
     * This method receives validated newsletter_subscribers form data and
     * persists it using the NewsletterSubscriber Eloquent model.
     *
     * Example input:
     * [
     *     'email' => 'john@example.com',
     *     'country' => 'Afghanistan',
     * ]
     *
     * @param array $data Validated newsletter_subscribers form data
     * @return NewsletterSubscriber The newly created NewsletterSubscriber model instance
     */    
    public function create(array $data): NewsletterSubscriber
    {
        return NewsletterSubscriber::create($data);
    }
}
<?php

namespace App\Repositories\Interfaces;

use App\Models\NewsletterSubscriber;

/**
 * Interface NewsletterSubscriberRepositoryInterface
 *
 * Defines the contract for newsletter_subscribers data persistence operations.
 * 
 * This repository is responsible for interacting with the
 * NewsletterSubscriber model and handling database-related actions.
 */
interface NewsletterSubscriberRepositoryInterface
{
    /**
     * Create and store a new newsletter_subscribers record.
     *
     * Typically used when a user submits the Newsletter Subscriber form.
     *
     * Example payload:
     * [
     *     'email' => 'john@example.com',
     *     'country' => 'Afghanistan',
     * ]
     *
     * @param array $data Validated Newsletter Subscriber form data
     * @return NewsletterSubscriber The newly created NewsletterSubscriber model instance
     */    
    public function create(array $data): NewsletterSubscriber;
}
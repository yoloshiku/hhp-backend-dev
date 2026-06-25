<?php

namespace App\Services\Interfaces;

use App\Models\NewsletterSubscriber;

/**
 * Interface NewsletterSubscriberServiceInterface
 *
 * Defines the contract for business logic related to newsletter_subscribers
 * 
 * This service layer sits between the controller and the repository
 * and is responsible for handling application logic when a user
 * submits a Newsletter Subscribers form.
 */
interface NewsletterSubscriberServiceInterface
{
    /**
     * Store a new newsletter subscriber.
     *
     * This method receives validated data from the controller,
     * applies any necessary business logic, and delegates
     * persistence to the repository layer.
     *
     * Example input:
     * [
     *     'email' => 'john@example.com',
     *     'country' => 'Afghanistan',
     * ]
     *
     * @param array $data Validated Newsletter Subscriber form data
     * @return NewsletterSubscriber The newly created NewsletterSubscriber model instance
     */    
    public function store(array $data): NewsletterSubscriber;
}
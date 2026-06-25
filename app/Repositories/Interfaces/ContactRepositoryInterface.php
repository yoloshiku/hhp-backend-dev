<?php

namespace App\Repositories\Interfaces;

use App\Models\Contact;

/**
 * Interface ContactRepositoryInterface
 *
 * Defines the contract for contact data persistence operations.
 * 
 * This repository is responsible for interacting with the
 * Contact model and handling database-related actions.
 */
interface ContactRepositoryInterface
{
    /**
     * Create and store a new contact record.
     *
     * Typically used when a user submits the contact form.
     *
     * Example payload:
     * [
     *     'name' => 'John Doe',
     *     'email' => 'john@example.com',
     *     'message' => 'Hello, I would like more information.'
     * ]
     *
     * @param array $data Validated contact form data
     * @return Contact The newly created Contact model instance
     */    
    public function create(array $data): Contact;
}
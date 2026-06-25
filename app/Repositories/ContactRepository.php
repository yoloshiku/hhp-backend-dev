<?php

namespace App\Repositories;

use App\Models\Contact;
use App\Repositories\Interfaces\ContactRepositoryInterface;

/**
 * Class ContactRepository
 *
 * Handles database operations related to the Contact model.
 * This class implements the ContactRepositoryInterface contract
 * and is responsible for persisting contact messages submitted
 * through the application's contact form.
 */
class ContactRepository implements ContactRepositoryInterface
{
    /**
     * Create a new contact record in the database.
     *
     * This method receives validated contact form data and
     * persists it using the Contact Eloquent model.
     *
     * Example input:
     * [
     *     'name' => 'John Doe',
     *     'email' => 'john@example.com',
     *     'message' => 'I would like more information.'
     * ]
     *
     * @param array $data Validated contact form data
     * @return Contact The newly created Contact model instance
     */    
    public function create(array $data): Contact
    {
        return Contact::create($data);
    }
}
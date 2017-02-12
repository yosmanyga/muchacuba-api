<?php

namespace Muchacuba\Chuchuchu;

use MongoDB\BSON\Persistable;

class Profile implements Persistable
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string[]
     */
    private $contacts;

    /**
     * @param string   $id
     * @param string[] $contacts
     */
    public function __construct($id, array $contacts)
    {
        $this->id = $id;
        $this->contacts = $contacts;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string[]
     */
    public function getContacts()
    {
        return $this->contacts;
    }

    /**
     * {@inheritdoc}
     */
    public function bsonSerialize()
    {
        return [
            '_id' => $this->id,
            'contacts' => $this->contacts,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function bsonUnserialize(array $data)
    {
        $this->id = $data['_id'];
        $this->contacts = $data['contacts'];
    }
}

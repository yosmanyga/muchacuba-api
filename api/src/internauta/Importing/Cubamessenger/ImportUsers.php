<?php

namespace Muchacuba\Internauta\Importing\Cubamessenger;

use Muchacuba\Internauta\Importing\CreateUser;

/**
 * @di\service()
 */
class ImportUsers
{
    /**
     * @var string
     */
    private $host;

    /**
     * @var string
     */
    private $db;

    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $pass;

    /**
     * @var CreateUser
     */
    private $createUser;

    /**
     * @param string $host
     * @param string $db
     * @param string $user
     * @param string $pass
     * @param CreateUser $createUser
     *
     * @di\arguments({
     *     host: '%mysql_host%',
     *     db:   '%mysql_db%',
     *     user: '%mysql_user%',
     *     pass: '%mysql_pass%'
     * })
     */
    public function __construct($host, $db, $user, $pass, CreateUser $createUser)
    {
        $this->host = $host;
        $this->db = $db;
        $this->user = $user;
        $this->pass = $pass;
        $this->createUser = $createUser;
    }

    /**
     * @return int
     */
    public function import()
    {
        //$max = 300;
        $c = 0;
        $offset = 0;
        $limit = 100;
        while ($rows = $this->connect()
            ->query(
                sprintf(
                    "
                        SELECT email, numero 
                        FROM usuarios_cuba
                        WHERE email LIKE '%%.cu'
                        LIMIT %s OFFSET %s
                    ",
                    $limit,
                    $offset
                )
            )
        ) {
            if (isset($max) && $c > $max) {
                break;
            }

            foreach($rows as $row) {
                try {
                    $this->insert(
                        $row['email'],
                        $row['numero']
                    );

                    $c++;
                } catch (ExistentEmailException $e) {
                    continue;
                }
            }

            $offset += $limit;
        }

        return $c;
    }

    /**
     * @return \PDO
     */
    private function connect()
    {
        return new \PDO(
            sprintf('mysql:host=%s;dbname=%s;charset=utf8', $this->host, $this->db),
            $this->user,
            $this->pass
        );
    }

    /**
     * @param string $email
     * @param string $mobile
     *
     * @throws ExistentEmailException
     */
    private function insert($email, $mobile)
    {
        try {
            $this->createUser->create($email, $mobile);
        } catch (ExistentEmailException $e) {
            throw $e;
        }
    }
}
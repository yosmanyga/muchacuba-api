<?php

namespace Cubalider\Facebook;

use Cubalider\Facebook\Profile\ManageStorage;
use MongoDB\Driver\Exception\BulkWriteException;

/**
 * @author Yosmany Garcia <yosmanyga@gmail.com>
 *
 * @di\service({deductible: true})
 */
class CreateProfile
{
    /**
     * @var ManageStorage
     */
    private $manageStorage;

    /**
     * @param ManageStorage $manageStorage
     */
    public function __construct(
        ManageStorage $manageStorage
    ) {
        $this->manageStorage = $manageStorage;
    }

    /**
     * Creates a profile.
     *
     * @param string $uniqueness
     * @param string $id
     * @param string $name
     * @param string $email
     * @param string $picture The url
     *
     * @throws ExistentProfileException
     *
     * @return string
     */
    public function create($uniqueness, $id, $name, $email, $picture)
    {
//        // Does firebase need pnd format?
//        try {
//            ob_start();
//            imagepng(imagecreatefromstring(file_get_contents($picture)));
//            $picture = base64_encode(ob_get_clean());
//        }
//            // Problem downloading the image?
//        catch (\Exception $e) {
//            $picture = null;
//        }

//        // Save raw data image
//        try {
//            $picture = base64_encode(file_get_contents($picture));
//        }
//        // Problem downloading the image?
//        catch (\Exception $e) {
//            $picture = null;
//        }

        try {
            $this->manageStorage->connect()->insertOne(
                new Profile($uniqueness, $id, $name, $email, $picture)
            );
        } catch (BulkWriteException $e) {
            if ($e->getWriteResult()->getWriteErrors()[0]->getCode() == 11000) {
                throw new ExistentProfileException();
            }

            throw $e;
        }

        return $uniqueness;
    }
}

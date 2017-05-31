<?php

namespace Muchacuba\Internauta\Test;

use Behat\Behat\Context\Context as BaseContext;
use Behat\Gherkin\Node\PyStringNode;
use Muchacuba\Internauta\Importing\InsertUser;
use Muchacuba\Internauta\Importing\User\ManageStorage;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symsonte\Behat\ContainerAwareContext;
use Symsonte\Service\Container;

class Context implements BaseContext, ContainerAwareContext
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @BeforeScenario
     */
    public function purgeStorage()
    {
        /** @var ManageStorage $manageStorage */
        $manageStorage = $this->container->get('muchacuba.internauta.importing.user.manage_storage');
        $manageStorage->purge();
    }

    /**
     * @Given there are these users:
     *
     * @param PyStringNode $string
     */
    public function thereAreTheseUsers(PyStringNode $string)
    {
        $users = json_decode($string->getRaw(), true);

        /** @var InsertUser $insertUser */
        $insertUser = $this->container->get('muchacuba.internauta.importing.insert_user');

        foreach ($users as $user) {
            $insertUser->insert(
                $user['email'],
                $user['mobile'],
                $user['id']
            );
        }
    }
}

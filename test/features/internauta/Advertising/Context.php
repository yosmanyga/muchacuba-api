<?php

namespace Muchacuba\Internauta\Advertising;

use Behat\Behat\Context\Context as BaseContext;
use Behat\Gherkin\Node\PyStringNode;
use Coduo\PHPMatcher\Factory\SimpleFactory;
use Muchacuba\Internauta\Advertising\Email\ManageStorage as EmailManageStorage;
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
     * @var Profile[]
     */
    private $profiles;

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
        /** @var EmailManageStorage $manageStorage */
        $manageStorage = $this->container->get('muchacuba.internauta.advertising.email.manage_storage');
        $manageStorage->purge();
    }

    /**
     * @Given there are these emails in advertising:
     *
     * @param PyStringNode $string
     */
    public function thereAreTheseEmails(PyStringNode $string)
    {
        $emails = json_decode($string->getRaw(), true);

        /** @var InsertEmail $insertEmail */
        $insertEmail = $this->container->get('muchacuba.internauta.advertising.insert_email');

        foreach ($emails as $email) {
            $insertEmail->insert(
                $email['subject'],
                $email['body'],
                $email['id']
            );
        }
    }

    /**
     * @Given there are these profiles in advertising:
     *
     * @param PyStringNode $string
     */
    public function thereAreTheseProfiles(PyStringNode $string)
    {
        $profiles = json_decode($string->getRaw(), true);

        /** @var InsertProfile $insertProfile */
        $insertProfile = $this->container->get('muchacuba.internauta.advertising.insert_profile');

        foreach ($profiles as $profile) {
            if (!empty($profile['advertisements'])) {
                foreach ($profile['advertisements'] as $i => $advertisement) {
                    $profile['advertisements'][$i] = new Advertisement($advertisement['email'], time());
                }
            }

            $insertProfile->insert(
                $profile['user'],
                $profile['email'],
                $profile['advertisements']
            );
        }
    }

    /**
     * @When I resolve profiles in advertising:
     *
     * @param PyStringNode $string
     */
    public function iResolveProfiles(PyStringNode $string)
    {
        $parameters = json_decode($string->getRaw(), true);

        /** @var ResolveProfiles $resolveProfiles */
        $resolveProfiles = $this->container->get('muchacuba.internauta.advertising.resolve_profiles');

        $this->profiles = $resolveProfiles->resolve(
            $parameters['email'],
            $parameters['amount']
        );
    }

    /**
     * @Then I should get these profiles:
     *
     * @param PyStringNode $string
     *
     * @throws \Exception
     */
    public function iShouldGetThisResult(PyStringNode $string)
    {
        $matcher = (new SimpleFactory())->createMatcher();

        if (!$matcher->match(
            json_decode(json_encode($this->profiles), true),
            json_decode($string->getRaw(), true)
        )
        ) {
            throw new \Exception($matcher->getError());
        }
    }
}

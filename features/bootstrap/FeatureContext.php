<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Symfony\Bundle\FrameworkBundle\Client;

class FeatureContext implements Context, SnippetAcceptingContext
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $client->setServerParameter('PHP_AUTH_USER', 'admin');
        $client->setServerParameter('PHP_AUTH_PW', 'admin');

        $this->client = $client;
    }

    /**
     * @When I get my profile
     */
    public function iGetMyProfile()
    {
        $this->client->request('GET', '/profile');
    }

    /**
     * @Then I should see my complete profile as JSON
     */
    public function iShouldSeeMyCompleteProfileAsJSON()
    {
        $expected = <<<JSON
{
    "roles": ["ROLE_ALLOWED_TO_SWITCH"],
    "password": "admin",
    "salt": null,
    "username": "admin",
    "accountNonExpired": true,
    "accountNonLocked": true,
    "credentialsNonExpired": true,
    "enabled": true
}
JSON;
        PHPUnit_Framework_Assert::assertJsonStringEqualsJsonString($expected, $this->client->getResponse()->getContent());
    }

    /**
     * @When I impersonate somebody's profile
     */
    public function iImpersonateSomebodysProfile()
    {
        $this->client->request('GET', '/profile', [], [], ['HTTP_X-Switch-User' => 'john.doe']);
    }

    /**
     * @Then I should see somebody's complete profile as JSON
     */
    public function iShouldSeeSomebodysCompleteProfileAsJSON()
    {
        $expected = <<<JSON
{
    "roles": ["ROLE_USER"],
    "password": "john.doe",
    "salt": null,
    "username": "john.doe",
    "accountNonExpired": true,
    "accountNonLocked": true,
    "credentialsNonExpired": true,
    "enabled": true
}
JSON;
        PHPUnit_Framework_Assert::assertJsonStringEqualsJsonString($expected, $this->client->getResponse()->getContent());
    }
}

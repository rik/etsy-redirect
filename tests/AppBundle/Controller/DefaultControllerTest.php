<?php

namespace Tests\AppBundle\Controller;

use Tests\AppBundle\FunctionalTestCase;

class DefaultControllerTest extends FunctionalTestCase
{
    private $em;

    protected function setUp()
    {
        self::bootKernel();
        parent::setUp();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->em->close();
        $this->em = null; // avoid memory leaks
    }

    private function getMinifiedUrl($client, $url_to_minify)
    {
        $crawler = $client->request('GET', '/');
        $form = $crawler->selectButton('Shorten')->form();
        $client->submit($form, array('form[url]' => $url_to_minify));
        $crawler = $client->followRedirect();

        return $crawler->filter('input')->attr('value');
    }

    public function testIndex()
    {
        $url_to_minify = 'http://exemple.org';
        $client = static::createClient();

        $redirect_url = $this->getMinifiedUrl($client, $url_to_minify);

        $crawler = $client->request('GET', $redirect_url);
        $this->assertTrue(
            $client->getResponse()->isRedirect($url_to_minify)
        );
    }

    public function testUrlMinifySameUuid()
    {
        $url_to_minify = 'http://exemple.org';
        $client = static::createClient();

        $first_redirect_url = $this->getMinifiedUrl($client, $url_to_minify);
        $second_redirect_url = $this->getMinifiedUrl($client, $url_to_minify);

        $this->assertEquals($first_redirect_url, $second_redirect_url);
    }

    public function testIncrementTotalViews() {
        $url_to_minify = 'http://exemple.org';
        $client = static::createClient();

        $redirect_url = $this->getMinifiedUrl($client, $url_to_minify);

        $repository = $this->em->getRepository('AppBundle:Redirect');
        $views_before = $repository->findOneByUrl($url_to_minify)->getTotalViews();

        $crawler = $client->request('GET', $redirect_url);

        // Clear to force Doctrine to do a SQL query
        $this->em->clear();

        $views_after = $repository->findOneByUrl($url_to_minify)->getTotalViews();

        $this->assertEquals($views_before + 1, $views_after);
    }
}

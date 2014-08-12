<?php

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Raspberry\Bundle\MonitorBundle\Entity\Site;

class LoadSiteData implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $siteGoogle = new Site();

        $siteGoogle->setName('Google.com');
        $siteGoogle->setIcon('fa fa-google');
        $siteGoogle->setUrl('https://www.google.com');
        $manager->persist($siteGoogle);

        $siteFacebook = new Site();
        $siteFacebook->setName('Facebook.com');
        $siteGoogle->setIcon('fa fa-facebook');
        $siteFacebook->setUrl('https://www.facebook.com');
        $manager->persist($siteFacebook);

        $manager->flush();
    }
}

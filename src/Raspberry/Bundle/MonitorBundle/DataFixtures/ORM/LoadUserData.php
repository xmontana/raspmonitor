<?php

use Doctrine\Common\DataFixtures\AbstractFixture;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData extends AbstractFixture  implements  ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $userManager = $this->container->get('fos_user.user_manager');

        $userAdmin = $userManager->createUser();
        $userAdmin->setUsername('admin');
        $userAdmin->setPlainPassword('raspadmin');
        $userAdmin->setEmail('root@localhost')
            ->setRoles(array('ROLE_SUPER_ADMIN'))
            ->setEnabled(true);

        $manager->persist($userAdmin);

        $manager->flush();
    }
}

<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{

    public function __construct(UserPasswordHasherInterface $password_hasher)
    {
        $this->password_hasher = $password_hasher;
    }

    public function load(ObjectManager $manager)
    {
        foreach ($this->getUserData() as [
            $first_name, $last_name, $email, $password, $roles
        ]) {
            $user = new User();
            $user->setFirstName($first_name);
            $user->setLastName($last_name);
            $user->setEmail($email);
            $user->setPassword($this->password_hasher->hashPassword($user, $password));

            $user->setRoles($roles);

            $manager->persist($user);
        }

        $manager->flush();
    }


    private function getUserData(): array
    {
        return [

            ['Abdi', 'Ahmed', 'aa@gm.com', '111111',  ['ROLE_ADMIN']],
            ['Mark', 'Wayne', 'mw@gm.com', '111111', ['ROLE_USER']],
            ['John', 'Doe', 'jd@gm.com', '111111',  ['ROLE_USER']],
            ['Anne', 'Doe', 'ad@gm.com', '111111', ['ROLE_USER']],
            ['Abdi', 'Ahmed', 'enatfikkir@yahoo.com', '111111', ['ROLE_ADMIN']],
            ['Ab', 'Ah', 'abdulkadirahmed1985@gmail.com', '111111', ['ROLE_ADMIN']]

        ];
    }
}

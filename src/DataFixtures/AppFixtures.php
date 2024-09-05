<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\School;
use App\Entity\Training;
use App\Entity\Module;

class AppFixtures extends Fixture
{
    private array $createdSchools = [];
    private array $createdModules = [];
    private array $createdTrainings = [];

    public function setCreatedSchool(School $createdSchool): void
    {
        $this->createdSchools[] = $createdSchool;
    }

    public function setCreatedModule(Module $createdModule): void
    {
        $this->createdModules[] = $createdModule;
    }

    public function setCreatedTraining(Training $createdTraining): void
    {
        $this->createdTrainings[] = $createdTraining;
    }

    public function getCreatedSchools(): array
    {
        return $this->createdSchools;
    }

    public function getCreatedModules(): array
    {
        return $this->createdModules;
    }

    public function getCreatedTrainings(): array
    {
        return $this->createdTrainings;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Créer 3 écoles
        $schools = [];
        for ($i = 0; $i < 3; $i++) 
        {
            $school = new School();
            $school->setName($faker->company());
            $school->setDescription($faker->catchPhrase());

            $manager->persist($school);
            $schools[] = $school;  // Sauvegarder chaque école dans un tableau

            $this->setCreatedSchool($school);
        }

        // Créer 6 modules
        $modules = [];
        for ($i = 0; $i < 6; $i++) 
        {
            $module = new Module();
            $module->setName($faker->word());
            $module->setDescription($faker->sentence());

            $manager->persist($module);
            $modules[] = $module;  // Sauvegarder chaque module dans un tableau

            $this->setCreatedModule($module);
        }

        // Créer 6 formations et les associer aux écoles et aux modules
        for ($i = 0; $i < 6; $i++) 
        {
            $training = new Training();
            $training->setName($faker->jobTitle());
            $training->setDescription($faker->sentence());

            // Associer aléatoirement chaque formation à une école
            $randomSchool = $schools[array_rand($schools)];
            $training->setSchool($randomSchool);

            // Associer aléatoirement 3 modules à chaque formation
            $randomModules = (array)array_rand($modules, 3);
            foreach ($randomModules as $moduleIndex) 
            {
                $training->addModule($modules[$moduleIndex]);
            }

            $manager->persist($training);

            $this->setCreatedTraining($training);
        }

        // Flush pour sauvegarder toutes les entités en base de données
        $manager->flush();
    }

   
}
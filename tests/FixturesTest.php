<?php 

namespace App\Tests;

use App\DataFixtures\AppFixtures;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Entity\Module;
use App\Entity\School;
use App\Entity\Training;

class FixturesTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private AppFixtures $fixture;

    protected function setUp(): void
    {
        self::bootKernel(); 
        $this->entityManager = self::$kernel->getContainer()->get('doctrine')->getManager();
        $this->fixture = new AppFixtures();
        $this->fixture->load($this->entityManager);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        foreach ($this->fixture->getCreatedTrainings() as $training)
        {
            $managedTraining = $this->entityManager->find(Training::class, $training->getId());

            if ($managedTraining) 
            {
                $this->entityManager->remove($managedTraining);
            }
        }

        foreach ($this->fixture->getCreatedModules() as $module) 
        {
            $managedModule = $this->entityManager->find(Module::class, $module->getId());
            
            if ($managedModule) 
            {
                $this->entityManager->remove($managedModule);
            }
        }

        foreach ($this->fixture->getCreatedSchools() as $school)
        {
            $managedSchool = $this->entityManager->find(School::class, $school->getId());

            if ($managedSchool) 
            {
                $this->entityManager->remove($managedSchool);
            }
        }

        $this->entityManager->flush();
    }

    public function testTrainingHasThreeModules(): void
    {
        $trainingRepository = $this->entityManager->getRepository(Training::class);
        $trainings = $trainingRepository->findAll();

        foreach ($trainings as $training) 
        {
            $this->assertCount(3, $training->getModules());
        }
    }
}
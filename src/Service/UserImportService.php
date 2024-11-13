<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Site;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;

class UserImportService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function importFromCsv(string $csvFilePath): array
    {
        $csv = Reader::createFromPath($csvFilePath, 'r');
        $csv->setHeaderOffset(0); // S'assurer que la première ligne est l'entête
        $records = $csv->getRecords();

        $results = ['success' => false, 'message' => ''];

        try {
            foreach ($records as $record) {
                $user = new User();
                $user->setEmail($record['email']);
                $user->setUsername($record['username']);
                $user->setFirstname($record['firstname']);
                $user->setSurname($record['surname']);
                $user->setRoles(["ROLE_USER"]);
                $user->setPhonenumber($record['phonenumber']);
                $user->setAvatarurl($record['avatarurl']);
                $user->setVerified((bool)$record['is_verified']);

                $siteId = $record['site_id'];
                $site = $this->entityManager->getRepository(Site::class)->find($siteId);

                if ($site) {
                    $user->setSite($site);
                } else {
                    throw new \Exception("Site with ID $siteId not found.");
                }

                $hashedPassword = password_hash($record['password'], PASSWORD_BCRYPT);
                $user->setPassword($hashedPassword);

                $this->entityManager->persist($user);
            }

            $this->entityManager->flush();

            $results['success'] = true;
            $results['message'] = 'Users imported successfully.';
        } catch (\Exception $e) {
            $results['success'] = false;
            $results['message'] = 'Error: ' . $e->getMessage();
        }

        return $results;
    }
}


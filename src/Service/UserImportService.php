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
        // Chargement et lecture du fichier CSV
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
                $user->setPhonenumber($record['phonenumber']);
                $user->setAvatarurl($record['avatarurl']);
                $user->setVerified((bool)$record['is_verified']);

                // Récupérer l'entité Site à partir de l'ID
                $siteId = $record['site_id'];
                $site = $this->entityManager->getRepository(Site::class)->find($siteId);

                if ($site) {
                    $user->setSite($site);
                } else {
                    throw new \Exception("Site with ID $siteId not found.");
                }

                // Hashage du mot de passe
                $hashedPassword = password_hash($record['password'], PASSWORD_BCRYPT);
                $user->setPassword($hashedPassword);

                // Persister l'utilisateur
                $this->entityManager->persist($user);
            }

            // Sauvegarder tous les utilisateurs en base de données
            $this->entityManager->flush();

            $results['success'] = true;
            $results['message'] = 'Users imported successfully.';
        } catch (\Exception $e) {
            $results['success'] = false;
            $results['message'] = 'Error: ' . $e->getMessage();
        }

        return $results;  // Retourner les résultats de l'importation
    }
}


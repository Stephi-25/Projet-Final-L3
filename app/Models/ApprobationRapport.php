<?php

namespace App\Models;

use App\Database\BaseModel;
use App\Models\PersonnelAdministratif;
use App\Models\RapportEtudiant;
use PDO;

/**
 * Class ApprobationRapport
 *
 * Represents the approbation_rapport table.
 * Composite PK: (utilisateur_id, rapport_etudiant_id).
 *
 * @package App\Models
 */
class ApprobationRapport extends BaseModel
{
    /**
     * @var string The database table name.
     */
    protected string $table = 'approbation_rapport';

    /**
     * @var string The ID of the approving user (FK to personnel_administratif.utilisateur_id, part of CPK).
     */
    public string $utilisateur_id;

    /**
     * @var string The ID of the student report (FK to rapport_etudiant.id, part of CPK).
     */
    public string $rapport_etudiant_id;

    /**
     * @var string|null The date of approval.
     */
    public ?string $date_approbation; // DDL specifies DATE

    /**
     * ApprobationRapport constructor.
     *
     * @param PDO $pdo The PDO database connection object.
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
    }

    // Getters and Setters

    public function getUtilisateurId(): string
    {
        return $this->utilisateur_id;
    }

    public function setUtilisateurId(string $utilisateur_id): void
    {
        $this->utilisateur_id = $utilisateur_id;
    }

    public function getRapportEtudiantId(): string
    {
        return $this->rapport_etudiant_id;
    }

    public function setRapportEtudiantId(string $rapport_etudiant_id): void
    {
        $this->rapport_etudiant_id = $rapport_etudiant_id;
    }

    public function getDateApprobation(): ?string
    {
        return $this->date_approbation;
    }

    public function setDateApprobation(?string $date_approbation): void
    {
        $this->date_approbation = $date_approbation;
    }

    // Relationship methods

    /**
     * Gets the related PersonnelAdministratif who approved.
     * @return PersonnelAdministratif|null
     */
    public function getPersonnelAdministratif(): ?PersonnelAdministratif
    {
        if (empty($this->utilisateur_id)) {
            return null;
        }
        $stmt = $this->pdo->prepare("SELECT * FROM personnel_administratif WHERE id_utilisateur = :id");
        $stmt->bindParam(':id', $this->utilisateur_id);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }
        $personnel = new PersonnelAdministratif($this->pdo);
        $personnel->id_utilisateur = $data['id_utilisateur'];
        // PersonnelAdministratif is lean, only id_utilisateur
        return $personnel;
    }

    /**
     * Gets the related RapportEtudiant.
     * @return RapportEtudiant|null
     */
    public function getRapportEtudiant(): ?RapportEtudiant
    {
        if (empty($this->rapport_etudiant_id)) {
            return null;
        }
        $stmt = $this->pdo->prepare("SELECT * FROM rapport_etudiant WHERE id = :id");
        $stmt->bindParam(':id', $this->rapport_etudiant_id);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }
        $rapport = new RapportEtudiant($this->pdo);
        $rapport->id = $data['id'];
        $rapport->titre = $data['titre'] ?? null;
        $rapport->date_rapport = $data['date_rapport'] ?? null;
        $rapport->theme_memoire = $data['theme_memoire'] ?? null;
        return $rapport;
    }
}

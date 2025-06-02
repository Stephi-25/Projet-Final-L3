<?php

namespace App\Models;

use App\Database\BaseModel;
use App\Models\Enseignant;
use App\Models\RapportEtudiant;
use PDO;

/**
 * Class ValidationRapport
 *
 * Represents the validation_rapport table.
 * Composite PK: (utilisateur_id, rapport_etudiant_id).
 *
 * @package App\Models
 */
class ValidationRapport extends BaseModel
{
    /**
     * @var string The database table name.
     */
    protected string $table = 'validation_rapport';

    /**
     * @var string The ID of the validating user (FK to enseignant.utilisateur_id, part of CPK).
     */
    public string $utilisateur_id;

    /**
     * @var string The ID of the student report (FK to rapport_etudiant.id, part of CPK).
     */
    public string $rapport_etudiant_id;

    /**
     * @var string|null The date of validation.
     */
    public ?string $date_validation; // DDL specifies DATE

    /**
     * @var string|null Comments regarding the validation.
     */
    public ?string $commentaire;     // DDL specifies VARCHAR(255)

    /**
     * ValidationRapport constructor.
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

    public function getDateValidation(): ?string
    {
        return $this->date_validation;
    }

    public function setDateValidation(?string $date_validation): void
    {
        $this->date_validation = $date_validation;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): void
    {
        $this->commentaire = $commentaire;
    }

    // Relationship methods

    /**
     * Gets the related Enseignant who validated.
     * @return Enseignant|null
     */
    public function getEnseignant(): ?Enseignant
    {
        if (empty($this->utilisateur_id)) {
            return null;
        }
        $stmt = $this->pdo->prepare("SELECT * FROM enseignant WHERE id_utilisateur = :id");
        $stmt->bindParam(':id', $this->utilisateur_id);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }
        $enseignant = new Enseignant($this->pdo);
        $enseignant->id_utilisateur = $data['id_utilisateur']; // Enseignant is lean
        return $enseignant;
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

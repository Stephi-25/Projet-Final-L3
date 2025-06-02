<?php

namespace App\Models;

use App\Database\BaseModel;
use App\Models\Enseignant;
use App\Models\RapportEtudiant;
use App\Models\StatutJury;
use PDO;

/**
 * Class AffectationEncadrant
 *
 * Represents the affectation_encadrant table.
 * Composite PK: (utilisateur_id, rapport_etudiant_id, statut_jury_id).
 *
 * @package App\Models
 */
class AffectationEncadrant extends BaseModel
{
    /**
     * @var string The database table name.
     */
    protected string $table = 'affectation_encadrant';

    /**
     * @var string The ID of the enseignant (FK to enseignant.utilisateur_id, part of CPK).
     */
    public string $utilisateur_id;

    /**
     * @var string The ID of the student report (FK to rapport_etudiant.id, part of CPK).
     */
    public string $rapport_etudiant_id;

    /**
     * @var string The ID of the statut_jury (FK to statut_jury.id, part of CPK).
     */
    public string $statut_jury_id;

    /**
     * @var string|null The date of assignment.
     */
    public ?string $date_affectation; // DDL specifies DATE

    /**
     * AffectationEncadrant constructor.
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

    public function getStatutJuryId(): string
    {
        return $this->statut_jury_id;
    }

    public function setStatutJuryId(string $statut_jury_id): void
    {
        $this->statut_jury_id = $statut_jury_id;
    }

    public function getDateAffectation(): ?string
    {
        return $this->date_affectation;
    }

    public function setDateAffectation(?string $date_affectation): void
    {
        $this->date_affectation = $date_affectation;
    }

    // Relationship methods

    /**
     * Gets the related Enseignant.
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

    /**
     * Gets the related StatutJury.
     * @return StatutJury|null
     */
    public function getStatutJury(): ?StatutJury
    {
        if (empty($this->statut_jury_id)) {
            return null;
        }
        $stmt = $this->pdo->prepare("SELECT * FROM statut_jury WHERE id = :id");
        $stmt->bindParam(':id', $this->statut_jury_id);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }
        $statut = new StatutJury($this->pdo);
        $statut->id = $data['id'];
        $statut->libelle = $data['libelle'] ?? null;
        return $statut;
    }
}

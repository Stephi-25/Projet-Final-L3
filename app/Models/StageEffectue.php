<?php

namespace App\Models;

use App\Database\BaseModel;
use App\Models\Etudiant;
use App\Models\Entreprise;
// AnneeAcademique and Enseignant are no longer directly related from this model
use PDO;

/**
 * Class StageEffectue
 *
 * Represents the stage_effectue table (internships).
 * Composite PK: (utilisateur_id, entreprise_id).
 *
 * @package App\Models
 */
class StageEffectue extends BaseModel
{
    /**
     * @var string The database table name.
     */
    protected string $table = 'stage_effectue';

    /**
     * @var string|null The ID of the student (FK to etudiant.utilisateur_id, part of composite PK).
     */
    public ?string $utilisateur_id;

    /**
     * @var string|null The ID of the company (FK to entreprise.id, part of composite PK).
     */
    public ?string $entreprise_id;

    /**
     * @var string|null The start date of the internship.
     */
    public ?string $date_debut;

    /**
     * @var string|null The end date of the internship.
     */
    public ?string $date_fin;

    // Removed sujet_stage, id_annee_academique, id_enseignant_encadrant as per strict DDL

    /**
     * StageEffectue constructor.
     *
     * @param PDO $pdo The PDO database connection object.
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
    }

    // Getters and Setters for corrected properties
    public function getUtilisateurId(): ?string
    {
        return $this->utilisateur_id;
    }

    public function setUtilisateurId(?string $utilisateur_id): void
    {
        $this->utilisateur_id = $utilisateur_id;
    }

    public function getEntrepriseId(): ?string
    {
        return $this->entreprise_id;
    }

    public function setEntrepriseId(?string $entreprise_id): void
    {
        $this->entreprise_id = $entreprise_id;
    }

    public function getDateDebut(): ?string
    {
        return $this->date_debut;
    }

    public function setDateDebut(?string $date_debut): void
    {
        $this->date_debut = $date_debut;
    }

    public function getDateFin(): ?string
    {
        return $this->date_fin;
    }

    public function setDateFin(?string $date_fin): void
    {
        $this->date_fin = $date_fin;
    }

    // Relationship methods
    /**
     * Gets the related Etudiant.
     * @return Etudiant|null
     */
    public function getEtudiant(): ?Etudiant
    {
        if (empty($this->utilisateur_id)) {
            return null;
        }
        // FK in stage_effectue is utilisateur_id, which refers to etudiant.utilisateur_id (PK of Etudiant)
        $stmt = $this->pdo->prepare("SELECT * FROM etudiant WHERE id_utilisateur = :utilisateur_id");
        $stmt->bindParam(':utilisateur_id', $this->utilisateur_id);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }
        $etudiant = new Etudiant($this->pdo);
        $etudiant->id_utilisateur = $data['id_utilisateur']; // Etudiant's PK
        $etudiant->ine = $data['ine'];
        $etudiant->nom = $data['nom'];
        $etudiant->prenom = $data['prenom'];
        $etudiant->date_naissance = $data['date_naissance'] ?? null;
        $etudiant->lieu_naissance = $data['lieu_naissance'] ?? null;
        $etudiant->contact = $data['contact'] ?? null;
        return $etudiant;
    }

    /**
     * Gets the related Entreprise.
     * @return Entreprise|null
     */
    public function getEntreprise(): ?Entreprise
    {
        if (empty($this->entreprise_id)) {
            return null;
        }
        $stmt = $this->pdo->prepare("SELECT * FROM entreprise WHERE id = :entreprise_id");
        $stmt->bindParam(':entreprise_id', $this->entreprise_id);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }
        $entreprise = new Entreprise($this->pdo);
        $entreprise->id = $data['id'];
        $entreprise->nom_entreprise = $data['nom_entreprise'];
        $entreprise->adresse_entreprise = $data['adresse_entreprise'] ?? null;
        $entreprise->telephone_entreprise = $data['telephone_entreprise'] ?? null;
        $entreprise->email_entreprise = $data['email_entreprise'] ?? null;
        $entreprise->secteur_activite = $data['secteur_activite'] ?? null;
        return $entreprise;
    }
}

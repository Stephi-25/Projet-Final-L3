<?php

namespace App\Models;

use App\Database\BaseModel;
use App\Models\Etudiant;
use App\Models\RapportEtudiant;
use PDO;

/**
 * Class DepotRapport
 *
 * Represents the depot_rapport table (report submissions).
 * Composite PK: (utilisateur_id, rapport_etudiant_id).
 *
 * @package App\Models
 */
class DepotRapport extends BaseModel
{
    /**
     * @var string The database table name.
     */
    protected string $table = 'depot_rapport';

    /**
     * @var string The ID of the student who submitted (FK to etudiant.utilisateur_id, part of CPK).
     */
    public string $utilisateur_id;
    
    /**
     * @var string The ID of the student report (FK to rapport_etudiant.id, part of CPK).
     */
    public string $rapport_etudiant_id;

    /**
     * @var string|null The date of submission.
     */
    public ?string $date_depot; // DDL specifies DATE

    /**
     * DepotRapport constructor.
     *
     * @param PDO $pdo The PDO database connection object.
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
    }

    // Getters and Setters for composite PK fields and other attributes

    /**
     * Gets the ID of the student (utilisateur_id).
     * @return string
     */
    public function getUtilisateurId(): string
    {
        return $this->utilisateur_id;
    }

    /**
     * Sets the ID of the student (utilisateur_id).
     * @param string $utilisateur_id
     */
    public function setUtilisateurId(string $utilisateur_id): void
    {
        $this->utilisateur_id = $utilisateur_id;
    }

    /**
     * Gets the ID of the student report.
     * @return string
     */
    public function getRapportEtudiantId(): string
    {
        return $this->rapport_etudiant_id;
    }

    /**
     * Sets the ID of the student report.
     * @param string $rapport_etudiant_id
     */
    public function setRapportEtudiantId(string $rapport_etudiant_id): void
    {
        $this->rapport_etudiant_id = $rapport_etudiant_id;
    }
    
    /**
     * Gets the date of submission.
     * @return string|null
     */
    public function getDateDepot(): ?string
    {
        return $this->date_depot;
    }

    /**
     * Sets the date of submission.
     * @param string|null $date_depot
     */
    public function setDateDepot(?string $date_depot): void
    {
        $this->date_depot = $date_depot;
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
        $stmt = $this->pdo->prepare("SELECT * FROM etudiant WHERE id_utilisateur = :utilisateur_id");
        $stmt->bindParam(':utilisateur_id', $this->utilisateur_id);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }
        $etudiant = new Etudiant($this->pdo);
        $etudiant->id_utilisateur = $data['id_utilisateur'];
        $etudiant->utili = $data['ine'];
        $etudiant->nom = $data['nom'];
        $etudiant->prenom = $data['prenom'];
        // Populate other Etudiant properties as defined in Etudiant model
        return $etudiant;
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
        $rapport->id = $data['id']; // rapport_etudiant.id is INT
        $rapport->titre = $data['titre'] ?? null;
        $rapport->date_rapport = $data['date_rapport'] ?? null;
        $rapport->theme_memoire = $data['theme_memoire'] ?? null;
        return $rapport;
    }
}

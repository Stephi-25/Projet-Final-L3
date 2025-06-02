<?php

namespace App\Models;

use App\Database\BaseModel;
use App\Models\Enseignant;
use App\Models\Fonction;
use PDO;

/**
 * Class HistoriqueFonction
 *
 * Represents the historique_fonction table.
 * Composite PK: (utilisateur_id, fonction_id, date_occupation).
 *
 * @package App\Models
 */
class HistoriqueFonction extends BaseModel
{
    /**
     * @var string The database table name.
     */
    protected string $table = 'historique_fonction';

    /**
     * @var string The ID of the enseignant (FK to enseignant.utilisateur_id, part of CPK).
     */
    public string $utilisateur_id;

    /**
     * @var string The ID of the function (FK to fonction.id, part of CPK).
     */
    public string $fonction_id;

    /**
     * @var string The date of occupation (part of CPK).
     */
    public string $date_occupation; // DDL specifies DATE

    /**
     * HistoriqueFonction constructor.
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

    public function getFonctionId(): string
    {
        return $this->fonction_id;
    }

    public function setFonctionId(string $fonction_id): void
    {
        $this->fonction_id = $fonction_id;
    }

    public function getDateOccupation(): string
    {
        return $this->date_occupation;
    }

    public function setDateOccupation(string $date_occupation): void
    {
        $this->date_occupation = $date_occupation;
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
     * Gets the related Fonction.
     * @return Fonction|null
     */
    public function getFonction(): ?Fonction
    {
        if (empty($this->fonction_id)) {
            return null;
        }
        $stmt = $this->pdo->prepare("SELECT * FROM fonction WHERE id = :id");
        $stmt->bindParam(':id', $this->fonction_id);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }
        $fonction = new Fonction($this->pdo);
        $fonction->id = $data['id'];
        $fonction->libelle = $data['libelle'] ?? null;
        // description is not in DDL for fonction but was in model, remove if not in DDL
        // $fonction->description = $data['description'] ?? null; 
        return $fonction;
    }
}

<?php

namespace App\Models;

use App\Database\BaseModel;
use App\Models\Enseignant;
use App\Models\CompteRendu;
use PDO;

/**
 * Class RemiseCompteRendu
 *
 * Represents the remise_compte_rendu table.
 * Composite PK: (utilisateur_id, compte_rendu_id).
 *
 * @package App\Models
 */
class RemiseCompteRendu extends BaseModel
{
    /**
     * @var string The database table name.
     */
    protected string $table = 'remise_compte_rendu';

    /**
     * @var string The ID of the enseignant (FK to enseignant.utilisateur_id, part of CPK).
     */
    public string $utilisateur_id;

    /**
     * @var string The ID of the compte rendu (FK to compte_rendu.id, part of CPK).
     */
    public string $compte_rendu_id;

    /**
     * @var string|null The date of submission.
     */
    public ?string $date_rendu; // DDL specifies DATE

    /**
     * RemiseCompteRendu constructor.
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

    public function getCompteRenduId(): string
    {
        return $this->compte_rendu_id;
    }

    public function setCompteRenduId(string $compte_rendu_id): void
    {
        $this->compte_rendu_id = $compte_rendu_id;
    }

    public function getDateRendu(): ?string
    {
        return $this->date_rendu;
    }

    public function setDateRendu(?string $date_rendu): void
    {
        $this->date_rendu = $date_rendu;
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
     * Gets the related CompteRendu.
     * @return CompteRendu|null
     */
    public function getCompteRendu(): ?CompteRendu
    {
        if (empty($this->compte_rendu_id)) {
            return null;
        }
        $stmt = $this->pdo->prepare("SELECT * FROM compte_rendu WHERE id = :id");
        $stmt->bindParam(':id', $this->compte_rendu_id);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }
        $compteRendu = new CompteRendu($this->pdo);
        $compteRendu->id = $data['id'];
        $compteRendu->titre = $data['titre'] ?? null;
        $compteRendu->contenu = $data['contenu'] ?? null;
        $compteRendu->date_creation = $data['date_creation'] ?? null;
        $compteRendu->id_etudiant = $data['id_etudiant'] ?? null; // Assuming CompteRendu has id_etudiant FK
        return $compteRendu;
    }
}

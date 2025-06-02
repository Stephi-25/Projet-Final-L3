<?php

namespace App\Models;

use App\Database\BaseModel;
use App\Models\NiveauApprobation;
use App\Models\CompteRendu;
use PDO;

/**
 * Class HistoriqueApprobation
 *
 * Represents the historique_approbation table.
 * Composite PK: (niveau_approbation_id, compte_rendu_id, date_approbation).
 *
 * @package App\Models
 */
class HistoriqueApprobation extends BaseModel
{
    /**
     * @var string The database table name.
     */
    protected string $table = 'historique_approbation';

    /**
     * @var string The ID of the niveau_approbation (FK, part of CPK).
     */
    public string $niveau_approbation_id;

    /**
     * @var string The ID of the compte_rendu (FK, part of CPK).
     */
    public string $compte_rendu_id;

    /**
     * @var string The date of approval (part of CPK).
     */
    public string $date_approbation; // DDL specifies DATE

    /**
     * HistoriqueApprobation constructor.
     *
     * @param PDO $pdo The PDO database connection object.
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
    }

    // Getters and Setters

    public function getNiveauApprobationId(): string
    {
        return $this->niveau_approbation_id;
    }

    public function setNiveauApprobationId(string $niveau_approbation_id): void
    {
        $this->niveau_approbation_id = $niveau_approbation_id;
    }

    public function getCompteRenduId(): string
    {
        return $this->compte_rendu_id;
    }

    public function setCompteRenduId(string $compte_rendu_id): void
    {
        $this->compte_rendu_id = $compte_rendu_id;
    }

    public function getDateApprobation(): string
    {
        return $this->date_approbation;
    }

    public function setDateApprobation(string $date_approbation): void
    {
        $this->date_approbation = $date_approbation;
    }

    // Relationship methods

    /**
     * Gets the related NiveauApprobation.
     * @return NiveauApprobation|null
     */
    public function getNiveauApprobation(): ?NiveauApprobation
    {
        if (empty($this->niveau_approbation_id)) {
            return null;
        }
        $stmt = $this->pdo->prepare("SELECT * FROM niveau_approbation WHERE id = :id");
        $stmt->bindParam(':id', $this->niveau_approbation_id);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }
        $niveau = new NiveauApprobation($this->pdo);
        $niveau->id = $data['id'];
        $niveau->libelle = $data['libelle'] ?? null;
        return $niveau;
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
        // Corrected: DDL for compte_rendu is id, titre, date_rapport
        $compteRendu->date_rapport = $data['date_rapport'] ?? null; 
        // Removed ->contenu and ->id_etudiant as per strict DDL for compte_rendu
        return $compteRendu;
    }
}

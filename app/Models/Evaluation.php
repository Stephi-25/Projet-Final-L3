<?php

namespace App\Models;

use App\Database\BaseModel;
use App\Models\Enseignant;
use App\Models\Etudiant;
use App\Models\Ecue;
use PDO;

/**
 * Class Evaluation
 *
 * Represents the evaluation table.
 * Composite PK: (enseignant_id, etudiant_id, ecue_id).
 *
 * @package App\Models
 */
class Evaluation extends BaseModel
{
    /**
     * @var string The database table name.
     */
    protected string $table = 'evaluation';

    // Properties representing the composite primary key and other fields
    /**
     * @var string The ID of the teacher (FK to enseignant.utilisateur_id, part of CPK).
     */
    public string $enseignant_id;

    /**
     * @var string The ID of the student (FK to etudiant.utilisateur_id, part of CPK).
     */
    public string $etudiant_id;

    /**
     * @var string The ID of the ECUE (FK to ecue.id, part of CPK).
     */
    public string $ecue_id;

    /**
     * @var float|null The student's grade/score. Note: DDL says TINYINT UNSIGNED, using float for flexibility.
     */
    public ?float $note; // DECIMAL(4,2) maps to float or string

    /**
     * @var string|null The date of the evaluation.
     */
    public ?string $date_evaluation; // Assuming DATE SQL type

    /**
     * Evaluation constructor.
     *
     * @param PDO $pdo The PDO database connection object.
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
    }

    // Getters and Setters for composite PK fields and other attributes

    /**
     * Gets the ID of the teacher.
     * @return string
     */
    public function getEnseignantId(): string
    {
        return $this->enseignant_id;
    }

    /**
     * Sets the ID of the teacher.
     * @param string $enseignant_id
     */
    public function setEnseignantId(string $enseignant_id): void
    {
        $this->enseignant_id = $enseignant_id;
    }

    /**
     * Gets the ID of the student.
     * @return string
     */
    public function getEtudiantId(): string
    {
        return $this->etudiant_id;
    }

    /**
     * Sets the ID of the student.
     * @param string $etudiant_id
     */
    public function setEtudiantId(string $etudiant_id): void
    {
        $this->etudiant_id = $etudiant_id;
    }

    /**
     * Gets the ID of the ECUE.
     * @return string
     */
    public function getEcueId(): string
    {
        return $this->ecue_id;
    }

    /**
     * Sets the ID of the ECUE.
     * @param string $ecue_id
     */
    public function setEcueId(string $ecue_id): void
    {
        $this->ecue_id = $ecue_id;
    }
    
    /**
     * Gets the student's grade/score.
     * @return float|null
     */
    public function getNote(): ?float
    {
        return $this->note;
    }

    /**
     * Sets the student's grade/score.
     * @param float|null $note
     */
    public function setNote(?float $note): void
    {
        $this->note = $note;
    }

    /**
     * Gets the date of the evaluation.
     * @return string|null
     */
    public function getDateEvaluation(): ?string
    {
        return $this->date_evaluation;
    }

    /**
     * Sets the date of the evaluation.
     * @param string|null $date_evaluation
     */
    public function setDateEvaluation(?string $date_evaluation): void
    {
        $this->date_evaluation = $date_evaluation;
    }

    /**
     * Gets the related Enseignant.
     * @return Enseignant|null
     */
    public function getEnseignant(): ?Enseignant
    {
        if (empty($this->enseignant_id)) {
            return null;
        }
        $stmt = $this->pdo->prepare("SELECT * FROM enseignant WHERE id_utilisateur = :id");
        $stmt->bindParam(':id', $this->enseignant_id);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }
        $enseignant = new Enseignant($this->pdo);
        // Only id_utilisateur should be set as per Enseignant model's corrected properties
        $enseignant->id_utilisateur = $data['id_utilisateur']; 
        return $enseignant;
    }

    /**
     * Gets the related Etudiant.
     * @return Etudiant|null
     */
    public function getEtudiant(): ?Etudiant
    {
        if (empty($this->etudiant_id)) {
            return null;
        }
        $stmt = $this->pdo->prepare("SELECT * FROM etudiant WHERE id_utilisateur = :id");
        $stmt->bindParam(':id', $this->etudiant_id);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }
        $etudiant = new Etudiant($this->pdo);
        // Only id_utilisateur should be set as per Etudiant model's corrected properties
        $etudiant->id_utilisateur = $data['id_utilisateur'];
        return $etudiant;
    }

    /**
     * Gets the related Ecue.
     * @return Ecue|null
     */
    public function getEcue(): ?Ecue
    {
        if (empty($this->ecue_id)) {
            return null;
        }
        $stmt = $this->pdo->prepare("SELECT * FROM ecue WHERE id = :id");
        $stmt->bindParam(':id', $this->ecue_id);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }
        $ecue = new Ecue($this->pdo);
        $ecue->id = $data['id'];
        $ecue->libelle = $data['libelle'];
        $ecue->credit = $data['credit'];
        // Fill other Ecue properties as needed
        return $ecue;
    }
}

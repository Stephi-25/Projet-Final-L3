<?php

namespace App\Models;

use App\Database\BaseModel;
use App\Models\Enseignant;
use App\Models\Grade;
use PDO;

/**
 * Class HistoriqueGrade
 *
 * Represents the historique_grade table.
 * Composite PK: (utilisateur_id, grade_id, date_grade).
 *
 * @package App\Models
 */
class HistoriqueGrade extends BaseModel
{
    /**
     * @var string The database table name.
     */
    protected string $table = 'historique_grade';

    /**
     * @var string The ID of the enseignant (FK to enseignant.utilisateur_id, part of CPK).
     */
    public string $utilisateur_id;

    /**
     * @var string The ID of the grade (FK to grade.id, part of CPK).
     */
    public string $grade_id;

    /**
     * @var string The date the grade was obtained/assigned (part of CPK).
     */
    public string $date_grade; // DDL specifies DATE

    /**
     * HistoriqueGrade constructor.
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

    public function getGradeId(): string
    {
        return $this->grade_id;
    }

    public function setGradeId(string $grade_id): void
    {
        $this->grade_id = $grade_id;
    }

    public function getDateGrade(): string
    {
        return $this->date_grade;
    }

    public function setDateGrade(string $date_grade): void
    {
        $this->date_grade = $date_grade;
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
     * Gets the related Grade.
     * @return Grade|null
     */
    public function getGrade(): ?Grade
    {
        if (empty($this->grade_id)) {
            return null;
        }
        $stmt = $this->pdo->prepare("SELECT * FROM grade WHERE id = :id");
        $stmt->bindParam(':id', $this->grade_id);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }
        $grade = new Grade($this->pdo);
        $grade->id = $data['id'];
        $grade->libelle = $data['libelle'] ?? null;
        return $grade;
    }
}

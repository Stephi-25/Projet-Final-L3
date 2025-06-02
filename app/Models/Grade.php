<?php

namespace App\Models;

use App\Database\BaseModel;
use App\Models\HistoriqueGrade; // Added import
use PDO;

/**
 * Class Grade
 *
 * Represents the grade table.
 *
 * @package App\Models
 */
class Grade extends BaseModel
{
    /**
     * @var string The database table name.
     */
    protected string $table = 'grade';

    /**
     * @var string The ID of the grade.
     */
    public string $id;

    /**
     * @var string The label of the grade.
     */
    public string $libelle;

    /**
     * Grade constructor.
     *
     * @param PDO $pdo The PDO database connection object.
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
    }

    /**
     * Gets the ID of the grade.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Sets the ID of the grade.
     *
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * Gets the label of the grade.
     *
     * @return string
     */
    public function getLibelle(): string
    {
        return $this->libelle;
    }

    /**
     * Sets the label of the grade.
     *
     * @param string $libelle
     */
    public function setLibelle(string $libelle): void
    {
        $this->libelle = $libelle;
    }

    /**
     * Gets all HistoriqueGrade records for this Grade.
     *
     * @return HistoriqueGrade[]
     */
    public function getHistoriqueGrades(): array
    {
        if (empty($this->id)) {
            return [];
        }

        $stmt = $this->pdo->prepare("SELECT * FROM historique_grade WHERE grade_id = :grade_id");
        $stmt->bindParam(':grade_id', $this->id);
        $stmt->execute();
        $historiqueData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $historiques = [];
        if ($historiqueData) {
            foreach ($historiqueData as $data) {
                $historique = new HistoriqueGrade($this->pdo);
                $historique->utilisateur_id = $data['utilisateur_id'];
                $historique->grade_id = $data['grade_id'];
                $historique->date_grade = $data['date_grade'];
                $historiques[] = $historique;
            }
        }
        return $historiques;
    }
}

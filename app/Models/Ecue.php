<?php

namespace App\Models;

use App\Database\BaseModel;
use App\Models\Ue;
use App\Models\Evaluation; // Added import
use PDO;

/**
 * Class Ecue
 *
 * Represents the ecue table.
 *
 * @package App\Models
 */
class Ecue extends BaseModel
{
    /**
     * @var string The database table name.
     */
    protected string $table = 'ecue';

    /**
     * @var string The ID of the ECUE.
     */
    public string $id;

    /**
     * @var string The label of the ECUE.
     */
    public string $libelle;

    /**
     * @var int The number of credits for the ECUE (TINYINT UNSIGNED).
     */
    public int $credit;

    /**
     * Ecue constructor.
     *
     * @param PDO $pdo The PDO database connection object.
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
    }

    /**
     * Gets the ID of the ECUE.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Sets the ID of the ECUE.
     *
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * Gets the label of the ECUE.
     *
     * @return string
     */
    public function getLibelle(): string
    {
        return $this->libelle;
    }

    /**
     * Sets the label of the ECUE.
     *
     * @param string $libelle
     */
    public function setLibelle(string $libelle): void
    {
        $this->libelle = $libelle;
    }

    /**
     * Gets the number of credits for the ECUE.
     *
     * @return int
     */
    public function getCredit(): int
    {
        return $this->credit;
    }

    /**
     * Sets the number of credits for the ECUE.
     *
     * @param int $credit
     */
    public function setCredit(int $credit): void
    {
        $this->credit = $credit;
    }

    /**
     * Gets all Ue records related to this Ecue.
     *
     * @return Ue[] An array of Ue objects. Returns an empty array if none found or current Ecue id is not set.
     */
    public function getUes(): array
    {
        if (empty($this->id)) { // Current Ecue's ID
            return [];
        }

        $stmt = $this->pdo->prepare("SELECT * FROM ue WHERE ecue_id = :ecue_id_val");
        $stmt->bindParam(':ecue_id_val', $this->id); // Use current Ecue's ID to find matching UEs
        $stmt->execute();
        $uesData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $ues = [];
        if ($uesData) {
            foreach ($uesData as $data) {
                $ue = new Ue($this->pdo); // Pass PDO connection
                // Populate Ue based on its corrected defined properties
                $ue->id = $data['id'];
                $ue->libelle = $data['libelle'];
                // Ensure 'credit' column exists in 'ue' table and matches Ue model property
                if (isset($data['credit'])) {
                    $ue->credit = (int)$data['credit'];
                }
                // Ensure 'ecue_id' column exists in 'ue' table
                if (isset($data['ecue_id'])) {
                    $ue->ecue_id = $data['ecue_id'];
                }
                // Do NOT set $ue->code_ue, $ue->heure_ue, $ue->semestre as they are not part of Ue schema
                $ues[] = $ue;
            }
        }
        return $ues;
    }

    /**
     * Gets all Evaluation records for this Ecue.
     *
     * @return Evaluation[]
     */
    public function getEvaluations(): array
    {
        if (empty($this->id)) {
            return [];
        }

        $stmt = $this->pdo->prepare("SELECT * FROM evaluation WHERE ecue_id = :ecue_id");
        $stmt->bindParam(':ecue_id', $this->id);
        $stmt->execute();
        $evaluationsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $evaluations = [];
        if ($evaluationsData) {
            foreach ($evaluationsData as $data) {
                $evaluation = new Evaluation($this->pdo);
                $evaluation->etudiant_id = $data['etudiant_id'] ?? null;
                $evaluation->ecue_id = $data['ecue_id'] ?? null;
                $evaluation->enseignant_id = $data['enseignant_id'] ?? null;
                $evaluation->note = $data['note'] ?? null;
                $evaluation->date_evaluation = $data['date_evaluation'] ?? null;
                $evaluations[] = $evaluation;
            }
        }
        return $evaluations;
    }
}

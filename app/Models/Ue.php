<?php

namespace App\Models;

use App\Database\BaseModel;
use App\Models\Ecue; // Ensure Ecue is imported
use PDO;

/**
 * Class Ue
 *
 * Represents the ue table.
 *
 * @package App\Models
 */
class Ue extends BaseModel
{
    /**
     * @var string The database table name.
     */
    protected string $table = 'ue';

    /**
     * @var string The ID of the UE.
     */
    public string $id;

    /**
     * @var string The label of the UE.
     */
    public string $libelle;

    /**
     * @var int The number of credits for the UE (TINYINT UNSIGNED).
     */
    public int $credit;

    /**
     * @var string|null The ID of the ECUE this UE belongs to (FK to ecue.id).
     */
    public ?string $ecue_id;

    /**
     * Ue constructor.
     *
     * @param PDO $pdo The PDO database connection object.
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
    }

    /**
     * Gets the ID of the UE.
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Sets the ID of the UE.
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * Gets the label of the UE.
     * @return string
     */
    public function getLibelle(): string
    {
        return $this->libelle;
    }

    /**
     * Sets the label of the UE.
     * @param string $libelle
     */
    public function setLibelle(string $libelle): void
    {
        $this->libelle = $libelle;
    }

    /**
     * Gets the number of credits for the UE.
     * @return int
     */
    public function getCredit(): int
    {
        return $this->credit;
    }

    /**
     * Sets the number of credits for the UE.
     * @param int $credit
     */
    public function setCredit(int $credit): void
    {
        $this->credit = $credit;
    }

    /**
     * Gets the ID of the ECUE this UE belongs to.
     * @return string|null
     */
    public function getEcueId(): ?string
    {
        return $this->ecue_id;
    }

    /**
     * Sets the ID of the ECUE this UE belongs to.
     * @param string|null $ecue_id
     */
    public function setEcueId(?string $ecue_id): void
    {
        $this->ecue_id = $ecue_id;
    }

    /**
     * Gets the related Ecue for this Ue.
     *
     * @return Ecue|null The related Ecue object, or null if not found or ecue_id is not set.
     */
    public function getEcue(): ?Ecue
    {
        if (empty($this->ecue_id)) {
            return null;
        }
        
        $stmt = $this->pdo->prepare("SELECT * FROM ecue WHERE id = :ecue_id_val");
        $stmt->bindParam(':ecue_id_val', $this->ecue_id); 
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        $ecue = new Ecue($this->pdo); // Pass PDO connection
        // Populate Ecue based on its defined properties
        $ecue->id = $data['id']; 
        $ecue->libelle = $data['libelle'];
        // Ensure 'credit' column exists in 'ecue' table and matches Ecue model property
        if (isset($data['credit'])) { 
            $ecue->credit = (int)$data['credit'];
        }
        // Do NOT set $ecue->code_ecue or $ecue->id_ue as they are not part of the corrected Ecue schema
        return $ecue;
    }
}

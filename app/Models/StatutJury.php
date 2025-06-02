<?php

namespace App\Models;

use App\Database\BaseModel;
use App\Models\AffectationEncadrant; // Added import
use PDO;

/**
 * Class StatutJury
 *
 * Represents the statut_jury table.
 *
 * @package App\Models
 */
class StatutJury extends BaseModel
{
    /**
     * @var string The database table name.
     */
    protected string $table = 'statut_jury';

    /**
     * @var string The ID of the jury status.
     */
    public string $id;

    /**
     * @var string The label of the jury status.
     */
    public string $libelle;

    /**
     * StatutJury constructor.
     *
     * @param PDO $pdo The PDO database connection object.
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
    }

    /**
     * Gets the ID of the jury status.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Sets the ID of the jury status.
     *
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * Gets the label of the jury status.
     *
     * @return string
     */
    public function getLibelle(): string
    {
        return $this->libelle;
    }

    /**
     * Sets the label of the jury status.
     *
     * @param string $libelle
     */
    public function setLibelle(string $libelle): void
    {
        $this->libelle = $libelle;
    }

    /**
     * Gets all AffectationEncadrant records for this StatutJury.
     *
     * @return AffectationEncadrant[]
     */
    public function getAffectationsEncadrant(): array
    {
        if (empty($this->id)) {
            return [];
        }

        $stmt = $this->pdo->prepare("SELECT * FROM affectation_encadrant WHERE statut_jury_id = :statut_id");
        $stmt->bindParam(':statut_id', $this->id);
        $stmt->execute();
        $affectationsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $affectations = [];
        if ($affectationsData) {
            foreach ($affectationsData as $data) {
                $affectation = new AffectationEncadrant($this->pdo);
                $affectation->utilisateur_id = $data['utilisateur_id'];
                $affectation->rapport_etudiant_id = $data['rapport_etudiant_id'];
                $affectation->statut_jury_id = $data['statut_jury_id'];
                $affectation->date_affectation = $data['date_affectation'] ?? null;
                $affectations[] = $affectation;
            }
        }
        return $affectations;
    }
}

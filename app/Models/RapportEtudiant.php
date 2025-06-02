
<?php

namespace App\Models;

use App\Database\BaseModel;
use App\Models\DepotRapport;
use App\Models\ApprobationRapport;
use App\Models\ValidationRapport;
use App\Models\AffectationEncadrant; // Added import
use PDO;

/**
 * Class RapportEtudiant
 *
 * Represents a student report in the rapport_etudiant table.
 *
 * @package App\Models
 */
class RapportEtudiant extends BaseModel
{
    /**
     * @var string The database table name.
     */
    protected string $table = 'rapport_etudiant';

    /**
     * @var int|null The ID of the report.
     */
    public ?int $id;

    /**
     * @var string|null The title of the report.
     */
    public ?string $titre;

    /**
     * @var string|null The date of the report.
     */
    public ?string $date_rapport;

    /**
     * @var string|null The theme of the thesis.
     */
    public ?string $theme_memoire;

    /**
     * RapportEtudiant constructor.
     *
     * @param PDO $pdo The PDO database connection object.
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
    }

    /**
     * Gets the ID of the report.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Sets the ID of the report.
     *
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * Gets the title of the report.
     *
     * @return string|null
     */
    public function getTitre(): ?string
    {
        return $this->titre;
    }

    /**
     * Sets the title of the report.
     *
     * @param string|null $titre
     */
    public function setTitre(?string $titre): void
    {
        $this->titre = $titre;
    }

    /**
     * Gets the date of the report.
     *
     * @return string|null
     */
    public function getDateRapport(): ?string
    {
        return $this->date_rapport;
    }

    /**
     * Sets the date of the report.
     *
     * @param string|null $date_rapport
     */
    public function setDateRapport(?string $date_rapport): void
    {
        $this->date_rapport = $date_rapport;
    }

    /**
     * Gets the theme of the thesis.
     *
     * @return string|null
     */
    public function getThemeMemoire(): ?string
    {
        return $this->theme_memoire;
    }

    /**
     * Sets the theme of the thesis.
     *
     * @param string|null $theme_memoire
     */
    public function setThemeMemoire(?string $theme_memoire): void
    {
        $this->theme_memoire = $theme_memoire;
    }

    /**
     * Gets all DepotRapport records for this RapportEtudiant.
     *
     * @return DepotRapport[]
     */
    public function getDepotsRapport(): array
    {
        if (empty($this->id)) {
            return [];
        }

        $stmt = $this->pdo->prepare("SELECT * FROM depot_rapport WHERE rapport_etudiant_id = :rapport_id");
        $stmt->bindParam(':rapport_id', $this->id);
        $stmt->execute();
        $depotsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $depots = [];
        if ($depotsData) {
            foreach ($depotsData as $data) {
                $depot = new DepotRapport($this->pdo);
                // Populate DepotRapport based on its corrected properties
                $depot->utilisateur_id = $data['utilisateur_id']; // Corrected FK name
                $depot->rapport_etudiant_id = $data['rapport_etudiant_id'];
                $depot->date_depot = $data['date_depot'] ?? null;
                $depots[] = $depot;
            }
        }
        return $depots;
    }

    /**
     * Gets all ApprobationRapport records for this RapportEtudiant.
     *
     * @return ApprobationRapport[]
     */
    public function getApprobationsRapport(): array
    {
        if (empty($this->id)) {
            return [];
        }

        $stmt = $this->pdo->prepare("SELECT * FROM approbation_rapport WHERE rapport_etudiant_id = :rapport_id");
        $stmt->bindParam(':rapport_id', $this->id);
        $stmt->execute();
        $approbationsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $approbations = [];
        if ($approbationsData) {
            foreach ($approbationsData as $data) {
                $approbation = new ApprobationRapport($this->pdo);
                $approbation->utilisateur_id = $data['utilisateur_id'];
                $approbation->rapport_etudiant_id = $data['rapport_etudiant_id'];
                $approbation->date_approbation = $data['date_approbation'] ?? null;
                $approbations[] = $approbation;
            }
        }
        return $approbations;
    }

    /**
     * Gets all ValidationRapport records for this RapportEtudiant.
     *
     * @return ValidationRapport[]
     */
    public function getValidationsRapport(): array
    {
        if (empty($this->id)) {
            return [];
        }

        $stmt = $this->pdo->prepare("SELECT * FROM validation_rapport WHERE rapport_etudiant_id = :rapport_id");
        $stmt->bindParam(':rapport_id', $this->id);
        $stmt->execute();
        $validationsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $validations = [];
        if ($validationsData) {
            foreach ($validationsData as $data) {
                $validation = new ValidationRapport($this->pdo);
                $validation->utilisateur_id = $data['utilisateur_id'];
                $validation->rapport_etudiant_id = $data['rapport_etudiant_id'];
                $validation->date_validation = $data['date_validation'] ?? null;
                $validation->commentaire = $data['commentaire'] ?? null;
                $validations[] = $validation;
            }
        }
        return $validations;
    }

    /**
     * Gets all AffectationEncadrant records for this RapportEtudiant.
     *
     * @return AffectationEncadrant[]
     */
    public function getAffectationsEncadrant(): array
    {
        if (empty($this->id)) {
            return [];
        }

        $stmt = $this->pdo->prepare("SELECT * FROM affectation_encadrant WHERE rapport_etudiant_id = :rapport_id");
        $stmt->bindParam(':rapport_id', $this->id);
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

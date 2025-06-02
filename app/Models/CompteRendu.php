<?php

namespace App\Models;

use App\Database\BaseModel;
// Removed Etudiant import as id_etudiant FK is being removed
use App\Models\RemiseCompteRendu; // For existing getRemisesCompteRendu
use App\Models\HistoriqueApprobation; // For upcoming getHistoriqueApprobations
use PDO;

/**
 * Class CompteRendu
 *
 * Represents the compte_rendu table.
 * DDL: id VARCHAR(15), titre VARCHAR(255), date_rapport DATE.
 *
 * @package App\Models
 */
class CompteRendu extends BaseModel
{
    /**
     * @var string The database table name.
     */
    protected string $table = 'compte_rendu';

    /**
     * @var string The ID of the report.
     */
    public string $id;

    /**
     * @var string|null The title of the report.
     */
    public ?string $titre;

    /**
     * @var string|null The date of the report. (DDL: DATE)
     */
    public ?string $date_rapport;

    // Removed: contenu, date_creation, id_etudiant

    /**
     * CompteRendu constructor.
     *
     * @param PDO $pdo The PDO database connection object.
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
    }

    // Getters and Setters

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(?string $titre): void
    {
        $this->titre = $titre;
    }

    public function getDateRapport(): ?string
    {
        return $this->date_rapport;
    }

    public function setDateRapport(?string $date_rapport): void
    {
        $this->date_rapport = $date_rapport;
    }

    // Removed getters/setters for contenu, date_creation, id_etudiant
    // Removed CompteRendu::getEtudiant()

    /**
     * Gets all RemiseCompteRendu records for this CompteRendu.
     *
     * @return RemiseCompteRendu[]
     */
    public function getRemisesCompteRendu(): array
    {
        if (empty($this->id)) {
            return [];
        }

        $stmt = $this->pdo->prepare("SELECT * FROM remise_compte_rendu WHERE compte_rendu_id = :compte_rendu_id");
        $stmt->bindParam(':compte_rendu_id', $this->id);
        $stmt->execute();
        $remisesData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $remises = [];
        if ($remisesData) {
            foreach ($remisesData as $data) {
                $remise = new RemiseCompteRendu($this->pdo);
                $remise->utilisateur_id = $data['utilisateur_id'];
                $remise->compte_rendu_id = $data['compte_rendu_id'];
                $remise->date_rendu = $data['date_rendu'] ?? null;
                $remises[] = $remise;
            }
        }
        return $remises;
    }

    /**
     * Gets all HistoriqueApprobation records for this CompteRendu.
     *
     * @return HistoriqueApprobation[]
     */
    public function getHistoriqueApprobations(): array
    {
        if (empty($this->id)) {
            return [];
        }

        $stmt = $this->pdo->prepare("SELECT * FROM historique_approbation WHERE compte_rendu_id = :compte_rendu_id");
        $stmt->bindParam(':compte_rendu_id', $this->id);
        $stmt->execute();
        $historiqueData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $historiques = [];
        if ($historiqueData) {
            foreach ($historiqueData as $data) {
                $hist = new HistoriqueApprobation($this->pdo);
                $hist->niveau_approbation_id = $data['niveau_approbation_id'];
                $hist->compte_rendu_id = $data['compte_rendu_id'];
                $hist->date_approbation = $data['date_approbation'];
                $historiques[] = $hist;
            }
        }
        return $historiques;
    }
}

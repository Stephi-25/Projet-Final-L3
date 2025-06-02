<?php

namespace App\Models;

use App\Database\BaseModel;
use App\Models\HistoriqueApprobation; // Added import
use PDO;

/**
 * Class NiveauApprobation
 *
 * Represents the niveau_approbation table.
 *
 * @package App\Models
 */
class NiveauApprobation extends BaseModel
{
    /**
     * @var string The database table name.
     */
    protected string $table = 'niveau_approbation';

    /**
     * @var string The ID of the approval level.
     */
    public string $id;

    /**
     * @var string The label of the approval level.
     */
    public string $libelle;

    /**
     * NiveauApprobation constructor.
     *
     * @param PDO $pdo The PDO database connection object.
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
    }

    /**
     * Gets the ID of the approval level.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Sets the ID of the approval level.
     *
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * Gets the label of the approval level.
     *
     * @return string
     */
    public function getLibelle(): string
    {
        return $this->libelle;
    }

    /**
     * Sets the label of the approval level.
     *
     * @param string $libelle
     */
    public function setLibelle(string $libelle): void
    {
        $this->libelle = $libelle;
    }

    /**
     * Gets all HistoriqueApprobation records for this NiveauApprobation.
     *
     * @return HistoriqueApprobation[]
     */
    public function getHistoriqueApprobations(): array
    {
        if (empty($this->id)) {
            return [];
        }

        $stmt = $this->pdo->prepare("SELECT * FROM historique_approbation WHERE niveau_approbation_id = :niveau_id");
        $stmt->bindParam(':niveau_id', $this->id);
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

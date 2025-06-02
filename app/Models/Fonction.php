<?php

namespace App\Models;

use App\Database\BaseModel;
use App\Models\HistoriqueFonction; // Added import
use PDO;

/**
 * Class Fonction
 *
 * Represents the fonction table.
 *
 * @package App\Models
 */
class Fonction extends BaseModel
{
    /**
     * @var string The database table name.
     */
    protected string $table = 'fonction';

    /**
     * @var string The ID of the function.
     */
    public string $id;

    /**
     * @var string The label of the function.
     */
    public string $libelle;

    /**
     * Fonction constructor.
     *
     * @param PDO $pdo The PDO database connection object.
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
    }

    /**
     * Gets the ID of the function.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Sets the ID of the function.
     *
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * Gets the label of the function.
     *
     * @return string
     */
    public function getLibelle(): string
    {
        return $this->libelle;
    }

    /**
     * Sets the label of the function.
     *
     * @param string $libelle
     */
    public function setLibelle(string $libelle): void
    {
        $this->libelle = $libelle;
    }

    /**
     * Gets all HistoriqueFonction records for this Fonction.
     *
     * @return HistoriqueFonction[]
     */
    public function getHistoriqueFonctions(): array
    {
        if (empty($this->id)) {
            return [];
        }

        $stmt = $this->pdo->prepare("SELECT * FROM historique_fonction WHERE fonction_id = :fonction_id");
        $stmt->bindParam(':fonction_id', $this->id);
        $stmt->execute();
        $historiqueData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $historiques = [];
        if ($historiqueData) {
            foreach ($historiqueData as $data) {
                $historique = new HistoriqueFonction($this->pdo);
                $historique->utilisateur_id = $data['utilisateur_id'];
                $historique->fonction_id = $data['fonction_id'];
                $historique->date_occupation = $data['date_occupation'];
                $historiques[] = $historique;
            }
        }
        return $historiques;
    }
}

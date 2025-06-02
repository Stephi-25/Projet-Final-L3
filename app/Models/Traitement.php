<?php

namespace App\Models;

use App\Database\BaseModel;
use App\Models\Audit;
use App\Models\AccesTraitement;
use App\Models\AutorisationAction; // Added import
use PDO;

/**
 * Class Traitement
 *
 * Represents the traitement table.
 *
 * @package App\Models
 */
class Traitement extends BaseModel
{
    /**
     * @var string The database table name.
     */
    protected string $table = 'traitement';

    /**
     * @var string The ID of the treatment.
     */
    public string $id;

    /**
     * @var string The label of the treatment.
     */
    public string $libelle;

    /**
     * @var string|null A description of the treatment.
     */
    public ?string $description;

    /**
     * Traitement constructor.
     *
     * @param PDO $pdo The PDO database connection object.
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
    }

    /**
     * Gets the ID of the treatment.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Sets the ID of the treatment.
     *
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * Gets the label of the treatment.
     *
     * @return string
     */
    public function getLibelle(): string
    {
        return $this->libelle;
    }

    /**
     * Sets the label of the treatment.
     *
     * @param string $libelle
     */
    public function setLibelle(string $libelle): void
    {
        $this->libelle = $libelle;
    }

    /**
     * Gets the description of the treatment.
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Sets the description of the treatment.
     *
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * Gets all Audit records related to this Traitement.
     *
     * @return Audit[]
     */
    public function getAudits(): array
    {
        if (empty($this->id)) {
            return [];
        }

        $stmt = $this->pdo->prepare("SELECT * FROM audit WHERE traitement_id = :traitement_id");
        $stmt->bindParam(':traitement_id', $this->id);
        $stmt->execute();
        $auditsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $audits = [];
        if ($auditsData) {
            foreach ($auditsData as $data) {
                $audit = new Audit($this->pdo);
                $audit->id = $data['id'];
                $audit->utilisateur_id = $data['id_utilisateur'] ?? null;
                $audit->action_id = $data['action_id'] ?? null;
                $audit->traitement_id = $data['traitement_id'] ?? null;
                $audit->action_effectuee = $data['action_effectuee'] ?? null;
                $audit->timestamp_action = $data['timestamp_action'] ?? null;
                $audit->adresse_ip = $data['adresse_ip'] ?? null;
                $audits[] = $audit;
            }
        }
        return $audits;
    }

    /**
     * Gets all AccesTraitement records for this Traitement.
     *
     * @return AccesTraitement[]
     */
    public function getAccesTraitements(): array
    {
        if (empty($this->id)) {
            return [];
        }

        $stmt = $this->pdo->prepare("SELECT * FROM acces_traitement WHERE traitement_id = :traitement_id");
        $stmt->bindParam(':traitement_id', $this->id);
        $stmt->execute();
        $accesData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $accesItems = [];
        if ($accesData) {
            foreach ($accesData as $data) {
                $acces = new AccesTraitement($this->pdo);
                $acces->traitement_id = $data['traitement_id'];
                $acces->utilisateur_id = $data['utilisateur_id'];
                $acces->date_accesion = $data['date_accesion'] ?? null;
                $acces->heure_accesion = $data['heure_accesion'] ?? null;
                $accesItems[] = $acces;
            }
        }
        return $accesItems;
    }

    /**
     * Gets all AutorisationAction records for this Traitement.
     *
     * @return AutorisationAction[]
     */
    public function getAutorisationsAction(): array
    {
        if (empty($this->id)) {
            return [];
        }

        $stmt = $this->pdo->prepare("SELECT * FROM autorisation_action WHERE traitement_id = :traitement_id");
        $stmt->bindParam(':traitement_id', $this->id);
        $stmt->execute();
        $autorisationsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $autorisations = [];
        if ($autorisationsData) {
            foreach ($autorisationsData as $data) {
                $autorisation = new AutorisationAction($this->pdo);
                $autorisation->groupe_utilisateur_id = $data['groupe_utilisateur_id'];
                $autorisation->traitement_id = $data['traitement_id'];
                $autorisation->action_id = $data['action_id'];
                $autorisations[] = $autorisation;
            }
        }
        return $autorisations;
    }
}

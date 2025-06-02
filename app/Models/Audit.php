<?php

namespace App\Models;

use App\Database\BaseModel;
use App\Models\Action;
use App\Models\Traitement;
use App\Models\Utilisateur;
use PDO;

/**
 * Class Audit
 *
 * Represents the audit table.
 *
 * @package App\Models
 */
class Audit extends BaseModel
{
    /**
     * @var string The database table name.
     */
    protected string $table = 'audit';

    /**
     * @var int|null The ID of the audit entry (auto-incrementing).
     */
    public ?int $id;

    /**
     * @var string|null The ID of the user who performed the action (FK to utilisateur.id).
     */
    public ?string $utilisateur_id;

    /**
     * @var string|null The ID of the action related to this audit entry.
     */
    public ?string $action_id;

    /**
     * @var string|null The ID of the traitement related to this audit entry.
     */
    public ?string $traitement_id;

    /**
     * @var string|null The action performed.
     */
    public ?string $action_effectuee;

    /**
     * @var string|null The timestamp of the action.
     */
    public ?string $timestamp_action; // Assuming DATETIME or TIMESTAMP

    /**
     * @var string|null The IP address from which the action was performed.
     */
    public ?string $adresse_ip;

    /**
     * Audit constructor.
     *
     * @param PDO $pdo The PDO database connection object.
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
    }

    /**
     * Gets the ID of the audit entry.
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Sets the ID of the audit entry.
     * Note: Usually not set manually for auto-incrementing keys.
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * Gets the ID of the user who performed the action.
     * @return string|null
     */
    public function getUtilisateurId(): ?string
    {
        return $this->utilisateur_id;
    }

    /**
     * Sets the ID of the user who performed the action.
     * @param string|null $utilisateur_id
     */
    public function setUtilisateurId(?string $utilisateur_id): void
    {
        $this->utilisateur_id = $utilisateur_id;
    }

    /**
     * Gets the action performed.
     * @return string|null
     */
    public function getActionEffectuee(): ?string
    {
        return $this->action_effectuee;
    }

    /**
     * Sets the action performed.
     * @param string|null $action_effectuee
     */
    public function setActionEffectuee(?string $action_effectuee): void
    {
        $this->action_effectuee = $action_effectuee;
    }

    /**
     * Gets the timestamp of the action.
     * @return string|null
     */
    public function getTimestampAction(): ?string
    {
        return $this->timestamp_action;
    }

    /**
     * Sets the timestamp of the action.
     * @param string|null $timestamp_action
     */
    public function setTimestampAction(?string $timestamp_action): void
    {
        $this->timestamp_action = $timestamp_action;
    }

    /**
     * Gets the IP address.
     * @return string|null
     */
    public function getAdresseIp(): ?string
    {
        return $this->adresse_ip;
    }

    /**
     * Sets the IP address.
     * @param string|null $adresse_ip
     */
    public function setAdresseIp(?string $adresse_ip): void
    {
        $this->adresse_ip = $adresse_ip;
    }

    /**
     * Gets the ID of the action.
     * @return string|null
     */
    public function getActionId(): ?string
    {
        return $this->action_id;
    }

    /**
     * Sets the ID of the action.
     * @param string|null $action_id
     */
    public function setActionId(?string $action_id): void
    {
        $this->action_id = $action_id;
    }

    /**
     * Gets the ID of the traitement.
     * @return string|null
     */
    public function getTraitementId(): ?string
    {
        return $this->traitement_id;
    }

    /**
     * Sets the ID of the traitement.
     * @param string|null $traitement_id
     */
    public function setTraitementId(?string $traitement_id): void
    {
        $this->traitement_id = $traitement_id;
    }

    /**
     * Gets the related Action.
     * @return Action|null
     */
    public function getAction(): ?Action
    {
        if (empty($this->action_id)) {
            return null;
        }
        $stmt = $this->pdo->prepare("SELECT * FROM action WHERE id = :id");
        $stmt->bindParam(':id', $this->action_id);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }
        $action = new Action($this->pdo);
        $action->id = $data['id'];
        $action->libelle = $data['libelle'];
        $action->description = $data['description'] ?? null;
        return $action;
    }

    /**
     * Gets the related Traitement.
     * @return Traitement|null
     */
    public function getTraitement(): ?Traitement
    {
        if (empty($this->traitement_id)) {
            return null;
        }
        $stmt = $this->pdo->prepare("SELECT * FROM traitement WHERE id = :id");
        $stmt->bindParam(':id', $this->traitement_id);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }
        $traitement = new Traitement($this->pdo);
        $traitement->id = $data['id'];
        $traitement->libelle = $data['libelle'];
        $traitement->description = $data['description'] ?? null;
        return $traitement;
    }

    /**
     * Gets the related Utilisateur.
     * @return Utilisateur|null
     */
    public function getUtilisateur(): ?Utilisateur
    {
        if (empty($this->utilisateur_id)) {
            return null;
        }
        $stmt = $this->pdo->prepare("SELECT * FROM utilisateur WHERE id = :id");
        $stmt->bindParam(':id', $this->utilisateur_id);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }
        $utilisateur = new Utilisateur($this->pdo);
        $utilisateur->id = $data['id'];
        $utilisateur->nom_utilisateur = $data['nom_utilisateur'];
        $utilisateur->mot_de_passe = $data['mot_de_passe'];
        $utilisateur->email = $data['email'] ?? null;
        $utilisateur->date_creation_compte = $data['date_creation_compte'] ?? null;
        $utilisateur->date_derniere_connexion = $data['date_derniere_connexion'] ?? null;
        $utilisateur->type_utilisateur_id = $data['type_utilisateur_id'] ?? null; // Updated FK name
        $utilisateur->groupe_utilisateur_id = $data['groupe_utilisateur_id'] ?? null;
        $utilisateur->niveau_acces_donnees_id = $data['niveau_acces_donnees_id'] ?? null;
        return $utilisateur;
    }
}

<?php

namespace App\Models;

use App\Database\BaseModel;
use App\Models\GroupeUtilisateur;
use App\Models\Traitement;
use App\Models\Action;
use PDO;

/**
 * Class AutorisationAction
 *
 * Represents the autorisation_action table.
 * Composite PK: (groupe_utilisateur_id, traitement_id, action_id).
 *
 * @package App\Models
 */
class AutorisationAction extends BaseModel
{
    /**
     * @var string The database table name.
     */
    protected string $table = 'autorisation_action';

    /**
     * @var string The ID of the groupe_utilisateur (FK, part of CPK).
     */
    public string $groupe_utilisateur_id;

    /**
     * @var string The ID of the traitement (FK, part of CPK).
     */
    public string $traitement_id;

    /**
     * @var string The ID of the action (FK, part of CPK).
     */
    public string $action_id;

    /**
     * AutorisationAction constructor.
     *
     * @param PDO $pdo The PDO database connection object.
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
    }

    // Getters and Setters

    public function getGroupeUtilisateurId(): string
    {
        return $this->groupe_utilisateur_id;
    }

    public function setGroupeUtilisateurId(string $groupe_utilisateur_id): void
    {
        $this->groupe_utilisateur_id = $groupe_utilisateur_id;
    }

    public function getTraitementId(): string
    {
        return $this->traitement_id;
    }

    public function setTraitementId(string $traitement_id): void
    {
        $this->traitement_id = $traitement_id;
    }

    public function getActionId(): string
    {
        return $this->action_id;
    }

    public function setActionId(string $action_id): void
    {
        $this->action_id = $action_id;
    }

    // Relationship methods

    /**
     * Gets the related GroupeUtilisateur.
     * @return GroupeUtilisateur|null
     */
    public function getGroupeUtilisateur(): ?GroupeUtilisateur
    {
        if (empty($this->groupe_utilisateur_id)) {
            return null;
        }
        $stmt = $this->pdo->prepare("SELECT * FROM groupe_utilisateur WHERE id = :id");
        $stmt->bindParam(':id', $this->groupe_utilisateur_id);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }
        $groupe = new GroupeUtilisateur($this->pdo);
        $groupe->id = $data['id'];
        $groupe->libelle = $data['libelle'];
        $groupe->description = $data['description'] ?? null;
        return $groupe;
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
        // $traitement->description = $data['description'] ?? null; // Traitement DDL not specified
        return $traitement;
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
        // $action->description = $data['description'] ?? null; // Action DDL not specified
        return $action;
    }
}

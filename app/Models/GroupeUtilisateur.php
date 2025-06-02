<?php

namespace App\Models;

use App\Database\BaseModel;
use App\Models\Utilisateur;
use App\Models\AutorisationAction; // Added import
use PDO;

/**
 * Class GroupeUtilisateur
 *
 * Represents the groupe_utilisateur table.
 *
 * @package App\Models
 */
class GroupeUtilisateur extends BaseModel
{
    /**
     * @var string The database table name.
     */
    protected string $table = 'groupe_utilisateur';

    /**
     * @var string The ID of the user group.
     */
    public string $id;

    /**
     * @var string The label of the user group.
     */
    public string $libelle;

    /**
     * @var string|null A description of the user group.
     */
    public ?string $description;

    /**
     * GroupeUtilisateur constructor.
     *
     * @param PDO $pdo The PDO database connection object.
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
    }

    /**
     * Gets the ID of the user group.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Sets the ID of the user group.
     *
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * Gets the label of the user group.
     *
     * @return string
     */
    public function getLibelle(): string
    {
        return $this->libelle;
    }

    /**
     * Sets the label of the user group.
     *
     * @param string $libelle
     */
    public function setLibelle(string $libelle): void
    {
        $this->libelle = $libelle;
    }

    /**
     * Gets the description of the user group.
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Sets the description of the user group.
     *
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * Gets all Utilisateur records related to this GroupeUtilisateur.
     *
     * @return Utilisateur[]
     */
    public function getUtilisateurs(): array
    {
        if (empty($this->id)) {
            return [];
        }

        $stmt = $this->pdo->prepare("SELECT * FROM utilisateur WHERE groupe_utilisateur_id = :groupe_id");
        $stmt->bindParam(':groupe_id', $this->id);
        $stmt->execute();
        $usersData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $users = [];
        if ($usersData) {
            foreach ($usersData as $data) {
                $user = new Utilisateur($this->pdo);
                $user->id = $data['id'];
                $user->nom_utilisateur = $data['nom_utilisateur'];
                $user->mot_de_passe = $data['mot_de_passe'];
                $user->email = $data['email'] ?? null;
                $user->date_creation_compte = $data['date_creation_compte'] ?? null;
                $user->date_derniere_connexion = $data['date_derniere_connexion'] ?? null;
                $user->type_utilisateur_id = $data['id_type_utilisateur'] ?? null;
                $user->groupe_utilisateur_id = $data['groupe_utilisateur_id'] ?? null;
                $user->niveau_acces_donnees_id = $data['niveau_acces_donnees_id'] ?? null;
                $users[] = $user;
            }
        }
        return $users;
    }

    /**
     * Gets all AutorisationAction records for this GroupeUtilisateur.
     *
     * @return AutorisationAction[]
     */
    public function getAutorisationsAction(): array
    {
        if (empty($this->id)) {
            return [];
        }

        $stmt = $this->pdo->prepare("SELECT * FROM autorisation_action WHERE groupe_utilisateur_id = :groupe_id");
        $stmt->bindParam(':groupe_id', $this->id);
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
